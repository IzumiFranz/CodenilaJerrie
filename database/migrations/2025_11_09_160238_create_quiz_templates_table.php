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
        Schema::create('quiz_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('time_limit');
            $table->integer('passing_score');
            $table->integer('max_attempts');
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_choices')->default(false);
            $table->boolean('show_results')->default(true);
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('allow_review_mode')->default(false);
            $table->boolean('allow_practice_mode')->default(false);
            $table->boolean('is_shared')->default(false); // Share with other instructors
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_templates');
    }
};
