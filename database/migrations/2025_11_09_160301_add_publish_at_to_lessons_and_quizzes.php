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
        // Add publish_at to lessons if it doesn't exist
        if (!Schema::hasColumn('lessons', 'publish_at')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->timestamp('publish_at')->nullable()->after('published_at');
            });
        }

        // Add publish_at to quizzes if it doesn't exist (may already exist from previous migration)
        if (!Schema::hasColumn('quizzes', 'publish_at')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->timestamp('publish_at')->nullable()->after('published_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if column exists
        if (Schema::hasColumn('lessons', 'publish_at')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropColumn('publish_at');
            });
        }

        if (Schema::hasColumn('quizzes', 'publish_at')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropColumn('publish_at');
            });
        }
    }
};
