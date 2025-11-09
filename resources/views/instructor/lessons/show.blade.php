@extends('layouts.instructor')
@section('title', 'Lesson Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book mr-2"></i>Lesson Details</h1>
    <div>
        <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        @if($lesson->file_path)
            <a href="{{ route('instructor.lessons.download', $lesson) }}" class="btn btn-info">
                <i class="fas fa-download"></i> Download File
            </a>
        @endif
        <form action="{{ route('instructor.lessons.toggle-publish', $lesson) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-{{ $lesson->is_published ? 'secondary' : 'success' }}">
                <i class="fas fa-{{ $lesson->is_published ? 'eye-slash' : 'check' }}"></i>
                {{ $lesson->is_published ? 'Unpublish' : 'Publish' }}
            </button>
        </form>
        <a href="{{ route('instructor.lessons.index') }}" class="btn btn-secondary">
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
                        <strong>Attached File:</strong> {{ $lesson->file_name }}
                        <a href="{{ route('instructor.lessons.download', $lesson) }}" class="btn btn-sm btn-info float-right">
                            <i class="fas fa-download"></i> Download
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
                        <td>{{ $lesson->subject->subject_name }}</td>
                    </tr>
                    <tr>
                        <th>Course:</th>
                        <td>{{ $lesson->subject->course->course_name }}</td>
                    </tr>
                    <tr>
                        <th>Order:</th>
                        <td>{{ $lesson->order }}</td>
                    </tr>
                    <tr>
                        <th>File:</th>
                        <td>
                            @if($lesson->file_path)
                                <i class="fas fa-check-circle text-success"></i> Yes
                            @else
                                <i class="fas fa-times-circle text-danger"></i> No
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