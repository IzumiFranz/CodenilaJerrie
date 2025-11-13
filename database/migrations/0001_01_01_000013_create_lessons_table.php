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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            
            // Basic Information
            $table->string('title');
            $table->text('description')->nullable(); // Short description for previews
            $table->longText('content')->nullable();
            $table->integer('order')->default(0);
            
            // Legacy File Upload (kept for backward compatibility)
            // NOTE: New system uses lesson_attachments table
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            
            // Publishing Status
            // Keep both is_published (legacy) and status (new) for compatibility
            $table->boolean('is_published')->default(false); // LEGACY - kept for backward compatibility
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft'); // NEW SYSTEM
            $table->timestamp('published_at')->nullable();
            
            // Scheduling Features
            $table->timestamp('scheduled_publish_at')->nullable();
            $table->timestamp('scheduled_unpublish_at')->nullable();
            $table->boolean('auto_publish')->default(false);
            
            // Auto-calculated Fields
            $table->integer('word_count')->default(0);
            $table->integer('read_time_minutes')->default(0);
            
            // Analytics (kept for backward compatibility, but use lesson_views table for detailed tracking)
            $table->integer('view_count')->default(0); // LEGACY
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for Performance
            $table->index('instructor_id');
            $table->index('subject_id');
            $table->index('is_published'); // Legacy
            $table->index('status'); // New system
            $table->index('scheduled_publish_at');
            $table->index('scheduled_unpublish_at');
            $table->index('auto_publish');
            $table->index(['subject_id', 'order']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};