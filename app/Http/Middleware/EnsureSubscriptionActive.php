<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Site;

class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Get the workspace (Site) for this user
        $site = Site::where('site_id', $user->site_id)->first();

        // If site doesn't exist or subscription is invalid/expired
        if (!$site || !$site->isSubscribed()) {
            
            // Allow Admins to access subscription/billing routes so they can pay
            if ($user->isAdmin()) {
                if ($request->is('admin/subscription*') || $request->is('admin/logout*') || $request->is('logout*')) {
                    return $next($request);
                }

                return redirect()->route('subscription.index')
                    ->with('error', 'Your subscription is inactive or expired. Please renew to continue.');
            }

            // If Agent, block them completely from the workspace
            abort(403, 'Workspace access suspended. Please contact your administrator to renew the subscription.');
        }

        // If subscription is active, let everyone (Admins + Agents) through
        return $next($request);
    }
}