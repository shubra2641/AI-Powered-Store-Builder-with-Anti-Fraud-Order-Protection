<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * Class UserService
 * Provides user management functionality (CRUD, Roles, Bulk Actions).
 */
class UserService
{
    /**
     * Get all users.
     *
     *
     * @return Collection Collection of User models.
     */
    public function getAllUsers(): Collection
    {
        return User::with(['role', 'subscription.plan'])->latest()->get();
    }

    /**
     * Get all available system roles.
     *
     *
     * @return Collection Collection of Role models.
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = DB::transaction(function () use ($data) {
            $data['is_active'] = $data['is_active'] ?? true;
            return User::create($data);
        });

        if (auth()->check()) {
            auth()->user()->notify(new SystemNotification(
                __('admin.new_user_registered'),
                __('admin.user_created_notification', ['name' => $user->name]),
                route('admin.users.index'),
                'success'
            ));
        }

        return $user;
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $user->update($data);

            return $user;
        });
    }

    /**
     * Delete a user.
     *
     * @param User $user
     * @return bool
     * @throws LogicException
     */
    public function deleteUser(User $user): bool
    {
        if ($user->id === auth()->id()) {
            throw new LogicException(__('admin.cannot_delete_self'));
        }

        return $user->delete();
    }

    /**
     * Bulk delete users by IDs (skips current user).
     *
     * @param array $ids List of user IDs.
     * @return DS_BulkDeleteResult Result enum.
     */
    public function bulkDeleteUsers(array $ids): DS_BulkDeleteResult
    {
        $currentUserId = auth()->id();
        
        if (count($ids) === 1 && in_array($currentUserId, $ids)) {
            return DS_BulkDeleteResult::NONE_SELF;
        }

        $count = 0;
        
        DB::transaction(function () use ($ids, &$count, $currentUserId) {
            $count = User::whereIn('id', $ids)
                ->where('id', '!=', $currentUserId)
                ->delete();
        });

        $requestedCount = count($ids);
        
        if ($count === 0 && in_array($currentUserId, $ids)) {
            return DS_BulkDeleteResult::NONE_SELF;
        }
        
        if ($count < $requestedCount) {
            return DS_BulkDeleteResult::PARTIAL;
        }

        return DS_BulkDeleteResult::SUCCESS;
    }

    /**
     * Get user statistics for the dashboard.
     *
     * @return array
     */
    public function getUserStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'pending' => User::where('email_verified_at', null)->count(),
            'suspended' => User::where('is_active', false)->count(),
        ];
    }

    /**
     * Verify a user's email manually.
     *
     * @param User $user
     * @return bool
     */
    public function verifyUserEmail(User $user): bool
    {
        return $user->markEmailAsVerified();
    }
}
