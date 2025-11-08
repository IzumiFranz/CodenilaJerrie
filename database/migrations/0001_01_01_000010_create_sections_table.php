<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('section_name');
            $table->integer('year_level')->default(1);
            $table->integer('max_students')->default(40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('course_id');
            $table->index('year_level');
            $table->unique(['course_id', 'year_level', 'section_name', 'deleted_at'], 'unique_section');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};