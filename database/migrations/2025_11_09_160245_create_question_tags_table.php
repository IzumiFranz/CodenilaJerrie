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
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#007bff'); // Hex color code
            $table->text('description')->nullable();
            $table->integer('question_count')->default(0);
            $table->timestamps();
            
            $table->index('instructor_id');
            $table->index('subject_id');
            $table->index('slug');
        });
    
        Schema::create('question_tag_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_bank')->onDelete('cascade');
            $table->foreignId('question_tag_id')->constrained('question_tags')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['question_bank_id', 'question_tag_id']);
            $table->index('question_bank_id');
            $table->index('question_tag_id');
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
