<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookReceived;
use App\Listeners\StripeEventListener;
use App\Models\Tenant; // 👈 Added
use Illuminate\Http\Request; // 👈 Added

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->usePublicPath(realpath(base_path('../public_html')));

        // Register currentTenant as a singleton binding
        $this->app->singleton(Tenant::class, function ($app) {
            
            // If running in console (artisan, queue worker, webhooks), 
            // there is no HTTP request, so return null to prevent errors.
            if ($app->runningInConsole()) {
                return null;
            }

            $request = $app->make(Request::class);
            $host = $request->getHost();
            $mainDomain = config('app.main_domain');

            // Skip tenant resolution for main domain
            if ($host === $mainDomain || $host === 'www.' . $mainDomain || $host === 'localhost') {
                return null;
            }

            // Fetch and cache the tenant for this request
            return Tenant::where('domain_name', $host)->first();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share $tenant globally with all Blade views
        View::composer('*', function ($view) {
            $view->with('tenant', tenant());
        });

        // Stripe Webhook Listener
        Event::listen(
            WebhookReceived::class,
            StripeEventListener::class
        );
    }
}