<?php

use App\Http\Controllers\Api\StudentApiController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/student/dashboard', [StudentApiController::class, 'dashboard']);
    Route::get('/student/quizzes', [StudentApiController::class, 'quizzes']);
    Route::get('/student/lessons', [StudentApiController::class, 'lessons']);
    Route::post('/student/quizzes/{quiz}/attempt', [StudentApiController::class, 'attemptQuiz']);
});
