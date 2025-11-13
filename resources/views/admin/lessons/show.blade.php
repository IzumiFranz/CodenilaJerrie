@extends('layouts.admin')

@section('title', 'View Lesson')

@php
    $pageTitle = 'Lesson Details: ' . $lesson->title;
    $pageActions = '
        <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    ';
@endphp

@section('content')
    <div class="row">
        {{-- Lesson Sidebar Info --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Lesson Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 text-primary">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Subject:</strong><br>
                        <a href="{{ route('admin.subjects.show', $lesson->subject) }}">
                            {{ $lesson->subject->subject_code }} - {{ $lesson->subject->subject_name }}
                        </a>
                    </div>

                    <div class="mb-3">
                        <strong>Course:</strong><br>
                        {{ $lesson->subject->course->course_code }} - Year {{ $lesson->subject->year_level }}
                    </div>

                    <div class="mb-3">
                        <strong>Instructor:</strong><br>
                        <a href="{{ route('admin.users.show', $lesson->instructor->user) }}">
                            {{ $lesson->instructor->full_name }}
                        </a>
                        <br>
                        <small class="text-muted">{{ $lesson->instructor->employee_id }}</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        @if($lesson->is_published)
                            <span class="badge badge-success badge-lg">Published</span>
                        @else
                            <span class="badge badge-secondary badge-lg">Draft</span>
                        @endif
                    </div>

                    @if($lesson->is_published && $lesson->published_at)
                        <div class="mb-3">
                            <strong>Published:</strong><br>
                            {{ $lesson->published_at->format('F d, Y h:i A') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Order:</strong><br>
                        {{ $lesson->order }}
                    </div>

                    <div class="mb-3">
                        <strong>Views:</strong><br>
                        <span class="badge badge-info badge-lg">{{ $lesson->view_count }}</span>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $lesson->created_at->format('M d, Y h:i A') }}
                    </div>

                    @if($lesson->updated_at != $lesson->created_at)
                        <div class="mb-3">
                            <strong>Last Updated:</strong><br>
                            {{ $lesson->updated_at->format('M d, Y h:i A') }}
                        </div>
                    @endif

                    <hr>

                    @if($lesson->file_path)
                        <div class="mb-3">
                            <strong>Attachment:</strong><br>
                            <a href="{{ $lesson->file_url }}" class="btn btn-sm btn-info btn-block" target="_blank">
                                <i class="fas fa-download"></i> Download File
                            </a>
                            <small class="text-muted">Type: {{ strtoupper($lesson->file_type ?? 'Unknown') }}</small>
                        </div>
                        <hr>
                    @endif

                    <button type="button" 
                            class="btn btn-danger btn-sm btn-block" 
                            data-toggle="modal" 
                            data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Delete Lesson
                    </button>
                </div>
            </div>
        </div>

        {{-- Lesson Content --}}
        <div class="col-lg-8">
            {{-- Title Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lesson Title</h6>
                </div>
                <div class="card-body">
                    <h3 class="mb-0">{{ $lesson->title }}</h3>
                </div>
            </div>

            {{-- Content Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lesson Content</h6>
                </div>
                <div class="card-body">
                    @if($lesson->content)
                        <div class="lesson-content">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    @else
                        <p class="text-muted text-center py-5">
                            <i class="fas fa-file-alt fa-3x mb-3"></i><br>
                            No content available
                        </p>
                    @endif
                </div>
            </div>

            {{-- File Preview/Info --}}
            @if($lesson->file_path)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Attached File</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>File Type:</strong> {{ strtoupper($lesson->file_type ?? 'Unknown') }}</p>
                                <p><strong>File Path:</strong> <code>{{ basename($lesson->file_path) }}</code></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ $lesson->file_url }}" 
                                   class="btn btn-primary" 
                                   target="_blank"
                                   download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>

                        @if(in_array($lesson->file_type, ['pdf', 'image']))
                            <hr>
                            <div class="text-center">
                                @if($lesson->file_type === 'image')
                                    <img src="{{ $lesson->file_url }}" 
                                         alt="{{ $lesson->title }}" 
                                         class="img-fluid rounded"
                                         style="max-height: 500px;">
                                @elseif($lesson->file_type === 'pdf')
                                    <iframe src="{{ $lesson->file_url }}" 
                                            width="100%" 
                                            height="600px" 
                                            style="border: 1px solid #ddd;">
                                    </iframe>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Sections with this Lesson --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sections with Access</h6>
                </div>
                <div class="card-body">
                    @php
                        $assignments = $lesson->subject->assignments()
                            ->with('section.course', 'instructor')
                            ->get();
                    @endphp

                    @if($assignments->count() > 0)
                        <p class="text-muted">This lesson is accessible to students in the following sections:</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Section</th>
                                        <th>Course</th>
                                        <th>Year</th>
                                        <th>Instructor</th>
                                        <th>Academic Period</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.sections.show', $assignment->section) }}">
                                                    {{ $assignment->section->section_name }}
                                                </a>
                                            </td>
                                            <td>{{ $assignment->section->course->course_code }}</td>
                                            <td>Year {{ $assignment->section->year_level }}</td>
                                            <td>{{ $assignment->instructor->full_name }}</td>
                                            <td>{{ $assignment->semester }} {{ $assignment->academic_year }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">
                            No sections assigned to this subject yet.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Related Lessons --}}
            @php
                $relatedLessons = $lesson->subject->lessons()
                    ->where('id', '!=', $lesson->id)
                    ->where('is_published', true)
                    ->orderBy('order')
                    ->limit(5)
                    ->get();
            @endphp

            @if($relatedLessons->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Other Lessons in {{ $lesson->subject->subject_code }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($relatedLessons as $related)
                                <a href="{{ route('admin.lessons.show', $related) }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $related->title }}</h6>
                                        <small>
                                            <span class="badge badge-info">{{ $related->view_count }} views</span>
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        By {{ $related->instructor->full_name }} • 
                                        {{ $related->published_at ? $related->published_at->format('M d, Y') : 'Not published' }}
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
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
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            This lesson has an attached file that will also be deleted.
                        </div>
                    @endif
                    @if($lesson->view_count > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            This lesson has been viewed {{ $lesson->view_count }} time(s) by students.
                        </div>
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
@endsection

@push('styles')
<style>
    .lesson-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }
</style>
@endpush