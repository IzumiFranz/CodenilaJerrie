@extends('layouts.student')

@section('title', $lesson->title)

@section('content')
<div class="container-fluid px-4">
    <!-- Header & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('student.lessons.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to Lessons
            </a>
            <h1 class="h3 mb-0">{{ $lesson->title }}</h1>
        </div>

        <!-- Download Dropdown -->
        @if($lesson->visibleAttachments->isNotEmpty())
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                <i class="fas fa-download mr-1"></i> Download Files
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                @foreach($lesson->visibleAttachments as $attachment)
                <a class="dropdown-item" href="{{ route('student.lessons.attachments.download', [$lesson, $attachment]) }}">
                    <i class="fas fa-file mr-2"></i>{{ Str::limit($attachment->original_filename, 40) }}
                </a>
                @endforeach
                @if($lesson->visibleAttachments->count() > 1)
                <div class="dropdown-divider"></div>
                <form action="{{ route('student.lessons.attachments.download-all', $lesson) }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item text-success">
                        <i class="fas fa-cloud-download-alt mr-2"></i>Download All
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Lesson Info -->
            <div class="card shadow-sm mb-4 border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $lesson->instructor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($lesson->instructor->name) }}"
                             alt="{{ $lesson->instructor->name }}"
                             class="rounded-circle mr-3"
                             width="50" height="50">
                        <div>
                            <h6 class="mb-0">{{ $lesson->instructor->name }}</h6>
                            <small class="text-muted">Instructor</small>
                        </div>
                        <div class="ml-auto">
                            <span class="badge badge-{{ $lesson->status === 'published' ? 'success' : 'warning' }}">
                                {{ ucfirst($lesson->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row text-center border-top pt-3 mb-3">
                        <div class="col-4">
                            <i class="fas fa-book-reader text-primary fa-lg"></i>
                            <p class="mb-0 mt-2"><small class="text-muted">Subject</small></p>
                            <p class="mb-0"><strong>{{ $lesson->subject->subject_code }}</strong></p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-layer-group text-success fa-lg"></i>
                            <p class="mb-0 mt-2"><small class="text-muted">Course</small></p>
                            <p class="mb-0"><strong>{{ $lesson->subject->course->course_name }}</strong></p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-calendar text-info fa-lg"></i>
                            <p class="mb-0 mt-2"><small class="text-muted">Published</small></p>
                            <p class="mb-0"><strong>{{ $lesson->published_at->format('M d, Y') }}</strong></p>
                        </div>
                    </div>

                    @if($lesson->description)
                    <div class="border-top pt-3">
                        <h5 class="mb-3">Description</h5>
                        <div class="text-muted">{!! nl2br(e($lesson->description)) !!}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Lesson Content -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt mr-2 text-primary"></i>Lesson Content</h5>
                </div>
                <div class="card-body lesson-content">
                    @if($lesson->content)
                        {!! nl2br(e($lesson->content)) !!}
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>No text content available.
                        </div>
                    @endif
                </div>
            </div>

            <!-- PDF Preview -->
            @if($lesson->hasFile() && $lesson->file_type === 'pdf')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-file-pdf mr-2 text-danger"></i>File Preview</h5>
                </div>
                <div class="card-body p-0">
                    <iframe src="{{ $lesson->file_url }}" width="100%" height="600px" style="border: none;"></iframe>
                </div>
            </div>
            @endif

            <!-- Related Lessons -->
            @if($relatedLessons->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list mr-2 text-info"></i>Related Lessons</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($relatedLessons as $relatedLesson)
                    <a href="{{ route('student.lessons.show', $relatedLesson) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ Str::limit($relatedLesson->title, 40) }}</strong>
                                <small class="text-muted d-block">
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
            @endif

            <!-- Action Buttons -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('student.lessons.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                    <div>
                        <button id="markCompletedBtn" class="btn btn-success mr-2">
                            <i class="fas fa-check-circle mr-2"></i>Mark Completed
                        </button>
                        <button class="btn btn-outline-info mr-2" onclick="shareLesson()">
                            <i class="fas fa-share-alt mr-2"></i>Share
                        </button>
                        <a href="{{ route('student.feedback.create', ['type' => 'lesson', 'id' => $lesson->id]) }}" class="btn btn-outline-warning">
                            <i class="fas fa-comment mr-2"></i>Feedback
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Attachments -->
            @if($lesson->visibleAttachments->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-paperclip mr-2 text-warning"></i>Attachments</h5>
                </div>
                <div class="card-body">
                    @foreach($lesson->visibleAttachments as $attachment)
                    <div class="mb-3 p-2 border rounded {{ $attachment->hasBeenDownloadedBy(auth()->id()) ? 'border-success' : 'border-secondary' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file mr-2 text-primary"></i>{{ Str::limit($attachment->original_filename, 30) }}
                            </div>
                            <small class="text-muted">{{ $attachment->formatted_file_size }}</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                @if($attachment->isImage() || $attachment->file_extension === 'pdf')
                                    <a href="{{ route('student.lessons.attachments.view', [$lesson, $attachment]) }}" target="_blank" class="btn btn-outline-primary btn-sm mr-2">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                @endif
                                <a href="{{ route('student.lessons.attachments.download', [$lesson, $attachment]) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                            <small class="text-muted"><i class="fas fa-download mr-1"></i>{{ $attachment->download_count }} downloads</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quiz Progress -->
            @if($lesson->quizzes->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list mr-2 text-success"></i>Related Quizzes</h5>
                </div>
                <div class="card-body">
                    @php
                        $completedQuizzes = $lesson->quizzes->filter(function($quiz) {
                            return $quiz->attempts->where('user_id', auth()->id())->where('status', 'completed')->count() > 0;
                        })->count();
                        $totalQuizzes = $lesson->quizzes->count();
                        $progressPercent = $totalQuizzes > 0 ? round(($completedQuizzes / $totalQuizzes) * 100) : 0;
                    @endphp
                    <div class="text-center mb-3">
                        <div class="display-4 text-primary">{{ $progressPercent }}%</div>
                        <p class="text-muted mb-0">Completion Rate</p>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($lesson->quizzes as $quiz)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $quiz->title }}</span>
                            <a href="{{ route('student.quizzes.show', $quiz) }}" class="btn btn-sm btn-outline-success">Take</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
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
@media print {
    .btn, .card-header, nav, .sidebar, .dropdown { display:none !important; }
    .card { border:none; box-shadow:none !important; }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let viewId = null;
    let studySeconds = 0;
    let updateInterval;

    // Track lesson view
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
        updateInterval = setInterval(() => {
            studySeconds++;
            if(studySeconds % 30 === 0) updateDuration();
        }, 1000);
    }

    function updateDuration() {
        if(viewId) {
            $.post('{{ route("student.lessons.update-duration", $lesson) }}', {
                _token: '{{ csrf_token() }}',
                view_id: viewId,
                duration: studySeconds
            });
        }
    }

    $('#markCompletedBtn').click(function() {
        if(confirm('Mark this lesson as completed?')) {
            $.post('{{ route("student.lessons.mark-completed", $lesson) }}', {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if(response.success) {
                    $('#markCompletedBtn').replaceWith('<span class="badge badge-success"><i class="fas fa-check-circle"></i> Completed</span>');
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

function shareLesson() {
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({ title: '{{ $lesson->title }}', url: url });
    } else {
        navigator.clipboard.writeText(url).then(() => alert('Lesson link copied to clipboard!'));
    }
}
</script>
@endpush
