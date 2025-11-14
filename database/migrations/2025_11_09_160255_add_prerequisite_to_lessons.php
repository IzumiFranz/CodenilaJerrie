<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('lessons', 'prerequisite_lesson_id')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->foreignId('prerequisite_lesson_id')->nullable()->after('order')
                    ->constrained('lessons')->onDelete('set null');
                $table->index('prerequisite_lesson_id');
            });
        } else {
            // Column exists, just ensure index exists
            try {
                Schema::table('lessons', function (Blueprint $table) {
                    $table->index('prerequisite_lesson_id');
                });
            } catch (\Exception $e) {
                // Index may already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('lessons', 'prerequisite_lesson_id')) {
            Schema::table('lessons', function (Blueprint $table) {
                // Try to drop foreign key (may not exist)
                try {
                    $table->dropForeign(['prerequisite_lesson_id']);
                } catch (\Exception $e) {
                    // Foreign key may not exist, try alternative name
                    try {
                        DB::statement('ALTER TABLE lessons DROP CONSTRAINT IF EXISTS lessons_prerequisite_lesson_id_foreign');
                    } catch (\Exception $e2) {
                        // Ignore
                    }
                }
                $table->dropColumn('prerequisite_lesson_id');
            });
        }
    }
};
