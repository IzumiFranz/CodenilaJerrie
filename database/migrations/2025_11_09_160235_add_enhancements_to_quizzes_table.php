<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->timestamp('publish_at')->nullable()->after('is_published');
            $table->boolean('allow_review_mode')->default(false)->after('show_answers');
            $table->boolean('allow_practice_mode')->default(false)->after('allow_review_mode');
            $table->string('difficulty_level')->default('medium')->after('passing_score'); // easy, medium, hard
            $table->timestamp('scheduled_publish_at')->nullable()->after('published_at');
            $table->timestamp('scheduled_unpublish_at')->nullable()->after('scheduled_publish_at');
            $table->boolean('auto_publish')->default(false)->after('scheduled_unpublish_at');
            $table->boolean('show_correct_in_review')->default(true)->after('allow_review_mode');
            $table->boolean('show_explanation_in_review')->default(true)->after('show_correct_in_review');
            $table->integer('review_available_after')->nullable()->after('show_explanation_in_review'); // minutes after submission

            // Additional enhancements
            $table->integer('estimated_duration')->nullable()->after('time_limit');
            
            // Indexes
            $table->index('scheduled_publish_at');
            $table->index('scheduled_unpublish_at');
            $table->index('auto_publish');
            $table->index('difficulty_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex(['scheduled_publish_at']);
            $table->dropIndex(['scheduled_unpublish_at']);
            $table->dropIndex(['auto_publish']);
            $table->dropIndex(['difficulty_level']);
            
            $table->dropColumn([
                'scheduled_publish_at',
                'scheduled_unpublish_at',
                'auto_publish',
                'difficulty_level',
                'estimated_duration', 
                'publish_at', 
                'allow_review_mode', 
                'allow_practice_mode',
                'show_correct_in_review',
                'show_explanation_in_review',
                'review_available_after'
            ]);
        });
    }
};
