@extends('layouts.instructor')
@section('title', 'Question Preview')
@section('content')

<div class="question-preview">
    <!-- Question Header -->
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <span class="badge badge-primary mr-2">ID: {{ $questionBank->id }}</span>
            <span class="badge badge-info mr-2">{{ ucfirst(str_replace('_', ' ', $questionBank->type)) }}</span>
            <span class="badge badge-{{ $questionBank->difficulty === 'easy' ? 'success' : ($questionBank->difficulty === 'medium' ? 'warning' : 'danger') }}">
                {{ ucfirst($questionBank->difficulty) }}
            </span>
            @if($questionBank->bloom_level)
                <span class="badge badge-secondary ml-2">{{ ucfirst($questionBank->bloom_level) }}</span>
            @endif
        </div>
        <div class="text-right">
            <strong class="text-primary">{{ $questionBank->points }} {{ Str::plural('point', $questionBank->points) }}</strong>
        </div>
    </div>

    <!-- Subject Info -->
    <div class="mb-3 pb-3 border-bottom">
        <small class="text-muted">
            <i class="fas fa-book mr-1"></i>
            {{ $questionBank->subject->subject_code }} - {{ $questionBank->subject->subject_name }}
        </small>
    </div>

    <!-- Question Text -->
    <div class="mb-4">
        <strong class="d-block mb-2">Question:</strong>
        <div class="p-3 bg-light rounded">
            {!! nl2br(e($questionBank->question_text)) !!}
        </div>
    </div>

    <!-- Choices/Answer -->
    @if($questionBank->isMultipleChoice())
        <div class="mb-3">
            <strong class="d-block mb-2">Choices:</strong>
            <div class="list-group">
                @foreach($questionBank->choices->sortBy('order') as $choice)
                    <div class="list-group-item {{ $choice->is_correct ? 'list-group-item-success' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                @if($choice->is_correct)
                                    <i class="fas fa-check-circle text-success fa-lg"></i>
                                @else
                                    <i class="far fa-circle text-muted"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                {{ chr(64 + $choice->order) }}. {{ $choice->choice_text }}
                                @if($choice->is_correct)
                                    <span class="badge badge-success ml-2">Correct Answer</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @elseif($questionBank->isTrueFalse())
        <div class="mb-3">
            <strong class="d-block mb-2">Answer:</strong>
            <div class="p-3 bg-success text-white rounded">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>{{ $questionBank->getCorrectChoice()->choice_text }}</strong>
            </div>
        </div>

    @elseif($questionBank->isIdentification())
        <div class="mb-3">
            <strong class="d-block mb-2">Correct Answer:</strong>
            <div class="p-3 bg-success text-white rounded">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>{{ $questionBank->getCorrectChoice()->choice_text }}</strong>
            </div>
        </div>

    @elseif($questionBank->isEssay())
        <div class="mb-3">
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Essay Question</strong> - This requires manual grading by the instructor.
            </div>
        </div>
    @endif

    <!-- Explanation -->
    @if($questionBank->explanation)
        <div class="mb-3">
            <strong class="d-block mb-2">Explanation:</strong>
            <div class="p-3 bg-light rounded">
                <i class="fas fa-lightbulb text-warning mr-2"></i>
                {!! nl2br(e($questionBank->explanation)) !!}
            </div>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="text-center p-3 bg-light rounded">
                <div class="text-muted small">Times Used</div>
                <div class="h5 mb-0">{{ $questionBank->usage_count }}</div>
            </div>
        </div>
        @if($questionBank->difficulty_index)
            <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                    <div class="text-muted small">Difficulty Index</div>
                    <div class="h5 mb-0">{{ number_format($questionBank->difficulty_index * 100, 1) }}%</div>
                </div>
            </div>
        @endif
        @if($questionBank->discrimination_index)
            <div class="col-md-4">
                <div class="text-center p-3 bg-light rounded">
                    <div class="text-muted small">Discrimination</div>
                    <div class="h5 mb-0">{{ number_format($questionBank->discrimination_index, 2) }}</div>
                </div>
            </div>
        @endif
    </div>

    <!-- Validation Status -->
    @if($questionBank->is_validated)
        <div class="mt-3">
            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>Validated</strong>
                @if($questionBank->quality_score)
                    - Quality Score: {{ $questionBank->quality_score }}/100
                @endif
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="mt-4 text-right border-top pt-3">
        <a href="{{ route('instructor.question-bank.edit', $questionBank) }}" class="btn btn-primary" target="_blank">
            <i class="fas fa-edit"></i> Edit Question
        </a>
        <a href="{{ route('instructor.question-bank.show', $questionBank) }}" class="btn btn-info" target="_blank">
            <i class="fas fa-eye"></i> View Details
        </a>
    </div>
</div>

<style>
.question-preview {
    font-size: 14px;
}

.question-preview .badge {
    font-size: 11px;
    padding: 4px 8px;
}

.question-preview .list-group-item {
    border-left: 3px solid transparent;
}

.question-preview .list-group-item-success {
    border-left-color: #28a745;
    background-color: #d4edda;
}
</style>
@endsection