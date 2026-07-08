<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Laravel\Cashier\Http\Controllers\WebhookController;

use App\Http\Middleware\EnsureTenantMatch;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatFrameWidgetController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrafficController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\Settings\BannedCustomersController;
use App\Http\Controllers\Settings\ShortcutController;
use App\Http\Controllers\Settings\WidgetController;
use App\Http\Controllers\Settings\InstallChatController;
use App\Http\Controllers\Settings\PostChatFormController;
use App\Http\Controllers\Settings\TrustedDomainsController;
use App\Http\Controllers\Settings\CountryRestrictionsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Subscription\InvoiceController;
use App\Http\Controllers\Subscription\ManageSubscriptionController;
use App\Http\Controllers\Subscription\AccountDetailController;




// Stripe Webhooks (Isko middleware groups ke bahar rakhein)
// Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('cashier.webhook');
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('cashier.webhook');


/*
|--------------------------------------------------------------------------
| Domain Detection & Public Front-End Routes
|--------------------------------------------------------------------------
*/

 $host = request()->getHost();
 $mainDomain = config('app.main_domain');

// If we are on the MAIN domain (e.g., abc.com)
if ($host === $mainDomain || $host === 'www.' . $mainDomain || $host === 'localhost') {
    
    Route::get('/', [FrontController::class, 'home'])->name('front.home');
    Route::get('/features', [FrontController::class, 'features'])->name('front.features');
    Route::get('/about', [FrontController::class, 'about'])->name('front.about');
    Route::get('/product-tour', [FrontController::class, 'productTour'])->name('front.product-tour');
    Route::get('/pricing', [FrontController::class, 'pricing'])->name('front.pricing');
    Route::get('/blog', [FrontController::class, 'blog'])->name('front.blog');
    Route::get('/blog/{slug}', [FrontController::class, 'blogShow'])->name('front.blog.show');
    Route::get('/contact', [FrontController::class, 'contact'])->name('front.contact');
    Route::post('/contact', [FrontController::class, 'contactSend'])->name('front.contact.send');
    Route::get('/help', [FrontController::class, 'help'])->name('front.help');

} 
// If we are on a SUBDOMAIN (e.g., ab.abc.com) or any other domain
else {
    
    // Redirect root (/) to login
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Optional: Redirect all other public pages (like /pricing) to the dashboard or login
    // so subdomain users can't access the marketing site on their subdomain.
    Route::get('/{any}', function () {
        return redirect()->route('login');
    })->where('any', '^(?!admin|login|api|livechat|invite|forgot-workspace|storage|assets|auto-login|reset-password).*$');
}

/*
|--------------------------------------------------------------------------
| Public Utility Routes (No Auth Required)
|--------------------------------------------------------------------------
*/

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return "Application cache cleared!";
});

// Widget iframe (public-facing, embedded on customer sites)
Route::get('/livechat/widget/iframe', [ChatFrameWidgetController::class, 'iframe'])->name('widget.iframe');

// User invitation (public token-based)
Route::get('/invite/accept/{token}', [AgentController::class, 'acceptInvite']);
Route::post('/invite/complete', [AgentController::class, 'completeInvite']);

// Forgot workspace (public)
Route::get('/forgot-workspace', [WorkspaceController::class, 'showRequestForm'])->name('workspace.request');
Route::post('/forgot-workspace', [WorkspaceController::class, 'sendWorkspaceLinks'])->name('workspace.email');

/*
|--------------------------------------------------------------------------
| Admin / Backend Routes — All under /admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    Route::middleware(['auth', 'tenantMatch'])->group(function () {
        Route::prefix('subscription')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('subscription.index');
            Route::get('/manage/{package}', [ManageSubscriptionController::class, 'index'])->name('subscription.manage.index');
            Route::post('/create-payment', [ManageSubscriptionController::class, 'createPaymentIntent'])->name('subscription.pay');
            // routes/web.php mein add karo
            Route::post('/confirm', [ManageSubscriptionController::class, 'confirmSubscription'])
                ->name('subscription.confirm');
            Route::get('/success', [ManageSubscriptionController::class, 'success'])->name('subscription.success');
            
        });
    });
    // ──────────────────────────────────────
    //  Authenticated + Verified + Tenant
    // ──────────────────────────────────────
    Route::middleware(['auth', 'verified', 'tenantMatch', 'subscription.active'])->group(function () {
        // Dashboard
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

        Route::get('/api/dashboard/stats', [HomeController::class, 'stats'])->name('dashboard.stats');
        Route::get('/api/dashboard/chart', [HomeController::class, 'chart'])->name('dashboard.chart');

        // Traffic
        Route::get('/traffic', [TrafficController::class, 'index'])->name('traffic.index');
        Route::get('/traffic/live', [TrafficController::class, 'live'])->name('traffic.live');
        Route::get('/traffic/{visitorId}', [TrafficController::class, 'show'])->name('traffic.show');

        // Chats
        Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
        Route::get('/chats/archive', [ChatController::class, 'archive'])->name('chats.archive');

        // Agents (non-admin actions)
        Route::get('/agents/{id}/edit', [AgentController::class, 'edit'])->name('agents.edit');
        Route::put('/agents/{id}', [AgentController::class, 'update'])->name('agents.update');



        // Settings: Shortcuts (available to all authenticated users)
        Route::prefix('settings')->group(function () {
            Route::get('/shortcut', [ShortcutController::class, 'index'])->name('settings.shortcut.index');
            Route::post('/shortcut', [ShortcutController::class, 'store'])->name('settings.shortcut.store');
            Route::put('/shortcut/{shortcut}', [ShortcutController::class, 'update'])->name('settings.shortcut.update');
            Route::delete('/shortcut/{shortcut}', [ShortcutController::class, 'destroy'])->name('settings.shortcut.destroy');
            Route::get('/shortcuts/json', [ShortcutController::class, 'getJson'])->name('shortcuts.json');
        });
    });

    // ──────────────────────────────────────
    //  Authenticated + Admin + Tenant
    // ──────────────────────────────────────
    Route::middleware(['auth', 'admin', 'tenantMatch', 'subscription.active'])->group(function () {

        // Settings Module
        Route::prefix('settings')->group(function () {
            // Banned Customers
            Route::get('/banned-customers', [BannedCustomersController::class, 'index'])->name('settings.banned-customers.index');
            Route::get('/banned-customers/list', [BannedCustomersController::class, 'list'])->name('banned-customers.list');
            Route::post('/banned-customers', [BannedCustomersController::class, 'store'])->name('banned-customers.store');
            Route::delete('/banned-customers/{id}', [BannedCustomersController::class, 'destroy'])->name('banned-customers.destroy');

            // Widget
            Route::get('/widget', [WidgetController::class, 'index'])->name('settings.widget.index');
            Route::post('/widget/save', [WidgetController::class, 'save'])->name('settings.widget.save');
            Route::post('/widget/upload-logo', [WidgetController::class, 'uploadLogo'])->name('settings.widget.upload-logo');
            Route::post('/widget/save-post-chat', [WidgetController::class, 'savePostChat'])->name('settings.widget.save-post-chat');
            Route::post('/widget/upload-eye-catcher', [WidgetController::class, 'uploadEyeCatcher'])->name('settings.widget.upload-eye-catcher');

            // Chat Install
            Route::get('/chat-install', [InstallChatController::class, 'index'])->name('settings.chat-install.index');

            // Post Chat Form
            Route::get('/post-chat-form', [PostChatFormController::class, 'index'])->name('settings.post-chat-form.index');
            Route::post('/post-chat-form/save', [PostChatFormController::class, 'save'])->name('settings.post-chat-form.save');

            // Trusted Domains
            Route::get('/trusted-domains', [TrustedDomainsController::class, 'index'])->name('settings.trusted-domains.index');
            Route::post('/trusted-domains', [TrustedDomainsController::class, 'store'])->name('settings.trusted-domains.store');
            Route::delete('/trusted-domains/{id}', [TrustedDomainsController::class, 'destroy'])->name('settings.trusted-domains.destroy');
            Route::post('/detected-domains/{id}/trust', [TrustedDomainsController::class, 'trustDetected'])->name('settings.detected-domains.trust');
            Route::delete('/detected-domains/{id}', [TrustedDomainsController::class, 'dismissDetected'])->name('settings.detected-domains.dismiss');

            Route::get('/country-restrictions', [CountryRestrictionsController::class, 'index'])->name('settings.country-restrictions.index');
            Route::get('/country-restrictions/list', [CountryRestrictionsController::class, 'list'])->name('country-restrictions.list');
            Route::post('/country-restrictions/toggle', [CountryRestrictionsController::class, 'toggle'])->name('country-restrictions.toggle');
        });

        // Subscription Module
        Route::prefix('subscription')->group(function () {
            Route::get('/invoice', [InvoiceController::class, 'index'])->name('subscription.invoices.index');
            Route::get('/invoice/{id}/download', [InvoiceController::class, 'downloadInvoice'])->name('subscription.invoices.download');
            
            // 🔹 Account Details Routes
            // Route::get('/account-details', [AccountDetailController::class, 'index'])->name('subscription.account-details.index');
            // Route::put('/account-details', [AccountDetailController::class, 'update'])->name('subscription.account-details.update');
            
            // // 🔹 NAYE ROUTES: Card Update & Cancel Subscription
            // Route::get('/account-details/setup-intent', [AccountDetailController::class, 'getSetupIntent'])->name('subscription.account-details.setup-intent');
            // Route::post('/account-details/payment-method', [AccountDetailController::class, 'updatePaymentMethod'])->name('subscription.account-details.payment-method');
            // Route::post('/account-details/cancel', [AccountDetailController::class, 'cancelSubscription'])->name('subscription.account-details.cancel');

            Route::get('/account-details', [AccountDetailController::class, 'index'])->name('subscription.account-details.index');
            Route::post('/account-details/company', [AccountDetailController::class, 'updateCompany'])->name('subscription.account-details.company');
            Route::post('/account-details/owner', [AccountDetailController::class, 'changeOwner'])->name('subscription.account-details.owner');
            Route::get('/account-details/setup-intent', [AccountDetailController::class, 'getSetupIntent'])->name('subscription.account-details.setup-intent');
            Route::post('/account-details/payment-method', [AccountDetailController::class, 'updatePaymentMethod'])->name('subscription.account-details.payment-method');
            Route::post('/account-details/cancel', [AccountDetailController::class, 'cancelSubscription'])->name('subscription.account-details.cancel');
        });
        // Agents (admin actions)
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        Route::post('/agents/store', [AgentController::class, 'store'])->name('agents.store');
        Route::post('/agents/invite', [AgentController::class, 'invite'])->name('agents.invite');

        // Fetch agents for transfer dropdown
        Route::get('/agents/list', [AgentController::class, 'listAgents'])->name('agents.list');

        // ✅ NEW ROUTES FOR STATUS, LIMIT, AND DELETE
        Route::post('/agents/{id}/status', [AgentController::class, 'updateStatus'])->name('agents.updateStatus');
        Route::post('/agents/{id}/chat-limit', [AgentController::class, 'updateChatLimit'])->name('agents.updateChatLimit');
        Route::delete('/agents/{id}', [AgentController::class, 'destroy'])->name('agents.destroy');

        Route::post('/agents/{id}/suspend', [AgentController::class, 'suspend'])->name('agents.suspend');
        Route::post('/agents/{id}/activate', [AgentController::class, 'activate'])->name('agents.activate');

        // Fetch agent details for offcanvas
        Route::get('/agents/{id}', [AgentController::class, 'show'])->name('agents.show');
    });

});

// ──────────────────────────────────────
//  AUTO-LOGIN ROUTE (For cross-subdomain login after registration)
// ──────────────────────────────────────
Route::get('/auto-login/{user_id}', function (Request $request, $user_id) {
    // Validate relative signature
    if (!$request->hasValidSignature(false)) {
        abort(401, 'Invalid or expired login link.');
    }

    // Log the user in
    Auth::loginUsingId($user_id);
    
    // Get package ID from URL
    $packageId = $request->get('package');
    
    // If we have a package ID, redirect to the checkout page
    if ($packageId) {
        return redirect()->route('subscription.manage.index', ['package' => $packageId]);
    }

    // Fallback if no package is provided
    return redirect()->route('subscription.index'); // Change this to your actual pricing route name if different
})->name('auto.login');

require __DIR__.'/auth.php';