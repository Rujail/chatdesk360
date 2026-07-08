<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatFile extends Model
{
    protected $fillable = [
        'site_id', 'visitor_id', 'original_name',
        'file_path', 'file_url', 'mime_type',
        'file_size', 'file_type',
    ];
}