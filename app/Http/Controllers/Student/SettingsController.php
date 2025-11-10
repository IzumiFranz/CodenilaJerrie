<?php

namespace App\Http\Controllers\Student;

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
                'email_lesson_published' => true,
                'email_quiz_published' => true,
                'email_quiz_result' => true,
                'email_feedback_response' => true,
                'email_enrollment' => true,
                'email_announcement' => true,
                
                // In-app notification preferences
                'notification_lesson_published' => true,
                'notification_quiz_published' => true,
                'notification_quiz_result' => true,
                'notification_feedback_response' => true,
                'notification_enrollment' => true,
                'notification_announcement' => true,
                
                // Display preferences
                'theme' => 'light',
                'language' => 'en',
                'timezone' => 'Asia/Manila',
                'items_per_page' => 20,
                
                // Privacy preferences
                'show_profile_to_others' => false,
                'show_progress_to_instructors' => true,
                
                // Quiz preferences
                'auto_save_interval' => 2, // seconds
                'show_timer_warning' => true,
                'play_timer_sound' => true,
            ]
        );
        
        return view('student.settings.index', compact('settings'));
    }
    
    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $user = $request->user();
        $settings = UserSetting::firstOrCreate(['user_id' => $user->id]);
        
        $validated = $request->validate([
            'email_lesson_published' => 'boolean',
            'email_quiz_published' => 'boolean',
            'email_quiz_result' => 'boolean',
            'email_feedback_response' => 'boolean',
            'email_enrollment' => 'boolean',
            'email_announcement' => 'boolean',
            'notification_lesson_published' => 'boolean',
            'notification_quiz_published' => 'boolean',
            'notification_quiz_result' => 'boolean',
            'notification_feedback_response' => 'boolean',
            'notification_enrollment' => 'boolean',
            'notification_announcement' => 'boolean',
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
     * Update privacy preferences
     */
    public function updatePrivacy(Request $request)
    {
        $user = $request->user();
        $settings = UserSetting::firstOrCreate(['user_id' => $user->id]);
        
        $validated = [
            'show_profile_to_others' => $request->has('show_profile_to_others'),
            'show_progress_to_instructors' => $request->has('show_progress_to_instructors'),
        ];
        
        $settings->update($validated);
        
        AuditLog::log('settings_updated', null, [], ['section' => 'privacy']);
        
        return back()->with('success', 'Privacy preferences updated successfully.');
    }
    
    /**
     * Update quiz preferences
     */
    public function updateQuizPreferences(Request $request)
    {
        $user = $request->user();
        $settings = UserSetting::firstOrCreate(['user_id' => $user->id]);
        
        $validated = $request->validate([
            'auto_save_interval' => 'required|integer|min:1|max:10',
            'show_timer_warning' => 'boolean',
            'play_timer_sound' => 'boolean',
        ]);
        
        $validated['show_timer_warning'] = $request->has('show_timer_warning');
        $validated['play_timer_sound'] = $request->has('play_timer_sound');
        
        $settings->update($validated);
        
        AuditLog::log('settings_updated', null, [], ['section' => 'quiz_preferences']);
        
        return back()->with('success', 'Quiz preferences updated successfully.');
    }
    
    /**
     * Reset settings to default
     */
    public function reset(Request $request)
    {
        $user = $request->user();
        $settings = UserSetting::firstOrCreate(['user_id' => $user->id]);
        
        $settings->update([
            'email_lesson_published' => true,
            'email_quiz_published' => true,
            'email_quiz_result' => true,
            'email_feedback_response' => true,
            'email_enrollment' => true,
            'email_announcement' => true,
            'notification_lesson_published' => true,
            'notification_quiz_published' => true,
            'notification_quiz_result' => true,
            'notification_feedback_response' => true,
            'notification_enrollment' => true,
            'notification_announcement' => true,
            'theme' => 'light',
            'language' => 'en',
            'timezone' => 'Asia/Manila',
            'items_per_page' => 20,
            'show_profile_to_others' => false,
            'show_progress_to_instructors' => true,
            'auto_save_interval' => 2,
            'show_timer_warning' => true,
            'play_timer_sound' => true,
        ]);
        
        AuditLog::log('settings_reset', null);
        
        return back()->with('success', 'Settings reset to default successfully.');
    }
}