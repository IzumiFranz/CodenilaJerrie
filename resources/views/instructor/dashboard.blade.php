@extends('layouts.instructor')
@section('title', 'Dashboard')
@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <div>
        <a href="{{ route('instructor.lessons.create') }}" class="btn btn-success btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm"></i> New Lesson
        </a>
        <a href="{{ route('instructor.question-bank.create') }}" class="btn btn-info btn-sm shadow-sm">
            <i class="fas fa-question fa-sm"></i> Add Question
        </a>
        <a href="{{ route('instructor.quizzes.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-clipboard-list fa-sm"></i> Create Quiz
        </a>
    </div>
</div>

<!-- Statistics Row -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lessons</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLessons }}</div>
                        <small class="text-muted">{{ $publishedLessons }} published</small>
                    </div>
                    <div class="col-auto"><i class="fas fa-book fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Quizzes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalQuizzes }}</div>
                        <small class="text-muted">{{ $publishedQuizzes }} published</small>
                    </div>
                    <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Questions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalQuestions }}</div>
                        <small class="text-muted">In question bank</small>
                    </div>
                    <div class="col-auto"><i class="fas fa-question-circle fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        <small class="text-muted">In {{ $totalSections }} sections</small>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Class Average</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ number_format($classAverage, 1) }}%
                </div>
                <small class="text-muted">Across all quizzes</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Hardest Quiz</div>
                @if($hardestQuiz)
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ Str::limit($hardestQuiz->title, 20) }}
                    </div>
                    <small class="text-muted">Avg: {{ number_format($hardestQuiz->avg_score ?? 0, 1) }}%</small>
                @else
                    <div class="text-muted">No data yet</div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Most Popular</div>
                @if($popularLesson)
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ Str::limit($popularLesson->title, 20) }}
                    </div>
                    <small class="text-muted">{{ $popularLesson->view_count }} views</small>
                @else
                    <div class="text-muted">No lessons yet</div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Need Attention</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ $strugglingStudents->count() }}
                </div>
                <small class="text-muted">Students below 50%</small>
            </div>
        </div>
    </div>
</div>

{{-- Struggling Students Alert --}}
@if($strugglingStudents->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-warning text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-exclamation-triangle mr-2"></i>Students Needing Attention
        </h6>
    </div>
    <div class="card-body">
        <div class="list-group">
            @foreach($strugglingStudents as $student)
            <a href="{{ route('instructor.student-progress.show', $student) }}" 
               class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $student->full_name }}</strong>
                        <br>
                        <small class="text-muted">{{ $student->student_number }}</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-danger badge-pill">
                            {{ number_format($student->avg_score, 1) }}%
                        </span>
                        <br>
                        <small class="text-muted">Average score</small>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Teaching Assignments -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Current Teaching Assignments</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Section</th>
                        <th>Course</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    <tr>
                        <td><strong>{{ $assignment->subject->subject_name }}</strong></td>
                        <td>{{ $assignment->section->section_name }}</td>
                        <td>{{ $assignment->section->course->course_name }}</td>
                        <td>
                            {{ \App\Models\Enrollment::where('section_id', $assignment->section_id)
                                ->where('academic_year', $currentAcademicYear)
                                ->where('semester', $currentSemester)
                                ->where('status', 'enrolled')->count() }}
                        </td>
                        <td>
                            <a href="{{ route('instructor.lessons.create', ['subject' => $assignment->subject_id]) }}" 
                               class="btn btn-sm btn-success"><i class="fas fa-book"></i> Lesson</a>
                            <a href="{{ route('instructor.quizzes.create', ['subject' => $assignment->subject_id]) }}" 
                               class="btn btn-sm btn-info"><i class="fas fa-clipboard-list"></i> Quiz</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">No teaching assignments</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Performance by Subject</h6>
    </div>
    <div class="card-body">
        <canvas id="performanceChart" height="80"></canvas>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-success">Recent Lessons</h6>
                <a href="{{ route('instructor.lessons.index') }}" class="btn btn-sm btn-success">View All</a>
            </div>
            <div class="card-body">
                @forelse($recentLessons as $lesson)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <h6 class="mb-0">{{ $lesson->title }}</h6>
                        <small class="text-muted">{{ $lesson->subject->subject_name }}</small>
                    </div>
                    <span class="badge badge-{{ $lesson->is_published ? 'success' : 'secondary' }}">
                        {{ $lesson->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center">No lessons yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Quizzes</h6>
                <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($recentQuizzes as $quiz)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <h6 class="mb-0">{{ $quiz->title }}</h6>
                        <small class="text-muted">{{ $quiz->subject->subject_name }} • {{ $quiz->attempts_count }} attempts</small>
                    </div>
                    <span class="badge badge-{{ $quiz->is_published ? 'success' : 'secondary' }}">
                        {{ $quiz->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center">No quizzes yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('performanceChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($performanceBySubject, 'subject')) !!},
        datasets: [{
            label: 'Average Score (%)',
            data: {!! json_encode(array_column($performanceBySubject, 'average')) !!},
            backgroundColor: 'rgba(28, 200, 138, 0.5)',
            borderColor: 'rgba(28, 200, 138, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Average: ' + context.parsed.y.toFixed(1) + '%';
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection