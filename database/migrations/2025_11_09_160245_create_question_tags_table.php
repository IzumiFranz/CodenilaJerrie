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
        Schema::create('question_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#007bff');
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->onDelete('cascade');
            $table->timestamps();
        });
    
        Schema::create('question_bank_tag', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->constrained('question_bank')->onDelete('cascade');
            $table->foreignId('question_tag_id')->constrained('question_tags')->onDelete('cascade');
            $table->primary(['question_bank_id', 'question_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_tags', 'question_bank_tag');
    }
};
