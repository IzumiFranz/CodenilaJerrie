<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->onDelete('set null');
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('department')->nullable();
            $table->string('phone')->nullable();
            $table->date('hire_date')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('employee_id');
            $table->index('specialization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};