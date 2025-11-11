<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $role = $user->role;

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route($role . '.dashboard') . '?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            // Log email verification
            \App\Models\AuditLog::log(
                'email_verified',
                $user,
                [],
                [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]
            );
        }

        return redirect()->intended(route($role . '.dashboard') . '?verified=1')
            ->with('status', 'Your email has been verified successfully!');
    }
}
