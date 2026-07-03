<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()   
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->data['message'] ?? null,
                    'date' => $notification->created_at,
                    'read' => !is_null($notification->read_at),
                ];
            });
        return apiSuccess("جميع الإشعارات", $notifications);
    }

    function markAsRead(Request $request){
         $request->user()
            ->notifications->markAsRead();
           
        return apiSuccess("تم تعديل حالة الاشعارات إلى مقروءة" );
    }

    function unreadCount(Request $request){
        $count =  $request->user()
            ->unreadNotifications->count();
           
        return apiSuccess("عدد الإشعارات غير المقروءة", $count);

    }
}
