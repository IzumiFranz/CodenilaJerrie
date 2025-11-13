@extends('layouts.instructor')
@section('title', 'Question Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-question-circle mr-2"></i>Question Details</h1>
    <div>
        <a href="{{ route('instructor.question-bank.edit', $questionBank) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('instructor.question-bank.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Question Content -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Question</h6>
            </div>
            <div class="card-body">
                <!-- Question Text -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($questionBank->type)) }}</span>
                            <span class="badge badge-{{ $questionBank->difficulty == 'easy' ? 'success' : ($questionBank->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                {{ ucfirst($questionBank->difficulty) }}
                            </span>
                            @if($questionBank->bloom_level)
                                <span class="badge badge-primary">{{ ucfirst($questionBank->bloom_level) }}</span>
                            @endif
                        </div>
                        <span class="badge badge-dark badge-lg">{{ $questionBank->points }} points</span>
                    </div>
                    
                    <div class="alert alert-light border">
                        <h5 class="mb-0">{{ $questionBank->question_text }}</h5>
                    </div>
                </div>

                <!-- Choices/Answers -->
                @if($questionBank->type == 'multiple_choice')
                    <h6 class="text-success mb-3"><i class="fas fa-list mr-2"></i>Answer Choices</h6>
                    <div class="list-group">
                        @foreach($questionBank->choices->sortBy('order') as $choice)
                        <div class="list-group-item {{ $choice->is_correct ? 'list-group-item-success' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($choice->is_correct)
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                    @else
                                        <i class="far fa-circle text-muted mr-2"></i>
                                    @endif
                                    <strong>{{ chr(64 + $choice->order) }}.</strong> {{ $choice->choice_text }}
                                </div>
                                @if($choice->is_correct)
                                    <span class="badge badge-success">Correct Answer</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                @elseif($questionBank->type == 'true_false')
                    <h6 class="text-success mb-3"><i class="fas fa-check-circle mr-2"></i>Correct Answer</h6>
                    <div class="alert alert-success">
                        <h4 class="mb-0">
                            <i class="fas fa-{{ $questionBank->choices->where('is_correct', true)->first()->choice_text == 'True' ? 'check' : 'times' }}-circle mr-2"></i>
                            {{ $questionBank->choices->where('is_correct', true)->first()->choice_text }}
                        </h4>
                    </div>

                @elseif($questionBank->type == 'identification')
                    <h6 class="text-success mb-3"><i class="fas fa-keyboard mr-2"></i>Correct Answer</h6>
                    <div class="alert alert-success">
                        <h5 class="mb-0">{{ $questionBank->choices->first()->choice_text }}</h5>
                    </div>

                @else
                    <div class="alert alert-info">
                        <i class="fas fa-pen mr-2"></i>
                        <strong>Essay Question</strong> - This question requires a written response and will be graded manually.
                    </div>
                @endif

                <!-- Explanation -->
                @if($questionBank->explanation)
                    <hr>
                    <h6 class="text-success mb-3"><i class="fas fa-info-circle mr-2"></i>Explanation</h6>
                    <div class="alert alert-info">
                        {{ $questionBank->explanation }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Question Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Subject:</th>
                        <td>{{ $questionBank->subject->subject_name }}</td>
                    </tr>
                    <tr>
                        <th>Course:</th>
                        <td>{{ $questionBank->subject->course->course_name }}</td>
                    </tr>
                    <tr>
                        <th>Type:</th>
                        <td>
                            <span class="badge badge-info">
                                {{ str_replace('_', ' ', ucfirst($questionBank->type)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Difficulty:</th>
                        <td>
                            <span class="badge badge-{{ $questionBank->difficulty == 'easy' ? 'success' : ($questionBank->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                {{ ucfirst($questionBank->difficulty) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Points:</th>
                        <td><strong>{{ $questionBank->points }}</strong></td>
                    </tr>
                    @if($questionBank->bloom_level)
                    <tr>
                        <th>Bloom's Level:</th>
                        <td>
                            <span class="badge badge-primary">
                                {{ ucfirst($questionBank->bloom_level) }}
                            </span>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th>Created:</th>
                        <td>{{ $questionBank->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>{{ $questionBank->updated_at->format('M d, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Usage in Quizzes</h6>
            </div>
            <div class="card-body">
                @if($questionBank->quizzes->count() > 0)
                    <p class="text-muted mb-2">This question is used in:</p>
                    <ul class="list-unstyled">
                        @foreach($questionBank->quizzes as $quiz)
                        <li class="mb-2">
                            <i class="fas fa-clipboard-list text-primary mr-2"></i>
                            <a href="{{ route('instructor.quizzes.show', $quiz) }}">
                                {{ $quiz->title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <div class="alert alert-warning mt-3 mb-0">
                        <small>
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Cannot delete while used in quizzes
                        </small>
                    </div>
                @else
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        Not currently used in any quiz
                    </p>
                @endif
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Quick Actions</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('instructor.question-bank.duplicate', $questionBank) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-copy mr-2"></i>Duplicate Question
                    </button>
                </form>
                
                @if($questionBank->quizzes->count() == 0)
                    <form action="{{ route('instructor.question-bank.destroy', $questionBank) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this question?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash mr-2"></i>Delete Question
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-danger btn-block" disabled title="Cannot delete while used in quizzes">
                        <i class="fas fa-trash mr-2"></i>Delete Question
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection