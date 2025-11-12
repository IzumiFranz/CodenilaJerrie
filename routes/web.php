<?php

// ============================================
// FILE: routes/web.php
// ============================================

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\SectionController as AdminSectionController;
use App\Http\Controllers\Admin\SpecializationController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\BulkActionsController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Instructor\AIController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Instructor\LessonController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Instructor\QuistionTagController;
use App\Http\Controllers\Instructor\QuestionBankController;
use App\Http\Controllers\Instructor\StudentProgressController;
use App\Http\Controllers\Instructor\QuizTemplateController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\QuizAttemptController;
use App\Http\Controllers\Student\FeedbackController;
use App\Http\Controllers\Student\SettingController;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Student\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'instructor' => redirect()->route('instructor.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return view('welcome');
})->name('home');

// Auth routes (Laravel Breeze)
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes (All Roles)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'password.change'])->group(function () {
    
    // Profile routes (accessible by all authenticated users)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('upload-avatar');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('delete-avatar');
    });

    // Password change route (exempt from password.change middleware)
    Route::get('/change-password', [ProfileController::class, 'changePassword'])
        ->withoutMiddleware('password.change')
        ->name('profile.change-password');
    
    Route::post('/change-password', [ProfileController::class, 'updatePassword'])
        ->withoutMiddleware('password.change')
        ->name('profile.update-password');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin', 'password.change'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/bulk-upload', [UserController::class, 'bulkUpload'])->name('users.bulk-upload');
    Route::get('users/export/template', [UserController::class, 'downloadTemplate'])->name('users.download-template');
    Route::get('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::get('trashed-users', [UserController::class, 'trashed'])->name('users.trashed');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/suspend', [UserController::class, 'suspend'])
    ->name('users.suspend');
    
    // Specialization Management
    Route::resource('specializations', SpecializationController::class);
    Route::get('specializations/{specialization}/restore', [SpecializationController::class, 'restore'])
        ->name('specializations.restore');
    Route::get('trashed-specializations', [SpecializationController::class, 'trashed'])
        ->name('specializations.trashed');
    
    // Course Management
    Route::resource('courses', AdminCourseController::class);
    Route::get('courses/{course}/restore', [AdminCourseController::class, 'restore'])->name('courses.restore');
    Route::get('trashed-courses', [AdminCourseController::class, 'trashed'])->name('courses.trashed');
    Route::post('courses/{course}/toggle-status', [AdminCourseController::class, 'toggleStatus'])
        ->name('courses.toggle-status');
    
    // Subject Management
    Route::resource('subjects', AdminSubjectController::class);
    Route::get('subjects/create/{course?}', [AdminSubjectController::class, 'create'])->name('subjects.create');
    Route::get('subjects/{subject}/restore', [AdminSubjectController::class, 'restore'])->name('subjects.restore');
    Route::get('trashed-subjects', [AdminSubjectController::class, 'trashed'])->name('subjects.trashed');
    Route::get('subjects/{subject}/qualified-instructors', [AdminSubjectController::class, 'getQualifiedInstructors'])
        ->name('subjects.qualified-instructors');
    
    // Section Management
    Route::resource('sections', AdminSectionController::class);
    Route::get('sections/{section}/restore', [AdminSectionController::class, 'restore'])->name('sections.restore');
    Route::get('trashed-sections', [AdminSectionController::class, 'trashed'])->name('sections.trashed');
    
    // Enrollment Management
    Route::resource('enrollments', EnrollmentController::class);
    Route::post('enrollments/bulk-enroll', [EnrollmentController::class, 'bulkEnroll'])
        ->name('enrollments.bulk-enroll');
    Route::get('enrollments/{enrollment}/restore', [EnrollmentController::class, 'restore'])
        ->name('enrollments.restore');
    Route::get('trashed-enrollments', [EnrollmentController::class, 'trashed'])->name('enrollments.trashed');
    Route::post('enrollments/{enrollment}/drop', [EnrollmentController::class, 'drop'])
        ->name('enrollments.drop');
    
    // Instructor-Subject-Section Assignments
    Route::resource('assignments', AssignmentController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('assignments/{assignment}/restore', [AssignmentController::class, 'restore'])
        ->name('assignments.restore');
    Route::get('trashed-assignments', [AssignmentController::class, 'trashed'])->name('assignments.trashed');
    
    // Lesson Management (View/Delete only)
    Route::get('lessons', [AdminLessonController::class, 'index'])->name('lessons.index');
    Route::get('lessons/{lesson}', [AdminLessonController::class, 'show'])->name('lessons.show');
    Route::delete('lessons/{lesson}', [AdminLessonController::class, 'destroy'])->name('lessons.destroy');
    
    // Quiz Management (View/Delete only)
    Route::get('quizzes', [AdminQuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/{quiz}', [AdminQuizController::class, 'show'])->name('quizzes.show');
    Route::delete('quizzes/{quiz}', [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::get('quizzes/{quiz}/results', [AdminQuizController::class, 'results'])->name('quizzes.results');
    
    // Feedback Management
    Route::get('feedback', [AdminFeedbackController::class, 'index'])->name('feedback.index');
    Route::get('feedback/{feedback}', [AdminFeedbackController::class, 'show'])->name('feedback.show');
    Route::post('feedback/{feedback}/respond', [AdminFeedbackController::class, 'respond'])
        ->name('feedback.respond');
    Route::patch('feedback/{feedback}/status', [AdminFeedbackController::class, 'updateStatus'])
        ->name('feedback.update-status');
    
    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::delete('audit-logs/clear', [AuditLogController::class, 'clear'])->name('audit-logs.clear');
    
    // Notifications
    Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/create', [AdminNotificationController::class, 'create'])
        ->name('notifications.create');
    Route::post('notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
    Route::post('notifications/send-bulk', [AdminNotificationController::class, 'sendBulk'])
        ->name('notifications.send-bulk');

    Route::prefix('export')->name('export.')->group(function () {
            // Export Users
        Route::get('/users', [ExportController::class, 'exportUsers'])->name('users');
            
            // Export Courses
        Route::get('/courses', [ExportController::class, 'exportCourses'])->name('courses');
            
            // Export Subjects
        Route::get('/subjects', [ExportController::class, 'exportSubjects'])->name('subjects');
            
            // Export Enrollments
        Route::get('/enrollments', [ExportController::class, 'exportEnrollments'])->name('enrollments');
            
            // Export Quiz Results
        Route::get('/quiz-results/{quiz}', [ExportController::class, 'exportQuizResults'])->name('quiz-results');
            
            // Export Analytics Report
        Route::get('/analytics-report', [ExportController::class, 'generateAnalyticsReport'])->name('analytics-report');
            
            // Generate PDF Reports
        Route::get('/users-pdf', [ExportController::class, 'generateUsersPDF'])->name('users-pdf');
        });
        // ============================================
    // BULK ACTIONS ROUTES
    // ============================================
    Route::prefix('bulk-actions')->name('bulk-actions.')->group(function () {
        // User Bulk Actions
        Route::post('/update-user-status', [BulkActionsController::class, 'bulkUpdateUserStatus'])->name('update-user-status');
        Route::delete('/delete-users', [BulkActionsController::class, 'bulkDeleteUsers'])->name('delete-users');
        Route::post('/restore-users', [BulkActionsController::class, 'bulkRestoreUsers'])->name('restore-users');
        Route::post('/assign-role', [BulkActionsController::class, 'bulkAssignRole'])->name('assign-role');
        // Notification Bulk Actions
        Route::post('/send-notifications', [BulkActionsController::class, 'bulkSendNotifications'])->name('send-notifications');      
        // Enrollment Bulk Actions
        Route::post('/drop-enrollments', [BulkActionsController::class, 'bulkDropEnrollments'])->name('drop-enrollments');
        // Generic Bulk Actions
        Route::delete('/delete', [BulkActionsController::class, 'bulkDelete'])->name('delete');
        Route::post('/update-status', [BulkActionsController::class, 'bulkUpdateStatus'])->name('update-status');
        Route::get('/export', [BulkActionsController::class, 'bulkExport'])->name('export');
    });

    // ============================================
    // ANALYTICS ROUTES
    // ============================================
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/performance', [App\Http\Controllers\Admin\AnalyticsController::class, 'performance'])->name('performance');
        Route::get('/trends', [App\Http\Controllers\Admin\AnalyticsController::class, 'trends'])->name('trends');
    });

    // ============================================
    // PRINT ROUTES
    // ============================================
    Route::prefix('print')->name('print.')->group(function () {
        Route::get('/users', [App\Http\Controllers\Admin\PrintController::class, 'users'])->name('users');
        Route::get('/enrollments/{section}', [App\Http\Controllers\Admin\PrintController::class, 'enrollments'])->name('enrollments');
        Route::get('/quiz-results/{quiz}', [App\Http\Controllers\Admin\PrintController::class, 'quizResults'])->name('quiz-results');
        Route::get('/attendance-sheet/{section}', [App\Http\Controllers\Admin\PrintController::class, 'attendanceSheet'])->name('attendance-sheet');
        Route::get('/grade-sheet/{section}', [App\Http\Controllers\Admin\PrintController::class, 'gradeSheet'])->name('grade-sheet');
    });
});




/*
|--------------------------------------------------------------------------
| Instructor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:instructor', 'password.change'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');

    // AI Job Management
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/', [AIController::class, 'index'])->name('index');
        Route::get('/{job}', [AIController::class, 'show'])->name('show');
        Route::get('/{job}/status', [AIController::class, 'checkStatus'])->name('status');
        
        // Question Generation
        Route::post('/generate-questions', [AIController::class, 'generateQuestions'])
            ->name('generate-questions');            
            // Question Validation
        Route::post('/validate-question/{question}', [AIController::class, 'validateQuestion'])
            ->name('validate-question');

        // Quiz Analysis
        Route::post('/analyze-quiz/{quiz}', [AIController::class, 'analyzeQuiz'])
            ->name('analyze-quiz');
   });
        
    // Question Statistics (add to existing question-bank routes)
    Route::get('question-bank/{question}/statistics', [QuestionBankController::class, 'statistics'])
        ->name('question-bank.statistics');/
});
    // AI Dashboard & History
    Route::get('/ai', [App\Http\Controllers\Instructor\AIController::class, 'index'])->name('ai.index');
    Route::get('/ai/{job}', [App\Http\Controllers\Instructor\AIController::class, 'show'])->name('ai.show');
    Route::get('/ai/statistics', [App\Http\Controllers\Instructor\AIController::class, 'statistics'])->name('ai.statistics');
    
    // AI Actions
    Route::post('/ai/generate-questions', [App\Http\Controllers\Instructor\AIController::class, 'generateQuestions'])->name('ai.generate-questions');
    Route::post('/ai/validate-question/{question}', [App\Http\Controllers\Instructor\AIController::class, 'validateQuestion'])->name('ai.validate-question');
    Route::post('/ai/analyze-quiz/{quiz}', [App\Http\Controllers\Instructor\AIController::class, 'analyzeQuiz'])->name('ai.analyze-quiz');
    
    // Job Management
    Route::get('/ai/job/{job}/status', [App\Http\Controllers\Instructor\AIController::class, 'checkStatus'])->name('ai.check-status');
    Route::post('/ai/job/{job}/cancel', [App\Http\Controllers\Instructor\AIController::class, 'cancel'])->name('ai.cancel');
    Route::post('/ai/job/{job}/retry', [App\Http\Controllers\Instructor\AIController::class, 'retry'])->name('ai.retry');
    
    // Helper endpoints
    Route::get('/subjects/{subject}/lessons', [App\Http\Controllers\Instructor\AIController::class, 'getLessonsBySubject'])->name('subjects.lessons');

    // Lesson Management
    Route::resource('lessons', LessonController::class);
    Route::post('lessons/{lesson}/toggle-publish', [LessonController::class, 'togglePublish'])
        ->name('lessons.toggle-publish');
    Route::post('lessons/{lesson}/duplicate', [LessonController::class, 'duplicate'])
        ->name('lessons.duplicate');
    Route::get('lessons/{lesson}/download', [LessonController::class, 'download'])
        ->name('lessons.download');
    Route::get('/lessons/{lesson}/view-statistics', [LessonController::class, 'viewStatistics'])
        ->name('lessons.view-statistics');
    Route::post('/lessons/{lesson}/schedule', [LessonController::class, 'schedule'])
        ->name('lessons.schedule');
    Route::delete('/lessons/{lesson}/schedule', [LessonController::class, 'cancelSchedule'])
        ->name('lessons.cancel-schedule');
    

    Route::prefix('lessons/{lesson}')->name('lessons.')->group(function () {
        Route::get('attachments', [LessonController::class, 'attachments'])
            ->name('attachments');
        Route::post('attachments/upload', [LessonController::class, 'uploadAttachments'])
            ->name('attachments.upload');
        Route::delete('attachments/{attachment}', [LessonController::class, 'deleteAttachment'])
            ->name('attachments.delete');
        Route::post('attachments/{attachment}/toggle-visibility', [LessonController::class, 'toggleAttachmentVisibility'])
            ->name('attachments.toggle-visibility');
        Route::post('attachments/{attachment}/description', [LessonController::class, 'updateAttachmentDescription'])
            ->name('attachments.update-description');
        Route::post('attachments/reorder', [LessonController::class, 'reorderAttachments'])
            ->name('attachments.reorder');
        Route::get('attachments/{attachment}/download', [LessonController::class, 'downloadAttachment'])
            ->name('attachments.download');
    });


    // Question Bank Management
    Route::resource('question-bank', QuestionBankController::class);
    Route::get('/question-bank/{questionBank}/edit', [QuestionBankController::class, 'edit'])
        ->name('question-bank.edit');
    Route::get('/question-bank/{questionBank}', [QuestionBankController::class, 'show'])
        ->name('question-bank.show');
    Route::get('/question-bank/{questionBank}', [QuestionBankController::class, 'create'])
        ->name('question-bank.create');
    Route::get('/question-bank/{questionBank}', [QuestionBankController::class, 'index'])
        ->name('question-bank.index');
    Route::post('question-bank/{question}/duplicate', [QuestionBankController::class, 'duplicate'])
        ->name('question-bank.duplicate');
    Route::post('question-bank/{question}/validate', [QuestionBankController::class, 'validateQuestion'])
        ->name('question-bank.validate');
    Route::post('question-bank/generate', [QuestionBankController::class, 'generateWithAI'])
        ->name('question-bank.generate');
    Route::get('question-bank/{question}/analytics', [QuestionBankController::class, 'analytics'])
        ->name('question-bank.analytics');
    Route::get('/question-bank/{questionBank}/preview', [QuestionBankController::class, 'preview'])
        ->name('question-bank.preview');
    Route::get('/question-bank/import', [QuestionBankController::class, 'importForm'])
        ->name('question-bank.import');
    Route::post('/question-bank/import', [QuestionBankController::class, 'import'])
        ->name('question-bank.import.process');
    Route::get('/question-bank/import/template', [QuestionBankController::class, 'downloadTemplate'])
        ->name('question-bank.import.template');
    
    // Quiz Management
    Route::resource('quiz-templates', QuizTemplateController::class)
    ->only(['index', 'create', 'store', 'destroy']);
    Route::resource('quizzes', QuizController::class);
    Route::post('quizzes/{quiz}/toggle-publish', [QuizController::class, 'togglePublish'])
        ->name('quizzes.toggle-publish');
    Route::get('quizzes/{quiz}/questions', [QuizController::class, 'manageQuestions'])
        ->name('quizzes.questions');
    Route::post('quizzes/{quiz}/questions', [QuizController::class, 'addQuestion'])
        ->name('quizzes.add-question');
    Route::delete('quizzes/{quiz}/questions/{question}', [QuizController::class, 'removeQuestion'])
        ->name('quizzes.remove-question');
    Route::post('quizzes/{quiz}/questions/reorder', [QuizController::class, 'reorderQuestions'])
        ->name('quizzes.reorder-questions');
    Route::get('quizzes/{quiz}/results', [QuizController::class, 'results'])->name('quizzes.results');
    Route::get('quizzes/{quiz}/analytics', [QuizController::class, 'analytics'])
        ->name('quizzes.analytics');
    Route::post('quizzes/{quiz}/duplicate', [QuizController::class, 'duplicate'])
        ->name('quizzes.duplicate');
    Route::post('/quizzes/{quiz}/questions/bulk-add', [QuizController::class, 'bulkAddQuestions'])
        ->name('quizzes.questions.bulk-add');
    Route::post('/quizzes/{quiz}/questions/bulk-remove', [QuizController::class, 'bulkRemoveQuestions'])
        ->name('quizzes.questions.bulk-remove');
    Route::post('/quizzes/{quiz}/schedule', [QuizController::class, 'schedule'])
        ->name('quizzes.schedule');
    Route::delete('/quizzes/{quiz}/schedule', [QuizController::class, 'cancelSchedule'])
        ->name('quizzes.cancel-schedule');
    

    //Question Tags
    // Question Tags
    Route::resource('question-tags', QuestionTagController::class)
        ->names([
    'index' => 'question-tags.index',
    'create' => 'question-tags.create',
    'store' => 'question-tags.store',
    'edit' => 'question-tags.edit',
    'update' => 'question-tags.update',
    'destroy' => 'question-tags.destroy',
    ]);
    Route::get('/question-tags/get-tags', [QuestionTagController::class, 'getTags'])
        ->name('question-tags.get-tags');

    // Student Progress
    Route::get('student-progress', [StudentProgressController::class, 'index'])
        ->name('student-progress.index');
    Route::get('student-progress/{student}', [StudentProgressController::class, 'show'])
        ->name('student-progress.show');
    Route::get('student-progress/export/{section}', [StudentProgressController::class, 'export'])
        ->name('student-progress.export');
    Route::get('/student-progress/alerts', [StudentProgressController::class, 'alerts'])
        ->name('student-progress.alerts');
    Route::post('/student-progress/dismiss-alert', [StudentProgressController::class, 'dismissAlert'])
        ->name('student-progress.dismiss-alert');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student', 'password.change'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // Lessons
    Route::get('lessons', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::get('lessons/{lesson}/download', [LessonController::class, 'download'])
        ->name('lessons.download');
    Route::get('/lessons/{attachment}/download', [LessonController::class, 'download'])
        ->name('lessons.download');
    Route::post('/lessons/{lesson}/track-view', [LessonController::class, 'trackView'])
        ->name('lessons.track-view');
    Route::post('/lessons/{lesson}/update-duration', [LessonController::class, 'updateDuration'])
        ->name('lessons.update-duration');
    Route::post('/lessons/{lesson}/mark-completed', [LessonController::class, 'markCompleted'])
        ->name('lessons.mark-completed');
    
       
        // Lesson Attachments
    Route::prefix('lessons/{lesson}')->name('lessons.')->group(function () {
        Route::get('attachments/{attachment}/download', [LessonController::class, 'downloadAttachment'])
            ->name('attachments.download');
        Route::get('/lessons/{lesson}/attachments/{attachmentId}/download', [LessonController::class, 'download'])
            ->name('lessons.attachments.download');
        Route::get('attachments/{attachment}/view', [LessonController::class, 'viewAttachment'])
            ->name('attachments.view');
        Route::post('attachments/download-all', [LessonController::class, 'downloadAllAttachments'])
            ->name('attachments.download-all');
    });
     
        
    // Quizzes
    Route::get('quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/{quiz}', [StudentQuizController::class, 'show'])->name('quizzes.show');
    
    // Quiz Attempts
    Route::post('quizzes/{quiz}/start', [QuizAttemptController::class, 'start'])
        ->name('quiz-attempts.start');
    Route::get('quiz-attempts/{attempt}/take', [QuizAttemptController::class, 'take'])
        ->name('quiz-attempts.take');
    Route::post('quiz-attempts/{attempt}/save-answer', [QuizAttemptController::class, 'saveAnswer'])
        ->name('quiz-attempts.save-answer');
    Route::post('quiz-attempts/{attempt}/submit', [QuizAttemptController::class, 'submit'])
        ->name('quiz-attempts.submit');
    Route::get('quiz-attempts/{attempt}/results', [QuizAttemptController::class, 'results'])
        ->name('quiz-attempts.results');
    Route::get('quiz-attempts/{attempt}/review', [QuizAttemptController::class, 'review'])
        ->name('quiz-attempts.review');
        Route::get('quiz-attempts/{attempt}/export-pdf', [QuizAttemptController::class, 'exportPdf'])
        ->name('quiz-attempts.export-pdf');
    Route::get('/quizzes/{quiz}/attempts/{attempt}/review', [QuizAttemptController::class, 'review'])
        ->name('quiz-attempts.review');
    Route::get('/quizzes/{quiz}/attempts/{attempt}/print-review', [QuizAttemptController::class, 'printReview'])
        ->name('quiz-attempts.print-review');

    // Feedback
    Route::get('feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');


    // Notifications (NEW!)
    Route::get('notifications', [\App\Http\Controllers\Student\NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::patch('notifications/{notification}/read', [\App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');
    Route::delete('notifications/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
    Route::get('notifications/unread', [\App\Http\Controllers\Student\NotificationController::class, 'unread'])
        ->name('notifications.unread');
    
    // Progress & Grades (NEW!)
    Route::get('progress', [\App\Http\Controllers\Student\ProgressController::class, 'index'])
        ->name('progress.index');

        // Settings (NEW!)
    Route::get('settings', [\App\Http\Controllers\Student\SettingsController::class, 'index'])
        ->name('settings.index');
    Route::post('settings/notifications', [\App\Http\Controllers\Student\SettingsController::class, 'updateNotifications'])
        ->name('settings.update-notifications');
    Route::post('settings/display', [\App\Http\Controllers\Student\SettingsController::class, 'updateDisplay'])
        ->name('settings.update-display');
    Route::post('settings/privacy', [\App\Http\Controllers\Student\SettingsController::class, 'updatePrivacy'])
        ->name('settings.update-privacy');
    Route::post('settings/quiz', [\App\Http\Controllers\Student\SettingsController::class, 'updateQuizPreferences'])
        ->name('settings.update-quiz');
    Route::post('settings/reset', [\App\Http\Controllers\Student\SettingsController::class, 'reset'])
        ->name('settings.reset');
});

/*
|--------------------------------------------------------------------------
| API Routes for AJAX requests
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    
    // Get sections by course
    Route::get('sections/by-course/{course}', function ($courseId) {
        return \App\Models\Section::where('course_id', $courseId)
            ->where('is_active', true)
            ->orderBy('year_level')
            ->orderBy('section_name')
            ->get(['id', 'section_name', 'year_level']);
    })->name('sections.by-course');
    
    // Get subjects by course and year level
    Route::get('subjects/by-course/{course}/{yearLevel?}', function ($courseId, $yearLevel = null) {
        $query = \App\Models\Subject::where('course_id', $courseId)
            ->where('is_active', true);
        
        if ($yearLevel) {
            $query->where('year_level', $yearLevel);
        }
        
        return $query->orderBy('subject_name')->get(['id', 'subject_code', 'subject_name', 'year_level']);
    })->name('subjects.by-course');
    
    // Get qualified instructors for subject
    Route::get('instructors/qualified/{subject}', function ($subjectId) {
        $subject = \App\Models\Subject::findOrFail($subjectId);
        return $subject->getQualifiedInstructors();
    })->name('instructors.qualified');
});