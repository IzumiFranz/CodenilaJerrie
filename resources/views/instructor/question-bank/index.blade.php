@extends('layouts.instructor')
@section('title', 'Question Bank')
@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-question-circle mr-2"></i>Question Bank</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('instructor.question-bank.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Create Question
        </a>
        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#aiGenerateModal">
            <i class="fas fa-robot mr-1"></i> Generate with AI
        </button>
    </div>
</div>

{{-- Include the modal at the bottom of the page --}}
@include('instructor.question-bank.partials.ai-generate-modal')

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="subject_id" class="form-control">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                        <option value="true_false" {{ request('type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                        <option value="identification" {{ request('type') == 'identification' ? 'selected' : '' }}>Identification</option>
                        <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>Essay</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="difficulty" class="form-control">
                        <option value="">All Difficulty</option>
                        <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                        <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Questions Table -->
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="40%">Question</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Difficulty</th>
                        <th>Points</th>
                        <th>Tags</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $question)
                    <tr>
                        <td>{{ Str::limit($question->question_text, 60) }}</td>
                        <td>{{ $question->subject->subject_name }}</td>
                        <td>
                            <span class="badge badge-info">
                                {{ str_replace('_', ' ', ucfirst($question->type)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                        </td>
                        <td>{{ $question->points }}</td>
                        <td>
                            @if($question->tags && $question->tags->count() > 0)
                                @foreach($question->tags as $tag)
                                    <span class="badge {{ method_exists($tag, 'getColorBadgeClass') ? $tag->getColorBadgeClass() : 'badge-info' }}" 
                                        style="background-color: {{ $tag->color ?? '#17a2b8' }}; font-size: 11px;">
                                        <i class="fas fa-tag"></i> {{ $tag->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted">No tags</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('instructor.question-bank.show', $question) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('instructor.question-bank.edit', $question) }}" class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-success validate-question-btn" 
                                        data-question-id="{{ $question->id }}"
                                        data-question-text="{{ Str::limit($question->question_text, 100) }}"
                                        title="Validate with AI">
                                    <i class="fas fa-check-double"></i>
                                </button>
                                <form action="{{ route('instructor.question-bank.duplicate', $question) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info" title="Duplicate">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>
                                <form action="{{ route('instructor.question-bank.destroy', $question) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this question?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No questions found. <a href="{{ route('instructor.question-bank.create') }}">Create one now</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $questions->links() }}</div>
    </div>
</div>
@include('instructor.question-bank.partials.ai-generate-modal')

@push('scripts')
<script>
// YOUR EXISTING SCRIPTS

// ADD AI VALIDATION HANDLER
$(document).on('click', '.validate-question-btn', function() {
    const questionId = $(this).data('question-id');
    const questionText = $(this).data('question-text');
    
    Swal.fire({
        title: 'Validate Question?',
        html: `<p>AI will analyze this question for quality and provide suggestions.</p>
               <p class="text-muted small">${questionText}</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Validate',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/instructor/ai/validate-question/${questionId}`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Validation Started!',
                text: 'You will see results in AI dashboard.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ route("instructor.ai.index") }}';
            });
        }
    });
});
</script>
@endpush
@endsection