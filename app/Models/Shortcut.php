<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shortcut extends Model
{
    use HasFactory;

    protected $fillable = [
        'shortcut',
        'response_text',
        'tags',
        'auto_apply_tags',
        'is_shared',
        'site_id',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'auto_apply_tags' => 'boolean',
        'is_shared' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}