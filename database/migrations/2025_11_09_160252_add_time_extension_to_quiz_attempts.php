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
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_attempts', 'time_extension')) {
                $table->integer('time_extension')->default(0)->after('completed_at'); // minutes
            }
            if (!Schema::hasColumn('quiz_attempts', 'instructor_comment')) {
                $table->text('instructor_comment')->nullable()->after('time_extension');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_attempts', 'time_extension')) {
                $table->dropColumn('time_extension');
            }
            if (Schema::hasColumn('quiz_attempts', 'instructor_comment')) {
                $table->dropColumn('instructor_comment');
            }
        });
    }
};
