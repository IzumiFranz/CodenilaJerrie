@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@php
    $pageTitle = 'Analytics Dashboard';
    $pageActions = '
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print"></i> Print Report
        </button>
        <a href="' . route('admin.export.analytics-report') . '?academic_year=' . $academicYear . '&semester=' . $semester . '" class="btn btn-success">
            <i class="fas fa-download"></i> Export CSV
        </a>';
@endphp

@section('content')
<!-- Filters -->
<div class="card shadow mb-4 no-print">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.analytics.dashboard') }}" class="row">
            <div class="col-md-4">
                <label>Academic Year</label>
                <select name="academic_year" class="form-control">
                    @foreach($academicYears as $year)
                        <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Semester</label>
                <select name="semester" class="form-control">
                    <option value="1st" {{ $semester == '1st' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd" {{ $semester == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                    <option value="summer" {{ $semester == 'summer' ? 'selected' : '' }}>Summer</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Overview Statistics -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Enrollments</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEnrollments) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Quiz Completion Rate</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($quizCompletionRate, 1) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Quiz Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageQuizScore, 1) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Courses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCourses }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Enrollment Trend -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Enrollment Trend (Last 6 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Enrollment by Course -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Enrollment by Course</h6>
            </div>
            <div class="card-body">
                <canvas id="courseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Performance Charts -->
<div class="row">
    <!-- Quiz Performance Distribution -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quiz Performance Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- User Activity -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Activity (Last 7 Days)</h6>
            </div>
            <div class="card-body">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Performers -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Performing Students</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Average Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topStudents as $index => $student)
                                <tr>
                                    <td>
                                        @if($index == 0)
                                            <i class="fas fa-trophy text-warning"></i> {{ $index + 1 }}
                                        @elseif($index == 1)
                                            <i class="fas fa-medal text-secondary"></i> {{ $index + 1 }}
                                        @elseif($index == 2)
                                            <i class="fas fa-medal text-warning"></i> {{ $index + 1 }}
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                        <br><small class="text-muted">{{ $student->student_number }}</small>
                                    </td>
                                    <td>{{ $student->course->course_code }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" style="width: {{ $student->average_score }}%">
                                                {{ number_format($student->average_score, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Performance Summary -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Course Performance Summary</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Enrollments</th>
                                <th>Avg. Score</th>
                                <th>Pass Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coursePerformance as $course)
                                <tr>
                                    <td>
                                        <strong>{{ $course->course_code }}</strong>
                                        <br><small>{{ $course->course_name }}</small>
                                    </td>
                                    <td>{{ $course->enrollment_count }}</td>
                                    <td>
                                        <span class="badge badge-{{ $course->average_score >= 75 ? 'success' : ($course->average_score >= 60 ? 'warning' : 'danger') }}">
                                            {{ number_format($course->average_score, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" style="width: {{ $course->pass_rate }}%">
                                                {{ number_format($course->pass_rate, 0) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Enrollment Trend Chart
const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
new Chart(enrollmentCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($enrollmentTrend->pluck('month')) !!},
        datasets: [{
            label: 'Enrollments',
            data: {!! json_encode($enrollmentTrend->pluck('count')) !!},
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Course Pie Chart
const courseCtx = document.getElementById('courseChart').getContext('2d');
new Chart(courseCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($enrollmentByCourse->pluck('course_name')) !!},
        datasets: [{
            data: {!! json_encode($enrollmentByCourse->pluck('enrollment_count')) !!},
            backgroundColor: [
                'rgb(78, 115, 223)',
                'rgb(28, 200, 138)',
                'rgb(54, 185, 204)',
                'rgb(246, 194, 62)',
                'rgb(231, 74, 59)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Performance Distribution Chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
new Chart(performanceCtx, {
    type: 'bar',
    data: {
        labels: ['0-50%', '51-60%', '61-70%', '71-80%', '81-90%', '91-100%'],
        datasets: [{
            label: 'Number of Students',
            data: {!! json_encode($performanceDistribution) !!},
            backgroundColor: [
                'rgba(231, 74, 59, 0.8)',
                'rgba(246, 194, 62, 0.8)',
                'rgba(54, 185, 204, 0.8)',
                'rgba(78, 115, 223, 0.8)',
                'rgba(28, 200, 138, 0.8)',
                'rgba(0, 150, 136, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Activity Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
new Chart(activityCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($activityData->pluck('date')) !!},
        datasets: [{
            label: 'Active Users',
            data: {!! json_encode($activityData->pluck('count')) !!},
            backgroundColor: 'rgba(78, 115, 223, 0.8)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .no-print, .sidebar, .topbar, .navbar, .page-actions {
        display: none !important;
    }
    .card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
    body {
        font-size: 12px;
    }
    .card-header {
        background-color: #4e73df !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endpush