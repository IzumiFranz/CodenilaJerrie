@extends('layouts.instructor')
@section('title', 'Student Progress Details')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-graduate mr-2"></i>{{ $student->full_name }}
    </h1>
    <a href="{{ route('instructor.student-progress.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>

<!-- Student Info Card -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 text-center">
                <img src="{{ $student->user->profile_picture ? asset('storage/' . $student->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&size=150' }}" 
                     class="img-fluid rounded-circle mb-3" alt="Student Photo" style="width: 150px; height: 150px; object-fit: cover;">
                <h5>{{ $student->full_name }}</h5>
                <p class="text-muted mb-0">{{ $student->student_number }}</p>
            </div>
            <div class="col-md-9">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Course:</th>
                        <td>{{ $student->course->course_name }}</td>
                    </tr>
                    <tr>
                        <th>Year Level:</th>
                        <td>{{ $student->year_level }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $student->user->email }}</td>
                    </tr>
                    <tr>
                        <th>Academic Year:</th>
                        <td>{{ $currentAcademicYear }}</td>
                    </tr>
                    <tr>
                        <th>Semester:</th>
                        <td>{{ $currentSemester }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Attempts</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAttempts }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Average Score</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageScore, 1) }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Passed Quizzes</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $passedAttempts }}/{{ $totalAttempts }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pass Rate</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($passRate, 1) }}%</div>
            </div>
        </div>
    </div>
</div>

<!-- Performance by Subject -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Performance by Subject</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Attempts</th>
                        <th>Average Score</th>
                        <th>Passed</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($performanceBySubject as $perf)
                    <tr>
                        <td><strong>{{ $perf['subject'] }}</strong></td>
                        <td>{{ $perf['attempts'] }}</td>
                        <td>{{ $perf['average'] }}%</td>
                        <td>{{ $perf['passed'] }}/{{ $perf['attempts'] }}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $perf['average'] >= 75 ? 'success' : ($perf['average'] >= 60 ? 'warning' : 'danger') }}" 
                                     style="width: {{ $perf['average'] }}%">
                                    {{ $perf['average'] }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No quiz attempts yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Score Trend</h6>
    </div>
    <div class="card-body">
        <canvas id="scoreTrendChart" height="100"></canvas>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const trendCtx = document.getElementById('scoreTrendChart').getContext('2d');

@php
    // Get recent attempts for trend
    $recentTrend = $quizAttempts->sortBy('completed_at')->take(10);
    $trendLabels = $recentTrend->map(function($a, $index) {
        return 'Attempt ' . ($index + 1);
    });
    $trendScores = $recentTrend->pluck('percentage');
@endphp

new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($trendLabels) !!},
        datasets: [{
            label: 'Score (%)',
            data: {!! json_encode($trendScores) !!},
            borderColor: 'rgba(28, 200, 138, 1)',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7
        }, {
            label: 'Passing Score',
            data: Array({{ $trendLabels->count() }}).fill(60),
            borderColor: 'rgba(220, 53, 69, 0.5)',
            borderDash: [5, 5],
            borderWidth: 2,
            pointRadius: 0,
            fill: false
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
                display: true,
                position: 'top'
            }
        }
    }
});
</script>
@endpush

<!-- Strengths & Weaknesses -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-check-circle mr-2"></i>Strengths (Based on Bloom's Taxonomy)
                </h6>
            </div>
            <div class="card-body">
                @forelse($strengths as $strength)
                <div class="mb-3">
                    <strong>{{ $strength['level'] }}</strong>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" style="width: {{ $strength['score'] }}%">
                            {{ $strength['score'] }}%
                        </div>
                    </div>
                    <small class="text-muted">{{ $strength['total'] }} questions</small>
                </div>
                @empty
                <p class="text-muted text-center">Not enough data to determine strengths</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header py-3 bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Areas for Improvement
                </h6>
            </div>
            <div class="card-body">
                @forelse($weaknesses as $weakness)
                <div class="mb-3">
                    <strong>{{ $weakness['level'] }}</strong>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-danger" style="width: {{ $weakness['score'] }}%">
                            {{ $weakness['score'] }}%
                        </div>
                    </div>
                    <small class="text-muted">{{ $weakness['total'] }} questions</small>
                </div>
                @empty
                <p class="text-muted text-center">Great! No significant weaknesses identified</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Quiz Attempts -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Recent Quiz Attempts</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Quiz</th>
                        <th>Subject</th>
                        <th>Attempt</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Status</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttempts as $attempt)
                    <tr>
                        <td><strong>{{ $attempt->quiz->title }}</strong></td>
                        <td>{{ $attempt->quiz->subject->subject_name }}</td>
                        <td>{{ $attempt->attempt_number }}</td>
                        <td>{{ $attempt->score }}/{{ $attempt->total_points }}</td>
                        <td>
                            <span class="badge badge-{{ $attempt->percentage >= $attempt->quiz->passing_score ? 'success' : 'danger' }}">
                                {{ number_format($attempt->percentage, 1) }}%
                            </span>
                        </td>
                        <td>
                            @if($attempt->percentage >= $attempt->quiz->passing_score)
                                <span class="badge badge-success"><i class="fas fa-check"></i> Passed</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times"></i> Failed</span>
                            @endif
                        </td>
                        <td>{{ $attempt->completed_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No quiz attempts yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection