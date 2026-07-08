<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'user_id',
        'domain_name',
    ];

    /**
     * Get the owner user of this tenant
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all users belonging to this tenant/workspace (same site_id)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'tenant_users')->withTimestamps();
    }
}