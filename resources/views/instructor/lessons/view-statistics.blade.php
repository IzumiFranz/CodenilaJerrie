@extends('layouts.instructor')

@section('title', 'Lesson View Statistics')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-line mr-2"></i>View Statistics
    </h1>
    <a href="{{ route('instructor.lessons.show', $lesson) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to Lesson
    </a>
</div>

{{-- Lesson Info --}}
<div class="card mb-4">
    <div class="card-body">
        <h5>{{ $lesson->title }}</h5>
        <p class="text-muted mb-0">{{ $lesson->subject->subject_name }}</p>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Views</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_views'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Unique Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['unique_students'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Completion Rate</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['completion_rate'], 1) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg. Duration</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['average_duration'] ? gmdate('i:s', $stats['average_duration']) : '0:00' }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Completion Timeline Chart --}}
@if($completionTimeline->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Completion Timeline</h6>
    </div>
    <div class="card-body">
        <canvas id="completionChart" height="80"></canvas>
    </div>
</div>
@endif

{{-- Students Not Viewed --}}
@if($notViewedStudents->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">
            Students Who Haven't Viewed ({{ $notViewedStudents->count() }})
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Student Number</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notViewedStudents as $student)
                        <tr>
                            <td>{{ $student->student_number }}</td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->user->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- View History --}}
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">View History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Viewed At</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($viewHistory as $view)
                        <tr>
                            <td>
                                <strong>{{ $view->student->full_name }}</strong><br>
                                <small class="text-muted">{{ $view->student->student_number }}</small>
                            </td>
                            <td>{{ $view->viewed_at->format('M d, Y h:i A') }}</td>
                            <td>{{ $view->getDurationFormatted() }}</td>
                            <td>
                                @if($view->completed)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Completed
                                    </span>
                                @else
                                    <span class="badge badge-secondary">In Progress</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $view->ip_address }}</small></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $viewHistory->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
@if($completionTimeline->count() > 0)
$(document).ready(function() {
    const ctx = document.getElementById('completionChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($completionTimeline->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))) !!},
            datasets: [{
                label: 'Students Completed',
                data: {!! json_encode($completionTimeline->pluck('count')) !!},
                borderColor: 'rgb(28, 200, 138)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
@endif
</script>
@endpush