<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'site_id',
        'owner_id',
        'name',
        'domain',
        'agent_limit',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'plan_name',
        'subscription_status',
        'subscription_ends_at'
    ];

    // 🔹 THIS IS REQUIRED FOR isPast() TO WORK
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'site_id', 'site_id');
    }

    /**
     * Check if this workspace has an active, valid subscription
     */
    public function isSubscribed(): bool
    {
        if ($this->subscription_status !== 'active') {
            return false;
        }

        if ($this->subscription_ends_at !== null && $this->subscription_ends_at->isPast()) {
            return false;
        }

        return true;
    }
}