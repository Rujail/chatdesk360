<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetSetting extends Model
{
    protected $fillable = ['site_id', 'settings'];

    protected $casts = [
        'settings' => 'array',
    ];
}