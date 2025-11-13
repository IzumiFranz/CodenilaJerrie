<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Notification::where('user_id', $user->id);
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('read_at', false);
            } elseif ($request->status === 'read') {
                $query->where('read_at', true);
            }
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get unread count
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('read_at', false)
            ->count();
        
        return view('student.notifications.index', compact('notifications', 'unreadCount'));
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        
        $notification->update([
            'read_at' => true,
            'read_at' => now()
        ]);
        
        // Redirect to action URL if exists
        if ($notification->action_url) {
            return redirect($notification->action_url);
        }
        
        return back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('read_at', false)
            ->update([
                'read_at' => true,
                'read_at' => now()
            ]);
        
        return back()->with('success', 'All notifications marked as read.');
    }
    
    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        
        $notification->delete();
        
        return back()->with('success', 'Notification deleted.');
    }
    
    /**
     * Get unread notifications (AJAX)
     */
    public function unread(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->where('read_at', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
            'count' => $notifications->count()
        ]);
    }
}