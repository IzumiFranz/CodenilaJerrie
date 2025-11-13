<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('time_limit')->nullable(); // in minutes
            $table->decimal('passing_score', 5, 2)->default(60.00);
            $table->integer('max_attempts')->default(1);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_choices')->default(false);
            $table->boolean('show_results')->default(true);
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft'); // NEW SYSTEM
            $table->boolean('show_answers')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('instructor_id');
            $table->index('subject_id');
            $table->index('is_published');
            $table->index(['available_from', 'available_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};