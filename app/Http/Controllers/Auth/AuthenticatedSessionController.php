<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Log the login
        \App\Models\AuditLog::log(
            'user_login',
            auth()->user(),
            [],
            [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        );

        // Update last login timestamp
        auth()->user()->update([
            'last_login_at' => now()
        ]);

        // Redirect based on role
        $user = auth()->user();
        
        return match($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'instructor' => redirect()->intended(route('instructor.dashboard')),
            'student' => redirect()->intended(route('student.dashboard')),
            default => redirect()->intended('/'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log the logout
        if (auth()->check()) {
            \App\Models\AuditLog::log(
                'user_logout',
                auth()->user(),
                [],
                [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'You have been logged out successfully.');
    }
}