<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    // 🔹 YEH LINE ZAROOR ADD KAREIN!
    // Isse yeh model Cashier ki default 'subscriptions' table ko use nahi karega
    protected $table = 'site_subscriptions';

    protected $fillable = [
        'user_id',
        'site_id',
        'package_id',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'quantity',
        'billing_cycle',
        'trial_ends_at',
        'ends_at'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // Relationships
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helpers (VERY USEFUL)
    public function isActive(): bool
    {
        return $this->stripe_status === 'active'
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }
}