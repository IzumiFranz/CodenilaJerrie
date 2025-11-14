@extends('layouts.admin')

@section('title', 'Quizzes Management')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clipboard-check mr-2"></i>Quizzes Management</h1>
    <a href="{{ route('admin.quizzes.trashed') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-trash-restore mr-1"></i> View Trashed
    </a>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Quizzes</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.quizzes.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Quiz title..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-control">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_code }} - {{ $subject->subject_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Quizzes List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Instructor</th>
                        <th>Questions</th>
                        <th>Attempts</th>
                        <th>Time Limit</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                        <tr>
                            <td>
                                <strong>{{ $quiz->title }}</strong>
                                @if($quiz->description)
                                    <br><small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $quiz->subject->subject_code }}</span><br>
                                <small>{{ $quiz->subject->subject_name }}</small>
                            </td>
                            <td>
                                {{ $quiz->instructor->first_name }} {{ $quiz->instructor->last_name }}
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $quiz->questions_count }}</span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $quiz->attempts_count }}</span>
                            </td>
                            <td>
                                @if($quiz->time_limit)
                                    <i class="fas fa-clock text-warning"></i> {{ $quiz->time_limit }} min
                                @else
                                    <span class="text-muted">No limit</span>
                                @endif
                            </td>
                            <td>
                                @if($quiz->is_published)
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge badge-secondary">Draft</span>
                                @endif
                            </td>
                            <td>{{ $quiz->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.quizzes.results', $quiz) }}" class="btn btn-sm btn-success" title="View Results">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No quizzes found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $quizzes->links() }}
        </div>
    </div>
</div>
@endsection