<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Email notification preferences
            $table->boolean('email_lesson_published')->default(true);
            $table->boolean('email_quiz_published')->default(true);
            $table->boolean('email_quiz_result')->default(true);
            $table->boolean('email_feedback_response')->default(true);
            $table->boolean('email_enrollment')->default(true);
            $table->boolean('email_announcement')->default(true);
            
            // In-app notification preferences
            $table->boolean('notification_lesson_published')->default(true);
            $table->boolean('notification_quiz_published')->default(true);
            $table->boolean('notification_quiz_result')->default(true);
            $table->boolean('notification_feedback_response')->default(true);
            $table->boolean('notification_enrollment')->default(true);
            $table->boolean('notification_announcement')->default(true);
            
            // Display preferences
            $table->string('theme', 10)->default('light'); // light, dark
            $table->string('language', 5)->default('en'); // en, es, fr, tl
            $table->string('timezone', 50)->default('Asia/Manila');
            $table->integer('items_per_page')->default(20);
            
            // Privacy preferences
            $table->boolean('show_profile_to_others')->default(false);
            $table->boolean('show_progress_to_instructors')->default(true);
            
            // Quiz preferences
            $table->integer('auto_save_interval')->default(2); // seconds
            $table->boolean('show_timer_warning')->default(true);
            $table->boolean('play_timer_sound')->default(true);
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
};