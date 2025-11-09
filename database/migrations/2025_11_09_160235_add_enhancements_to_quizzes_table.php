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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['publish_at', 'allow_review_mode', 'allow_practice_mode', 'difficulty_level']);
        });
    }
};
