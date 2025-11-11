<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised()
            ],
        ], [
            'password.mixed_case' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.uncompromised' => 'This password has appeared in a data leak. Please choose a different password.',
        ]);

        // Update password
        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false, // Remove force change flag if present
        ]);

        // Log the password change
        \App\Models\AuditLog::log(
            'password_changed',
            $request->user(),
            [],
            [
                'changed_via' => 'profile_update',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        );

        return back()->with('status', 'password-updated');
    }
}