<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantMatch
{

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $tenant = tenant();

        // Check if user belongs to this tenant via pivot table
        if (!$tenant || !$user->tenants->contains($tenant->id)) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'You do not have access to this workspace.']);
        }

        return $next($request);
    }
}