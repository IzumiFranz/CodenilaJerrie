<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Instructor\AIController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizAttemptController;
use App\Http\Controllers\Api\FeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes for Mobile App
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return $request->user()->load('profile');
    });
    
    Route::post('/user/update', [AuthController::class, 'updateProfile']);
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);
    
    // Dashboard
    Route::get('/student/dashboard', [StudentApiController::class, 'dashboard']);
    
    // Lessons
    Route::get('/student/lessons', [LessonController::class, 'index']);
    Route::get('/student/lessons/{lesson}', [LessonController::class, 'show']);
    Route::get('/student/lessons/{lesson}/download', [LessonController::class, 'download']);
    
    // Quizzes
    Route::get('/student/quizzes', [QuizController::class, 'index']);
    Route::get('/student/quizzes/{quiz}', [QuizController::class, 'show']);
    
    // Quiz Attempts
    Route::post('/student/quizzes/{quiz}/start', [QuizAttemptController::class, 'start']);
    Route::get('/student/attempts/{attempt}', [QuizAttemptController::class, 'show']);
    Route::post('/student/attempts/{attempt}/save-answer', [QuizAttemptController::class, 'saveAnswer']);
    Route::post('/student/attempts/{attempt}/submit', [QuizAttemptController::class, 'submit']);
    Route::get('/student/attempts/{attempt}/results', [QuizAttemptController::class, 'results']);
    Route::get('/student/attempts/{attempt}/review', [QuizAttemptController::class, 'review']);
    
    // Feedback
    Route::get('/student/feedback', [FeedbackController::class, 'index']);
    Route::post('/student/feedback', [FeedbackController::class, 'store']);
    Route::get('/student/feedback/{feedback}', [FeedbackController::class, 'show']);
    
    // Notifications
    Route::get('/student/notifications', [StudentApiController::class, 'notifications']);
    Route::post('/student/notifications/{notification}/read', [StudentApiController::class, 'markNotificationRead']);
});
Route::middleware(['auth:sanctum', 'role:instructor'])->prefix('instructor')->group(function () {
    
    // AI Job Status (for AJAX polling)
    Route::get('/ai/jobs/{job}/status', [App\Http\Controllers\Instructor\AIController::class, 'checkStatus']);
    
    // Get lessons by subject (for AJAX)
    Route::get('/subjects/{subject}/lessons', [App\Http\Controllers\Instructor\AIController::class, 'getLessonsBySubject']);
    
    // AI Statistics (for dashboard widgets)
    Route::get('/ai/statistics', [App\Http\Controllers\Instructor\AIController::class, 'statistics']);
});
