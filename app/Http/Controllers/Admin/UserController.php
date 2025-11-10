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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('profile');

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
        ]);

        try {
            DB::beginTransaction();

            // Generate username and password
            $username = $this->generateUsername($validated['first_name'], $validated['last_name']);
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


            DB::commit();

            // Log the creation
            AuditLog::log('user_created', $user, [], [
                'role' => $user->role,
                'email' => $user->email,
            ]);

            // 🔔 SEND WELCOME EMAIL WITH CREDENTIALS
            Mail::to($user->email)->queue(new WelcomeMail($user, $password));
        
            return redirect()
                ->route('admin.users.index')
                ->with('success', "User created successfully. Username: {$username}, Password: {$password}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load('profile', 'auditLogs', 'notifications');

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        $user->load('profile');
        $courses = Course::where('is_active', true)->get();
        $specializations = Specialization::where('is_active', true)->get();

        return view('admin.users.edit', compact('user', 'courses', 'specializations'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
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
            'employee_id' => ['nullable', 'string', 'unique:instructors,employee_id,' . ($user->instructor->id ?? 'NULL')],
            'specialization_id' => ['nullable', 'exists:specializations,id'],
            'department' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            'student_number' => ['nullable', 'string', 'unique:students,student_number,' . ($user->student->id ?? 'NULL')],
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
            ->with('profile')
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
    public function toggleStatus(User $user)
    {
        try {
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            
            $user->update(['status' => $newStatus]);

            AuditLog::log('user_status_changed', $user, 
                ['status' => $user->status], 
                ['status' => $newStatus]
            );

            return back()->with('success', "User status changed to {$newStatus}.");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to change status: ' . $e->getMessage());
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
        ]);

        try {
            $file = $request->file('csv_file');
            $role = $request->role;
            
            $csv = array_map('str_getcsv', file($file));
            $headers = array_shift($csv);
            
            $created = [];
            $failed = [];
            
            DB::beginTransaction();

            foreach ($csv as $index => $row) {
                try {
                    $data = array_combine($headers, $row);
                    
                    // Generate credentials
                    $username = $this->generateUsername($data['first_name'], $data['last_name']);
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

                    // Create profile based on role
                    $this->createProfile($user, $role, $data);

                    $created[] = [
                        'username' => $username,
                        'email' => $data['email'],
                        'password' => $password,
                        'name' => $data['first_name'] . ' ' . $data['last_name'],
                    ];

                } catch (\Exception $e) {
                    $failed[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            // TODO: Generate CSV with credentials and send via email
            // Mail::to(auth()->user()->email)->send(new BulkUsersCreatedMail($created));

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
    private function generateUsername(string $firstName, string $lastName): string
    {
        $base = strtolower(substr($firstName, 0, 1) . $lastName);
        $base = preg_replace('/[^a-z0-9]/', '', $base);
        
        $username = $base;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
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