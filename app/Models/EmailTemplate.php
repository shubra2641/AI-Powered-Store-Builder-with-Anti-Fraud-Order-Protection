<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'subject',
        'content',
        'is_system',
        'description',
        'language_id',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
