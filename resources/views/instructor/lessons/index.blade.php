@extends('layouts.instructor')
@section('title', 'Lessons')
@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book mr-2"></i>Lessons</h1>
    <a href="{{ route('instructor.lessons.create') }}" class="btn btn-success btn-sm">
        <i class="fas fa-plus mr-1"></i> Create Lesson
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search lessons..." 
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

<!-- Lessons Table -->
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>File</th>
                        <th>Updated</th>
                        <th width="220">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lessons as $lesson)
                    <tr>
                        <td><strong>{{ $lesson->title }}</strong></td>
                        <td>{{ $lesson->subject->subject_name }}</td>
                        <td>
                            <span class="badge badge-{{ $lesson->is_published ? 'success' : 'secondary' }}">
                                {{ $lesson->is_published ? 'Published' : 'Draft' }}
                            </span>

                            @if($lesson->isScheduledForPublish())
                                <span class="badge badge-info ml-2" title="Scheduled for {{ $lesson->scheduled_publish_at->format('M d, Y h:i A') }}">
                                    <i class="fas fa-clock"></i> Scheduled
                                </span>
                            @endif
                            
                        </td>
                        <td>{{ $lesson->order }}</td>
                        <td>
                            @if($lesson->file_path)
                                <a href="{{ route('instructor.lessons.download', $lesson) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $lesson->updated_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('instructor.lessons.show', $lesson) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <button type="button" 
                                                class="dropdown-item" 
                                                data-toggle="modal" 
                                                data-target="#scheduleModal{{ $lesson->id }}">
                                            <i class="fas fa-clock text-info"></i> Schedule
                                        </button>
                                        <form action="{{ route('instructor.lessons.toggle-publish', $lesson) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-{{ $lesson->is_published ? 'eye-slash' : 'check' }} text-{{ $lesson->is_published ? 'secondary' : 'success' }}"></i> 
                                                {{ $lesson->is_published ? 'Unpublish' : 'Publish' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('instructor.lessons.duplicate', $lesson) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-copy text-info"></i> Duplicate
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('instructor.lessons.destroy', $lesson) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this lesson?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No lessons found. <a href="{{ route('instructor.lessons.create') }}">Create one now</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $lessons->links() }}</div>
    </div>
</div>

@endsection