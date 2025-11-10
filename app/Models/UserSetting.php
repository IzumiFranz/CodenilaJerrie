<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
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
        'theme',
        'language',
        'timezone',
        'items_per_page',
        'show_profile_to_others',
        'show_progress_to_instructors',
        'auto_save_interval',
        'show_timer_warning',
        'play_timer_sound',
    ];
    
    protected $casts = [
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
        'show_profile_to_others' => 'boolean',
        'show_progress_to_instructors' => 'boolean',
        'show_timer_warning' => 'boolean',
        'play_timer_sound' => 'boolean',
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