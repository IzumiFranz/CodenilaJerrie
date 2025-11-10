@extends('layouts.instructor')
@section('title', 'Quizzes')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clipboard-list mr-2"></i>Quizzes</h1>
    <a href="{{ route('instructor.quizzes.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Create Quiz
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search quizzes..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="subject_id" class="form-control">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Quizzes Table -->
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Questions</th>
                        <th>Status</th>
                        <th>Attempts</th>
                        <th>Time Limit</th>
                        <th>Updated</th>
                        <th width="240">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                    <tr>
                        <td><strong>{{ $quiz->title }}</strong></td>
                        <td>{{ $quiz->subject->subject_name }}</td>
                        <td>
                            <span class="badge badge-info">{{ $quiz->questions_count }} questions</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $quiz->is_published ? 'success' : 'secondary' }}">
                                {{ $quiz->is_published ? 'Published' : 'Draft' }}
                            </span>

                            @if($lesson->isScheduledForPublish())
                                <span class="badge badge-info ml-2" title="Scheduled for {{ $lesson->scheduled_publish_at->format('M d, Y h:i A') }}">
                                    <i class="fas fa-clock"></i> Scheduled
                                </span>
                            @endif
                        </td>
                        <td>{{ $quiz->attempts_count }} attempts</td>
                        <td>{{ $quiz->time_limit }} min</td>
                        <td>{{ $quiz->updated_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm d-flex flex-wrap">
                                <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('instructor.quizzes.edit', $quiz) }}" class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('instructor.quizzes.questions', $quiz) }}" class="btn btn-primary" title="Manage Questions">
                                    <i class="fas fa-tasks"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-info" 
                                        data-toggle="modal" 
                                        data-target="#scheduleModal{{ $lesson->id }}"
                                        title="Schedule Publish">
                                    <i class="fas fa-clock"></i>
                                </button>
                                <form action="{{ route('instructor.quizzes.toggle-publish', $quiz) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-{{ $quiz->is_published ? 'secondary' : 'success' }}" 
                                            title="{{ $quiz->is_published ? 'Unpublish' : 'Publish' }}">
                                        <i class="fas fa-{{ $quiz->is_published ? 'eye-slash' : 'check' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('instructor.quizzes.results', $quiz) }}" class="btn btn-info" title="View Results">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <form action="{{ route('instructor.quizzes.duplicate', $quiz) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info" title="Duplicate">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>
                                <form action="{{ route('instructor.quizzes.destroy', $quiz) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this quiz?')">
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
                        <td colspan="8" class="text-center text-muted py-4">
                            No quizzes found. <a href="{{ route('instructor.quizzes.create') }}">Create one now</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $quizzes->links() }}</div>
    </div>
</div>

@endsection