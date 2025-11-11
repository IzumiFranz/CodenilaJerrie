<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkActionsController extends Controller
{
    /**
     * Bulk update user status
     */
    public function bulkUpdateUserStatus(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        try {
            DB::beginTransaction();

            $updated = User::whereIn('id', $validated['user_ids'])
                ->update(['status' => $validated['status']]);

            // Log bulk action
            AuditLog::log('bulk_user_status_updated', null, [], [
                'count' => $updated,
                'status' => $validated['status'],
                'user_ids' => $validated['user_ids'],
            ]);

            DB::commit();

            return back()->with('success', "{$updated} user(s) status updated to {$validated['status']}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update user status: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDeleteUsers(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        try {
            // Prevent deleting own account
            $userIds = array_diff($validated['user_ids'], [auth()->id()]);

            if (empty($userIds)) {
                return back()->with('error', 'Cannot delete your own account.');
            }

            DB::beginTransaction();

            $deleted = User::whereIn('id', $userIds)->delete();

            AuditLog::log('bulk_users_deleted', null, [], [
                'count' => $deleted,
                'user_ids' => $userIds,
            ]);

            DB::commit();

            return back()->with('success', "{$deleted} user(s) deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete users: ' . $e->getMessage());
        }
    }

    /**
     * Bulk restore users
     */
    public function bulkRestoreUsers(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer'],
        ]);

        try {
            DB::beginTransaction();

            $restored = User::onlyTrashed()
                ->whereIn('id', $validated['user_ids'])
                ->restore();

            AuditLog::log('bulk_users_restored', null, [], [
                'count' => $restored,
                'user_ids' => $validated['user_ids'],
            ]);

            DB::commit();

            return back()->with('success', "{$restored} user(s) restored successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to restore users: ' . $e->getMessage());
        }
    }

    /**
     * Bulk assign role
     */
    public function bulkAssignRole(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'role' => ['required', 'in:admin,instructor,student'],
        ]);

        try {
            DB::beginTransaction();

            $updated = User::whereIn('id', $validated['user_ids'])
                ->update(['role' => $validated['role']]);

            AuditLog::log('bulk_role_assigned', null, [], [
                'count' => $updated,
                'role' => $validated['role'],
                'user_ids' => $validated['user_ids'],
            ]);

            DB::commit();

            return back()->with('success', "{$updated} user(s) role updated to {$validated['role']}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to assign role: ' . $e->getMessage());
        }
    }

    /**
     * Bulk send notifications
     */
    public function bulkSendNotifications(Request $request)
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
            DB::beginTransaction();

            $created = 0;
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

            AuditLog::log('bulk_notifications_sent', null, [], [
                'count' => $created,
                'type' => $validated['type'],
            ]);

            DB::commit();

            return back()->with('success', "{$created} notification(s) sent successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to send notifications: ' . $e->getMessage());
        }
    }

    /**
     * Bulk drop enrollments
     */
    public function bulkDropEnrollments(Request $request)
    {
        $validated = $request->validate([
            'enrollment_ids' => ['required', 'array'],
            'enrollment_ids.*' => ['exists:enrollments,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            $updated = 0;
            foreach ($validated['enrollment_ids'] as $enrollmentId) {
                $enrollment = Enrollment::find($enrollmentId);
                if ($enrollment && $enrollment->status === 'enrolled') {
                    $enrollment->drop();
                    $updated++;
                }
            }

            AuditLog::log('bulk_enrollments_dropped', null, [], [
                'count' => $updated,
                'enrollment_ids' => $validated['enrollment_ids'],
                'reason' => $validated['reason'] ?? null,
            ]);

            DB::commit();

            return back()->with('success', "{$updated} enrollment(s) dropped successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to drop enrollments: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete records (generic)
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'model' => ['required', 'string'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        try {
            $modelClass = 'App\\Models\\' . $validated['model'];
            
            if (!class_exists($modelClass)) {
                return back()->with('error', 'Invalid model specified.');
            }

            DB::beginTransaction();

            $deleted = $modelClass::whereIn('id', $validated['ids'])->delete();

            AuditLog::log('bulk_delete_' . strtolower($validated['model']), null, [], [
                'model' => $validated['model'],
                'count' => $deleted,
                'ids' => $validated['ids'],
            ]);

            DB::commit();

            return back()->with('success', "{$deleted} record(s) deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete records: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update status (generic)
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'model' => ['required', 'string'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
            'status' => ['required', 'boolean'],
            'field' => ['required', 'string'], // e.g., 'is_active'
        ]);

        try {
            $modelClass = 'App\\Models\\' . $validated['model'];
            
            if (!class_exists($modelClass)) {
                return back()->with('error', 'Invalid model specified.');
            }

            DB::beginTransaction();

            $updated = $modelClass::whereIn('id', $validated['ids'])
                ->update([$validated['field'] => $validated['status']]);

            AuditLog::log('bulk_status_updated_' . strtolower($validated['model']), null, [], [
                'model' => $validated['model'],
                'count' => $updated,
                'field' => $validated['field'],
                'status' => $validated['status'],
                'ids' => $validated['ids'],
            ]);

            DB::commit();

            return back()->with('success', "{$updated} record(s) status updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Bulk export selected records
     */
    public function bulkExport(Request $request)
    {
        $validated = $request->validate([
            'model' => ['required', 'string'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
            'format' => ['required', 'in:csv,json'],
        ]);

        try {
            $modelClass = 'App\\Models\\' . $validated['model'];
            
            if (!class_exists($modelClass)) {
                return back()->with('error', 'Invalid model specified.');
            }

            $records = $modelClass::whereIn('id', $validated['ids'])->get();

            if ($validated['format'] === 'csv') {
                return $this->exportToCSV($records, $validated['model']);
            } else {
                return $this->exportToJSON($records, $validated['model']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export records: ' . $e->getMessage());
        }
    }

    // Helper methods
    private function exportToCSV($records, $modelName)
    {
        $filename = strtolower($modelName) . '_export_' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        if ($records->count() > 0) {
            // Headers (use first record's keys)
            $headers = array_keys($records->first()->toArray());
            fputcsv($handle, $headers);

            // Data
            foreach ($records as $record) {
                fputcsv($handle, $record->toArray());
            }
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    private function exportToJSON($records, $modelName)
    {
        $filename = strtolower($modelName) . '_export_' . now()->format('Y-m-d_His') . '.json';
        
        return response()->json($records, 200, [
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}