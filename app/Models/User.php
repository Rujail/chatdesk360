<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'site_id',
        'status',
        'concurrent_chat_limit',
        'last_seen_at',
        'groups',
        'total_chats_handled',
        'goals_achieved',
        'avg_satisfaction',
        'is_suspended', // ✅ ADDED HERE
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
            'is_suspended' => 'boolean', // ✅ ADDED HERE
        ];
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * The "Owned" Site (Default/Primary Workspace)
     */
    public function site() 
    {
        // 🔹 The third argument 'site_id' is REQUIRED here!
        return $this->belongsTo(Site::class, 'site_id', 'site_id');
    }

    /**
     * Check if the user's workspace (Site) has an active subscription
     */
    public function hasActiveSubscription(): bool
    {
        if (!$this->site) {
            return false; // If site relationship returns null, return false
        }

        return $this->site->isSubscribed();
    }

    /**
     * ALL Tenants/Workspaces this user belongs to (Multi-tenant pivot)
     */
    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users')->withTimestamps();
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    public function isAdmin() 
    {
        return $this->role === 'admin';
    }

    public function isAgent() 
    {
        return $this->role === 'agent';
    }

    // ==========================================
    // MODEL BOOT EVENTS
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // ✅ Only generate if site_id was NOT passed by the controller
            if (empty($user->site_id)) {
                do {
                    $siteId = 'site_' . Str::random(10);
                } while (self::where('site_id', $siteId)->exists());
                
                $user->site_id = $siteId;
            }
        });

        static::created(function ($user) {
            // ✅ Only create a Site record if it doesn't exist for this site_id
            if (!Site::where('site_id', $user->site_id)->exists()) {
                Site::create([
                    'owner_id' => $user->id,
                    'site_id' => $user->site_id,
                    'name' => $user->name . "'s Site",
                    'agent_limit' => 1,
                ]);
            }
        });
    }
}