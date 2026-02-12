<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Presenters\DS_PlanPresenter;

class Plan extends Model
{
    use HasFactory, DS_PlanPresenter;
    
    protected $appends = ['dollar_price'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'duration_days',
        'trial_days',
        'quotas',
        'is_active',
        'is_default',
        'is_featured',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name'        => 'array',
            'description' => 'array',
            'quotas'      => 'array',
            'is_active'   => 'boolean',
            'is_default'  => 'boolean',
            'is_featured' => 'boolean',
            'price'       => 'integer',
            'trial_days'  => 'integer',
        ];
    }

    /**
     * Get the subscriptions for the plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Helper to check if a feature is unlimited (-1), disabled (0), or has a limit (N).
     *
     * @param string $key
     * @return mixed
     */
    public function getQuota(string $key)
    {
        return $this->quotas[$key] ?? 0;
    }
}
