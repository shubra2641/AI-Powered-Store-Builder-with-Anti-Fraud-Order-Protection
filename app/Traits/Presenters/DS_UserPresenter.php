<?php

namespace App\Traits\Presenters;

trait DS_UserPresenter
{
    /**
     * Get UI-friendly role badge class.
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return 'ds-badge ' . match($this->role?->slug) {
            'admin' => 'ds-badge-purple',
            'support' => 'ds-badge-orange',
            default => 'ds-badge-cyan',
        };
    }

    /**
     * Get UI-friendly status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return 'ds-badge ' . ($this->is_active ? 'ds-badge-green' : 'ds-badge-gray');
    }

    /**
     * Get UI-friendly email verification badge class.
     */
    public function getEmailVerificationBadgeAttribute(): string
    {
        return 'ds-badge ' . ($this->email_verified_at ? 'ds-badge-green' : 'ds-badge-gray');
    }

    /**
     * Check if email is verified.
     */
    public function getIsEmailVerifiedAttribute(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? __('admin.active') : __('admin.inactive');
    }

    /**
     * Get avatar initials (max 2).
     */
    public function getAvatarInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $w) {
            $initials .= mb_substr($w, 0, 1);
            if (strlen($initials) >= 2) break;
        }
        return strtoupper($initials);
    }

    /**
     * Get the name of the current subscribed plan.
     */
    public function getPlanNameAttribute(): string
    {
        return $this->subscription?->plan?->translated_name ?? __('admin.free');
    }

    /**
     * Get UI-friendly plan badge class.
     */
    public function getPlanBadgeClassAttribute(): string
    {
        $plan = $this->subscription?->plan?->name['en'] ?? 'free';
        return 'ds-badge ' . match(strtolower($plan)) {
            'enterprise', 'business' => 'ds-badge-pink',
            'pro' => 'ds-badge-purple',
            'starter' => 'ds-badge-cyan',
            default => 'ds-badge-gray',
        };
    }
}
