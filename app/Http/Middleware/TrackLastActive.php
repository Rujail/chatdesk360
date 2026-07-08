<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TrackLastActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only run if the user is logged in
        if (auth()->check()) {
            $user = auth()->user();

            // Throttle: Only update last_seen_at if it hasn't been updated in the last 1 minute.
            // This prevents excessive DB writes from frequent AJAX polling (like the dashboard stats).
            if (is_null($user->last_seen_at) || $user->last_seen_at->diffInMinutes(Carbon::now()) >= 1) {
                $user->last_seen_at = Carbon::now();
                $user->saveQuietly(); // saveQuietly prevents firing extra Eloquent events
            }
        }

        return $response;
    }
}