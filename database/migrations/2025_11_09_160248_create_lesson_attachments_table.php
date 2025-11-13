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
        Schema::create('lesson_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('file_extension', 10);
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('download_count')->default(0);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('lesson_id');
            $table->index('is_visible');
            $table->index('display_order');
        });

        // Track student downloads
        Schema::create('lesson_attachment_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_attachment_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('downloaded_at');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Indexes
            $table->index(['lesson_attachment_id', 'student_id'], 'lesson_attach_student_id');
            $table->index('downloaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_attachment_downloads');
        Schema::dropIfExists('lesson_attachments');
    }
};