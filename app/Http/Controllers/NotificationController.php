<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => class_basename($notification->type),
                    'message' => $notification->data['message'] ?? null,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'read' => !is_null($notification->read_at),
                ];
            });

        return apiSuccess('تم استرجاع الإشعارات بنجاح', [
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function unread(Request $request)
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => class_basename($notification->type),
                    'message' => $notification->data['message'] ?? null,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at,
                    'read' => false,
                ];
            });

        return apiSuccess('تم استرجاع الإشعارات غير المقروءة', $notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (! $notification) {
            return apiError('الإشعار غير موجود', null, 404);
        }

        $notification->markAsRead();

        return apiSuccess('تم تمييز الإشعار كمقروء');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return apiSuccess('تم تمييز جميع الإشعارات كمقروءة');
    }

    public function destroy(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (! $notification) {
            return apiError('الإشعار غير موجود', null, 404);
        }

        $notification->delete();

        return apiSuccess('تم حذف الإشعار بنجاح');
    }
}
