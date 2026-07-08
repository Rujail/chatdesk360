<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'visitor_id',
        'site_id',
        'ip_address',
        'name',
        'email',
        'country',
        'state',
        'city',
        'lat',
        'lon',
        'countryCode',
        'assign_userID',
        'status',
        'referrer_url',
        'device_type',
        'os',
        'browser',
        'first_seen_at',
        'last_seen_at',
        'visit_count',
        'last_page_url',
        'chat_count',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at'  => 'datetime',
    ];
}