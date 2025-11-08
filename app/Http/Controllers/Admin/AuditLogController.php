<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('action', 'like', "%{$search}%")
                ->orWhere('model_type', 'like', "%{$search}%")
                ->orWhereHas('user', function($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
                });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get unique actions for filter dropdown
        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.audit-logs.index', compact('logs', 'actions'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user');

        // Apply same filters as index
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';

        $handle = fopen('php://temp', 'w');
        
        // Headers
        fputcsv($handle, ['ID', 'User', 'Action', 'Model Type', 'Model ID', 'IP Address', 'Date']);

        // Data
        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->user ? $log->user->username : 'System',
                $log->action,
                $log->model_type ?? '-',
                $log->model_id ?? '-',
                $log->ip_address ?? '-',
                $log->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    public function clear(Request $request)
    {
        $request->validate([
            'days' => ['required', 'integer', 'min:30'], // Minimum 30 days
        ]);

        try {
            $date = now()->subDays($request->days);
            $count = AuditLog::where('created_at', '<', $date)->delete();

            AuditLog::log('audit_logs_cleared', null, [], [
                'days' => $request->days,
                'count' => $count,
            ]);

            return back()->with('success', "{$count} audit logs cleared.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }
}

