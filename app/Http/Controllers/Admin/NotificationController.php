<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_type' => ['required', 'in:single,role,all'],
            'user_id' => ['required_if:recipient_type,single', 'exists:users,id'],
            'role' => ['required_if:recipient_type,role', 'in:admin,instructor,student'],
            'type' => ['required', 'in:info,success,warning,danger'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'action_url' => ['nullable', 'url', 'max:500'],
        ]);

        try {
            $recipients = $this->getRecipients(
                $validated['recipient_type'],
                $validated['user_id'] ?? null,
                $validated['role'] ?? null
            );

            $created = 0;

            DB::beginTransaction();

            foreach ($recipients as $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => $validated['type'],
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'action_url' => $validated['action_url'] ?? null,
                ]);
                $created++;
            }

            DB::commit();

            AuditLog::log('notifications_sent', null, [], [
                'recipient_type' => $validated['recipient_type'],
                'count' => $created,
            ]);

            return redirect()
                ->route('admin.notifications.index')
                ->with('success', "{$created} notification(s) sent successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to send notifications: ' . $e->getMessage());
        }
    }

    public function sendBulk(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'type' => ['required', 'in:info,success,warning,danger'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'action_url' => ['nullable', 'url', 'max:500'],
        ]);

        try {
            $created = 0;

            DB::beginTransaction();

            foreach ($validated['user_ids'] as $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => $validated['type'],
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'action_url' => $validated['action_url'] ?? null,
                ]);
                $created++;
            }

            DB::commit();

            AuditLog::log('bulk_notifications_sent', null, [], [
                'count' => $created,
            ]);

            return back()->with('success', "{$created} notification(s) sent successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to send notifications: ' . $e->getMessage());
        }
    }

    private function getRecipients(string $type, ?int $userId = null, ?string $role = null): array
    {
        return match($type) {
            'single' => [$userId],
            'role' => User::where('role', $role)->where('status', 'active')->pluck('id')->toArray(),
            'all' => User::where('status', 'active')->pluck('id')->toArray(),
            default => [],
        };
    }
}