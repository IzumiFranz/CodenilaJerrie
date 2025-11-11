@extends('layouts.admin')

@section('title', 'Quiz Details')

@php
    $pageTitle = 'Quiz Details';
    $pageActions = '
        <a href="' . route('admin.quizzes.index') . '" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <a href="' . route('admin.quizzes.results', $quiz) . '" class="btn btn-success">
            <i class="fas fa-chart-bar"></i> View Results
        </a>';
@endphp

@section('content')
<!-- Quiz Information -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Quiz Information</h6>
                @if($quiz->is_published)
                    <span class="badge badge-success">Published</span>
                @else
                    <span class="badge badge-secondary">Draft</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Title:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $quiz->title }}
                    </div>
                </div>
                
                @if($quiz->description)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $quiz->description }}
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Subject:</strong>
                    </div>
                    <div class="col-md-8">
                        <span class="badge badge-info">{{ $quiz->subject->subject_code }}</span>
                        {{ $quiz->subject->subject_name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Instructor:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $quiz->instructor->first_name }} {{ $quiz->instructor->last_name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Time Limit:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($quiz->time_limit)
                            <i class="fas fa-clock text-warning"></i> {{ $quiz->time_limit }} minutes
                        @else
                            <span class="text-muted">No time limit</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Passing Score:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $quiz->passing_score }}%
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Max Attempts:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($quiz->max_attempts)
                            {{ $quiz->max_attempts }} attempts
                        @else
                            <span class="text-muted">Unlimited</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Available:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($quiz->available_from)
                            {{ \Carbon\Carbon::parse($quiz->available_from)->format('M d, Y H:i') }}
                        @else
                            <span class="text-muted">Always available</span>
                        @endif
                        @if($quiz->available_until)
                            to {{ \Carbon\Carbon::parse($quiz->available_until)->format('M d, Y H:i') }}
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Settings:</strong>
                    </div>
                    <div class="col-md-8">
                        @if($quiz->randomize_questions)
                            <span class="badge badge-info"><i class="fas fa-random"></i> Randomize Questions</span>
                        @endif
                        @if($quiz->randomize_choices)
                            <span class="badge badge-info"><i class="fas fa-random"></i> Randomize Choices</span>
                        @endif
                        @if($quiz->show_results)
                            <span class="badge badge-success"><i class="fas fa-eye"></i> Show Results</span>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <strong>Created:</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $quiz->created_at->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h4 class="text-primary">{{ $quiz->questions->count() }}</h4>
                    <p class="text-muted mb-0">Total Questions</p>
                </div>
                <hr>
                <div class="mb-3">
                    <h4 class="text-success">{{ $quiz->attempts->where('status', 'completed')->count() }}</h4>
                    <p class="text-muted mb-0">Completed Attempts</p>
                </div>
                <hr>
                <div class="mb-3">
                    <h4 class="text-warning">{{ $quiz->attempts->where('status', 'in_progress')->count() }}</h4>
                    <p class="text-muted mb-0">In Progress</p>
                </div>
                <hr>
                <div>
                    <h4 class="text-info">
                        @if($quiz->attempts->where('status', 'completed')->count() > 0)
                            {{ number_format($quiz->attempts->where('status', 'completed')->avg('percentage'), 1) }}%
                        @else
                            N/A
                        @endif
                    </h4>
                    <p class="text-muted mb-0">Average Score</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Questions -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Questions ({{ $quiz->questions->count() }})</h6>
    </div>
    <div class="card-body">
        @forelse($quiz->questions as $index => $question)
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">
                        <span class="badge badge-secondary">Q{{ $index + 1 }}</span>
                        <span class="badge badge-info">{{ ucfirst($question->type) }}</span>
                        <span class="badge badge-warning">{{ $question->points }} pts</span>
                        @if($question->difficulty)
                            <span class="badge badge-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                        @endif
                    </h6>
                </div>
                <p class="mb-2">{{ $question->question_text }}</p>
                
                @if(in_array($question->type, ['multiple_choice', 'true_false']))
                    <ul class="list-unstyled ml-3">
                        @foreach($question->choices->sortBy('order') as $choice)
                            <li class="{{ $choice->is_correct ? 'text-success font-weight-bold' : '' }}">
                                @if($choice->is_correct)
                                    <i class="fas fa-check-circle"></i>
                                @else
                                    <i class="far fa-circle"></i>
                                @endif
                                {{ $choice->choice_text }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($question->explanation)
                    <div class="alert alert-info mt-2">
                        <strong>Explanation:</strong> {{ $question->explanation }}
                    </div>
                @endif
            </div>
        @empty
            <p class="text-center text-muted py-4">
                <i class="fas fa-question-circle fa-3x mb-3"></i><br>
                No questions added to this quiz yet
            </p>
        @endforelse
    </div>
</div>
@endsection