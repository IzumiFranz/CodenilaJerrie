@extends('layouts.instructor')
@section('title', 'Quiz Details')
@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clipboard-list mr-2"></i>Quiz Details</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('instructor.quizzes.edit', $quiz) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <a href="{{ route('instructor.quizzes.questions', $quiz) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-tasks mr-1"></i> Manage Questions
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-cog"></i> More
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('instructor.quizzes.results', $quiz) }}" class="dropdown-item">
                    <i class="fas fa-chart-bar text-info"></i> View Results
                </a>
                <form action="{{ route('instructor.quizzes.toggle-publish', $quiz) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-{{ $quiz->is_published ? 'eye-slash' : 'check' }} text-{{ $quiz->is_published ? 'secondary' : 'success' }}"></i>
                        {{ $quiz->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <form action="{{ route('instructor.quizzes.duplicate', $quiz) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-copy text-info"></i> Duplicate
                    </button>
                </form>
                <div class="dropdown-divider"></div>
                <form action="{{ route('instructor.quizzes.destroy', $quiz) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this quiz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
        <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Quiz Info -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Quiz Information</h6>
            </div>
            <div class="card-body">
                <h4>{{ $quiz->title }}</h4>
                <p class="text-muted">{{ $quiz->subject->subject_name }}</p>
                
                @if($quiz->description)
                    <div class="alert alert-info">
                        {{ $quiz->description }}
                    </div>
                @endif

                <table class="table table-bordered mt-3">
                    <tr>
                        <th width="30%">Status</th>
                        <td>
                            <span class="badge badge-{{ $quiz->is_published ? 'success' : 'secondary' }} badge-pill">
                                {{ $quiz->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Questions</th>
                        <td>{{ $quiz->questions->count() }} questions</td>
                    </tr>
                    <tr>
                        <th>Time Limit</th>
                        <td>{{ $quiz->time_limit }} minutes</td>
                    </tr>
                    <tr>
                        <th>Passing Score</th>
                        <td>{{ $quiz->passing_score }}%</td>
                    </tr>
                    <tr>
                        <th>Max Attempts</th>
                        <td>{{ $quiz->max_attempts }}</td>
                    </tr>
                    <tr>
                        <th>Randomize Questions</th>
                        <td>{{ $quiz->randomize_questions ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Randomize Choices</th>
                        <td>{{ $quiz->randomize_choices ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Show Results</th>
                        <td>{{ $quiz->show_results ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Show Correct Answers</th>
                        <td>{{ $quiz->show_correct_answers ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <th>Available From</th>
                        <td>{{ $quiz->available_from ? $quiz->available_from->format('M d, Y h:i A') : 'Immediately' }}</td>
                    </tr>
                    <tr>
                        <th>Available Until</th>
                        <td>{{ $quiz->available_until ? $quiz->available_until->format('M d, Y h:i A') : 'No end date' }}</td>
                    </tr>
                    <tr>
                        <th>Created</th>
                        <td>{{ $quiz->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>Last Updated</th>
                        <td>{{ $quiz->updated_at->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h2 class="text-primary">{{ $quiz->attempts->count() }}</h2>
                    <p class="text-muted mb-0">Total Attempts</p>
                </div>
                <hr>
                <div class="text-center mb-3">
                    <h2 class="text-success">{{ $quiz->attempts->where('status', 'completed')->count() }}</h2>
                    <p class="text-muted mb-0">Completed</p>
                </div>
                <hr>
                <div class="text-center">
                    <h2 class="text-info">{{ $quiz->attempts->where('status', 'completed')->avg('percentage') ? number_format($quiz->attempts->where('status', 'completed')->avg('percentage'), 1) : '0' }}%</h2>
                    <p class="text-muted mb-0">Average Score</p>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Quick Actions</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('instructor.quizzes.results', $quiz) }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-chart-bar mr-2"></i>View Results
                </a>
                <a href="{{ route('instructor.quizzes.edit', $quiz) }}" class="btn btn-info">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                @php
                    $completedAttempts = $quiz->attempts()->where('status', 'completed')->count();
                @endphp
                
                @if($completedAttempts >= 5)
                <button type="button" class="btn btn-warning btn-block mb-2 analyze-quiz-btn" 
                        data-quiz-id="{{ $quiz->id }}"
                        data-quiz-title="{{ $quiz->title }}"
                        data-attempts-count="{{ $completedAttempts }}">
                    <i class="fas fa-robot mr-2"></i> AI Analysis
                </button>
                @else
                <button type="button" class="btn btn-secondary btn-block mb-2" disabled 
                        title="Need at least 5 completed attempts for analysis">
                    <i class="fas fa-robot mr-2"></i> AI Analysis ({{ $completedAttempts }}/5)
                </button>
                @endif
                
                <a href="{{ route('instructor.quizzes.results', $quiz) }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-chart-bar mr-2"></i>View Results
                </a>
                <form action="{{ route('instructor.quizzes.toggle-publish', $quiz) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-{{ $quiz->is_published ? 'secondary' : 'success' }} btn-block mb-2">
                        <i class="fas fa-{{ $quiz->is_published ? 'eye-slash' : 'check' }} mr-2"></i>
                        {{ $quiz->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <form action="{{ route('instructor.quizzes.duplicate', $quiz) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-info btn-block">
                        <i class="fas fa-copy mr-2"></i>Duplicate Quiz
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Questions List -->
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Questions ({{ $quiz->questions->count() }})</h6>
        <a href="{{ route('instructor.quizzes.questions', $quiz) }}" class="btn btn-sm btn-primary">
            <i class="fas fa-edit mr-2"></i>Manage Questions
        </a>
    </div>
    <div class="card-body">
        @forelse($quiz->questions->sortBy('pivot.order') as $question)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="badge badge-secondary">Q{{ $question->pivot->order }}</span>
                        <span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($question->type)) }}</span>
                        <span class="badge badge-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                            {{ ucfirst($question->difficulty) }}
                        </span>
                    </div>
                    <span class="text-muted">{{ $question->points }} pts</span>
                </div>
                <p class="mt-2 mb-0">{{ $question->question_text }}</p>
            </div>
        </div>
        @empty
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>No questions added yet. 
            <a href="{{ route('instructor.quizzes.questions', $quiz) }}">Add questions now</a>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
// YOUR EXISTING SCRIPTS

// ADD AI ANALYSIS HANDLER
$(document).on('click', '.analyze-quiz-btn', function() {
    const quizId = $(this).data('quiz-id');
    const quizTitle = $(this).data('quiz-title');
    const attemptsCount = $(this).data('attempts-count');
    
    Swal.fire({
        title: 'Analyze Quiz?',
        html: `<p><strong>${quizTitle}</strong></p>
               <p>AI will analyze ${attemptsCount} attempts and provide insights on:</p>
               <ul class="text-left">
                   <li>Overall difficulty assessment</li>
                   <li>Question performance analysis</li>
                   <li>Student performance patterns</li>
                   <li>Recommendations for improvement</li>
               </ul>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-chart-line"></i> Analyze',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/instructor/ai/analyze-quiz/${quizId}`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Analysis Started!',
                text: 'Results will appear in AI dashboard.',
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