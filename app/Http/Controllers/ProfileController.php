<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\AuditLog;
use App\Mail\PasswordChangedMail;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        // Validate based on role
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];

        if ($user->isAdmin()) {
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['last_name'] = ['required', 'string', 'max:255'];
            $rules['middle_name'] = ['nullable', 'string', 'max:255'];
            $rules['position'] = ['nullable', 'string', 'max:255'];
            $rules['office'] = ['nullable', 'string', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:20'];
        } elseif ($user->isInstructor()) {
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['last_name'] = ['required', 'string', 'max:255'];
            $rules['middle_name'] = ['nullable', 'string', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:20'];
            $rules['department'] = ['nullable', 'string', 'max:255'];
        } elseif ($user->isStudent()) {
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['last_name'] = ['required', 'string', 'max:255'];
            $rules['middle_name'] = ['nullable', 'string', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:20'];
            $rules['address'] = ['nullable', 'string', 'max:500'];
        }

        $validated = $request->validate($rules);

        try {
            $oldValues = [
                'email' => $user->email,
            ];

            // Update profile
        $profileData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ];

            // Add role-specific data
            if ($user->isAdmin()) {
                $profileData['position'] = $validated['position'] ?? null;
                $profileData['office'] = $validated['office'] ?? null;
            } elseif ($user->isInstructor()) {
                $profileData['department'] = $validated['department'] ?? null;
            } elseif ($user->isStudent()) {
                $profileData['address'] = $validated['address'] ?? null;
            }

            $profile->update($profileData);

            // Log the update
            AuditLog::log('profile_updated', $user, $oldValues, ['email' => $user->email]);

            return back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Display the password change form.
     */
    public function changePassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'current_password' => ['required_if:must_change,false', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $rules = [
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
        ];

        // Only require current password if not forced to change
        if (!$request->user()->must_change_password) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules, [
            'password.min' => 'Password must be at least 8 characters.',
            'password.mixed_case' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'current_password.current_password' => 'The current password is incorrect.',
        ]);

        // Update password
        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        try {
            // Send confirmation email
            Mail::to($user->email)->queue(new PasswordChangedMail($user));
        } catch (\Exception $e) {
            \Log::warning('Failed to send password change email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

        // Log the password change
        \App\Models\AuditLog::log(
            'password_changed',
            $request->user(),
            [],
            [
                'changed_via' => 'profile',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        );

        return redirect()
            ->route($request->user()->role . '.dashboard')
            ->with('status', 'Password changed successfully!');
    }

    /**
     * Upload user avatar.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], // 2MB max
        ]);

        try {
            $user = $request->user();

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');

            $user->profile_picture = $path;
            $user->save();

            // Log avatar upload
            AuditLog::log('avatar_uploaded', $user);

            return back()->with('success', 'Profile picture updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload avatar: ' . $e->getMessage());
        }
    }

    /**
     * Delete user avatar.
     */
    public function deleteAvatar(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->profile_picture) {
                // Delete from storage
                if (Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $user->update([
                    'profile_picture' => null
                ]);

                // Log avatar deletion
                AuditLog::log('avatar_deleted', $user);

                return back()->with('success', 'Profile picture deleted successfully.');
            }

            return back()->with('info', 'No profile picture to delete.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete avatar: ' . $e->getMessage());
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Log before deletion
        \App\Models\AuditLog::log(
            'account_deleted',
            $user,
            [],
            [
                'deleted_by' => 'self',
                'ip_address' => $request->ip()
            ]
        );

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('status', 'Your account has been deleted successfully.');
    }
}