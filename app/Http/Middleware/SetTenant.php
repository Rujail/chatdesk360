<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant; // <-- ADD THIS LINE

class SetTenant
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost(); // e.g., test.abc.com
        $mainDomain = config('app.domain'); // abc.com

        // Skip tenant resolution for main domain (landing page, registration)
        if ($host === $mainDomain || $host === 'www.' . $mainDomain) {
            app()->instance('currentTenant', null);
            return $next($request);
        }

        // Find tenant by domain
        $tenant = Tenant::where('domain_name', $host)->first();

        if (!$tenant) {
            abort(404, 'This workspace does not exist.');
        }

        // Store tenant globally in the service container
        app()->instance('currentTenant', $tenant);

        return $next($request);
    }
}