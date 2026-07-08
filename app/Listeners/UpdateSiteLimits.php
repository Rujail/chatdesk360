<?php

namespace App\Listeners;

use App\Models\Site;
use Laravel\Cashier\Events\WebhookReceived;
use Carbon\Carbon;

class UpdateSiteLimits
{
    public function handle(WebhookReceived $event)
    {
        if ($event->payload['type'] === 'checkout.session.completed') {
            
            $metadata = $event->payload['data']['object']['metadata'];
            $siteId = $metadata['site_id'] ?? null;
            $planName = $metadata['plan_name'] ?? null;
            $agentLimit = $metadata['agent_limit'] ?? 1;

            if ($siteId) {
                $site = Site::where('site_id', $siteId)->first();
                if ($site) {
                    $site->update([
                        'plan_name' => $planName,
                        'agent_limit' => $agentLimit,
                        'subscription_status' => 'active',
                        'subscription_ends_at' => Carbon::now()->addMonth(), // Will be updated by Stripe recurring webhooks
                    ]);
                }
            }
        }
    }
}