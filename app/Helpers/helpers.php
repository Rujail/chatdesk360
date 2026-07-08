<?php

use App\Models\Tenant;

if (!function_exists('tenant')) {
    /**
     * Get the current tenant instance
     */
    function tenant(): ?Tenant
    {
        // Resolves from the AppServiceProvider singleton
        return app(Tenant::class);
    }
}

if (!function_exists('tenant_site_id')) {
    /**
     * Get the current tenant's site_id
     */
    function tenant_site_id(): ?string
    {
        $tenant = tenant();
        return $tenant ? $tenant->site_id : null;
    }
}

if (!function_exists('tenant_url')) {
    /**
     * Generate a URL for the current tenant's domain.
     * Works in HTTP requests AND background jobs/webhooks.
     *
     * @param string $path The path to append (e.g., 'admin/subscription/invoices')
     * @param string|null $site_id Pass site_id if in a queue/webhook where HTTP request is missing
     * @return string
     */
    function tenant_url(string $path = '', ?string $site_id = null): string
    {
        $tenant = null;

        // If a site_id is explicitly passed (e.g., in a queue/webhook), fetch it directly
        if ($site_id) {
            $tenant = Tenant::where('site_id', $site_id)->first();
        } else {
            // Otherwise, get it from the global singleton
            $tenant = tenant();
        }

        // Fallback to main app URL if no tenant is found
        if (!$tenant) {
            return url($path);
        }

        // Build the secure URL
        $domain = $tenant->domain_name;
        $base = str_starts_with($domain, 'http') ? $domain : 'https://' . $domain;
        
        // Ensure the path doesn't have double slashes
        $path = ltrim($path, '/');

        return $base . '/' . $path;
    }
}