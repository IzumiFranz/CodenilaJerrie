<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Check if columns already exist before adding
        $columnsToAdd = [];
        
        // Instructor email notifications
        if (!Schema::hasColumn('user_settings', 'email_student_enrolled')) {
            $columnsToAdd[] = ['email_student_enrolled', 'boolean', true, 'email_announcement'];
        }
        if (!Schema::hasColumn('user_settings', 'email_quiz_submitted')) {
            $columnsToAdd[] = ['email_quiz_submitted', 'boolean', true, 'email_student_enrolled'];
        }
        if (!Schema::hasColumn('user_settings', 'email_feedback_submitted')) {
            $columnsToAdd[] = ['email_feedback_submitted', 'boolean', true, 'email_quiz_submitted'];
        }
        if (!Schema::hasColumn('user_settings', 'email_low_performance_alert')) {
            $columnsToAdd[] = ['email_low_performance_alert', 'boolean', true, 'email_feedback_submitted'];
        }
        
        // Instructor in-app notifications
        if (!Schema::hasColumn('user_settings', 'notification_student_enrolled')) {
            $columnsToAdd[] = ['notification_student_enrolled', 'boolean', true, 'notification_announcement'];
        }
        if (!Schema::hasColumn('user_settings', 'notification_quiz_submitted')) {
            $columnsToAdd[] = ['notification_quiz_submitted', 'boolean', true, 'notification_student_enrolled'];
        }
        if (!Schema::hasColumn('user_settings', 'notification_feedback_submitted')) {
            $columnsToAdd[] = ['notification_feedback_submitted', 'boolean', true, 'notification_quiz_submitted'];
        }
        if (!Schema::hasColumn('user_settings', 'notification_low_performance_alert')) {
            $columnsToAdd[] = ['notification_low_performance_alert', 'boolean', true, 'notification_feedback_submitted'];
        }
        
        // Admin email notifications
        if (!Schema::hasColumn('user_settings', 'email_new_user')) {
            $columnsToAdd[] = ['email_new_user', 'boolean', true, 'email_low_performance_alert'];
        }
        if (!Schema::hasColumn('user_settings', 'email_system_error')) {
            $columnsToAdd[] = ['email_system_error', 'boolean', true, 'email_new_user'];
        }
        if (!Schema::hasColumn('user_settings', 'email_weekly_report')) {
            $columnsToAdd[] = ['email_weekly_report', 'boolean', true, 'email_system_error'];
        }
        if (!Schema::hasColumn('user_settings', 'email_course_milestone')) {
            $columnsToAdd[] = ['email_course_milestone', 'boolean', true, 'email_weekly_report'];
        }
        
        // Admin in-app notifications
        if (!Schema::hasColumn('user_settings', 'notification_new_user')) {
            $columnsToAdd[] = ['notification_new_user', 'boolean', true, 'notification_low_performance_alert'];
        }
        if (!Schema::hasColumn('user_settings', 'notification_system_error')) {
            $columnsToAdd[] = ['notification_system_error', 'boolean', true, 'notification_new_user'];
        }
        if (!Schema::hasColumn('user_settings', 'notification_weekly_report')) {
            $columnsToAdd[] = ['notification_weekly_report', 'boolean', true, 'notification_system_error'];
        }
        if (!Schema::hasColumn('user_settings', 'notification_course_milestone')) {
            $columnsToAdd[] = ['notification_course_milestone', 'boolean', true, 'notification_weekly_report'];
        }
        
        // Instructor quiz defaults
        if (!Schema::hasColumn('user_settings', 'default_quiz_time_limit')) {
            $columnsToAdd[] = ['default_quiz_time_limit', 'integer', 30, 'play_timer_sound'];
        }
        if (!Schema::hasColumn('user_settings', 'default_passing_score')) {
            $columnsToAdd[] = ['default_passing_score', 'integer', 75, 'default_quiz_time_limit'];
        }
        if (!Schema::hasColumn('user_settings', 'default_max_attempts')) {
            $columnsToAdd[] = ['default_max_attempts', 'integer', 3, 'default_passing_score'];
        }
        
        // Admin system defaults
        if (!Schema::hasColumn('user_settings', 'default_academic_year')) {
            $columnsToAdd[] = ['default_academic_year', 'string', null, 'default_max_attempts', 20];
        }
        if (!Schema::hasColumn('user_settings', 'default_semester')) {
            $columnsToAdd[] = ['default_semester', 'string', '1st', 'default_academic_year', 10];
        }
        if (!Schema::hasColumn('user_settings', 'default_section_max_students')) {
            $columnsToAdd[] = ['default_section_max_students', 'integer', 40, 'default_semester'];
        }
        if (!Schema::hasColumn('user_settings', 'auto_approve_enrollments')) {
            $columnsToAdd[] = ['auto_approve_enrollments', 'boolean', false, 'default_section_max_students'];
        }
        
        if (!empty($columnsToAdd)) {
            Schema::table('user_settings', function (Blueprint $table) use ($columnsToAdd) {
                foreach ($columnsToAdd as $column) {
                    $name = $column[0];
                    $type = $column[1];
                    $default = $column[2];
                    $after = $column[3];
                    $length = $column[4] ?? null;
                    
                    if ($type === 'boolean') {
                        $col = $table->boolean($name)->default($default);
                    } elseif ($type === 'integer') {
                        $col = $table->integer($name)->default($default);
                    } elseif ($type === 'string') {
                        $col = $length 
                            ? $table->string($name, $length)->default($default)
                            : $table->string($name)->default($default);
                        if ($default === null) {
                            $col->nullable();
                        }
                    }
                    
                    if ($after && Schema::hasColumn('user_settings', $after)) {
                        $col->after($after);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('user_settings', function (Blueprint $table) {
            // Drop instructor columns
            $table->dropColumn([
                'email_student_enrolled',
                'email_quiz_submitted',
                'email_feedback_submitted',
                'email_low_performance_alert',
                'notification_student_enrolled',
                'notification_quiz_submitted',
                'notification_feedback_submitted',
                'notification_low_performance_alert',
            ]);
            
            // Drop admin columns
            $table->dropColumn([
                'email_new_user',
                'email_system_error',
                'email_weekly_report',
                'email_course_milestone',
                'notification_new_user',
                'notification_system_error',
                'notification_weekly_report',
                'notification_course_milestone',
            ]);
            
            // Drop instructor quiz defaults
            $table->dropColumn([
                'default_quiz_time_limit',
                'default_passing_score',
                'default_max_attempts',
            ]);
            
            // Drop admin system defaults
            $table->dropColumn([
                'default_academic_year',
                'default_semester',
                'default_section_max_students',
                'auto_approve_enrollments',
            ]);
        });
    }
};