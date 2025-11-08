<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_subject_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('academic_year');
            $table->enum('semester', ['1st', '2nd', 'summer'])->default('1st');
            $table->timestamps();
            $table->softDeletes();

            $table->index('instructor_id');
            $table->index('subject_id');
            $table->index('section_id');
            $table->index(['academic_year', 'semester']);
            $table->unique(['instructor_id', 'subject_id', 'section_id', 'academic_year', 'semester', 'deleted_at'], 'unique_assignment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_subject_section');
    }
};