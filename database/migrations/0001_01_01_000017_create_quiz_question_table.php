<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->foreignId('question_bank_id')->constrained('question_bank')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('quiz_id');
            $table->index('question_bank_id');
            $table->unique(['quiz_id', 'question_bank_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_question');
    }
};
