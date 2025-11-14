@extends('layouts.instructor')

@section('title', 'Student Performance Alerts')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-exclamation-triangle mr-2"></i>Student Performance Alerts
    </h1>
    <a href="{{ route('instructor.student-progress.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to Student Progress
    </a>
</div>

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Failing Students
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $alerts['failing_students']->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-times fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Multiple Failures
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $alerts['multiple_failures']->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-redo fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            No Attempts
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $alerts['no_attempts']->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Improved Students
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $alerts['improved_students']->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Failing Students --}}
@if($alerts['failing_students']->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-danger text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-user-times mr-2"></i>
            Students Currently Failing ({{ $alerts['failing_students']->count() }})
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Recent Failures</th>
                        <th>Total Failures</th>
                        <th>Average Score</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts['failing_students'] as $alert)
                        <tr>
                            <td>
                                <strong>{{ $alert['student']->full_name }}</strong><br>
                                <small class="text-muted">{{ $alert['student']->student_number }}</small>
                            </td>
                            <td>
                                @foreach($alert['recent_failures'] as $attempt)
                                    <div class="mb-1">
                                        <small>
                                            <strong>{{ $attempt->quiz->title }}</strong>:
                                            <span class="text-danger">{{ number_format($attempt->percentage, 1) }}%</span>
                                        </small>
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge badge-danger badge-pill">
                                    {{ $alert['failure_count'] }}
                                </span>
                            </td>
                            <td>
                                <span class="text-danger font-weight-bold">
                                    {{ number_format($alert['average_score'], 1) }}%
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('instructor.student-progress.show', $alert['student']) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Multiple Failures --}}
@if($alerts['multiple_failures']->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-warning text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-redo mr-2"></i>
            Students with Multiple Failed Attempts ({{ $alerts['multiple_failures']->count() }})
        </h6>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <i class="fas fa-info-circle mr-2"></i>
            These students have failed the same quiz multiple times. They may need additional support or review materials.
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Quiz</th>
                        <th>Failed Attempts</th>
                        <th>Average Score</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts['multiple_failures'] as $alert)
                        <tr>
                            <td>
                                <strong>{{ $alert['student']->full_name }}</strong><br>
                                <small class="text-muted">{{ $alert['student']->student_number }}</small>
                            </td>
                            <td>{{ $alert['quiz']->title }}</td>
                            <td>
                                <span class="badge badge-warning badge-pill">
                                    {{ $alert['attempt_count'] }} attempts
                                </span>
                            </td>
                            <td>
                                <span class="text-warning font-weight-bold">
                                    {{ number_format($alert['average_score'], 1) }}%
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('instructor.student-progress.show', $alert['student']) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('instructor.quizzes.show', $alert['quiz']) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-clipboard-list"></i> Quiz
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- No Attempts --}}
@if($alerts['no_attempts']->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-info text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-clock mr-2"></i>
            Students Not Taking Quizzes ({{ $alerts['no_attempts']->count() }})
        </h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            These students haven't attempted any quizzes that have been published for 3+ days.
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Missed Quizzes</th>
                        <th>Total Missed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts['no_attempts'] as $alert)
                        <tr>
                            <td>
                                <strong>{{ $alert['student']->full_name }}</strong><br>
                                <small class="text-muted">{{ $alert['student']->student_number }}</small>
                            </td>
                            <td>
                                @foreach($alert['missed_quizzes']->take(3) as $quiz)
                                    <div class="mb-1">
                                        <small>
                                            <i class="fas fa-clipboard-list text-muted mr-1"></i>
                                            {{ $quiz->title }}
                                        </small>
                                    </div>
                                @endforeach
                                @if($alert['missed_quizzes']->count() > 3)
                                    <small class="text-muted">
                                        +{{ $alert['missed_quizzes']->count() - 3 }} more
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info badge-pill">
                                    {{ $alert['count'] }} quizzes
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('instructor.student-progress.show', $alert['student']) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Improved Students (Positive) --}}
@if($alerts['improved_students']->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-success text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-chart-line mr-2"></i>
            Students Showing Improvement ({{ $alerts['improved_students']->count() }})
        </h6>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <i class="fas fa-thumbs-up mr-2"></i>
            Great news! These students have shown significant improvement (20%+ score increase) this week!
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Score Progress</th>
                        <th>Improvement</th>
                        <th>Attempts</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts['improved_students'] as $alert)
                        <tr>
                            <td>
                                <strong>{{ $alert['student']->full_name }}</strong><br>
                                <small class="text-muted">{{ $alert['student']->student_number }}</small>
                            </td>
                            <td>
                                <span class="text-danger">{{ number_format($alert['from_score'], 1) }}%</span>
                                <i class="fas fa-arrow-right mx-2"></i>
                                <span class="text-success font-weight-bold">{{ number_format($alert['to_score'], 1) }}%</span>
                            </td>
                            <td>
                                <span class="badge badge-success badge-pill">
                                    +{{ number_format($alert['improvement'], 1) }}%
                                </span>
                            </td>
                            <td>{{ $alert['attempts_count'] }} attempts</td>
                            <td>
                                <a href="{{ route('instructor.student-progress.show', $alert['student']) }}" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-trophy"></i> View Progress
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- No Alerts --}}
@if($alerts['failing_students']->count() === 0 && 
    $alerts['multiple_failures']->count() === 0 && 
    $alerts['no_attempts']->count() === 0 && 
    $alerts['improved_students']->count() === 0)
<div class="card shadow">
    <div class="card-body text-center py-5">
        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
        <h4>No Performance Alerts</h4>
        <p class="text-muted">
            All your students are performing well! There are no alerts at this time.
        </p>
        <a href="{{ route('instructor.student-progress.index') }}" class="btn btn-primary">
            <i class="fas fa-users"></i> View All Students
        </a>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh alerts every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
});
</script>
@endpush