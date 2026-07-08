<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Site;
use App\Models\Subscription as SiteSubscription;
use App\Models\User;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail; // 👈 Added
use App\Mail\SubscriptionPurchased;   // 👈 Added

class ManageSubscriptionController extends Controller
{
    public function index(Package $package)
    {
        $user = auth()->user();
        $paymentMethod = $user->defaultPaymentMethod();
        return view('subscription.manage.index', compact('package', 'paymentMethod'));
    }

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'package_id'    => 'required|exists:packages,id',
            'quantity'      => 'required|integer|min:1',
            'billing_cycle' => 'required|in:monthly,annual'
        ]);

        $user    = auth()->user();
        $package = Package::findOrFail($request->package_id);

        if ($user->subscribed('default')) {
            return response()->json([
                'error' => 'You already have an active subscription.'
            ], 422);
        }

        // 🔹 FIX: Handle invalid/ghost Stripe Customer IDs
        try {
            $user->createOrGetStripeCustomer();
        } catch (\Exception $e) {
            $user->stripe_id = null;
            $user->save();
            $user->createOrGetStripeCustomer();
        }

        $priceId = $request->billing_cycle === 'annual'
            ? $package->stripe_annual_price_id
            : $package->stripe_monthly_price_id;

        if (!$priceId) {
            return response()->json(['error' => 'Stripe price ID missing'], 422);
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Incomplete subscription clean karo
            $existing = $user->subscriptions()->where('type', 'default')->first();
            if ($existing && $existing->stripe_status === 'incomplete') {
                try {
                    $stripeSub = \Stripe\Subscription::retrieve($existing->stripe_id);
                    $stripeSub->cancel();
                } catch (\Exception $e) {}
                $existing->items()->delete();
                $existing->delete();
            }

            // Stripe pe subscription banao
            $stripeSubscription = \Stripe\Subscription::create([
                'customer'         => $user->stripe_id,
                'items'            => [[
                    'price'    => $priceId,
                    'quantity' => $request->quantity,
                ]],
                'payment_behavior' => 'default_incomplete',
                'collection_method'=> 'charge_automatically',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                    'payment_method_types'        => ['card'],
                ],
                'metadata' => [
                    'package_id'    => $package->id,
                    'billing_cycle' => $request->billing_cycle,
                    'site_id'       => $user->site_id,
                    'user_id'       => $user->id,
                ],
            ]);

            // Cashier ki 'subscriptions' table mein save karo
            $cashierSub = $user->subscriptions()->updateOrCreate(
                ['type' => 'default'],
                [
                    'stripe_id'     => $stripeSubscription->id,
                    'stripe_status' => $stripeSubscription->status,
                    'stripe_price'  => $priceId,
                    'quantity'      => $request->quantity,
                    'trial_ends_at' => null,
                    'ends_at'       => null,
                ]
            );

            $subItems = $stripeSubscription->items->data ?? [];
            if (!empty($subItems)) {
                $item = $subItems[0];
                $cashierSub->items()->updateOrCreate(
                    ['stripe_id' => $item->id],
                    [
                        'stripe_product' => $item->price->product,
                        'stripe_price'   => $item->price->id,
                        'quantity'       => $item->quantity,
                    ]
                );
            }

            // Saved card flow
            if ($request->has('use_saved_card') && $request->use_saved_card == 1 && $user->hasDefaultPaymentMethod()) {
                $invoice = \Stripe\Invoice::retrieve($stripeSubscription->latest_invoice);
                $invoice->pay();

                $cashierSub->update(['stripe_status' => 'active']);

                $this->syncSiteSubscription($user, $stripeSubscription, $package, $request->billing_cycle);

                return response()->json([
                    'directSuccess' => true,
                    'subscriptionId' => $stripeSubscription->id
                ]);
            }

            // New card flow — SetupIntent
            \Stripe\Customer::update($user->stripe_id, [
                'invoice_settings' => ['default_payment_method' => null]
            ]);

            $setupIntent = \Stripe\SetupIntent::create([
                'customer'             => $user->stripe_id,
                'payment_method_types' => ['card'],
                'usage'                => 'off_session',
            ]);

            return response()->json([
                'setupIntentSecret' => $setupIntent->client_secret,
                'subscriptionId'    => $stripeSubscription->id,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirmSubscription(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
            'subscription_id'   => 'required|string',
            'save_card'         => 'nullable|boolean',
        ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $user = auth()->user();

        try {
            if ($request->boolean('save_card')) {
                $paymentMethod = \Stripe\PaymentMethod::retrieve($request->payment_method_id);
                $paymentMethod->attach(['customer' => $user->stripe_id]);

                \Stripe\Customer::update($user->stripe_id, [
                    'invoice_settings' => [
                        'default_payment_method' => $request->payment_method_id
                    ]
                ]);
            }

            $stripeSub = \Stripe\Subscription::retrieve($request->subscription_id);
            $invoiceId = $stripeSub->latest_invoice;

            $invoiceResponse = Http::withToken(config('services.stripe.secret'))
                ->asForm()
                ->post('https://api.stripe.com/v1/invoices/' . $invoiceId . '/pay', [
                    'payment_method' => $request->payment_method_id,
                    'expand[]'       => 'payment_intent',
                ]);

            $invoice = $invoiceResponse->json();

            if (isset($invoice['payment_intent']['status']) &&
                $invoice['payment_intent']['status'] === 'requires_action') {
                return response()->json([
                    'requires_action' => true,
                    'client_secret'   => $invoice['payment_intent']['client_secret'],
                ]);
            }

            $subscription = $user->subscriptions()->where('stripe_id', $request->subscription_id)->first();
            if ($subscription) {
                $subscription->update(['stripe_status' => 'active']);
            }

            $freshStripeSub = \Stripe\Subscription::retrieve([
                'id'     => $request->subscription_id,
                'expand' => ['items.data.price'],
            ]);

            $package = null;
            $billingCycle = 'monthly';
            if (!empty($freshStripeSub->metadata->package_id)) {
                $package = Package::find($freshStripeSub->metadata->package_id);
                $billingCycle = $freshStripeSub->metadata->billing_cycle ?? 'monthly';
            }

            $this->syncSiteSubscription($user, $freshStripeSub, $package, $billingCycle);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function success(Request $request)
    {
        $user = auth()->user();
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $stripeSubId = $request->subscription_id;

        if (!$stripeSubId) {
            return redirect()->route('subscription.index')->with('error', 'Subscription ID missing.');
        }

        try {
            $stripeSub = \Stripe\Subscription::retrieve([
                'id'     => $stripeSubId,
                'expand' => ['items.data.price', 'latest_invoice'],
            ]);
        } catch (\Exception $e) {
            return redirect()->route('subscription.index')->with('error', 'Stripe error: ' . $e->getMessage());
        }

        if ($stripeSub->status !== 'active') {
            return redirect()->route('subscription.index')->with('error', 'Payment not completed. Status: ' . $stripeSub->status);
        }

        $subscription = $user->subscriptions()->where('stripe_id', $stripeSubId)->first();
        if ($subscription) {
            $subscription->update(['stripe_status' => 'active']);
        }

        $package = null;
        $billingCycle = 'monthly';

        if (!empty($stripeSub->metadata->package_id)) {
            $package = Package::find($stripeSub->metadata->package_id);
            $billingCycle = $stripeSub->metadata->billing_cycle ?? 'monthly';
        } else {
            $priceId = $stripeSub->items->data[0]->price->id ?? null;
            if ($priceId) {
                $package = Package::where('stripe_monthly_price_id', $priceId)
                    ->orWhere('stripe_annual_price_id', $priceId)
                    ->first();
                if ($package) {
                    $billingCycle = ($package->stripe_annual_price_id === $priceId) ? 'annual' : 'monthly';
                }
            }
        }

        // 🔹 site_subscriptions sync
        $site = $this->syncSiteSubscription($user, $stripeSub, $package, $billingCycle);

        // 🔹 SEND EMAIL NOTIFICATION ON SUCCESS
        if ($site) {
            $quantity = $stripeSub->quantity ?? ($stripeSub->items->data[0]->quantity ?? 1);
            $dashboardUrl = tenant_url('admin/subscription/invoice', $site->site_id);

            Mail::to($user->email)->send(new SubscriptionPurchased(
                $user,
                $package,
                $site,
                $billingCycle,
                $quantity,
                $dashboardUrl
            ));
        }

        // Invoice for view
        $invoice = null;
        try {
            if (!empty($stripeSub->latest_invoice) && is_object($stripeSub->latest_invoice)) {
                $invoice = $stripeSub->latest_invoice;
            } elseif (!empty($stripeSub->latest_invoice)) {
                $invoice = \Stripe\Invoice::retrieve($stripeSub->latest_invoice);
            }
        } catch (\Exception $e) {}

        return view('subscription.success', compact('site', 'invoice'));
    }

    /**
     * 🔹 Central helper — site_subscriptions + sites table sync karo
     */
    private function syncSiteSubscription(\App\Models\User $user, $stripeSub, ?Package $package, string $billingCycle): ?Site
    {
        $site = Site::where('site_id', $user->site_id)->first();

        if (!$site) return null;

        $nextBillingDate = null;
        if (isset($stripeSub->current_period_end) && $stripeSub->current_period_end) {
            $nextBillingDate = \Carbon\Carbon::createFromTimestamp($stripeSub->current_period_end);
        }

        $quantity = $stripeSub->quantity ?? ($stripeSub->items->data[0]->quantity ?? 1);
        $priceId  = $stripeSub->items->data[0]->price->id ?? null;

        $site->update([
            'subscription_status' => 'active',
            'agent_limit'         => $quantity,
            'plan_name'           => $package?->title,
        ]);

        SiteSubscription::updateOrCreate(
            ['stripe_id' => $stripeSub->id],
            [
                'user_id'       => $user->id,
                'site_id'       => $site->id,
                'package_id'    => $package?->id,
                'stripe_status' => 'active',
                'stripe_price'  => $priceId,
                'quantity'      => $quantity,
                'billing_cycle' => $billingCycle,
                'ends_at'       => $nextBillingDate,
            ]
        );

        return $site;
    }
}