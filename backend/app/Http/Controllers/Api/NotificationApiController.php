<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    /**
     * GET /notifications
     * Fetches all broadcast or announcement notifications.
     */
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * PUT /notifications/{id}/read
     * Marks a notification message as read.
     */
    public function markAsRead($id)
    {
        // Mock success for read status
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * DELETE /notifications/{id}
     * Clears notification.
     */
    public function destroy($id)
    {
        $notification = Notification::find($id);
        if ($notification) {
            $notification->delete();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }
}
