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
            $table->timestamp('publish_at')->nullable()->after('published_at');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->timestamp('publish_at')->nullable()->after('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('publish_at');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('publish_at');
        });
    }
};
