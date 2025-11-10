@extends('layouts.student')

@section('title', $lesson->title)

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.lessons.index') }}">Lessons</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($lesson->title, 50) }}</li>
    </ol>
</nav>

<!-- Lesson Header -->
<div class="card shadow mb-4 border-left-primary">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-3">{{ $lesson->title }}</h2>
                <div class="mb-2">
                    <span class="badge badge-primary badge-lg mr-2">
                        <i class="fas fa-book mr-1"></i>{{ $lesson->subject->subject_code }}
                    </span>
                    <span class="badge badge-info badge-lg mr-2">
                        <i class="fas fa-graduation-cap mr-1"></i>{{ $lesson->subject->course->course_name }}
                    </span>
                    <span class="badge badge-secondary badge-lg">
                        <i class="fas fa-layer-group mr-1"></i>Year {{ $lesson->subject->year_level }}
                    </span>
                </div>
                <p class="text-muted mb-0 mt-3">
                    <i class="fas fa-user mr-2"></i>
                    <strong>Instructor:</strong> {{ $lesson->instructor->full_name }}
                </p>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar mr-2"></i>
                    <strong>Published:</strong> {{ $lesson->published_at->format('F d, Y \a\t h:i A') }}
                </p>
                <p class="text-muted mb-0">
                    <i class="fas fa-eye mr-2"></i>
                    <strong>Views:</strong> {{ $viewCount }}
                </p>
            </div>
            <div class="col-md-4 text-right">
                @if($lesson->hasFile())
                <a href="{{ route('student.lessons.download', $lesson) }}" class="btn btn-download btn-lg mb-2">
                    <i class="fas fa-download mr-2"></i>Download {{ Str::upper($lesson->file_type ?? 'File') }}
                </a>
                <p class="text-muted small mb-0">
                    <i class="fas fa-file mr-1"></i>Downloadable resource available
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Lesson Content -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-book-open mr-2"></i>Lesson Content
                </h6>
            </div>
            <div class="card-body lesson-content">
                @if($lesson->content)
                    {!! nl2br(e($lesson->content)) !!}
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        No text content available. Please download the file to view the lesson material.
                    </div>
                @endif
            </div>
        </div>

        <!-- Embedded File Preview (PDF) -->
        @if($lesson->hasFile() && $lesson->file_type === 'pdf')
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-file-pdf mr-2"></i>File Preview
                </h6>
            </div>
            <div class="card-body p-0">
                <iframe src="{{ $lesson->file_url }}" width="100%" height="600px" style="border: none;"></iframe>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('student.lessons.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Lessons
                    </a>
                    <div>
                        <a href="{{ route('student.feedback.create', ['type' => 'lesson', 'id' => $lesson->id]) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-comment-dots mr-2"></i>Give Feedback
                        </a>
                        @if($lesson->hasFile())
                        <a href="{{ route('student.lessons.download', $lesson) }}" class="btn btn-primary">
                            <i class="fas fa-download mr-2"></i>Download
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Subject Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i>Subject Information
                </h6>
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold">{{ $lesson->subject->subject_name }}</h6>
                <p class="text-muted small mb-2">{{ $lesson->subject->subject_code }}</p>
                
                @if($lesson->subject->description)
                <p class="text-muted small">{{ $lesson->subject->description }}</p>
                @endif
                
                <hr>
                
                <p class="mb-2"><strong>Course:</strong><br><span class="text-muted">{{ $lesson->subject->course->course_name }}</span></p>
                <p class="mb-2"><strong>Year Level:</strong><br><span class="text-muted">Year {{ $lesson->subject->year_level }}</span></p>
                <p class="mb-0"><strong>Units:</strong><br><span class="text-muted">{{ $lesson->subject->units }} unit(s)</span></p>
            </div>
        </div>

        <!-- Related Lessons -->
        @if($relatedLessons->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list mr-2"></i>Related Lessons
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($relatedLessons as $relatedLesson)
                    <a href="{{ route('student.lessons.show', $relatedLesson) }}" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ Str::limit($relatedLesson->title, 40) }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar mr-1"></i>{{ $relatedLesson->published_at->format('M d, Y') }}
                                </small>
                            </div>
                            @if($relatedLesson->hasFile())
                            <span class="badge badge-info"><i class="fas fa-file"></i></span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bolt mr-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('student.lessons.index', ['subject_id' => $lesson->subject_id]) }}" 
                   class="btn btn-outline-primary btn-block mb-2"><i class="fas fa-list mr-2"></i>All Lessons in This Subject</a>
                <a href="{{ route('student.quizzes.index', ['subject_id' => $lesson->subject_id]) }}" 
                   class="btn btn-outline-success btn-block mb-2"><i class="fas fa-clipboard-list mr-2"></i>View Quizzes</a>
                <button type="button" class="btn btn-outline-secondary btn-block" onclick="window.print()">
                    <i class="fas fa-print mr-2"></i>Print Lesson
                </button>
            </div>
        </div>
    </div>
</div>

@if($lesson->visibleAttachments->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-paperclip me-2"></i>
                Lesson Attachments
                <span class="badge bg-light text-dark ms-2">{{ $lesson->visibleAttachments->count() }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($lesson->visibleAttachments as $attachment)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border {{ $attachment->hasBeenDownloadedBy(auth()->id()) ? 'border-success' : '' }}">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="bi {{ $attachment->file_icon }} fs-1"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ Str::limit($attachment->original_filename, 30) }}</h6>
                                        <small class="text-muted">{{ $attachment->formatted_file_size }}</small>
                                        
                                        @if($attachment->hasBeenDownloadedBy(auth()->id()))
                                            <div class="mt-2">
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Downloaded
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($attachment->description)
                                    <p class="small text-muted mb-3">{{ $attachment->description }}</p>
                                @endif

                                <div class="d-grid gap-2">
                                    @if($attachment->isImage() || $attachment->file_extension === 'pdf')
                                        <a href="{{ route('student.lessons.attachments.view', [$lesson, $attachment]) }}" 
                                           class="btn btn-outline-primary btn-sm"
                                           target="_blank">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('student.lessons.attachments.download', [$lesson, $attachment]) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                </div>

                                <div class="mt-2 text-center">
                                    <small class="text-muted">
                                        <i class="bi bi-download me-1"></i>{{ $attachment->download_count }} downloads
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($lesson->visibleAttachments->count() > 1)
                <div class="mt-4 text-center">
                    <form action="{{ route('student.lessons.attachments.download-all', $lesson) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-cloud-arrow-down me-2"></i>
                            Download All ({{ $lesson->formatted_attachment_size }})
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
.lesson-content { font-size: 1.1rem; line-height: 1.8; color: #333; }
.lesson-content h1, h2, h3, h4, h5, h6 { margin-top:1.5rem; margin-bottom:1rem; font-weight:600; color:#36b9cc; }
.lesson-content p { margin-bottom:1rem; }
.lesson-content ul, .lesson-content ol { margin-bottom:1rem; padding-left:2rem; }
.lesson-content li { margin-bottom:0.5rem; }
.lesson-content img { max-width:100%; height:auto; border-radius:8px; box-shadow:0 0.125rem 0.25rem rgba(0,0,0,0.075); margin:1.5rem 0; }
.lesson-content blockquote { border-left:4px solid #36b9cc; padding-left:1rem; margin:1rem 0; font-style:italic; color:#6c757d; }
.lesson-content code { background-color:#f8f9fa; padding:2px 6px; border-radius:3px; font-family:'Courier New', monospace; font-size:0.9em; color:#e83e8c; }
.lesson-content pre { background-color:#f8f9fa; border:1px solid #dee2e6; border-radius:5px; padding:1rem; overflow-x:auto; margin:1rem 0; }
.lesson-content pre code { background-color:transparent; padding:0; color:#333; }
.lesson-content table { width:100%; margin-bottom:1rem; border-collapse:collapse; }
.lesson-content table th, .lesson-content table td { padding:0.75rem; border:1px solid #dee2e6; }
.lesson-content table thead th { background-color:#36b9cc; color:white; font-weight:600; }
.lesson-content a { color:#36b9cc; text-decoration:underline; }
.lesson-content a:hover { color:#2c9faf; }
@media print {
    .sidebar, .breadcrumb, .btn, .card-header, .no-print { display:none !important; }
    .col-lg-8 { width:100%; max-width:100%; }
    .card { border:none; box-shadow:none !important; }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Smooth scroll for anchor links
    $('a[href="#"]').click(function(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 'smooth');
    });

    // Study time tracking
    let viewId = null;
    let startTime = Date.now();
    let studySeconds = 0;
    let updateInterval;

    // Track initial view
    $.ajax({
        url: '{{ route("student.lessons.track-view", $lesson) }}',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if(response.success) {
                viewId = response.view_id;
                startTracking();
            }
        }
    });

    function startTracking() {
        updateInterval = setInterval(function() {
            studySeconds++;
            const minutes = Math.floor(studySeconds / 60);
            const seconds = studySeconds % 60;
            $('#studyTime').text(minutes + 'm ' + seconds + 's');

            if(studySeconds % 30 === 0) {
                updateDuration();
            }
        }, 1000);
    }

    function updateDuration() {
        if(viewId) {
            $.ajax({
                url: '{{ route("student.lessons.update-duration", $lesson) }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', view_id: viewId, duration: studySeconds }
            });
        }
    }

    $('#markCompletedBtn').click(function() {
        if(confirm('Mark this lesson as completed?')) {
            $.ajax({
                url: '{{ route("student.lessons.mark-completed", $lesson) }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if(response.success) {
                        $('#markCompletedBtn').replaceWith(
                            '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Completed</span>'
                        );
                    }
                }
            });
        }
    });

    $(window).on('beforeunload', function() {
        updateDuration();
        clearInterval(updateInterval);
    });

    document.addEventListener('visibilitychange', function() {
        if(document.hidden) {
            updateDuration();
            clearInterval(updateInterval);
        } else {
            startTracking();
        }
    });
});
</script>
@endpush
