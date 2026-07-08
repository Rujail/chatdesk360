@component('mail::message')
# Hello {{ $user->name }},

Thank you for subscribing! Your payment has been successfully processed and your subscription is now active. 

@component('mail::panel')
**Subscription Details:**
- **Plan:** {{ $package?->title ?? 'Custom Plan' }}
- **Billing Cycle:** {{ ucfirst($billingCycle) }}
- **Agents:** {{ $quantity }}
- **Status:** Active
@endcomponent

You can view your invoices and manage your account details directly from your dashboard.

@component('mail::button', ['url' => $dashboardUrl, 'color' => 'primary']) <!-- 👈 Changed here -->
Go to Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent