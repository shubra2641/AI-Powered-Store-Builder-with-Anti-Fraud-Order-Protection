<?php

namespace App\Policies;

use App\Models\User;

class DS_AdminPolicy
{
    /**
     * Determine if the user can access administrative areas.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Check if user is admin.
     */
    public function accessAdmin(User $user): bool
    {
        return $user->isAdmin();
    }
}
