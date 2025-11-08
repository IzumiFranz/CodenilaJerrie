<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\AuditLog;

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

            // Update user email
            $user->email = $validated['email'];
            $user->save();

            // Update profile based on role
            if ($profile) {
                $profileData = array_intersect_key($validated, array_flip([
                    'first_name', 'last_name', 'middle_name', 'phone', 'position', 
                    'office', 'department', 'address'
                ]));
                
                $profile->update($profileData);
            }

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
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required_if:must_change,false', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $user->password = Hash::make($validated['password']);
            $user->must_change_password = false;
            $user->save();

            // Log password change
            AuditLog::log('password_changed', $user);

            // Determine redirect based on role
            $redirectRoute = match($user->role) {
                'admin' => 'admin.dashboard',
                'instructor' => 'instructor.dashboard',
                'student' => 'student.dashboard',
                default => 'home',
            };

            return redirect()->route($redirectRoute)
                ->with('success', 'Password changed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to change password: ' . $e->getMessage());
        }
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

            // Delete old avatar if exists
            if ($user->profile_picture) {
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
                Storage::disk('public')->delete($user->profile_picture);
                $user->profile_picture = null;
                $user->save();

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
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Log account deletion
        AuditLog::log('account_deleted', $user);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}