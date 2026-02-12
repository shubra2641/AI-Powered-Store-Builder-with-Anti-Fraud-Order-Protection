<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DS_Integration extends Model
{
    use HasFactory;

    protected $table = 'ds_integrations';

    protected $fillable = [
        'user_id',
        'service',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get available integration providers configuration.
     */
    public static function getProviders(): array
    {
        return config('dropsaas.integrations', []);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
