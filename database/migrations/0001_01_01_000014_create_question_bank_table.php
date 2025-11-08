<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->text('question_text');
            $table->enum('type', ['multiple_choice', 'true_false', 'identification', 'essay'])->default('multiple_choice');
            $table->decimal('points', 5, 2)->default(1.00);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('bloom_level')->nullable();
            $table->text('explanation')->nullable();
            $table->json('tags')->nullable();
            $table->integer('usage_count')->default(0);
            $table->decimal('difficulty_index', 3, 2)->nullable();
            $table->decimal('discrimination_index', 3, 2)->nullable();
            $table->boolean('is_validated')->default(false);
            $table->integer('quality_score')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('instructor_id');
            $table->index('subject_id');
            $table->index('type');
            $table->index('difficulty');
            $table->index('is_validated');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank');
    }
};