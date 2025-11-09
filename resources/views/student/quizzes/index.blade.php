@extends('layouts.student')

@section('title', 'Quizzes')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-clipboard-list mr-2"></i>Available Quizzes
    </h1>
</div>

<!-- Search and Filter -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('student.quizzes.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Search Quizzes</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Search by title..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Filter by Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available Now</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
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

<!-- Quizzes Grid -->
<div class="row">
    @forelse($quizzes as $quiz)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card quiz-card shadow h-100 {{ !$quiz->isAvailable() ? 'border-secondary' : 'border-left-primary' }}">
            <div class="card-header {{ $quiz->isAvailable() ? 'bg-gradient-primary text-white' : 'bg-secondary text-white' }}">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-clipboard-list mr-2"></i>{{ Str::limit($quiz->title, 35) }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge badge-primary">
                        <i class="fas fa-book mr-1"></i>{{ $quiz->subject->subject_code }}
                    </span>
                    @if($quiz->isAvailable())
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle mr-1"></i>Available
                        </span>
                    @elseif($quiz->available_from && $quiz->available_from->isFuture())
                        <span class="badge badge-warning">
                            <i class="fas fa-clock mr-1"></i>Upcoming
                        </span>
                    @else
                        <span class="badge badge-danger">
                            <i class="fas fa-times-circle mr-1"></i>Expired
                        </span>
                    @endif
                </div>

                <p class="text-muted mb-2 small">
                    <i class="fas fa-user mr-1"></i><strong>Instructor:</strong> {{ $quiz->instructor->full_name }}
                </p>

                <hr>

                <div class="quiz-info">
                    <p class="mb-2">
                        <i class="fas fa-question-circle text-primary mr-2"></i>
                        <strong>Questions:</strong> {{ $quiz->questions_count }}
                    </p>
                    @if($quiz->time_limit)
                    <p class="mb-2">
                        <i class="fas fa-clock text-warning mr-2"></i>
                        <strong>Time Limit:</strong> {{ $quiz->time_limit }} minutes
                    </p>
                    @endif
                    <p class="mb-2">
                        <i class="fas fa-trophy text-success mr-2"></i>
                        <strong>Passing Score:</strong> {{ $quiz->passing_score }}%
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-redo text-info mr-2"></i>
                        <strong>Max Attempts:</strong> {{ $quiz->max_attempts }}
                    </p>
                </div>

                <hr>

                <!-- Student Attempts Info -->
                <div class="attempts-info">
                    @if($quiz->student_attempts > 0)
                    <p class="mb-2">
                        <i class="fas fa-history text-secondary mr-2"></i>
                        <strong>Your Attempts:</strong> {{ $quiz->student_attempts }} / {{ $quiz->max_attempts }}
                    </p>
                    @if($quiz->best_score !== null)
                    <p class="mb-2">
                        <i class="fas fa-star text-warning mr-2"></i>
                        <strong>Best Score:</strong> 
                        <span class="badge badge-{{ $quiz->best_score >= $quiz->passing_score ? 'success' : 'danger' }}">
                            {{ number_format($quiz->best_score, 1) }}%
                        </span>
                    </p>
                    @endif
                    @else
                    <p class="text-muted small mb-2">
                        <i class="fas fa-info-circle mr-1"></i>You haven't attempted this quiz yet
                    </p>
                    @endif
                </div>

                <!-- Availability Info -->
                @if($quiz->available_from || $quiz->available_until)
                <hr>
                <div class="availability-info">
                    @if($quiz->available_from)
                    <p class="mb-1 small text-muted">
                        <i class="far fa-calendar-plus mr-1"></i>
                        <strong>Available from:</strong><br>
                        {{ $quiz->available_from->format('M d, Y h:i A') }}
                    </p>
                    @endif
                    @if($quiz->available_until)
                    <p class="mb-1 small text-muted">
                        <i class="far fa-calendar-times mr-1"></i>
                        <strong>Available until:</strong><br>
                        {{ $quiz->available_until->format('M d, Y h:i A') }}
                        @if($quiz->available_until->isFuture() && $quiz->isAvailable())
                        <br><span class="text-danger">({{ $quiz->available_until->diffForHumans() }})</span>
                        @endif
                    </p>
                    @endif
                </div>
                @endif
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('student.quizzes.show', $quiz) }}" 
                   class="btn btn-{{ $quiz->can_take ? 'primary' : 'secondary' }} btn-sm btn-block">
                    <i class="fas fa-{{ $quiz->can_take ? 'play' : 'eye' }} mr-1"></i>
                    {{ $quiz->can_take ? 'Take Quiz' : 'View Details' }}
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle mr-2"></i>
            No quizzes available at this time. Check back later!
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($quizzes->hasPages())
<div class="d-flex justify-content-center">
    {{ $quizzes->links() }}
</div>
@endif

<!-- Legend -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-info-circle mr-2"></i>Status Legend
                </h6>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <span class="badge badge-success mr-2">Available</span>
                        Quiz is currently available to take
                    </div>
                    <div class="col-md-4 mb-2">
                        <span class="badge badge-warning mr-2">Upcoming</span>
                        Quiz will be available in the future
                    </div>
                    <div class="col-md-4 mb-2">
                        <span class="badge badge-danger mr-2">Expired</span>
                        Quiz deadline has passed
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#subject_id, #status').change(function() {
        $(this).closest('form').submit();
    });
    
    // Add hover effect
    $('.quiz-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );
});
</script>
@endpush