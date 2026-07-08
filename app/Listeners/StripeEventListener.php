<?php

namespace App\Listeners;

use App\Models\Site;
use App\Models\Subscription as SiteSubscription;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $type    = $payload['type'] ?? '';
        $data    = $payload['data']['object'] ?? [];

        match ($type) {
            'customer.subscription.deleted' => $this->onSubscriptionCanceled($data),
            'customer.subscription.updated' => $this->onSubscriptionUpdated($data),
            'invoice.payment_failed'        => $this->onPaymentFailed($data),
            default                         => null,
        };
    }

    private function onSubscriptionCanceled(array $data): void
    {
        $stripeSubId = $data['id'] ?? null;
        if (!$stripeSubId) return;

        // Stripe ka cancel_at_period_end check karo
        // Agar true tha matlab graceful cancel — period end pe expired hoga
        // Agar false tha matlab immediately cancel — status 'canceled'
        $cancelAtPeriodEnd = $data['cancel_at_period_end'] ?? false;
        $canceledAt        = $data['canceled_at'] ?? null;
        $currentPeriodEnd  = $data['current_period_end'] ?? null;

        // Agar period already end ho chuki hai = expired
        // Agar abhi period chal rahi thi aur immediately cancel = canceled
        $now = time();
        if ($currentPeriodEnd && $currentPeriodEnd < $now) {
            $newStatus = 'expired';
        } else {
            $newStatus = 'canceled';
        }

        SiteSubscription::where('stripe_id', $stripeSubId)
            ->update(['stripe_status' => $newStatus]);

        $siteSub = SiteSubscription::where('stripe_id', $stripeSubId)->first();
        if ($siteSub) {
            Site::where('id', $siteSub->site_id)
                ->update(['subscription_status' => $newStatus]);
        }
    }

    private function onSubscriptionUpdated(array $data): void
    {
        $stripeSubId = $data['id'] ?? null;
        $status      = $data['status'] ?? null;
        if (!$stripeSubId || !$status) return;

        // Stripe statuses: active, past_due, canceled, unpaid, incomplete, incomplete_expired, trialing
        // 'canceled' ko 'expired' treat karo agar period end ho chuki hai
        $mappedStatus = $this->mapStripeStatus($data);

        SiteSubscription::where('stripe_id', $stripeSubId)
            ->update(['stripe_status' => $mappedStatus]);

        // Sites table bhi update karo
        $siteSub = SiteSubscription::where('stripe_id', $stripeSubId)->first();
        if ($siteSub) {
            Site::where('id', $siteSub->site_id)
                ->update(['subscription_status' => $mappedStatus]);
        }
    }

    private function onPaymentFailed(array $data): void
    {
        $stripeSubId = $data['subscription'] ?? null;
        if (!$stripeSubId) return;

        SiteSubscription::where('stripe_id', $stripeSubId)
            ->update(['stripe_status' => 'past_due']);

        $siteSub = SiteSubscription::where('stripe_id', $stripeSubId)->first();
        if ($siteSub) {
            Site::where('id', $siteSub->site_id)
                ->update(['subscription_status' => 'past_due']);
        }
    }

    /**
     * Stripe status ko apne system ke liye map karo
     */
    private function mapStripeStatus(array $data): string
    {
        $status           = $data['status'] ?? 'canceled';
        $currentPeriodEnd = $data['current_period_end'] ?? null;
        $now              = time();

        return match(true) {
            // Period end ho chuki + canceled = expired
            $status === 'canceled' && $currentPeriodEnd && $currentPeriodEnd < $now => 'expired',
            // incomplete_expired bhi expired treat karo
            $status === 'incomplete_expired' => 'expired',
            // Baaki sab as-is
            default => $status,
        };
    }
}