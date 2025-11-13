<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSetting;
use App\Models\AuditLog;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get or create user settings
        $settings = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                // Notification preferences
                'email_new_user' => true,
                'email_feedback_submitted' => true,
                'email_system_error' => true,
                'email_weekly_report' => true,
                'email_course_milestone' => true,
                
                // In-app notification preferences
                'notification_new_user' => true,
                'notification_feedback_submitted' => true,
                'notification_system_error' => true,
                'notification_weekly_report' => true,
                'notification_course_milestone' => true,
                
                // Display preferences
                'theme' => 'light',
                'language' => 'en',
                'timezone' => 'Asia/Manila',
                'items_per_page' => 20,
                
                // System defaults
                'default_academic_year' => null,
                'default_semester' => '1st',
                'default_section_max_students' => 40,
                'auto_approve_enrollments' => false,
            ]
        );
        
        return view('admin.settings.index', compact('settings'));
    }
    
    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $user = $request->user();
        $settings = UserSetting::firstOrCreate(['user_id' => $user->id]);
        
        $validated = $request->validate([
            'email_new_user' => 'boolean',
            'email_feedback_submitted' => 'boolean',
            'email_system_error' => 'boolean',
            'email_weekly_report' => 'boolean',
            'email_course_milestone' => 'boolean',
            'notification_new_user' => 'boolean',
            'notification_feedback_submitted' => 'boolean',
            'notification_system_error' => 'boolean',
            'notification_weekly_report' => 'boolean',
            'notification_course_milestone' => 'boolean',
        ]);
        
        // Set false for unchecked checkboxes
        foreach ($validated as $key => $value) {
            $validated[$key] = $request->has($key);
        }
        
        $settings->update($validated);
        
        AuditLog::log('settings_updated', null, [], ['section' => 'notifications']);
        
        return back()->with('success', 'Notification preferences updated successfully.');
    }
    
    /**
     * Update display preferences
     */
    public function updateDisplay(Request $request)
    {
        $user = $request->user();
        $settings = UserSetting::firstOrCreate(['user_id' => $user->id]);
        
        $validated = $request->validate([
            'theme' => 'required|in:light,dark',
            'language' => 'required|in:en,es,fr,tl',
            'timezone' => 'required|string',
            'items_per_page' => 'required|integer|min:10|max:100',
        ]);
        
        $settings->update($validated);
        
        AuditLog::log('settings_updated', null, [], ['section' => 'display']);
        
        return back()->with('success', 'Display preferences updated successfully.');
    }
    
    /**
     * Update system defaults
     */
    public function updateSystemDefaults(Request $request)
    {
        $user = $request->user();
        $settings = UserSetting::firstOrCreate(['user_id' => $user->id]);
        
        $validated = $request->validate([
            'default_academic_year' => 'nullable|string|max:20',
            'default_semester' => 'required|in:1st,2nd,summer',
            'default_section_max_students' => 'required|integer|min:10|max:100',
            'auto_approve_enrollments' => 'boolean',
        ]);
        
        $validated['auto_approve_enrollments'] = $request->has('auto_approve_enrollments');
        
        $settings->update($validated);
        
        AuditLog::log('settings_updated', null, [], ['section' => 'system_defaults']);
        
        return back()->with('success', 'System defaults updated successfully.');
    }
    
    /**
     * Reset settings to default
     */
    public function reset(Request $request)
    {
        $user = $request->user();
        $settings = UserSetting::firstOrCreate(['user_id' => $user->id]);
        
        $settings->update([
            'email_new_user' => true,
            'email_feedback_submitted' => true,
            'email_system_error' => true,
            'email_weekly_report' => true,
            'email_course_milestone' => true,
            'notification_new_user' => true,
            'notification_feedback_submitted' => true,
            'notification_system_error' => true,
            'notification_weekly_report' => true,
            'notification_course_milestone' => true,
            'theme' => 'light',
            'language' => 'en',
            'timezone' => 'Asia/Manila',
            'items_per_page' => 20,
            'default_academic_year' => null,
            'default_semester' => '1st',
            'default_section_max_students' => 40,
            'auto_approve_enrollments' => false,
        ]);
        
        AuditLog::log('settings_reset', null);
        
        return back()->with('success', 'Settings reset to default successfully.');
    }
}
