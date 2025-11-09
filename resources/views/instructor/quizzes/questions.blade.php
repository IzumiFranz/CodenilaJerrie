@extends('layouts.instructor')
@section('title', 'Manage Quiz Questions')

@push('styles')
<style>
    .sortable-list { min-height: 100px; }
    .question-item { cursor: move; }
    .question-item:hover { background-color: #f8f9fa; }
    .drag-handle { cursor: grab; color: #6c757d; }
    .drag-handle:active { cursor: grabbing; }
    .available-question { cursor: pointer; transition: all 0.3s; }
    .available-question:hover { background-color: #e3f2fd; transform: translateX(5px); }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tasks mr-2"></i>Manage Questions: {{ $quiz->title }}
    </h1>
    <div>
        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> View Quiz
        </a>
        <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Info Alert -->
<div class="alert alert-info">
    <i class="fas fa-info-circle mr-2"></i>
    <strong>Drag & Drop</strong> to reorder questions. 
    Click <strong>Add</strong> to include questions from your question bank.
    Total Points: <strong>{{ $quiz->questions->sum('points') }}</strong>
</div>

<div class="row">
    <!-- LEFT: Questions in Quiz -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-list-ol mr-2"></i>Questions in Quiz ({{ $quiz->questions->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($quiz->questions->count() > 0)
                    <div id="quiz-questions" class="sortable-list">
                        @foreach($quiz->questions->sortBy('pivot.order') as $question)
                        <div class="card mb-3 question-item" data-question-id="{{ $question->id }}">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <!-- Drag Handle -->
                                    <div class="drag-handle mr-3">
                                        <i class="fas fa-grip-vertical fa-2x"></i>
                                    </div>
                                    
                                    <!-- Question Info -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge badge-secondary">Q{{ $question->pivot->order }}</span>
                                                <span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($question->type)) }}</span>
                                                <span class="badge badge-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($question->difficulty) }}
                                                </span>
                                                @if($question->blooms_level)
                                                    <span class="badge badge-primary">{{ ucfirst($question->blooms_level) }}</span>
                                                @endif
                                            </div>
                                            <span class="badge badge-dark">{{ $question->points }} pts</span>
                                        </div>
                                        
                                        <p class="mb-2"><strong>{{ Str::limit($question->question_text, 100) }}</strong></p>
                                        
                                        @if($question->type == 'multiple_choice')
                                            <small class="text-muted">
                                                <i class="fas fa-list mr-1"></i>
                                                {{ $question->choices->count() }} choices
                                            </small>
                                        @elseif($question->type == 'true_false')
                                            <small class="text-muted">
                                                <i class="fas fa-check-circle mr-1"></i>True/False
                                            </small>
                                        @elseif($question->type == 'identification')
                                            <small class="text-muted">
                                                <i class="fas fa-keyboard mr-1"></i>Identification
                                            </small>
                                        @else
                                            <small class="text-muted">
                                                <i class="fas fa-pen mr-1"></i>Essay
                                            </small>
                                        @endif
                                    </div>
                                    
                                    <!-- Remove Button -->
                                    <div class="ml-3">
                                        <form action="{{ route('instructor.quizzes.remove-question', [$quiz, $question]) }}" 
                                              method="POST" class="remove-question-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h5>No questions in this quiz yet</h5>
                        <p>Add questions from the question bank on the right</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- RIGHT: Available Questions -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-database mr-2"></i>Available Questions ({{ $availableQuestions->count() }})
                </h6>
            </div>
            <div class="card-body">
                <!-- Search/Filter -->
                <div class="input-group mb-3">
                    <input type="text" id="searchQuestions" class="form-control" placeholder="Search questions...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>

                <div class="mb-3">
                    <select id="filterDifficulty" class="form-control">
                        <option value="">All Difficulties</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>

                <div class="mb-3">
                    <select id="filterType" class="form-control">
                        <option value="">All Types</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="identification">Identification</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>

                <hr>

                <!-- Available Questions List -->
                <div id="available-questions-list" style="max-height: 600px; overflow-y: auto;">
                    @forelse($availableQuestions as $question)
                    <div class="card mb-2 available-question" 
                         data-difficulty="{{ $question->difficulty }}" 
                         data-type="{{ $question->type }}"
                         data-question-text="{{ strtolower($question->question_text) }}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="mb-2">
                                        <span class="badge badge-info badge-sm">{{ str_replace('_', ' ', ucfirst($question->type)) }}</span>
                                        <span class="badge badge-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }} badge-sm">
                                            {{ ucfirst($question->difficulty) }}
                                        </span>
                                        <span class="badge badge-dark badge-sm">{{ $question->points }} pts</span>
                                    </div>
                                    <p class="mb-0 small"><strong>{{ Str::limit($question->question_text, 80) }}</strong></p>
                                </div>
                                <div class="ml-2">
                                    <form action="{{ route('instructor.quizzes.add-question', $quiz) }}" 
                                          method="POST" class="add-question-form">
                                        @csrf
                                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                                        <button type="submit" class="btn btn-success btn-sm" title="Add to Quiz">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i>
                        <p class="mb-0">All questions from this subject are already in the quiz.</p>
                        <a href="{{ route('instructor.question-bank.create', ['subject' => $quiz->subject_id]) }}" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-plus mr-1"></i>Create New Question
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- jQuery UI for Sortable -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
$(document).ready(function() {
    // Make questions sortable
    $('#quiz-questions').sortable({
        handle: '.drag-handle',
        placeholder: 'ui-state-highlight',
        update: function(event, ui) {
            let order = [];
            $('#quiz-questions .question-item').each(function(index) {
                order.push({
                    id: $(this).data('question-id'),
                    order: index + 1
                });
            });
            
            // Update order via AJAX
            $.ajax({
                url: '{{ route("instructor.quizzes.reorder-questions", $quiz) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    questions: order
                },
                success: function(response) {
                    // Update question numbers
                    $('#quiz-questions .question-item').each(function(index) {
                        $(this).find('.badge-secondary').text('Q' + (index + 1));
                    });
                    
                    // Show success message (you can use toastr or custom notification)
                    showNotification('Questions reordered successfully', 'success');
                },
                error: function(xhr) {
                    showNotification('Failed to reorder questions', 'error');
                }
            });
        }
    });

    // Add question via AJAX
    $('.add-question-form').on('submit', function(e) {
        e.preventDefault();
        
        let form = $(this);
        let button = form.find('button');
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                showNotification('Question added successfully', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                button.prop('disabled', false).html('<i class="fas fa-plus"></i>');
                showNotification(xhr.responseJSON.message || 'Failed to add question', 'error');
            }
        });
    });

    // Remove question via AJAX
    $('.remove-question-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!confirm('Remove this question from the quiz?')) {
            return;
        }
        
        let form = $(this);
        let questionItem = form.closest('.question-item');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                questionItem.fadeOut(300, function() {
                    $(this).remove();
                    // Update question numbers
                    $('#quiz-questions .question-item').each(function(index) {
                        $(this).find('.badge-secondary').text('Q' + (index + 1));
                    });
                });
                showNotification('Question removed successfully', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                showNotification('Failed to remove question', 'error');
            }
        });
    });

    // Search questions
    $('#searchQuestions').on('keyup', function() {
        filterQuestions();
    });

    // Filter by difficulty
    $('#filterDifficulty').on('change', function() {
        filterQuestions();
    });

    // Filter by type
    $('#filterType').on('change', function() {
        filterQuestions();
    });

    function filterQuestions() {
        let searchText = $('#searchQuestions').val().toLowerCase();
        let difficulty = $('#filterDifficulty').val();
        let type = $('#filterType').val();

        $('.available-question').each(function() {
            let question = $(this);
            let questionText = question.data('question-text');
            let questionDifficulty = question.data('difficulty');
            let questionType = question.data('type');

            let showSearch = searchText === '' || questionText.includes(searchText);
            let showDifficulty = difficulty === '' || questionDifficulty === difficulty;
            let showType = type === '' || questionType === type;

            if (showSearch && showDifficulty && showType) {
                question.show();
            } else {
                question.hide();
            }
        });
    }

    function showNotification(message, type) {
        // Simple notification (you can replace with toastr or SweetAlert)
        let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        let notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' mr-2"></i>' + message +
            '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>
@endpush