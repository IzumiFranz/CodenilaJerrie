<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordMiddleware
{
    /**
     * Handle an incoming request.
     * Force users to change password on first login
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Skip password change requirement for these routes
        $exemptRoutes = [
            'profile.change-password',
            'profile.update-password',
            'logout',
        ];

        // Check if current route is exempt
        $currentRoute = $request->route()->getName();
        if (in_array($currentRoute, $exemptRoutes)) {
            return $next($request);
        }

        // Check if user must change password
        if ($user->must_change_password) {
            // Log the redirect
            \App\Models\AuditLog::log(
                'password_change_required',
                $user,
                [],
                ['redirected_from' => $currentRoute]
            );

            return redirect()
                ->route('profile.change-password')
                ->with('warning', 'You must change your password before continuing.');
        }

        return $next($request);
    }
}