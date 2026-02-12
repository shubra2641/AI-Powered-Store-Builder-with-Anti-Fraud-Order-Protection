<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Presenters\DS_UserPresenter;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, DS_UserPresenter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'language_id',
        'is_active',
        'activation_token',
        'email_verified_at',
        'balance',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the language associated with the user.
     * 
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the transactions for the user.
     * 
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(DS_BalanceTransaction::class);
    }

    /**
     * Get the active subscription for the user.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    /**
     * Check if the user is an administrator.
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Regenerate the activation token for the user.
     */
    public function regenerateActivationToken(): void
    {
        $this->update(['activation_token' => \Illuminate\Support\Str::random(60)]);
    }
}
