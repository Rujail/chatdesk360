<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedDomain extends Model
{
    protected $fillable = [
        'user_id',
        'site_id',
        'domain',
        'added_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Domain normalize karo — https://, www. hata do
     */
    public static function normalizeDomain(string $domain): string
    {
        $domain = trim(strtolower($domain));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        $domain = rtrim($domain, '/');
        return $domain;
    }

    /**
     * Check karo yeh domain trusted hai ya nahi — by site_id
     * Subdomains bhi check karta hai
     */
    public static function isTrusted(string $siteId, string $requestDomain): bool
    {
        $requestDomain = self::normalizeDomain($requestDomain);

        $trustedDomains = self::where('site_id', $siteId)->pluck('domain');

        foreach ($trustedDomains as $trusted) {
            if ($requestDomain === $trusted) return true;
            if (str_ends_with($requestDomain, '.' . $trusted)) return true;
        }

        return false;
    }
}