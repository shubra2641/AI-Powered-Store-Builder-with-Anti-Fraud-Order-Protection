<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
        'transaction_id',
        'trial_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with the subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Check if the subscription is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->starts_at <= now() && 
               ($this->ends_at === null || $this->ends_at >= now());
    }

    /**
     * Check if the subscription is pending (e.g. Bank Transfer).
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the subscription has expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->ends_at !== null && $this->ends_at < now());
    }
}
