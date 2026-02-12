<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Presenters\DS_AIKeyPresenter;

class DS_AIKey extends Model
{
    use DS_AIKeyPresenter;

    protected $table = 'ds_ai_keys';

    protected $fillable = [
        'provider',
        'model',
        'api_key',
        'max_tokens',
        'tokens_used',
        'is_active',
        'last_fail_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'max_tokens' => 'integer',
            'tokens_used' => 'integer',
            'api_key' => 'encrypted',
            'last_fail_at' => 'datetime',
        ];
    }
}
