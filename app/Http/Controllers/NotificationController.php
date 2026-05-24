<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where('user_id', auth()->id())
            ->orderByRaw('read_at IS NOT NULL, created_at DESC')
            ->paginate(20);

        $unreadCount = AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(AppNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->markAsRead();
        return back();
    }

    public function markAllRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
