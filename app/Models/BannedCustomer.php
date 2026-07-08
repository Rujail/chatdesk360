<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannedCustomer extends Model
{
    protected $fillable = [
        'site_id',
        'ip_address',
        'visitor_id',
        'chat_id',
        'reason',
        'banned_by',
        'start_date',
        'end_date',
        'is_permanent',
    ];

    protected $casts = [
        'start_date'   => 'datetime',
        'end_date'     => 'datetime',
        'is_permanent' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function bannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /**
     * Is this ban currently active (not expired)?
     */
    public function isActive(): bool
    {
        if ($this->is_permanent) return true;
        if (!$this->end_date) return true;
        return $this->end_date->isFuture();
    }

    /**
     * Scope: only active (non-expired) bans for a given site.
     */
    public function scopeActiveForSite($query, string $siteId)
    {
        return $query->where('site_id', $siteId)
            ->where(function ($q) {
                $q->where('is_permanent', true)
                ->orWhereNull('end_date')
                ->orWhere('end_date', '>', now());
            });
    }
}