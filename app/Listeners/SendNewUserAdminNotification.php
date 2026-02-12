<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendNewUserAdminNotification
{
    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $admins = User::whereHas('role', function($q) {
            $q->where('slug', 'admin');
        })->get();
        
        try {
            Notification::send($admins, new SystemNotification(
                title: __('admin.admin_new_user_title'),
                message: __('admin.admin_new_user_message', ['name' => $event->user->name]),
                url: route('admin.users.index'),
                type: 'info'
            ));
        } catch (\Exception $e) {
            Log::error('Admin registration notification failed: ' . $e->getMessage());
        }
    }
}
