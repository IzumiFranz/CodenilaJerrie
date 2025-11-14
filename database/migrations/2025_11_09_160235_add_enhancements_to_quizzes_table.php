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
            // Add columns only if they don't exist
            if (!Schema::hasColumn('quizzes', 'publish_at')) {
                $table->timestamp('publish_at')->nullable()->after('is_published');
            }
            if (!Schema::hasColumn('quizzes', 'allow_review_mode')) {
                $table->boolean('allow_review_mode')->default(false)->after('show_answers');
            }
            if (!Schema::hasColumn('quizzes', 'allow_practice_mode')) {
                $table->boolean('allow_practice_mode')->default(false)->after('allow_review_mode');
            }
            if (!Schema::hasColumn('quizzes', 'difficulty_level')) {
                $table->string('difficulty_level')->default('medium')->after('passing_score');
            }
            if (!Schema::hasColumn('quizzes', 'scheduled_publish_at')) {
                $table->timestamp('scheduled_publish_at')->nullable()->after('published_at');
            }
            if (!Schema::hasColumn('quizzes', 'scheduled_unpublish_at')) {
                $table->timestamp('scheduled_unpublish_at')->nullable()->after('scheduled_publish_at');
            }
            if (!Schema::hasColumn('quizzes', 'auto_publish')) {
                $table->boolean('auto_publish')->default(false)->after('scheduled_unpublish_at');
            }
            if (!Schema::hasColumn('quizzes', 'show_correct_in_review')) {
                $table->boolean('show_correct_in_review')->default(true)->after('allow_review_mode');
            }
            if (!Schema::hasColumn('quizzes', 'show_explanation_in_review')) {
                $table->boolean('show_explanation_in_review')->default(true)->after('show_correct_in_review');
            }
            if (!Schema::hasColumn('quizzes', 'review_available_after')) {
                $table->integer('review_available_after')->nullable()->after('show_explanation_in_review');
            }
            if (!Schema::hasColumn('quizzes', 'estimated_duration')) {
                $table->integer('estimated_duration')->nullable()->after('time_limit');
            }
        });
        
        // Add indexes (will fail silently if they already exist, which is fine)
        try {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->index('scheduled_publish_at');
            });
        } catch (\Exception $e) {
            // Index may already exist, ignore
        }
        try {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->index('scheduled_unpublish_at');
            });
        } catch (\Exception $e) {
            // Index may already exist, ignore
        }
        try {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->index('auto_publish');
            });
        } catch (\Exception $e) {
            // Index may already exist, ignore
        }
        try {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->index('difficulty_level');
            });
        } catch (\Exception $e) {
            // Index may already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes (will fail silently if they don't exist, which is fine)
        try {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropIndex(['scheduled_publish_at']);
            });
        } catch (\Exception $e) {
            // Index may not exist, ignore
        }
        try {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropIndex(['scheduled_unpublish_at']);
            });
        } catch (\Exception $e) {
            // Index may not exist, ignore
        }
        try {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropIndex(['auto_publish']);
            });
        } catch (\Exception $e) {
            // Index may not exist, ignore
        }
        try {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropIndex(['difficulty_level']);
            });
        } catch (\Exception $e) {
            // Index may not exist, ignore
        }
        
        Schema::table('quizzes', function (Blueprint $table) {
            
            // Drop columns only if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('quizzes', 'scheduled_publish_at')) {
                $columnsToDrop[] = 'scheduled_publish_at';
            }
            if (Schema::hasColumn('quizzes', 'scheduled_unpublish_at')) {
                $columnsToDrop[] = 'scheduled_unpublish_at';
            }
            if (Schema::hasColumn('quizzes', 'auto_publish')) {
                $columnsToDrop[] = 'auto_publish';
            }
            if (Schema::hasColumn('quizzes', 'difficulty_level')) {
                $columnsToDrop[] = 'difficulty_level';
            }
            if (Schema::hasColumn('quizzes', 'estimated_duration')) {
                $columnsToDrop[] = 'estimated_duration';
            }
            if (Schema::hasColumn('quizzes', 'publish_at')) {
                $columnsToDrop[] = 'publish_at';
            }
            if (Schema::hasColumn('quizzes', 'allow_review_mode')) {
                $columnsToDrop[] = 'allow_review_mode';
            }
            if (Schema::hasColumn('quizzes', 'allow_practice_mode')) {
                $columnsToDrop[] = 'allow_practice_mode';
            }
            if (Schema::hasColumn('quizzes', 'show_correct_in_review')) {
                $columnsToDrop[] = 'show_correct_in_review';
            }
            if (Schema::hasColumn('quizzes', 'show_explanation_in_review')) {
                $columnsToDrop[] = 'show_explanation_in_review';
            }
            if (Schema::hasColumn('quizzes', 'review_available_after')) {
                $columnsToDrop[] = 'review_available_after';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
