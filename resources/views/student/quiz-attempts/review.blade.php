@extends('layouts.student')

@section('title', 'Review Quiz Answers')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.quizzes.index') }}">Quizzes</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.quizzes.show', $attempt->quiz) }}">{{ $attempt->quiz->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.quiz-attempts.results', $attempt) }}">Results</a></li>
            <li class="breadcrumb-item active">Review</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('student.quiz-attempts.results', $attempt->id) }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Results
            </a>
            <h1 class="h3 mb-0">Review: {{ $attempt->quiz->title }}</h1>
            <p class="text-muted mb-0">
                Score: {{ number_format($attempt->score, 1) }}% | {{ $attempt->correct_answers }}/{{ $attempt->total_questions }} correct
            </p>
            <p class="text-muted mb-0">
                <i class="fas fa-book me-1"></i>{{ $attempt->quiz->subject->subject_name }} | 
                <i class="fas fa-user me-1"></i>{{ $attempt->quiz->instructor->full_name }}
            </p>
            <small class="text-muted">
                Attempt #{{ $attempt->attempt_number }} - Completed {{ $attempt->completed_at->format('M d, Y h:i A') }}
            </small>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-secondary mb-2">
                <i class="fas fa-print me-1"></i> Print Review
            </button>
            <h4 class="mt-2">
                <span class="badge badge-{{ $attempt->isPassed() ? 'success' : 'danger' }} p-3">
                    Score: {{ number_format($attempt->percentage, 1) }}%
                </span>
            </h4>
            <p class="text-muted mb-0">{{ $attempt->score }}/{{ $attempt->total_points }} points</p>
            <p class="text-muted mb-0">Time Spent: {{ $attempt->getTimeSpentFormatted() }}</p>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <h5 class="text-muted mb-2">Total Questions</h5>
                    <h2 class="mb-0">{{ $attempt->total_questions }}</h2>
                </div>
                <div class="col-md-3">
                    <h5 class="text-muted mb-2">Correct</h5>
                    <h2 class="mb-0 text-success">{{ $attempt->correct_answers }}</h2>
                </div>
                <div class="col-md-3">
                    <h5 class="text-muted mb-2">Wrong</h5>
                    <h2 class="mb-0 text-danger">{{ $attempt->wrong_answers }}</h2>
                </div>
                <div class="col-md-3">
                    <h5 class="text-muted mb-2">Final Score</h5>
                    <h2 class="mb-0 text-primary">{{ number_format($attempt->score, 1) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="alert alert-info mb-4">
        <div class="row">
            <div class="col-md-4">
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong>Green border</strong> = Correct answer
            </div>
            <div class="col-md-4">
                <i class="fas fa-times-circle text-danger me-2"></i>
                <strong>Red border</strong> = Incorrect answer
            </div>
            <div class="col-md-4">
                <i class="fas fa-lightbulb text-warning me-2"></i>
                <strong>Yellow icon</strong> = Correct answer shown
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" onclick="filterQuestions('all')">
                    <i class="fas fa-list me-1"></i> All Questions ({{ $attempt->total_questions }})
                </button>
                <button type="button" class="btn btn-outline-success" onclick="filterQuestions('correct')">
                    <i class="fas fa-check-circle me-1"></i> Correct ({{ $attempt->correct_answers }})
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="filterQuestions('wrong')">
                    <i class="fas fa-times-circle me-1"></i> Wrong ({{ $attempt->wrong_answers }})
                </button>
            </div>
        </div>
    </div>

    <!-- Questions Review -->
    @foreach($attempt->answers as $index => $answer)
        @php
            $question = $answer->question;
        @endphp
        <div class="card shadow-sm mb-4 question-card {{ $answer->is_correct ? 'correct-answer' : 'wrong-answer' }}">
            <div class="card-header {{ $answer->is_correct ? 'bg-success' : 'bg-danger' }} text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-{{ $answer->is_correct ? 'check-circle' : 'times-circle' }} me-2"></i>
                        Question {{ $index + 1 }} of {{ $attempt->total_questions }}
                    </h5>
                    <span class="badge bg-white text-{{ $answer->is_correct ? 'success' : 'danger' }}">
                        {{ $answer->is_correct ? 'Correct' : 'Wrong' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Question Text -->
                <div class="question-text mb-4">
                    <h5 class="mb-3">{!! $question->question_text !!}</h5>
                    @if($question->image)
                        <div class="text-center mb-3">
                            <img src="{{ asset('storage/' . $question->image) }}" class="img-fluid rounded shadow-sm" style="max-height: 300px;" alt="Question Image">
                        </div>
                    @endif
                </div>

                <!-- Multiple Choice / True-False -->
                @if($question->isMultipleChoice() || $question->isTrueFalse())
                    <div class="row mb-3">
                        @foreach($question->choices->sortBy('order') as $choice)
                            @php
                                $isStudentChoice = $answer && $answer->choice_id == $choice->id;
                                $isCorrectChoice = $choice->is_correct;
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 option-card
                                    {{ $isCorrectChoice ? 'border-success border-3' : '' }}
                                    {{ $isStudentChoice && !$isCorrectChoice ? 'border-danger border-3' : '' }}">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="me-3">
                                            @if($isStudentChoice)
                                                <i class="fas fa-{{ $isCorrectChoice ? 'check-circle text-success' : 'times-circle text-danger' }} fa-lg"></i>
                                            @elseif($answer->quiz->show_correct_in_review && $isCorrectChoice)
                                                <i class="fas fa-check-circle text-success fa-lg"></i>
                                            @else
                                                <i class="far fa-circle text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong>{{ chr(64 + $choice->order) }}.</strong> {{ $choice->choice_text }}
                                            @if($isStudentChoice && !$isCorrectChoice)
                                                <span class="badge bg-danger ms-2">Your Answer</span>
                                            @elseif($answer->quiz->show_correct_in_review && $isCorrectChoice)
                                                <span class="badge bg-success ms-2">Correct Answer</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                <!-- Identification -->
                @elseif($question->isIdentification())
                    <h6 class="font-weight-bold mb-2">Your Answer:</h6>
                    <div class="p-3 mb-3 rounded {{ $answer->is_correct ? 'bg-success-subtle border border-success' : 'bg-danger-subtle border border-danger' }}">
                        {{ $answer ? $answer->answer_text : 'No answer provided' }}
                    </div>
                    @if($question->quiz->show_correct_in_review && !$answer->is_correct)
                        <div class="mb-3">
                            <label class="font-weight-bold">Correct Answer:</label>
                            <div class="p-3 bg-success text-white rounded">
                                {{ $question->getCorrectChoice()->choice_text }}
                            </div>
                        </div>
                    @endif

                <!-- Essay -->
                @elseif($question->isEssay())
                    <h6 class="font-weight-bold mb-2">Your Answer:</h6>
                    <div class="p-3 mb-3 rounded border essay-content">
                        {!! nl2br(e($answer->answer_text ?? 'No answer provided')) !!}
                    </div>
                    @if($answer->instructor_feedback)
                        <div class="alert alert-info mb-0">
                            <h6 class="font-weight-bold">
                                <i class="fas fa-comment-dots me-2"></i>Instructor Feedback:
                            </h6>
                            <p class="mb-0">{!! nl2br(e($answer->instructor_feedback)) !!}</p>
                        </div>
                    @endif
                @endif

                <!-- Explanation -->
                @if($question->explanation)
                    <hr>
                    <div class="explanation">
                        <h6 class="font-weight-bold text-primary"><i class="fas fa-lightbulb me-2"></i>Explanation:</h6>
                        <p class="text-muted mb-0">{!! nl2br(e($question->explanation)) !!}</p>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-white text-muted">
                <small>
                    <i class="fas fa-tag me-1"></i>Category: {{ $question->category ?? 'General' }}
                    @if($question->difficulty)
                        | <i class="fas fa-chart-line me-1"></i>Difficulty: {{ ucfirst($question->difficulty) }}
                    @endif
                </small>
            </div>
        </div>
    @endforeach

    <!-- Bottom Actions -->
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <h5 class="mb-3">Review Complete</h5>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ route('student.quiz-attempts.results', $attempt->id) }}" class="btn btn-primary">
                    <i class="fas fa-chart-bar me-1"></i> View Statistics
                </a>
                @if($attempt->score < $attempt->quiz->passing_score && $attempt->quiz->allow_retake)
                    <a href="{{ route('student.quizzes.show', $attempt->quiz->id) }}" class="btn btn-success">
                        <i class="fas fa-redo me-1"></i> Retake Quiz
                    </a>
                @endif
                <a href="{{ route('student.quizzes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list me-1"></i> Browse Quizzes
                </a>
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-home me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.bg-success-subtle { background-color: rgba(28, 200, 138, 0.1); }
.bg-danger-subtle { background-color: rgba(231, 74, 59, 0.1); }

.essay-content { max-height: 300px; overflow-y: auto; font-family: 'Georgia', serif; line-height: 1.8; }

.explanation { background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; }

.option-card { transition: all 0.3s ease; cursor: default; }
.option-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

.question-text h5 { line-height: 1.6; }
.question-card { page-break-inside: avoid; }

@media print {
    nav, .btn, .card-header { display: none !important; }
    .question-card { page-break-inside: avoid; margin-bottom: 30px; }
    .card { box-shadow: none !important; }
}
</style>
@endpush

@push('scripts')
<script>
function filterQuestions(type) {
    const cards = document.querySelectorAll('.question-card');
    const buttons = document.querySelectorAll('.btn-group button');

    // Update button states
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.closest('button').classList.add('active');

    // Filter cards
    cards.forEach(card => {
        if (type === 'all') card.style.display = 'block';
        else if (type === 'correct' && card.classList.contains('correct-answer')) card.style.display = 'block';
        else if (type === 'wrong' && card.classList.contains('wrong-answer')) card.style.display = 'block';
        else card.style.display = 'none';
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

$(document).ready(function() {
    if (window.location.hash) {
        setTimeout(function() {
            $('html, body').animate({ scrollTop: $(window.location.hash).offset().top - 100 }, 500);
        }, 100);
    }
});
</script>
@endpush
@endsection
