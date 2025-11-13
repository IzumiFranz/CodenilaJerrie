@extends('layouts.student')

@section('title', 'Quiz Details')

@section('content')
<div class="container-fluid mt-4">

    <!-- QUIZ HEADER -->
    <div class="card shadow-sm border-left-primary mb-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-start justify-content-between">
            <div>
                <h3 class="fw-bold text-primary mb-1">{{ $quiz->title }}</h3>
                <p class="text-muted mb-2">{{ $quiz->description }}</p>
                <div class="d-flex flex-wrap gap-3 small text-muted">
                    <span><i class="fas fa-book me-1"></i> Subject: {{ $quiz->subject->subject_name ?? 'N/A' }}</span>
                    <span><i class="fas fa-user-tie me-1"></i> Instructor: {{ $quiz->instructor->full_name ?? 'N/A' }}</span>
                    <span><i class="fas fa-question-circle me-1"></i> {{ $quiz->questions->count() }} Questions</span>
                    <span><i class="fas fa-clock me-1"></i> {{ $quiz->time_limit ?? $quiz->estimated_duration ?? 'N/A' }} mins</span>
                    <span><i class="fas fa-star me-1"></i> Passing Score: {{ $quiz->passing_score }}%</span>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                @php
                    $inProgressAttempt = $quiz->attempts->where('student_id', auth()->user()->student->id)->where('status', 'in_progress')->first();
                @endphp
                @if($inProgressAttempt)
                    <a href="{{ route('student.quiz-attempts.take', $inProgressAttempt->id) }}" class="btn btn-warning shadow-sm">
                        <i class="fas fa-sync-alt me-1"></i> Resume Quiz
                    </a>
                @elseif($quiz->studentCanTakeQuiz(auth()->user()->student))
                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#startQuizModal">
                        <i class="fas fa-play-circle me-1"></i> Start Quiz
                    </button>
                @else
                    <button class="btn btn-secondary" disabled>
                        <i class="fas fa-ban me-1"></i> Quiz Not Available
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- QUIZ INFORMATION -->
    <div class="row">
        <div class="col-lg-8">

            <!-- INSTRUCTIONS -->
            <div class="card shadow-sm border-left-info mb-4">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-info-circle me-2"></i> Instructions
                </div>
                <div class="card-body">
                    {!! $quiz->instructions !!}
                    <hr>
                    <ul class="mb-0 small">
                        <li>Ensure a stable internet connection before starting.</li>
                        <li>Once started, the timer cannot be paused.</li>
                        <li>Leaving the page may result in auto-submission.</li>
                        <li>You can review results after submission (if enabled).</li>
                    </ul>
                </div>
            </div>

            <!-- ATTEMPT HISTORY -->
            <div class="card shadow-sm border-left-success mb-4">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="fas fa-history me-2"></i> Your Previous Attempts
                </div>
                <div class="card-body">
                    @if($attempts->isEmpty())
                        <p class="text-muted mb-0">You haven't taken this quiz yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attempts as $index => $attempt)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $attempt->created_at->format('M d, Y - h:i A') }}</td>
                                            <td>{{ $attempt->score }}%</td>
                                            <td>
                                                @if($attempt->isPassed())
                                                    <span class="badge bg-success">Passed</span>
                                                @else
                                                    <span class="badge bg-danger">Failed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('student.quiz-attempts.results', $attempt) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i> View Result
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- RELATED LESSON -->
            @if($quiz->lesson)
            <div class="card shadow-sm border-left-secondary mb-4">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="fas fa-link me-2"></i> Related Lesson
                </div>
                <div class="card-body">
                    <p class="mb-2">This quiz is part of: <strong>{{ $quiz->lesson->title }}</strong></p>
                    <a href="{{ route('student.lessons.show', $quiz->lesson_id) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-book-open me-1"></i> View Lesson
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-chart-bar me-2"></i> Your Quiz Stats
                </div>
                <div class="card-body small">
                    <p><strong>Average Score:</strong> {{ $averageScore }}%</p>
                    <p><strong>Best Score:</strong> {{ $bestScore }}%</p>
                    <p><strong>Attempts:</strong> {{ $attempts->count() }}</p>
                    <p><strong>Last Attempt:</strong> {{ optional($attempts->last())->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- TIPS -->
            <div class="card shadow-sm border-left-warning mb-4">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-lightbulb me-2"></i> Tips
                </div>
                <div class="card-body small">
                    <ul class="mb-0">
                        <li>Read all questions carefully.</li>
                        <li>Manage your time wisely.</li>
                        <li>Don’t leave the quiz until finished.</li>
                        <li>Check your internet connection.</li>
                    </ul>
                </div>
            </div>

            <!-- SHARE / FEEDBACK -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between">
                    <button class="btn btn-outline-info btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                        <i class="fas fa-comment-dots me-1"></i> Feedback
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- START QUIZ MODAL -->
<div class="modal fade" id="startQuizModal" tabindex="-1" aria-labelledby="startQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="startQuizModalLabel"><i class="fas fa-play-circle me-2"></i> Start Quiz</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to start <strong>{{ $quiz->title }}</strong>?</p>
                <p class="small text-muted">The timer will begin once you start.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ route('student.quiz-attempts.start', $quiz) }}" class="btn btn-primary">
                    Start Now
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FEEDBACK MODAL -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="feedbackModalLabel"><i class="fas fa-comment-dots me-2"></i> Quiz Feedback</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('student.feedback.store') }}" method="POST">
                <input type="hidden" name="type" value="quiz">
                <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="feedback_subject">Subject</label>
                        <input type="text" name="subject" class="form-control" id="feedback_subject" placeholder="Feedback subject" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="feedback_rating">Rating (Optional)</label>
                        <select name="rating" class="form-control" id="feedback_rating">
                            <option value="">No rating</option>
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="feedback_message">Message</label>
                        <textarea name="message" class="form-control" id="feedback_message" rows="4" placeholder="Write your feedback here..." required minlength="20"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PRINT STYLES -->
<style>
@media print {
    body * { visibility: hidden; }
    .container-fluid, .container-fluid * { visibility: visible; }
    .container-fluid { position: absolute; left: 0; top: 0; width: 100%; }
    button, a, .modal { display: none !important; }
}
</style>
@endsection
