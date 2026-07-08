<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\SetTenant;
use App\Http\Middleware\EnsureTenantMatch;
use App\Http\Middleware\TrackLastActive; 
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\EnsureUserIsNotSuspended;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', 
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ Added EnsureUserIsNotSuspended here
        $middleware->web(append: [
            TrackLastActive::class,
            SetTenant::class,
            EnsureUserIsNotSuspended::class,
        ]);

         // 2. Add 'subscription.active' to the alias array
        $middleware->alias([
            'admin' => CheckAdmin::class,
            'role'  => \App\Http\Middleware\RoleMiddleware::class,
            'tenantMatch' => EnsureTenantMatch::class, 
            'subscription.active' => EnsureSubscriptionActive::class, // 🔹 Added here
        ]);

        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();