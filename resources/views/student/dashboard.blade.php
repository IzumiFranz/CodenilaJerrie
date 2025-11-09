@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
    </h1>
    <div class="d-none d-sm-inline-block">
        <span class="text-gray-600">Welcome back, <strong>{{ $student->full_name }}</strong>!</span>
    </div>
</div>

<!-- Student Info -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Student Information</div>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <strong>Student Number:</strong> {{ $student->student_number }}
                            </div>
                            <div class="col-md-3">
                                <strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Year Level:</strong> {{ $student->year_level }}
                            </div>
                            <div class="col-md-3">
                                <strong>Academic Year:</strong> {{ $currentAcademicYear }} - {{ $currentSemester }} Semester
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards Row -->
<div class="row">
    <!-- Enrolled Subjects Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Enrolled Subjects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSubjects }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Quizzes Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Available Quizzes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $availableQuizzes }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Attempts Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Quiz Attempts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAttempts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Score Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Average Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageScore, 1) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pending Quizzes -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-exclamation-circle mr-2"></i>Quizzes Needing Attention
                </h6>
            </div>
            <div class="card-body">
                @forelse($pendingQuizzes as $quiz)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                <a href="{{ route('student.quizzes.show', $quiz) }}" class="text-decoration-none">
                                    {{ $quiz->title }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-book mr-1"></i>{{ $quiz->subject->subject_name }}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-user mr-1"></i>{{ $quiz->instructor->full_name }}
                            </small>
                        </div>
                        <div class="text-right">
                            @if($quiz->available_until)
                            <span class="badge badge-warning">
                                <i class="far fa-clock mr-1"></i>Due {{ $quiz->available_until->diffForHumans() }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center mb-0">
                    <i class="fas fa-check-circle mr-2"></i>No pending quizzes. Great job!
                </p>
                @endforelse
                
                @if($pendingQuizzes->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('student.quizzes.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-right mr-1"></i>View All Quizzes
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Lessons -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-book-open mr-2"></i>Recently Published Lessons
                </h6>
            </div>
            <div class="card-body">
                @forelse($recentLessons as $lesson)
                <div class="mb-3 pb-3 border-bottom">
                    <h6 class="mb-1">
                        <a href="{{ route('student.lessons.show', $lesson) }}" class="text-decoration-none">
                            {{ $lesson->title }}
                        </a>
                    </h6>
                    <small class="text-muted">
                        <i class="fas fa-book mr-1"></i>{{ $lesson->subject->subject_name }}
                    </small>
                    <br>
                    <small class="text-muted">
                        <i class="fas fa-clock mr-1"></i>Published {{ $lesson->published_at->diffForHumans() }}
                    </small>
                </div>
                @empty
                <p class="text-muted text-center mb-0">No recent lessons available.</p>
                @endforelse
                
                @if($recentLessons->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('student.lessons.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-right mr-1"></i>View All Lessons
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Quiz Attempts -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i>Recent Quiz Attempts
                </h6>
            </div>
            <div class="card-body">
                @forelse($recentAttempts as $attempt)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $attempt->quiz->title }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-book mr-1"></i>{{ $attempt->quiz->subject->subject_name }}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-1"></i>{{ $attempt->completed_at->format('M d, Y h:i A') }}
                            </small>
                        </div>
                        <div class="text-right">
                            <h4 class="mb-0">
                                <span class="badge badge-{{ $attempt->isPassed() ? 'success' : 'danger' }}">
                                    {{ number_format($attempt->percentage, 1) }}%
                                </span>
                            </h4>
                            <small class="text-muted">
                                {{ $attempt->score }}/{{ $attempt->total_points }}
                            </small>
                        </div>
                    </div>
                    @if($attempt->quiz->show_results)
                    <div class="mt-2">
                        <a href="{{ route('student.quiz-attempts.results', $attempt) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-chart-bar mr-1"></i>View Results
                        </a>
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-muted text-center mb-0">No quiz attempts yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Performance by Subject -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar mr-2"></i>Performance by Subject
                </h6>
            </div>
            <div class="card-body">
                @forelse($performanceBySubject as $performance)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="font-weight-bold">{{ $performance->subject_name }}</span>
                        <span class="text-muted">{{ number_format($performance->avg_score, 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-{{ $performance->avg_score >= 75 ? 'success' : ($performance->avg_score >= 60 ? 'warning' : 'danger') }}" 
                             role="progressbar" 
                             style="width: {{ $performance->avg_score }}%"
                             aria-valuenow="{{ $performance->avg_score }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($performance->avg_score, 1) }}%
                        </div>
                    </div>
                    <small class="text-muted">{{ $performance->attempts_count }} attempt(s)</small>
                </div>
                @empty
                <p class="text-muted text-center mb-0">No performance data available yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Enrolled Subjects -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-book mr-2"></i>Enrolled Subjects ({{ $currentSemester }} Semester, {{ $currentAcademicYear }})
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Section</th>
                                <th>Units</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrolledSubjects as $subject)
                            <tr>
                                <td>{{ $subject->subject_code }}</td>
                                <td>{{ $subject->subject_name }}</td>
                                <td>
                                    @php
                                        $section = $activeEnrollments->first(function($enrollment) use ($subject) {
                                            return $enrollment->section->subjects->contains($subject->id);
                                        })->section ?? null;
                                    @endphp
                                    {{ $section ? $section->full_name : 'N/A' }}
                                </td>
                                <td>{{ $subject->units }}</td>
                                <td>
                                    <a href="{{ route('student.lessons.index', ['subject_id' => $subject->id]) }}" class="btn btn-sm btn-primary" title="View Lessons">
                                        <i class="fas fa-book-open"></i>
                                    </a>
                                    <a href="{{ route('student.quizzes.index', ['subject_id' => $subject->id]) }}" class="btn btn-sm btn-success" title="View Quizzes">
                                        <i class="fas fa-clipboard-list"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No enrolled subjects for this semester.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
    // Auto-hide flash messages after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush