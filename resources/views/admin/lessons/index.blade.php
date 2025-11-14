@extends('layouts.admin')

@section('title', 'Lessons Management')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-alt mr-2"></i>Lessons Management</h1>
    <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-sync mr-1"></i> Refresh
    </a>
</div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filters</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.lessons.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search">Search</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               class="form-control" 
                               value="{{ request('search') }}"
                               placeholder="Lesson title...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="subject_id">Subject</label>
                        <select name="subject_id" id="subject_id" class="form-control">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_code }} - {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Lessons List</h6>
            <span class="text-muted">Total: {{ $lessons->total() }} lessons</span>
        </div>
        <div class="card-body">
            @if($lessons->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Instructor</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lessons as $lesson)
                                <tr>
                                    <td>{{ $lesson->id }}</td>
                                    <td>
                                        <strong>{{ $lesson->title }}</strong>
                                        @if($lesson->file_path)
                                            <br><small class="text-muted">
                                                <i class="fas fa-paperclip"></i> Has attachment
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subjects.show', $lesson->subject) }}">
                                            {{ $lesson->subject->subject_code }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $lesson->subject->subject_name }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $lesson->instructor->user) }}">
                                            {{ $lesson->instructor->full_name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($lesson->is_published)
                                            <span class="badge badge-success">Published</span>
                                            @if($lesson->published_at)
                                                <br><small class="text-muted">{{ $lesson->published_at->format('M d, Y') }}</small>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $lesson->view_count }}</span>
                                    </td>
                                    <td>{{ $lesson->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.lessons.show', $lesson) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                data-toggle="modal" 
                                                data-target="#deleteModal{{ $lesson->id }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        {{-- Delete Modal --}}
                                        <div class="modal fade" id="deleteModal{{ $lesson->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete this lesson?</p>
                                                        <p><strong>{{ $lesson->title }}</strong></p>
                                                        @if($lesson->file_path)
                                                            <p class="text-warning">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                This lesson has an attached file that will also be deleted.
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete Lesson</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $lessons->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No lessons found</p>
                    @if(request()->hasAny(['search', 'subject_id', 'status']))
                        <a href="{{ route('admin.lessons.index') }}" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Lessons</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLessons }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Published</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $publishedLessons }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Drafts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLessons - $publishedLessons }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Views</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lessons->sum('view_count') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection