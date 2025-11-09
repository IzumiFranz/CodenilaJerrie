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
        Schema::create('question_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_bank')->onDelete('cascade');
            $table->foreignId('revised_by')->constrained('users');
            $table->text('question_text');
            $table->json('choices')->nullable(); // Store choices as JSON
            $table->string('type');
            $table->integer('points');
            $table->string('difficulty');
            $table->string('blooms_level')->nullable();
            $table->text('explanation')->nullable();
            $table->text('revision_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_revisions');
    }
};
