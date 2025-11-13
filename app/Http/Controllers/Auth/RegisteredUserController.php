<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Check if public registration is disabled
        if (config('app.disable_registration', false)) {
            abort(404, 'Public registration is currently disabled.');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if public registration is disabled
        if (config('app.disable_registration', false)) {
            abort(404, 'Public registration is currently disabled.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);

        $role = 'student';

        $username = $this->generateUsername($role);
        $studentNumber = $this->generateStudentNumber();
        
        // Create user account
        $user = User::create([
            'username' => $username,
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $role,
            'status' => 'active',
            'must_change_password' => false,  // Self-registered users chose their password
            'email_verified_at' => null, // Require email verification
        ]);

        // Create student profile
        Student::create([
            'user_id' => $user->id,
            'student_number' => $studentNumber,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'year_level' => 1, // Default to first year
            'admission_date' => now(),
        ]);

        // Log the registration
        \App\Models\AuditLog::log(
            'user_registered',
            $user,
            [],
            [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'registration_type' => 'self'
            ]
        );

        event(new Registered($user));

        Auth::login($user);

        // Redirect to email verification
        return redirect()->route('verification.notice')
            ->with('status', 'Registration successful! Please verify your email address.');
    }

    /**
     * Generate unique username from name
     */
    private function generateUsername(): string
    {
        $year = date('Y');
        
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastStudent && preg_match('/-(\d+)$/', $lastStudent->student_number, $matches)) {
            $lastNumber = (int)$matches[1];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        $sequence = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $year . '-' . $sequence;
    }

    /**
     * Generate unique student number
     */
    private function generateStudentNumber(): string
    {
        $year = date('Y');
        
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastStudent && preg_match('/-(\d+)$/', $lastStudent->student_number, $matches)) {
            $lastNumber = (int)$matches[1];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        $sequence = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        return $year . '-' . $sequence;
    }
}