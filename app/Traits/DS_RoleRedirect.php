<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Trait DS_RoleRedirect
 * 
 * Provides centralized logic for role-base dashboard redirection, 
 * ensuring a consistent navigation experience across the application.
 */
trait DS_RoleRedirect
{
    /**
     * Get the appropriate dashboard route name based on the user's role.
     *
     * @param User|null $user
     * @return string
     */
    public function getDashboardRoute(?User $user = null): string
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return 'login';
        }

        if ($user->isAdmin()) {
            return 'admin.dashboard';
        }

        return 'user.dashboard';
    }

    /**
     * Get the full URL for the dashboard.
     *
     * @param User|null $user
     * @return string
     */
    public function getDashboardUrl(?User $user = null): string
    {
        return route($this->getDashboardRoute($user));
    }
}
