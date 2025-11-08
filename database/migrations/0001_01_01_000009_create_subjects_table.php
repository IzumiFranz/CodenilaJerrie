<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->onDelete('set null');
            $table->string('subject_code')->unique();
            $table->string('subject_name');
            $table->text('description')->nullable();
            $table->integer('year_level')->default(1);
            $table->integer('units')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('subject_code');
            $table->index('course_id');
            $table->index('specialization_id');
            $table->index(['course_id', 'year_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};