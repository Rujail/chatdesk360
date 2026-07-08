<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostChatResponse extends Model
{
    protected $fillable = ['site_id', 'visitor_id', 'response_data'];

    protected $casts = [
        'response_data' => 'array',
    ];
}