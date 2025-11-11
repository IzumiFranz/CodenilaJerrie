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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['quiz', 'lesson', 'instructor', 'general']);
            $table->string('subject');
            $table->text('message');
            $table->tinyInteger('rating')->nullable();
            $table->enum('status', ['pending', 'responded'])->default('pending');
            $table->boolean('is_anonymous')->default(false);
            
            // Polymorphic relationship
            $table->string('feedbackable_type')->nullable();
            $table->unsignedBigInteger('feedbackable_id')->nullable();
            
            // Response fields
            $table->text('response')->nullable();
            $table->foreignId('response_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('responded_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['feedbackable_type', 'feedbackable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};