<?php

namespace App\Services\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * Class ProfileService
 * 
 * Handles user profile updates, including identity management 
 * and credential security.
 */
class ProfileService
{
    /**
     * Update user profile information.
     *
     * @param User $user
     * @param array $data
     * @return User
     * @throws Exception
     */
    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (isset($data['email']) && $data['email'] !== $user->email) {
                $user->email = $data['email'];
                if ($user instanceof MustVerifyEmail) {
                    $user->email_verified_at = null;
                    $user->sendEmailVerificationNotification();
                }
            }

            if (isset($data['name'])) {
                $user->name = $data['name'];
            }

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            if (!$user->save()) {
                throw new Exception(__('auth.profile_update_failed'));
            }

            return $user;
        });
    }
}
