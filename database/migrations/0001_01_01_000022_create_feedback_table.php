<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('feedbackable_type')->nullable();
            $table->unsignedBigInteger('feedbackable_id')->nullable();
            $table->integer('rating')->nullable();
            $table->text('comment');
            $table->enum('status', ['pending', 'reviewed', 'resolved'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['feedbackable_type', 'feedbackable_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};