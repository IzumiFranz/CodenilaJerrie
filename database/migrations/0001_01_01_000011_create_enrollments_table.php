<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('academic_year');
            $table->enum('semester', ['1st', '2nd', 'summer'])->default('1st');
            $table->enum('status', ['enrolled', 'dropped', 'completed'])->default('enrolled');
            $table->date('enrollment_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index('student_id');
            $table->index('section_id');
            $table->index(['academic_year', 'semester']);
            $table->unique(['student_id', 'section_id', 'academic_year', 'semester', 'deleted_at'], 'unique_enrollment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};