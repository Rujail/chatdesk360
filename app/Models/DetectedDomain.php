<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetectedDomain extends Model
{
    protected $fillable = [
        'user_id',
        'site_id',
        'domain',
        'ip_address',
        'attempt_count',
        'last_attempt_at',
    ];

    protected $casts = [
        'last_attempt_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log or update a detected domain
     */
    public static function logAttempt(string $siteId, int $userId, string $domain, ?string $ip = null): void
    {
        $domain = \App\Models\TrustedDomain::normalizeDomain($domain);

        $existing = self::where('site_id', $siteId)
            ->where('domain', $domain)
            ->first();

        if ($existing) {
            $existing->increment('attempt_count');
            $existing->update([
                'ip_address'      => $ip ?? $existing->ip_address,
                'last_attempt_at' => now(),
            ]);
        } else {
            self::create([
                'user_id'         => $userId,
                'site_id'         => $siteId,
                'domain'          => $domain,
                'ip_address'      => $ip,
                'attempt_count'   => 1,
                'last_attempt_at' => now(),
            ]);
        }
    }
}