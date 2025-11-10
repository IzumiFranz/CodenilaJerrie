@extends('layouts.student')

@section('title', 'Quiz Results')

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.quizzes.index') }}">Quizzes</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.quizzes.show', $attempt->quiz) }}">{{ $attempt->quiz->title }}</a></li>
        <li class="breadcrumb-item active">Results</li>
    </ol>
</nav>

<!-- Results Header -->
<div class="card shadow mb-4 result-card {{ $attempt->isPassed() ? 'passed' : 'failed' }}">
    <div class="card-body text-center py-5">
        <div class="score-circle {{ $attempt->isPassed() ? 'passed' : 'failed' }} mb-4">
            {{ number_format($attempt->percentage, 1) }}%
        </div>
        
        <h2 class="mb-3">
            @if($attempt->isPassed())
                <i class="fas fa-check-circle text-success mr-2"></i>Congratulations!
            @else
                <i class="fas fa-times-circle text-danger mr-2"></i>Keep Trying!
            @endif
        </h2>
        
        <p class="lead mb-4">
            @if($attempt->isPassed())
                You passed the quiz with a score of <strong>{{ number_format($attempt->percentage, 1) }}%</strong>
            @else
                You scored <strong>{{ number_format($attempt->percentage, 1) }}%</strong>. The passing score is {{ $attempt->quiz->passing_score }}%
            @endif
        </p>
        
        <h4 class="text-muted">{{ $attempt->quiz->title }}</h4>
        <p class="text-muted mb-0">
            <i class="fas fa-book mr-1"></i>{{ $attempt->quiz->subject->subject_name }}
        </p>
    </div>
</div>

<!-- Results Summary -->
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $attempt->score }}/{{ $attempt->total_points }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Percentage</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($attempt->percentage, 1) }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Time Spent</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $attempt->getTimeSpentFormatted() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Attempt</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $attempt->attempt_number }}/{{ $attempt->quiz->max_attempts }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-redo fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Breakdown -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <!-- Answer Breakdown -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie mr-2"></i>Answer Breakdown
                </h6>
            </div>
            <div class="card-body">
                @php
                    $correctCount = $attempt->answers->where('is_correct', true)->count();
                    $incorrectCount = $attempt->answers->where('is_correct', false)->count();
                    $totalAnswered = $attempt->answers->count();
                    $totalQuestions = $attempt->quiz->questions->count();
                @endphp

                <div class="row mb-4">
                    <div class="col-md-4 text-center">
                        <h1 class="text-success">{{ $correctCount }}</h1>
                        <p class="text-muted mb-0">Correct</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h1 class="text-danger">{{ $incorrectCount }}</h1>
                        <p class="text-muted mb-0">Incorrect</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h1 class="text-info">{{ $totalQuestions }}</h1>
                        <p class="text-muted mb-0">Total Questions</p>
                    </div>
                </div>

                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-success" 
                         style="width: {{ $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0 }}%"
                         role="progressbar">
                        {{ $correctCount }} Correct
                    </div>
                    <div class="progress-bar bg-danger" 
                         style="width: {{ $totalQuestions > 0 ? ($incorrectCount / $totalQuestions) * 100 : 0 }}%"
                         role="progressbar">
                        {{ $incorrectCount }} Incorrect
                    </div>
                </div>

                <hr>

                <!-- By Question Type -->
                @php
                    $byType = $attempt->answers->groupBy(function($answer) {
                        return $answer->question->type;
                    })->map(function($answers) {
                        return [
                            'total' => $answers->count(),
                            'correct' => $answers->where('is_correct', true)->count(),
                            'percentage' => $answers->count() > 0 ? ($answers->where('is_correct', true)->count() / $answers->count()) * 100 : 0
                        ];
                    });
                @endphp

                <h6 class="font-weight-bold mb-3">Performance by Question Type</h6>
                
                @foreach($byType as $type => $stats)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="font-weight-bold">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                        <span class="text-muted">{{ $stats['correct'] }}/{{ $stats['total'] }} ({{ number_format($stats['percentage'], 1) }}%)</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-{{ $stats['percentage'] >= 75 ? 'success' : ($stats['percentage'] >= 60 ? 'warning' : 'danger') }}" 
                             role="progressbar" 
                             style="width: {{ $stats['percentage'] }}%">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- All Attempts Comparison -->
        @if($allAttempts->count() > 1)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i>All Attempts Comparison
                </h6>
            </div>
            <div class="card-body">
                <canvas id="attemptsChart"></canvas>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Quiz Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i>Quiz Information
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Subject:</strong><br>
                    {{ $attempt->quiz->subject->subject_name }}
                </p>
                <p class="mb-2">
                    <strong>Instructor:</strong><br>
                    {{ $attempt->quiz->instructor->full_name }}
                </p>
                <p class="mb-2">
                    <strong>Completed:</strong><br>
                    {{ $attempt->completed_at->format('M d, Y h:i A') }}
                </p>
                <p class="mb-2">
                    <strong>Passing Score:</strong><br>
                    {{ $attempt->quiz->passing_score }}%
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tasks mr-2"></i>Actions
                </h6>
            </div>
            <div class="card-body">
            @if($quiz->allow_review)
                @if($quiz->canReview($attempt))
                    <a href="{{ route('student.quiz-attempts.review', [$quiz, $attempt]) }}" 
                    class="btn btn-info btn-lg">
                        <i class="fas fa-clipboard-check"></i> Review Answers
                    </a>
                @else
                    <button class="btn btn-secondary btn-lg" disabled title="Review not yet available">
                        <i class="fas fa-clock"></i> Review Available in {{ $quiz->review_available_after }} minutes
                    </button>
                @endif
            @endif

                @if($attempt->quiz->studentCanTakeQuiz(auth()->user()->student))
                <a href="{{ route('student.quizzes.show', $attempt->quiz) }}" 
                   class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-redo mr-2"></i>Retake Quiz
                </a>
                @endif

                <a href="{{ route('student.quizzes.show', $attempt->quiz) }}" 
                   class="btn btn-secondary btn-block mb-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Quiz
                </a>

                <button type="button" class="btn btn-outline-primary btn-block" onclick="window.print()">
                    <i class="fas fa-print mr-2"></i>Print Results
                </button>
                <a href="{{ route('student.quiz-attempts.export-pdf', $attempt) }}" 
                   class="btn btn-danger btn-block mb-2">
                    <i class="fas fa-file-pdf mr-2"></i>Download PDF Report
                </a>
            </div>
        </div>

        <!-- Feedback -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-comment-dots mr-2"></i>Feedback
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">How was your quiz experience?</p>
                <a href="{{ route('student.feedback.create', ['type' => 'quiz', 'id' => $attempt->quiz->id]) }}" 
                   class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-comment mr-2"></i>Give Feedback
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
@if($allAttempts->count() > 1)
// Attempts comparison chart
var ctx = document.getElementById('attemptsChart').getContext('2d');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($allAttempts->pluck('attempt_number')->map(function($num) { return 'Attempt ' . $num; })),
        datasets: [{
            label: 'Score (%)',
            data: @json($allAttempts->pluck('percentage')),
            borderColor: '#36b9cc',
            backgroundColor: 'rgba(54, 185, 204, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Score: ' + context.parsed.y.toFixed(1) + '%';
                    }
                }
            }
        }
    }
});
@endif

// Confetti effect for passing
@if($attempt->isPassed())
function celebrate() {
    // Simple confetti effect with emojis
    const emojis = ['🎉', '🎊', '⭐', '✨', '🏆'];
    for (let i = 0; i < 30; i++) {
        setTimeout(() => {
            const emoji = emojis[Math.floor(Math.random() * emojis.length)];
            const x = Math.random() * window.innerWidth;
            const y = Math.random() * window.innerHeight;
            const elem = document.createElement('div');
            elem.textContent = emoji;
            elem.style.position = 'fixed';
            elem.style.left = x + 'px';
            elem.style.top = '-50px';
            elem.style.fontSize = '30px';
            elem.style.zIndex = '9999';
            elem.style.pointerEvents = 'none';
            document.body.appendChild(elem);
            
            let top = -50;
            const fall = setInterval(() => {
                top += 5;
                elem.style.top = top + 'px';
                if (top > window.innerHeight) {
                    clearInterval(fall);
                    elem.remove();
                }
            }, 30);
        }, i * 100);
    }
}

// Trigger celebration
setTimeout(celebrate, 500);
@endif
</script>
@endpush

@push('styles')
<style>
    @media print {
        .no-print, .btn, .card-header, .breadcrumb {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd;
            box-shadow: none !important;
            page-break-inside: avoid;
        }
        .score-circle {
            border: 3px solid #333;
        }
    }
</style>
@endpush