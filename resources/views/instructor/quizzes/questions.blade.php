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

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tasks mr-2"></i>Manage Questions: {{ $quiz->title }}
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye mr-1"></i> View Quiz
        </a>
        <a href="{{ route('instructor.question-bank.create', ['subject' => $quiz->subject_id]) }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus mr-1"></i> Create New Question
        </a>
        <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>

<!-- NEW: Quiz Info Summary -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>Subject:</strong> {{ $quiz->subject->subject_name ?? 'N/A' }}</div>
            <div class="col-md-3"><strong>Questions:</strong> {{ $quiz->questions->count() }}</div>
            <div class="col-md-3"><strong>Total Points:</strong> {{ $quiz->questions->sum('points') }}</div>
            <div class="col-md-3"><strong>Status:</strong>
                <span class="badge badge-{{ $quiz->is_published ? 'success' : 'secondary' }}">
                    {{ $quiz->is_published ? 'Published' : 'Draft' }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Info Alert -->
<div class="alert alert-info">
    <i class="fas fa-info-circle mr-2"></i>
    <strong>Tip:</strong> Drag & Drop to reorder questions. You can also select multiple questions from the right to add them in bulk.
</div>

<div class="row">
    <!-- LEFT: Questions in Quiz -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-success text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-list-ol mr-2"></i>Questions in Quiz ({{ $quiz->questions->count() }})
                </h6>
                <button type="button" class="btn btn-sm btn-danger" id="bulkRemoveBtn" disabled>
                    <i class="fas fa-trash"></i> Remove Selected (<span id="removeCount">0</span>)
                </button>
            </div>

            <div class="card-body">
                @if($quiz->questions->count() > 0)
                <form id="bulkRemoveForm" method="POST" action="{{ route('instructor.quizzes.questions.bulk-remove', $quiz) }}">
                    @csrf
                    <div id="quiz-questions" class="sortable-list">
                        @foreach($quiz->questions->sortBy('pivot.order') as $question)
                        <div class="card mb-3 question-item p-2" data-question-id="{{ $question->id }}">
                            <div class="d-flex align-items-start">
                                <div class="drag-handle mr-3"><i class="fas fa-grip-vertical fa-2x"></i></div>
                                <div class="flex-grow-1">
                                    <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" class="question-checkbox-current mr-2">
                                    <strong>{{ Str::limit($question->question_text, 100) }}</strong>
                                    <div class="mt-1">
                                        <span class="badge badge-info">{{ ucfirst(str_replace('_',' ', $question->type)) }}</span>
                                        <span class="badge badge-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($question->difficulty) }}
                                        </span>
                                        <span class="badge badge-dark">{{ $question->points }} pts</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <button type="button" class="btn btn-sm btn-info preview-question" data-id="{{ $question->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form action="{{ route('instructor.quizzes.remove-question', [$quiz, $question]) }}" method="POST" class="d-inline remove-question-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </form>
                @else
                <div class="alert alert-warning text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>No questions yet. Add from the right panel.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- RIGHT: Available Questions -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-database mr-2"></i>Available Questions ({{ $availableQuestions->count() }})</h6>
                <button type="button" class="btn btn-sm btn-success" id="bulkAddBtn" disabled>
                    <i class="fas fa-plus"></i> Add Selected (<span id="addCount">0</span>)
                </button>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="mb-3">
                    <input type="text" id="searchQuestions" class="form-control form-control-sm mb-2" placeholder="Search...">
                    <select id="filterDifficulty" class="form-control form-control-sm mb-2">
                        <option value="">All Difficulties</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                    <select id="filterType" class="form-control form-control-sm">
                        <option value="">All Types</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="identification">Identification</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>

                <form id="bulkAddForm" method="POST" action="{{ route('instructor.quizzes.questions.bulk-add', $quiz) }}">
                    @csrf
                    <div id="available-questions-list" style="max-height: 600px; overflow-y: auto;">
                        @forelse($availableQuestions as $question)
                        <div class="card mb-2 available-question" data-difficulty="{{ $question->difficulty }}" data-type="{{ $question->type }}" data-question-text="{{ strtolower($question->question_text) }}">
                            <div class="card-body p-2 d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" class="question-checkbox-available mr-2">
                                    <strong>{{ Str::limit($question->question_text, 80) }}</strong>
                                    <div class="small mt-1">
                                        <span class="badge badge-info">{{ ucfirst(str_replace('_',' ', $question->type)) }}</span>
                                        <span class="badge badge-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">{{ ucfirst($question->difficulty) }}</span>
                                        <span class="badge badge-dark">{{ $question->points }} pts</span>
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <button type="button" class="btn btn-sm btn-info preview-question" data-id="{{ $question->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> All questions already in quiz.
                        </div>
                        @endforelse
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Question Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Question Preview</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="previewContent">
                <div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading...</p></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
$(function() {
    // sortable order
    $('#quiz-questions').sortable({
        handle: '.drag-handle',
        update: function() {
            let order = [];
            $('#quiz-questions .question-item').each(function(i) {
                order.push({ id: $(this).data('question-id'), order: i + 1 });
            });
            $.post('{{ route("instructor.quizzes.reorder-questions", $quiz) }}', { _token: '{{ csrf_token() }}', questions: order });
        }
    });

    // preview modal
    $('.preview-question').click(function() {
        const id = $(this).data('id');
        $('#previewModal').modal('show');
        $('#previewContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading...</p></div>');
        $.get('/instructor/question-bank/' + id + '/preview', res => $('#previewContent').html(res))
         .fail(() => $('#previewContent').html('<div class="alert alert-danger">Failed to load.</div>'));
    });

    // bulk select add/remove
    $('#bulkAddBtn').click(() => $('#bulkAddForm').submit());
    $('#bulkRemoveBtn').click(() => $('#bulkRemoveForm').submit());

    $('.question-checkbox-current').change(updateRemoveCount);
    $('.question-checkbox-available').change(updateAddCount);

    function updateRemoveCount() {
        const c = $('.question-checkbox-current:checked').length;
        $('#removeCount').text(c);
        $('#bulkRemoveBtn').prop('disabled', c === 0);
    }

    function updateAddCount() {
        const c = $('.question-checkbox-available:checked').length;
        $('#addCount').text(c);
        $('#bulkAddBtn').prop('disabled', c === 0);
    }

    // filters
    $('#searchQuestions, #filterDifficulty, #filterType').on('keyup change', function() {
        let search = $('#searchQuestions').val().toLowerCase(),
            diff = $('#filterDifficulty').val(),
            type = $('#filterType').val();
        $('.available-question').each(function() {
            let q = $(this),
                match = q.data('question-text').includes(search) &&
                        (!diff || q.data('difficulty') === diff) &&
                        (!type || q.data('type') === type);
            q.toggle(match);
        });
    });
});
</script>
@endpush
