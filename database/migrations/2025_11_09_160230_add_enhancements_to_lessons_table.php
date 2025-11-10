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
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('prerequisite_lesson_id')->nullable()->constrained('lessons')->after('subject_id');
            $table->timestamp('scheduled_publish_at')->nullable()->after('published_at');
            $table->timestamp('scheduled_unpublish_at')->nullable()->after('scheduled_publish_at');
            $table->boolean('auto_publish')->default(false)->after('scheduled_unpublish_at');
            
            // Additional enhancements
            $table->integer('word_count')->nullable()->after('content');
            $table->integer('read_time_minutes')->nullable()->after('word_count');
            $table->string('file_name')->nullable()->after('file_path');
            
            // Indexes for scheduling
            $table->index('scheduled_publish_at');
            $table->index('scheduled_unpublish_at');
            $table->index('auto_publish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['scheduled_publish_at']);
            $table->dropIndex(['scheduled_unpublish_at']);
            $table->dropIndex(['auto_publish']);
            
            $table->dropColumn([
                'scheduled_publish_at',
                'scheduled_unpublish_at',
                'auto_publish',
                'word_count',
                'read_time_minutes',
                'file_name',
                'prerequisite_lesson_id'
            ]);
        });
    }
};
