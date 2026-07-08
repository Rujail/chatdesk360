<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Invoices — canceled users ki bhi dikhao
        $invoices = $user->invoices();

        

        // 1b. Pre-process invoice data for the view
        $processedInvoices = $invoices->map(function ($invoice) use ($user) {
            $statusClass = $invoice->status === 'paid' ? 'paid' : ($invoice->status === 'open' ? 'pending' : 'overdue');
            $statusText  = ucfirst($invoice->status === 'open' ? 'pending' : $invoice->status);

            $lineDescription = 'ChatDesk360 Subscription';
            if (isset($invoice->lines->data[0])) {
                $line = $invoice->lines->data[0];
                $lineDescription = !empty($line->description)
                    ? $line->description
                    : (isset($line->plan) ? ($line->plan->nickname ?? $lineDescription) : $lineDescription);
            }

            // ✅ Due date fix — charge_automatically subscriptions mein due_date null hoti hai
            $periodStart = 'N/A';
            $periodEnd   = 'N/A';
            if (isset($invoice->lines->data[0]->period)) {
                $period = $invoice->lines->data[0]->period;
                if (!empty($period->start)) {
                    $periodStart = Carbon::createFromTimestamp($period->start)->format('M d, Y');
                }
                if (!empty($period->end)) {
                    $periodEnd = Carbon::createFromTimestamp($period->end)->format('M d, Y');
                }
            }

            return [
                'invoice'       => $invoice,
                'number'        => $invoice->number ?? $invoice->id,
                'date'          => Carbon::createFromTimestamp($invoice->created)->format('M d, Y'),
                'total'         => number_format($invoice->total / 100, 2),
                'status_class'  => $statusClass,
                'status_text'   => $statusText,
                'invoice_data'  => [
                    "id"           => $invoice->number ?? $invoice->id,
                    "client"       => $user->name,
                    "email"        => $user->email,
                    "date"         => Carbon::createFromTimestamp($invoice->created)->format('M d, Y'),
                    "period_start" => $periodStart,
                    "period_end"   => $periodEnd,
                    "amount"       => number_format($invoice->total / 100, 2),
                    "status"       => $statusClass,
                    "statusText"   => $statusText,
                    "items"        => [
                        ["desc" => $lineDescription, "qty" => 1, "price" => $invoice->total / 100]
                    ]
                ]
            ];
        });

        // 2. Upcoming Invoice — sirf active subscribed users ke liye
        $upcomingInvoice = null;
        if ($user->subscribed('default')) {
            try {
                $upcomingInvoice = $user->upcomingInvoice();
            } catch (\Exception $e) {
                // Agar upcoming invoice na ho toh ignore karo
            }
        }

        // 3. Subscription details
        $subscriptionDetails = null;
        $subscription = $user->subscription('default');

        if ($subscription) {
            $package = Package::where('stripe_monthly_price_id', $subscription->stripe_price)
                ->orWhere('stripe_annual_price_id', $subscription->stripe_price)
                ->first();

            $billingCycle = 'Monthly';
            if ($package && $package->stripe_annual_price_id === $subscription->stripe_price) {
                $billingCycle = 'Yearly';
            }

            $nextBillingDate = 'N/A';
            if ($upcomingInvoice && $upcomingInvoice->next_payment_attempt) {
                $nextBillingDate = Carbon::createFromTimestamp($upcomingInvoice->next_payment_attempt)->format('M d, Y');
            } else {
                try {
                    $stripeSub = $subscription->asStripeSubscription();
                    if ($stripeSub && isset($stripeSub->current_period_end) && $stripeSub->current_period_end) {
                        $nextBillingDate = Carbon::createFromTimestamp($stripeSub->current_period_end)->format('M d, Y');
                    }
                } catch (\Exception $e) {}
            }

            [$statusText, $badgeClass] = $this->resolveStatus($subscription);

            $subscriptionDetails = [
                'plan_name'          => $package?->title ?? 'Custom Plan',
                'billing_cycle'      => $billingCycle,
                'status'             => $statusText,
                'status_badge'       => $badgeClass,
                'next_billing_date'  => $nextBillingDate,
                'ends_at'            => $subscription->ends_at
                                            ? Carbon::parse($subscription->ends_at)->format('M d, Y')
                                            : null,
            ];
        }

        return view('subscription.invoice.index', compact('processedInvoices', 'subscriptionDetails', 'upcomingInvoice'));
    }

    private function resolveStatus($subscription): array
    {
        $stripeStatus = $subscription->stripe_status;

        if ($subscription->canceled() && !$subscription->ended()) {
            return ['Cancels on ' . Carbon::parse($subscription->ends_at)->format('M d, Y'), 'pending'];
        }

        return match($stripeStatus) {
            'active'              => ['Active', 'paid'],
            'trialing'            => ['Trial', 'paid'],
            'past_due'            => ['Past Due — Payment Failed', 'pending'],
            'unpaid'              => ['Unpaid', 'overdue'],
            'canceled'            => ['Canceled', 'overdue'],
            'expired'             => ['Expired', 'overdue'],
            'incomplete'          => ['Incomplete', 'pending'],
            'incomplete_expired'  => ['Expired', 'overdue'],
            default               => [ucfirst($stripeStatus), 'pending'],
        };
    }
}