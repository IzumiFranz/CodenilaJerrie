<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        // Student notifications
        'email_lesson_published',
        'email_quiz_published',
        'email_quiz_result',
        'email_feedback_response',
        'email_enrollment',
        'email_announcement',
        'notification_lesson_published',
        'notification_quiz_published',
        'notification_quiz_result',
        'notification_feedback_response',
        'notification_enrollment',
        'notification_announcement',
        // Instructor notifications
        'email_student_enrolled',
        'email_quiz_submitted',
        'email_feedback_submitted',
        'email_low_performance_alert',
        'notification_student_enrolled',
        'notification_quiz_submitted',
        'notification_feedback_submitted',
        'notification_low_performance_alert',
        // Admin notifications
        'email_new_user',
        'email_system_error',
        'email_weekly_report',
        'email_course_milestone',
        'notification_new_user',
        'notification_system_error',
        'notification_weekly_report',
        'notification_course_milestone',
        // Display preferences (all roles)
        'theme',
        'language',
        'timezone',
        'items_per_page',
        // Student privacy
        'show_profile_to_others',
        'show_progress_to_instructors',
        // Student quiz preferences
        'auto_save_interval',
        'show_timer_warning',
        'play_timer_sound',
        // Instructor quiz defaults
        'default_quiz_time_limit',
        'default_passing_score',
        'default_max_attempts',
        // Admin system defaults
        'default_academic_year',
        'default_semester',
        'default_section_max_students',
        'auto_approve_enrollments',
    ];
    
    protected $casts = [
        // Student notifications
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
        // Instructor notifications
        'email_student_enrolled' => 'boolean',
        'email_quiz_submitted' => 'boolean',
        'email_feedback_submitted' => 'boolean',
        'email_low_performance_alert' => 'boolean',
        'notification_student_enrolled' => 'boolean',
        'notification_quiz_submitted' => 'boolean',
        'notification_feedback_submitted' => 'boolean',
        'notification_low_performance_alert' => 'boolean',
        // Admin notifications
        'email_new_user' => 'boolean',
        'email_system_error' => 'boolean',
        'email_weekly_report' => 'boolean',
        'email_course_milestone' => 'boolean',
        'notification_new_user' => 'boolean',
        'notification_system_error' => 'boolean',
        'notification_weekly_report' => 'boolean',
        'notification_course_milestone' => 'boolean',
        // Student privacy
        'show_profile_to_others' => 'boolean',
        'show_progress_to_instructors' => 'boolean',
        // Student quiz preferences
        'show_timer_warning' => 'boolean',
        'play_timer_sound' => 'boolean',
        // Admin system defaults
        'auto_approve_enrollments' => 'boolean',
    ];
    
    /**
     * Relationship: User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
?>