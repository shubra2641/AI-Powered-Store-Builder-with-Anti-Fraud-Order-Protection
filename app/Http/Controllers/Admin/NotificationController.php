<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    use DS_TranslationHelper;

    /**
     * Create a new controller instance.
     */
    public function __construct() {}

    /**
     * Display a listing of notifications.
     */
    public function index(): View
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id): RedirectResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $this->notifySuccess('admin.notification_marked_read');
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->notifySuccess('admin.all_notifications_marked_read');
        
        return redirect()->back();
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id): RedirectResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
            $this->notifySuccess('admin.notification_deleted');
        }

        return redirect()->back();
    }
    
    /**
     * Delete all notifications.
     */
    public function destroyAll(): RedirectResponse
    {
        Auth::user()->notifications()->delete();
        $this->notifySuccess('admin.all_notifications_deleted');

        return redirect()->back();
    }
}
