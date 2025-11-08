<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->integer('year_level')->default(1);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('admission_date')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('student_number');
            $table->index('course_id');
            $table->index('year_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
