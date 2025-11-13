<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\Course;
use App\Models\Specialization;
use App\Models\AuditLog;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendBulkUserEmailsJob;
use App\Jobs\SendBulkCreationSummaryJob;
use App\Jobs\SendWelcomeEmailJob;
use App\Mail\BulkUsersCreatedMail;
use App\Mail\UserCreatedMail;
use App\Mail\AccountActivatedMail;
use App\Mail\AccountSuspendedMail;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['admin', 'instructor', 'student']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $courses = Course::where('is_active', true)->get();
        $specializations = Specialization::where('is_active', true)->get();
        
        return view('admin.users.create', compact('courses', 'specializations'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,instructor,student'],
            'email' => ['required', 'email', 'unique:users,email'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            
            // Admin fields
            'position' => ['required_if:role,admin', 'nullable', 'string', 'max:255'],
            'office' => ['nullable', 'string', 'max:255'],
            
            // Instructor fields
            'employee_id' => ['required_if:role,instructor', 'nullable', 'string', 'unique:instructors,employee_id'],
            'specialization_id' => ['required_if:role,instructor', 'nullable', 'exists:specializations,id'],
            'department' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            
            // Student fields
            'student_number' => ['required_if:role,student', 'nullable', 'string', 'unique:students,student_number'],
            'course_id' => ['required_if:role,student', 'nullable', 'exists:courses,id'],
            'year_level' => ['required_if:role,student', 'nullable', 'integer', 'min:1', 'max:6'],
            'address' => ['nullable', 'string', 'max:500'],
            'admission_date' => ['nullable', 'date'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        try {
            DB::beginTransaction();

            // Generate username and password
            $username = $this->generateUsername($validated['role']);
            $password = Str::random(12);

            // Create user
            $user = User::create([
                'username' => $username,
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'role' => $validated['role'],
                'status' => 'active',
                'must_change_password' => true,
            ]);

            // Create role-specific profile
            switch ($validated['role']) {
                case 'admin':
                    Admin::create([
                        'user_id' => $user->id,
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'],
                        'middle_name' => $validated['middle_name'] ?? null,
                        'position' => $validated['position'] ?? null,
                        'office' => $validated['office'] ?? null,
                        'phone' => $validated['phone'] ?? null,
                    ]);
                    break;

                case 'instructor':
                    Instructor::create([
                        'user_id' => $user->id,
                        'employee_id' => $validated['employee_id'],
                        'specialization_id' => $validated['specialization_id'] ?? null,
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'],
                        'middle_name' => $validated['middle_name'] ?? null,
                        'department' => $validated['department'] ?? null,
                        'phone' => $validated['phone'] ?? null,
                        'hire_date' => $validated['hire_date'] ?? null,
                    ]);
                    break;

                case 'student':
                    Student::create([
                        'user_id' => $user->id,
                        'student_number' => $validated['student_number'],
                        'course_id' => $validated['course_id'] ?? null,
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'],
                        'middle_name' => $validated['middle_name'] ?? null,
                        'year_level' => $validated['year_level'] ?? 1,
                        'phone' => $validated['phone'] ?? null,
                        'address' => $validated['address'] ?? null,
                        'admission_date' => $validated['admission_date'] ?? now(),
                    ]);
                    break;
            }

           // Log the creation
        AuditLog::log('user_created', $user, [], [
            'role' => $user->role,
            'email' => $user->email,
        ]);
        
        DB::commit();
        // After creating user
        $admins = User::where('role', 'admin')->where('status', 'active')->get();

        foreach ($admins as $admin) {
            $settings = $admin->settings ?? (object)[
                'email_new_user' => true,
                'notification_new_user' => true
            ];
            
            if ($settings->notification_new_user ?? true) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'success',
                    'title' => 'New User Created',
                    'message' => "New {$user->role}: {$user->username}",
                    'action_url' => route('admin.users.show', $user),
                ]);
            }
        }
        SendWelcomeEmailJob::dispatch($user, $password);
        \App\Models\Notification::create([
        'user_id' => auth()->id(),
        'type' => 'success',
        'title' => 'User Created Successfully',
        'message' => "New {$user->role} account created: {$user->username} ({$user->email})",
    ]);
        AuditLog::log('welcome_email_queued', $user, [], [
            'email' => $user->email,
            'username' => $user->username
        ]);

        // 🔔 SEND EMAILS
        if ($request->input('send_email', true)) {
            try {
                // Send the admin/system notification email
                Mail::to($user->email)->send(new UserCreatedMail($user, $password));

                AuditLog::log('user_creation_email_sent', $user, [], [
                    'email' => $user->email,
                    'type'  => 'UserCreatedMail',
                    'sent_at' => now(),
                ]);

                // Optionally send the friendly welcome email
                Mail::to($user->email)->queue(new WelcomeMail($user, $password));

                AuditLog::log('welcome_email_sent', $user, [], [
                    'email' => $user->email,
                    'type'  => 'WelcomeMail',
                    'queued_at' => now(),
                ]);

                $emailStatus = 'Both User Created and Welcome emails sent to ' . $user->email;

            } catch (\Exception $e) {
                \Log::error('Failed to send creation/welcome emails: ' . $e->getMessage());
                $emailStatus = 'User created but email(s) failed. Please manually send credentials.';
            }
        } else {
            $emailStatus = 'Email notifications disabled.';
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User created successfully! Username: {$username}, Password: {$password}. {$emailStatus}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    private function generateEmployeeId(): string
    {
        $year = date('Y');
        
        $lastInstructor = Instructor::where('employee_id', 'like', 'EMP-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastInstructor) {
            preg_match('/-(\d+)$/', $lastInstructor->employee_id, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        $sequence = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return 'EMP-' . $year . '-' . $sequence;
    }

    private function generateStudentNumber(): string
    {
        $year = date('Y');
        
        $lastStudent = Student::where('student_number', 'like', 'ST-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastStudent) {
            preg_match('/-(\d+)$/', $lastStudent->student_number, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        $sequence = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return 'ST-' . $year . '-' . $sequence;
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['admin', 'instructor.specialization', 'student.course', 'auditLogs', 'notifications']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        $user->load(['admin', 'instructor', 'student']);
        $courses = Course::where('is_active', true)->get();
        $specializations = Specialization::where('is_active', true)->get();

        return view('admin.users.edit', compact('user', 'courses', 'specializations'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Load relationships to avoid errors
        $user->load(['admin', 'instructor', 'student']);
        
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'status' => ['required', 'in:active,inactive,suspended'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            
            // Role-specific fields
            'position' => ['nullable', 'string', 'max:255'],
            'office' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'unique:instructors,employee_id,' . ($user->instructor?->id ?? 'NULL')],
            'specialization_id' => ['nullable', 'exists:specializations,id'],
            'department' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            'student_number' => ['nullable', 'string', 'unique:students,student_number,' . ($user->student?->id ?? 'NULL')],
            'course_id' => ['nullable', 'exists:courses,id'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:6'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            $oldValues = $user->toArray();

            // Update user
            $user->update([
                'email' => $validated['email'],
                'status' => $validated['status'],
            ]);

            // Update profile
            $profile = $user->profile;
            if ($profile) {
                $profileData = [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                ];

                if ($user->isAdmin()) {
                    $profileData['position'] = $validated['position'] ?? null;
                    $profileData['office'] = $validated['office'] ?? null;
                } elseif ($user->isInstructor()) {
                    $profileData['employee_id'] = $validated['employee_id'] ?? $profile->employee_id;
                    $profileData['specialization_id'] = $validated['specialization_id'] ?? null;
                    $profileData['department'] = $validated['department'] ?? null;
                    $profileData['hire_date'] = $validated['hire_date'] ?? null;
                } elseif ($user->isStudent()) {
                    $profileData['student_number'] = $validated['student_number'] ?? $profile->student_number;
                    $profileData['course_id'] = $validated['course_id'] ?? null;
                    $profileData['year_level'] = $validated['year_level'] ?? $profile->year_level;
                    $profileData['address'] = $validated['address'] ?? null;
                }

                $profile->update($profileData);
            }

            // Log the update
            AuditLog::log('user_updated', $user, $oldValues, $user->toArray());

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting own account
            if ($user->id === auth()->id()) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            AuditLog::log('user_deleted', $user);
            
            $user->delete();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Show trashed users
     */
    public function trashed()
    {
        $users = User::onlyTrashed()
            ->with(['admin', 'instructor', 'student'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.users.trashed', compact('users'));
    }

    /**
     * Restore trashed user
     */
    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();

            AuditLog::log('user_restored', $user);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User restored successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore user: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete user
     */
    public function forceDelete($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            
            AuditLog::log('user_permanently_deleted', $user);
            
            $user->forceDelete();

            return redirect()
                ->route('admin.users.trashed')
                ->with('success', 'User permanently deleted.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to permanently delete user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(Request $request, User $user)
    {
        try {
            $oldStatus = $user->status;
            
            // Get new status from request or toggle
            if ($request->has('status')) {
                $newStatus = $request->status;
            } else {
                $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            }
            
            // Get suspension reason if provided
            $reason = $request->input('reason');
            
            $user->update(['status' => $newStatus]);
            
            // Send appropriate email
            if ($newStatus === 'active' && $oldStatus !== 'active') {
                // Account activated
                Mail::to($user->email)->queue(new AccountActivatedMail($user));
                
                // In-app notification
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'success',
                    'title' => 'Account Activated',
                    'message' => 'Your account has been activated. You can now access the system.',
                    'action_url' => route('login'),
                ]);
                
            } elseif ($newStatus === 'suspended' || $newStatus === 'inactive') {
                // Account suspended/deactivated
                Mail::to($user->email)->queue(new AccountSuspendedMail($user, $reason));
                
                // In-app notification
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'warning',
                    'title' => 'Account Suspended',
                    'message' => $reason ?? 'Your account access has been suspended. Please contact support.',
                ]);
            }
            
            AuditLog::log('user_status_changed', $user,
                ['status' => $oldStatus],
                ['status' => $newStatus, 'reason' => $reason]
            );
            
            return back()->with('success', "User status changed to {$newStatus}. Email notification sent.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to change status: ' . $e->getMessage());
        }
    }

    public function suspend(Request $request, User $user)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);
        
        try {
            $oldStatus = $user->status;
            $user->update(['status' => 'suspended']);
            
            // Send suspension email with reason
            Mail::to($user->email)->queue(new AccountSuspendedMail($user, $validated['reason']));
            
            // Create notification
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'danger',
                'title' => 'Account Suspended',
                'message' => 'Your account has been suspended. Reason: ' . $validated['reason'],
            ]);
            
            AuditLog::log('user_suspended', $user,
                ['status' => $oldStatus],
                ['status' => 'suspended', 'reason' => $validated['reason']]
            );
            
            return back()->with('success', 'User suspended successfully. Notification email sent.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to suspend user: ' . $e->getMessage());
        }
    }
    /**
     * Bulk upload users from CSV
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // 5MB max
            'role' => ['required', 'in:admin,instructor,student'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        try {
            $file = $request->file('csv_file');
            $role = $request->role;
            $sendEmail = $request->input('send_email', true);

            $csv = array_map('str_getcsv', file($file));
            $headers = array_shift($csv);
            
            $created = [];
            $failed = [];

            DB::beginTransaction();

            foreach ($csv as $index => $row) {
                try {
                    $data = array_combine($headers, $row);
                    
                    // Generate credentials
                    $username = $this->generateUsername($role);
                    $password = Str::random(12);

                    // Create user
                    $user = User::create([
                        'username' => $username,
                        'email' => $data['email'],
                        'password' => Hash::make($password),
                        'role' => $role,
                        'status' => 'active',
                        'must_change_password' => true,
                    ]);

                    // Create role-specific profile with name from CSV
                    if ($role === 'student') {
                        Student::create([
                            'user_id' => $user->id,
                            'student_number' => $this->generateStudentNumber(),
                            'first_name' => $data['first_name'],
                            'last_name' => $data['last_name'],
                            'middle_name' => $data['middle_name'] ?? null,
                        ]);
                    } elseif ($role === 'instructor') {
                        Instructor::create([
                            'user_id' => $user->id,
                            'employee_id' => $this->generateEmployeeId(),
                            'first_name' => $data['first_name'],
                            'last_name' => $data['last_name'],
                            'middle_name' => $data['middle_name'] ?? null,
                        ]);
                    } else {
                        Admin::create([
                            'user_id' => $user->id,
                            'first_name' => $data['first_name'],
                            'last_name' => $data['last_name'],
                            'middle_name' => $data['middle_name'] ?? null,
                        ]);
                    }

                    $created[] = [
                        'username' => $username,
                        'email' => $data['email'],
                        'password' => $password,
                        'name' => trim($data['first_name'] . ' ' . $data['last_name']),
                    ];

                } catch (\Exception $e) {
                    $failed[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();
            
            foreach ($created as $userData) {
                try {
                    $user = User::where('email', $userData['email'])->first();
                    if ($user) {
                        // Queue individual welcome email
                        SendWelcomeEmailJob::dispatch($user, $userData['password']);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to queue welcome email for bulk user', [
                        'email' => $userData['email'],
                        'error' => $e->getMessage()
                    ]);
                    $failed[] = "Email failed for: " . $userData['email'];
                }
            }

            // Also send summary to admin with CSV attachment
            SendBulkCreationSummaryJob::dispatch($created, auth()->user()->email);

            AuditLog::log('bulk_users_created', null, [], [
                'role' => $role,
                'count' => count($created),
                'emails_queued' => count($created)
            ]);

            $message = count($created) . " users created successfully. Welcome emails have been sent to each user.";
            if (!empty($failed)) {
                $message .= " " . count($failed) . " emails failed to send.";
            }

            return back()->with('success', $message);

            // Send bulk email with CSV attachment if requested
            if ($sendEmail && count($created) > 0) {
                try {
                    Mail::to(auth()->user()->email)
                        ->send(new BulkUsersCreatedMail($created, $role));

                    AuditLog::log('bulk_users_created', null, [], [
                        'role' => $role,
                        'count' => count($created),
                        'email_sent' => true,
                        'sent_to' => auth()->user()->email
                    ]);

                    $emailStatus = 'Credentials sent to your email with CSV attachment.';
                } catch (\Exception $e) {
                    \Log::error('Failed to send bulk creation email: ' . $e->getMessage());
                    $emailStatus = 'Users created but email failed. Please export credentials manually.';
                }
            } else {
                AuditLog::log('bulk_users_created', null, [], [
                    'role' => $role,
                    'count' => count($created),
                    'email_sent' => false
                ]);
                $emailStatus = 'Email notification disabled.';
            }
            AuditLog::log('bulk_users_created', null, [], [
                'role' => $role,
                'count' => count($created),
            ]);

            $message = count($created) . " users created successfully.";
            if (!empty($failed)) {
                $message .= " " . count($failed) . " failed.";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload users: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate(Request $request)
    {
        $role = $request->get('role', 'student');
        
        $headers = match($role) {
            'admin' => ['first_name', 'last_name', 'middle_name', 'email', 'phone', 'position', 'office'],
            'instructor' => ['first_name', 'last_name', 'middle_name', 'email', 'phone', 'employee_id', 'specialization_id', 'department', 'hire_date'],
            'student' => ['first_name', 'last_name', 'middle_name', 'email', 'phone', 'student_number', 'course_id', 'year_level', 'address', 'admission_date'],
        };

        $filename = "bulk_upload_template_{$role}.csv";
        
        $handle = fopen('php://temp', 'w');
        fputcsv($handle, $headers);
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Generate unique username
     */
    private function generateUsername(string $role): string
    {
        // Get role prefix
        $prefix = $this->getRolePrefix($role);
        
        // Get current year
        $year = date('Y');
        
        // Find the last username for this role and year
        $lastUser = User::where('role', $role)
            ->where('username', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastUser) {
            // Extract the number from the last username
            // Example: "AD-2025-0005" -> extract "0005"
            preg_match('/-(\d+)$/', $lastUser->username, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $nextNumber = $lastNumber + 1;
        } else {
            // First user of this role for this year
            $nextNumber = 1;
        }
        
        // Format: PREFIX-YEAR-NNNN (4 digits, zero-padded)
        $sequence = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $year . '-' . $sequence;
    }

    private function getRolePrefix(string $role): string
    {
        return match(strtolower($role)) {
            'admin' => 'AD',
            'instructor' => 'INS',
            'student' => 'ST',
            default => 'USR', // fallback
        };
    }

    /**
     * Create user profile based on role
     */
    private function createProfile(User $user, string $role, array $data): void
    {
        switch ($role) {
            case 'admin':
                Admin::create([
                    'user_id' => $user->id,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'position' => $data['position'] ?? null,
                    'office' => $data['office'] ?? null,
                    'phone' => $data['phone'] ?? null,
                ]);
                break;

            case 'instructor':
                Instructor::create([
                    'user_id' => $user->id,
                    'employee_id' => $data['employee_id'],
                    'specialization_id' => $data['specialization_id'] ?? null,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'department' => $data['department'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'hire_date' => $data['hire_date'] ?? null,
                ]);
                break;

            case 'student':
                Student::create([
                    'user_id' => $user->id,
                    'student_number' => $data['student_number'],
                    'course_id' => $data['course_id'] ?? null,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'year_level' => $data['year_level'] ?? 1,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'admission_date' => $data['admission_date'] ?? now(),
                ]);
                break;
        }
    }
}