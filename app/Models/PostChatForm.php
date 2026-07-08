<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostChatForm extends Model
{
    protected $fillable = ['site_id', 'enabled', 'form_config'];

    protected $casts = [
        'enabled'     => 'boolean',
        'form_config' => 'array',
    ];
}