<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of in-app notifications for the authenticated user.
     * Customers only view notifications addressed to their personal user account.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->with(['shipment.carrier', 'shipment.originLocation', 'shipment.destinationLocation'])
            ->paginate(15)
            ->withQueryString();

        $unreadCount = $user->notifications()->whereNull('read_at')->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark an individual notification as read.
     * Strictly verifies that the notification belongs to the authenticated user.
     */
    public function markAsRead(Request $request, Notification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== $request->user()->id, 403, 'Unauthorized access to notification record.');

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        return back()->with('status', 'Notification marked as read.');
    }

    /**
     * Mark all unread notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $updated = $request->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('status', "{$updated} notifications marked as read.");
    }
}
