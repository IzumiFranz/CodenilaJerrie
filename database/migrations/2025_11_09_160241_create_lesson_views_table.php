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
        Schema::create('lesson_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamp('viewed_at');
            $table->integer('duration_seconds')->nullable(); // Time spent viewing
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index('lesson_id');
            $table->index('student_id');
            $table->index('viewed_at');
            $table->unique(['lesson_id', 'student_id', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_views');
    }
};
