<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorPage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'visitor_id',
        'url',
        'title',
        'time_spent',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];
}
