@extends('layouts.student')

@section('title', 'Review Answers')

@section('content')
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
<div class="card shadow mb-4 border-left-primary">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-2">{{ $attempt->quiz->title }} - Answer Review</h3>
                <p class="text-muted mb-0">
                    <i class="fas fa-book mr-1"></i>{{ $attempt->quiz->subject->subject_name }} | 
                    <i class="fas fa-user mr-1"></i>{{ $attempt->quiz->instructor->full_name }}
                </p>
            </div>
            <div class="col-md-4 text-right">
                <h4>
                    <span class="badge badge-{{ $attempt->isPassed() ? 'success' : 'danger' }} p-3">
                        Score: {{ number_format($attempt->percentage, 1) }}%
                    </span>
                </h4>
                <p class="text-muted mb-0">{{ $attempt->score }}/{{ $attempt->total_points }} points</p>
            </div>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="alert alert-info">
    <div class="row">
        <div class="col-md-4">
            <i class="fas fa-check-circle text-success mr-2"></i>
            <strong>Green border</strong> = Correct answer
        </div>
        <div class="col-md-4">
            <i class="fas fa-times-circle text-danger mr-2"></i>
            <strong>Red border</strong> = Incorrect answer
        </div>
        <div class="col-md-4">
            <i class="fas fa-lightbulb text-warning mr-2"></i>
            <strong>Yellow icon</strong> = Correct answer shown
        </div>
    </div>
</div>

<!-- Questions Review -->
@foreach($questions as $index => $question)
    @php
        $answer = $answers->get($question->id);
        $isCorrect = $answer ? $answer->is_correct : false;
    @endphp

    <div class="card shadow mb-4 {{ $isCorrect ? 'answer-correct' : 'answer-incorrect' }}">
        <div class="card-header {{ $isCorrect ? 'bg-success' : 'bg-danger' }} text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-{{ $isCorrect ? 'check-circle' : 'times-circle' }} mr-2"></i>
                    Question {{ $index + 1 }}
                </h5>
                <div>
                    <span class="badge badge-light">
                        {{ $answer ? $answer->points_earned : 0 }}/{{ $question->points }} points
                    </span>
                    @if($question->difficulty)
                    <span class="badge badge-light">{{ ucfirst($question->difficulty) }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Question Text -->
            <div class="question-text mb-4">
                <h6 class="font-weight-bold">Question:</h6>
                <p class="lead">{!! nl2br(e($question->question_text)) !!}</p>
            </div>

            <!-- Multiple Choice / True-False -->
            @if($question->isMultipleChoice() || $question->isTrueFalse())
                <h6 class="font-weight-bold mb-3">Choices:</h6>
                <div class="choices-review">
                    @foreach($question->choices as $choice)
                        @php
                            $isStudentChoice = $answer && $answer->choice_id == $choice->id;
                            $isCorrectChoice = $choice->is_correct;
                        @endphp

                        <div class="mb-3 p-3 rounded {{ $isCorrectChoice ? 'bg-success-subtle border border-success' : ($isStudentChoice ? 'bg-danger-subtle border border-danger' : 'bg-light') }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    {{ $choice->choice_text }}
                                </div>
                                <div class="ml-3">
                                    @if($isCorrectChoice)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i>Correct Answer
                                        </span>
                                    @endif
                                    @if($isStudentChoice && !$isCorrectChoice)
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times mr-1"></i>Your Answer
                                        </span>
                                    @elseif($isStudentChoice && $isCorrectChoice)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i>Your Answer
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            <!-- Identification -->
            @elseif($question->isIdentification())
                <h6 class="font-weight-bold mb-2">Your Answer:</h6>
                <div class="p-3 mb-3 rounded {{ $isCorrect ? 'bg-success-subtle border border-success' : 'bg-danger-subtle border border-danger' }}">
                    {{ $answer ? $answer->answer_text : 'No answer provided' }}
                </div>

            <!-- Essay -->
            @elseif($question->isEssay())
                <h6 class="font-weight-bold mb-2">Your Answer:</h6>
                <div class="p-3 mb-3 rounded border">
                    <div class="essay-content">
                        {!! nl2br(e($answer ? $answer->answer_text : 'No answer provided')) !!}
                    </div>
                </div>
                
                @if($answer && $answer->instructor_feedback)
                <div class="alert alert-info mb-0">
                    <h6 class="font-weight-bold">
                        <i class="fas fa-comment-dots mr-2"></i>Instructor Feedback:
                    </h6>
                    <p class="mb-0">{!! nl2br(e($answer->instructor_feedback)) !!}</p>
                </div>
                @endif
            @endif

            <!-- Explanation (if provided) -->
            @if($question->explanation)
                <hr>
                <div class="explanation">
                    <h6 class="font-weight-bold text-primary">
                        <i class="fas fa-lightbulb mr-2"></i>Explanation:
                    </h6>
                    <p class="text-muted mb-0">{!! nl2br(e($question->explanation)) !!}</p>
                </div>
            @endif
        </div>
    </div>
@endforeach

<!-- Navigation -->
<div class="card shadow">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <a href="{{ route('student.quiz-attempts.results', $attempt) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Results
            </a>
            <div>
                <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print mr-2"></i>Print Review
                </button>
                <a href="{{ route('student.quizzes.show', $attempt->quiz) }}" class="btn btn-primary">
                    <i class="fas fa-eye mr-2"></i>Back to Quiz
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-success-subtle {
        background-color: rgba(28, 200, 138, 0.1);
    }
    
    .bg-danger-subtle {
        background-color: rgba(231, 74, 59, 0.1);
    }
    
    .essay-content {
        max-height: 300px;
        overflow-y: auto;
        font-family: 'Georgia', serif;
        line-height: 1.8;
    }
    
    .explanation {
        background-color: #fff3cd;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #ffc107;
    }

    @media print {
        .breadcrumb, .btn, .no-print {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd;
            box-shadow: none !important;
            page-break-inside: avoid;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Smooth scroll to question on hash
    if (window.location.hash) {
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: $(window.location.hash).offset().top - 100
            }, 500);
        }, 100);
    }
});
</script>
@endpush