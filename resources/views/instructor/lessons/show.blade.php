@extends('layouts.instructor')
@section('title', 'Lesson Details')
@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book mr-2"></i>Lesson Details</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-cog"></i> More
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                @if($lesson->file_path)
                    <a href="{{ route('instructor.lessons.download', $lesson) }}" class="dropdown-item">
                        <i class="fas fa-download text-info"></i> Download File
                    </a>
                @endif
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
        <a href="{{ route('instructor.lessons.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Lesson Content</h6>
            </div>
            <div class="card-body">
                <h3>{{ $lesson->title }}</h3>
                <p class="text-muted">
                    <i class="fas fa-book mr-2"></i>{{ $lesson->subject->subject_name }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-graduation-cap mr-2"></i>{{ $lesson->subject->course->course_name }}
                </p>

                <hr>

                <div class="lesson-content">
                    {!! $lesson->content !!}
                </div>

                @if($lesson->file_path)
                    <hr>
                    <div class="alert alert-info">
                        <i class="fas fa-paperclip mr-2"></i>
                        <strong>Legacy Attached File:</strong> {{ $lesson->file_name }}
                        <a href="{{ route('instructor.lessons.download', $lesson) }}" class="btn btn-sm btn-info float-right">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                @endif

                @if($lesson->attachments && $lesson->attachments->count() > 0)
                    <hr>
                    <h5 class="mb-3"><i class="fas fa-paperclip mr-2"></i>Attachments</h5>
                    <div class="list-group">
                        @foreach($lesson->attachments as $attachment)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file mr-2 text-primary"></i>
                                    <strong>{{ $attachment->original_filename }}</strong>
                                    @if($attachment->description)
                                        <br><small class="text-muted">{{ $attachment->description }}</small>
                                    @endif
                                    <br><small class="text-muted">
                                        <i class="fas fa-download mr-1"></i>{{ $attachment->download_count }} downloads
                                        <span class="mx-2">•</span>
                                        {{ $attachment->formatted_file_size }}
                                    </small>
                                </div>
                                <div>
                                    @if($attachment->isImage() || $attachment->file_extension === 'pdf')
                                        <a href="{{ route('instructor.lessons.attachments.view', [$lesson, $attachment]) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary mr-2">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    <a href="{{ route('instructor.lessons.attachments.download', [$lesson, $attachment]) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('instructor.lessons.attachments', $lesson) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-cog mr-1"></i> Manage Attachments
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Lesson Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge badge-{{ $lesson->is_published ? 'success' : 'secondary' }}">
                                {{ $lesson->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Subject:</th>
                        <td>{{ $lesson->subject->subject_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Course:</th>
                        <td>{{ $lesson->subject->course->course_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Order:</th>
                        <td>{{ $lesson->order }}</td>
                    </tr>
                    <tr>
                        <th>Legacy File:</th>
                        <td>
                            @if($lesson->file_path)
                                <i class="fas fa-check-circle text-success"></i> Yes
                            @else
                                <i class="fas fa-times-circle text-danger"></i> No
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Attachments:</th>
                        <td>
                            @if($lesson->attachments && $lesson->attachments->count() > 0)
                                <i class="fas fa-check-circle text-success"></i> {{ $lesson->attachments->count() }} file(s)
                            @else
                                <i class="fas fa-times-circle text-danger"></i> None
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $lesson->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>{{ $lesson->updated_at->format('M d, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Quick Actions</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('instructor.lessons.duplicate', $lesson) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-copy mr-2"></i>Duplicate Lesson
                    </button>
                </form>
                
                <form action="{{ route('instructor.lessons.destroy', $lesson) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this lesson?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-trash mr-2"></i>Delete Lesson
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection