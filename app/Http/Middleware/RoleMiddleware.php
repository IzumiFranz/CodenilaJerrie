<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = auth()->user();

        // Check if user's account is active
        if ($user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        // Check if user has one of the required roles
        if (!in_array($user->role, $roles)) {
            // Log unauthorized access attempt
            \App\Models\AuditLog::log(
                'unauthorized_access_attempt',
                null,
                [],
                ['attempted_roles' => $roles, 'user_role' => $user->role]
            );

            // Redirect based on user's actual role
            return $this->redirectToRoleDashboard($user->role);
        }

        return $next($request);
    }

    /**
     * Redirect user to their appropriate dashboard
     */
    protected function redirectToRoleDashboard(string $role): Response
    {
        $message = 'You do not have permission to access this page.';

        return match($role) {
            'admin' => redirect()->route('admin.dashboard')->with('error', $message),
            'instructor' => redirect()->route('instructor.dashboard')->with('error', $message),
            'student' => redirect()->route('student.dashboard')->with('error', $message),
            default => redirect()->route('login')->with('error', $message),
        };
    }
}