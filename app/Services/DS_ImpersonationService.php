<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Class DS_ImpersonationService
 * Handles admin impersonation of users.
 */
class DS_ImpersonationService
{
    /**
     * Start impersonating a user.
     *
     * @param User $user
     * @return void
     */
    public function impersonate(User $user): void
    {
        $adminId = Auth::id();
        
        Session::put('ds_impersonate_admin_id', $adminId);
        
        Auth::login($user);
    }

    /**
     * Check if currently impersonating.
     *
     * @return bool
     */
    public function isImpersonating(): bool
    {
        return Session::has('ds_impersonate_admin_id');
    }

    /**
     * Stop impersonating and return to admin.
     *
     * @return void
     */
    public function stopImpersonating(): void
    {
        $adminId = Session::pull('ds_impersonate_admin_id');
        
        if ($adminId) {
            $admin = User::find($adminId);
            if ($admin) {
                Auth::login($admin);
            }
        }
    }
}
