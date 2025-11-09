@extends('layouts.student')

@section('title', $quiz->title)

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.quizzes.index') }}">Quizzes</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($quiz->title, 50) }}</li>
    </ol>
</nav>

<!-- Quiz Header -->
<div class="card shadow mb-4 border-left-primary">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h2 class="mb-3">{{ $quiz->title }}</h2>
                <p class="text-muted mb-2">
                    <i class="fas fa-book mr-1"></i><strong>Subject:</strong> {{ $quiz->subject->subject_name }} ({{ $quiz->subject->subject_code }})
                </p>
                <p class="text-muted mb-2">
                    <i class="fas fa-user mr-1"></i><strong>Instructor:</strong> {{ $quiz->instructor->full_name }}
                </p>
                @if($quiz->description)
                <p class="text-muted mt-3">{{ $quiz->description }}</p>
                @endif
            </div>
            <div class="col-md-4 text-right">
                @if($quiz->isAvailable())
                    <span class="badge badge-success badge-lg p-3">
                        <i class="fas fa-check-circle mr-1"></i>Available Now
                    </span>
                @elseif($quiz->available_from && $quiz->available_from->isFuture())
                    <span class="badge badge-warning badge-lg p-3">
                        <i class="fas fa-clock mr-1"></i>Upcoming
                    </span>
                @else
                    <span class="badge badge-danger badge-lg p-3">
                        <i class="fas fa-times-circle mr-1"></i>Expired
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quiz Information -->
<div class="row">
    <div class="col-lg-8">
        <!-- Instructions -->
        @if($quiz->instructions)
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-info-circle mr-2"></i>Instructions
                </h6>
            </div>
            <div class="card-body">
                <div class="instructions-content">
                    {!! nl2br(e($quiz->instructions)) !!}
                </div>
            </div>
        </div>
        @endif

        <!-- Quiz Details -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list mr-2"></i>Quiz Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-primary text-white mr-3">
                                <i class="fas fa-question"></i>
                            </div>
                            <div>
                                <small class="text-muted">Total Questions</small>
                                <h5 class="mb-0">{{ $quiz->questions_count }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-warning text-white mr-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <small class="text-muted">Time Limit</small>
                                <h5 class="mb-0">{{ $quiz->time_limit ? $quiz->time_limit . ' mins' : 'Unlimited' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-success text-white mr-3">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div>
                                <small class="text-muted">Passing Score</small>
                                <h5 class="mb-0">{{ $quiz->passing_score }}%</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-info text-white mr-3">
                                <i class="fas fa-redo"></i>
                            </div>
                            <div>
                                <small class="text-muted">Max Attempts</small>
                                <h5 class="mb-0">{{ $quiz->max_attempts }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <i class="fas fa-{{ $quiz->randomize_questions ? 'check text-success' : 'times text-muted' }} mr-2"></i>
                            Questions will be {{ $quiz->randomize_questions ? '' : 'NOT ' }}randomized
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-{{ $quiz->randomize_choices ? 'check text-success' : 'times text-muted' }} mr-2"></i>
                            Choices will be {{ $quiz->randomize_choices ? '' : 'NOT ' }}randomized
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <i class="fas fa-{{ $quiz->show_results ? 'check text-success' : 'times text-muted' }} mr-2"></i>
                            Results will be {{ $quiz->show_results ? 'shown' : 'hidden' }} after submission
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-{{ $quiz->show_answers ? 'check text-success' : 'times text-muted' }} mr-2"></i>
                            Answers will be {{ $quiz->show_answers ? 'shown' : 'hidden' }} after submission
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previous Attempts -->
        @if($attempts->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>Your Previous Attempts
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Attempt</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Time Spent</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                            <tr>
                                <td>{{ $attempt->attempt_number }}</td>
                                <td>{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y h:i A') : 'In Progress' }}</td>
                                <td>{{ $attempt->score }}/{{ $attempt->total_points }}</td>
                                <td>
                                    <span class="badge badge-{{ $attempt->isPassed() ? 'success' : 'danger' }}">
                                        {{ number_format($attempt->percentage, 1) }}%
                                    </span>
                                </td>
                                <td>{{ $attempt->getTimeSpentFormatted() }}</td>
                                <td>
                                    @if($attempt->isInProgress())
                                        <span class="badge badge-warning">In Progress</span>
                                    @elseif($attempt->isPassed())
                                        <span class="badge badge-success">Passed</span>
                                    @else
                                        <span class="badge badge-danger">Failed</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt->isInProgress())
                                        <a href="{{ route('student.quiz-attempts.take', $attempt) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-play mr-1"></i>Resume
                                        </a>
                                    @elseif($quiz->show_results)
                                        <a href="{{ route('student.quiz-attempts.results', $attempt) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-chart-bar mr-1"></i>Results
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('student.quizzes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Quizzes
                    </a>
                    <div>
                        @if($inProgressAttempt)
                            <a href="{{ route('student.quiz-attempts.take', $inProgressAttempt) }}" 
                               class="btn btn-warning btn-lg">
                                <i class="fas fa-play mr-2"></i>Resume Quiz
                            </a>
                        @elseif($canTake)
                            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#startQuizModal">
                                <i class="fas fa-play-circle mr-2"></i>Start Quiz
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-ban mr-2"></i>Cannot Take Quiz
                            </button>
                        @endif
                    </div>
                </div>
                
                @if(!$canTake && $attempts->count() >= $quiz->max_attempts)
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    You have reached the maximum number of attempts ({{ $quiz->max_attempts }}) for this quiz.
                </div>
                @endif
                
                @if(!$quiz->isAvailable())
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    This quiz is not currently available.
                    @if($quiz->available_from && $quiz->available_from->isFuture())
                        It will be available on {{ $quiz->available_from->format('M d, Y \a\t h:i A') }}.
                    @elseif($quiz->available_until && $quiz->available_until->isPast())
                        The deadline was {{ $quiz->available_until->format('M d, Y \a\t h:i A') }}.
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Availability -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar mr-2"></i>Availability
                </h6>
            </div>
            <div class="card-body">
                @if($quiz->available_from)
                <p class="mb-2">
                    <strong>Available from:</strong><br>
                    <span class="text-muted">{{ $quiz->available_from->format('F d, Y \a\t h:i A') }}</span>
                </p>
                @endif
                @if($quiz->available_until)
                <p class="mb-2">
                    <strong>Available until:</strong><br>
                    <span class="text-muted">{{ $quiz->available_until->format('F d, Y \a\t h:i A') }}</span>
                    @if($quiz->available_until->isFuture() && $quiz->isAvailable())
                    <br><span class="badge badge-warning mt-1">{{ $quiz->available_until->diffForHumans() }}</span>
                    @endif
                </p>
                @endif
                @if(!$quiz->available_from && !$quiz->available_until)
                <p class="text-muted mb-0">No time restrictions</p>
                @endif
            </div>
        </div>

        <!-- Statistics -->
        @if($attempts->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i>Your Statistics
                </h6>
            </div>
            <div class="card-body">
                @php
                    $completedAttempts = $attempts->where('status', 'completed');
                    $bestAttempt = $completedAttempts->sortByDesc('percentage')->first();
                    $avgScore = $completedAttempts->avg('percentage');
                @endphp
                
                <p class="mb-2">
                    <strong>Attempts Made:</strong><br>
                    <span class="h4">{{ $attempts->count() }}/{{ $quiz->max_attempts }}</span>
                </p>
                
                @if($bestAttempt)
                <hr>
                <p class="mb-2">
                    <strong>Best Score:</strong><br>
                    <span class="h4">
                        <span class="badge badge-{{ $bestAttempt->isPassed() ? 'success' : 'danger' }}">
                            {{ number_format($bestAttempt->percentage, 1) }}%
                        </span>
                    </span>
                </p>
                
                <p class="mb-2">
                    <strong>Average Score:</strong><br>
                    <span class="h5 text-muted">{{ number_format($avgScore, 1) }}%</span>
                </p>
                @endif
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt mr-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <a href="{{ route('student.lessons.index', ['subject_id' => $quiz->subject_id]) }}" 
                   class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-book-open mr-2"></i>View Lessons
                </a>
                <a href="{{ route('student.feedback.create', ['type' => 'quiz', 'id' => $quiz->id]) }}" 
                   class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-comment-dots mr-2"></i>Give Feedback
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Start Quiz Modal -->
<div class="modal fade" id="startQuizModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-play-circle mr-2"></i>Start Quiz: {{ $quiz->title }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6 class="font-weight-bold mb-3">Please read before starting:</h6>
                
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        You have <strong>{{ $quiz->max_attempts - $attempts->count() }}</strong> attempt(s) remaining
                    </li>
                    @if($quiz->time_limit)
                    <li class="mb-2">
                        <i class="fas fa-clock text-warning mr-2"></i>
                        Time limit: <strong>{{ $quiz->time_limit }} minutes</strong>
                    </li>
                    @endif
                    <li class="mb-2">
                        <i class="fas fa-question text-info mr-2"></i>
                        Total questions: <strong>{{ $quiz->questions_count }}</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-trophy text-success mr-2"></i>
                        Passing score: <strong>{{ $quiz->passing_score }}%</strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-save text-primary mr-2"></i>
                        Your answers will be <strong>auto-saved</strong> as you answer
                    </li>
                </ul>

                @if($quiz->instructions)
                <div class="alert alert-info">
                    <strong>Instructions:</strong><br>
                    {{ Str::limit($quiz->instructions, 200) }}
                </div>
                @endif

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Important:</strong> Once you start, the timer will begin counting down. Make sure you have a stable internet connection.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <form method="POST" action="{{ route('student.quiz-attempts.start', $quiz) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-play mr-1"></i>Start Quiz Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .instructions-content {
        font-size: 1.05rem;
        line-height: 1.8;
    }
</style>
@endpush