@extends('layouts.student')

@section('title', 'Lessons')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-book-open mr-2"></i>Lessons
    </h1>
</div>

<!-- Search and Filter -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('student.lessons.index') }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="search">Search Lessons</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Search by title..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="subject_id">Filter by Subject</label>
                        <select class="form-control" id="subject_id" name="subject_id">
                            <option value="">All Subjects</option>
                            @foreach($enrolledSubjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->subject_code }} - {{ $subject->subject_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i>Search
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lessons Grid -->
<div class="row">
    @forelse($lessons as $lesson)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card lesson-card shadow h-100">
            <div class="card-header bg-gradient-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-book-open mr-2"></i>{{ Str::limit($lesson->title, 40) }}
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-2">
                    <i class="fas fa-book mr-1"></i><strong>Subject:</strong> {{ $lesson->subject->subject_name }}
                </p>
                <p class="text-muted mb-2">
                    <i class="fas fa-user mr-1"></i><strong>Instructor:</strong> {{ $lesson->instructor->full_name }}
                </p>
                <p class="text-muted mb-2">
                    <i class="fas fa-calendar mr-1"></i><strong>Published:</strong> {{ $lesson->published_at->format('M d, Y') }}
                </p>
                <p class="text-muted mb-3">
                    <i class="fas fa-eye mr-1"></i><strong>Views:</strong> {{ $lesson->view_count }}
                </p>
                
                @if($lesson->content)
                <p class="card-text text-muted small">
                    {{ Str::limit(strip_tags($lesson->content), 100) }}
                </p>
                @endif
                
                @if($lesson->hasFile())
                <div class="mt-2">
                    <span class="badge badge-info">
                        <i class="fas fa-file mr-1"></i>{{ Str::upper($lesson->file_type ?? 'File') }}
                    </span>
                </div>
                @endif
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('student.lessons.show', $lesson) }}" class="btn btn-primary btn-sm btn-block">
                    <i class="fas fa-eye mr-1"></i>View Lesson
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle mr-2"></i>No lessons available at this time.
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($lessons->hasPages())
<div class="d-flex justify-content-center">
    {{ $lessons->links() }}
</div>
@endif
@endsection