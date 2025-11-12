@extends('layouts.instructor')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-robot fa-fw"></i> AI Assistant Dashboard
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" data-toggle="modal" data-target="#aiGenerateModal">
                <i class="fas fa-magic"></i> Generate Questions
            </button>
            <button class="btn btn-success" onclick="bulkValidateQuestions()">
                <i class="fas fa-check-double"></i> Validate All
            </button>
        </div>
    </div>

    <!-- AI Usage Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Questions Generated
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_generated'] ?? 0 }}
                            </div>
                            <div class="text-xs text-muted">This month: {{ $stats['monthly_generated'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-brain fa-2x text-gray-300"></i>
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
                                Validations Done
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_validated'] ?? 0 }}
                            </div>
                            <div class="text-xs text-muted">Avg Score: {{ $stats['avg_validation_score'] ?? 0 }}%</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Quizzes Analyzed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_analyzed'] ?? 0 }}
                            </div>
                            <div class="text-xs text-muted">Insights generated</div>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                API Usage
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($stats['estimated_cost'] ?? 0, 2) }}
                            </div>
                            <div class="text-xs text-muted">This month</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Suggestions -->
    @if(isset($suggestions) && count($suggestions) > 0)
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow border-left-success">
                <div class="card-header py-3 bg-gradient-success">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-lightbulb"></i> AI Suggestions for You
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($suggestions as $suggestion)
                        <div class="col-md-6 mb-3">
                            <div class="alert alert-success mb-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="alert-heading">
                                            <i class="fas {{ $suggestion['icon'] }}"></i> 
                                            {{ $suggestion['title'] }}
                                        </h6>
                                        <p class="mb-2">{{ $suggestion['description'] }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $suggestion['reason'] }}
                                        </small>
                                    </div>
                                    <a href="{{ $suggestion['action_url'] }}" class="btn btn-sm btn-success ml-2">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Smart Recommendations -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-magic"></i> What You Can Do
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#aiGenerateModal">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-brain text-primary"></i> Generate Questions from Lessons
                                </h6>
                                <small class="text-primary">Quick</small>
                            </div>
                            <p class="mb-1 text-muted">
                                Let AI create quiz questions automatically from your lesson content
                            </p>
                            <small class="text-success">Saves 30-60 minutes per quiz</small>
                        </a>

                        <a href="{{ route('instructor.question-bank.index') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-check-double text-success"></i> Validate Existing Questions
                                </h6>
                                <small class="text-success">Recommended</small>
                            </div>
                            <p class="mb-1 text-muted">
                                Get AI feedback on question quality, clarity, and effectiveness
                            </p>
                            <small class="text-info">Improve question quality by 40%</small>
                        </a>

                        <a href="{{ route('instructor.quizzes.index') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-chart-line text-info"></i> Analyze Quiz Performance
                                </h6>
                                <small class="text-info">Insights</small>
                            </div>
                            <p class="mb-1 text-muted">
                                Get detailed analytics on student performance and question effectiveness
                            </p>
                            <small class="text-warning">Identify weak areas instantly</small>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action" onclick="showBulkOperations()">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-layer-group text-warning"></i> Bulk Operations
                                </h6>
                                <small class="text-warning">Advanced</small>
                            </div>
                            <p class="mb-1 text-muted">
                                Generate or validate multiple items at once
                            </p>
                            <small class="text-danger">Process 100+ questions in minutes</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-chart-pie"></i> Usage Insights
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Usage Chart -->
                    <canvas id="usageChart" height="200"></canvas>
                    
                    <div class="mt-4">
                        <h6 class="font-weight-bold">Performance Metrics</h6>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-0 text-primary">{{ $stats['avg_generation_time'] ?? '45s' }}</div>
                                    <small class="text-muted">Avg Generation Time</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-0 text-success">{{ $stats['success_rate'] ?? '98%' }}</div>
                                    <small class="text-muted">Success Rate</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-0 text-info">{{ $stats['questions_per_hour'] ?? '120' }}</div>
                                    <small class="text-muted">Questions/Hour</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-0 text-warning">{{ $stats['monthly_limit'] ?? '500' }}</div>
                                    <small class="text-muted">Monthly Limit</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent AI Jobs -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i> Recent AI Jobs
                    </h6>
                    <a href="{{ route('instructor.ai.index') }}" class="btn btn-sm btn-primary">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentJobs as $job)
                                <tr>
                                    <td>
                                        @if($job->job_type == 'generate_questions')
                                            <span class="badge badge-primary">
                                                <i class="fas fa-brain"></i> Generate
                                            </span>
                                        @elseif($job->job_type == 'validate_question')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Validate
                                            </span>
                                        @else
                                            <span class="badge badge-info">
                                                <i class="fas fa-chart-line"></i> Analyze
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $job->subject->subject_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($job->status == 'completed')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> Completed
                                            </span>
                                        @elseif($job->status == 'processing')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-spinner fa-spin"></i> Processing
                                            </span>
                                        @elseif($job->status == 'failed')
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle"></i> Failed
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $job->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($job->started_at && $job->completed_at)
                                            <small>{{ $job->started_at->diffInSeconds($job->completed_at) }}s</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('instructor.ai.show', $job) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-robot fa-3x text-gray-300 mb-3 d-block"></i>
                                        <p class="text-muted">No AI jobs yet. Get started by generating questions!</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('instructor.question-bank.partials.ai-generate-modal')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Usage Chart
const ctx = document.getElementById('usageChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
            {
                label: 'Questions Generated',
                data: {{ json_encode($chartData['generated'] ?? [0,0,0,0]) }},
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4
            },
            {
                label: 'Validations',
                data: {{ json_encode($chartData['validated'] ?? [0,0,0,0]) }},
                borderColor: 'rgb(28, 200, 138)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Auto-refresh if there are processing jobs
@if(isset($hasProcessingJobs) && $hasProcessingJobs)
    setTimeout(() => {
        location.reload();
    }, 10000); // Refresh every 10 seconds
@endif

function bulkValidateQuestions() {
    Swal.fire({
        title: 'Bulk Validate Questions?',
        text: 'This will validate all unvalidated questions in your question bank.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, validate all',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement bulk validation
            window.location.href = '{{ route("instructor.question-bank.index") }}?bulk_validate=1';
        }
    });
}

function showBulkOperations() {
    Swal.fire({
        title: 'Bulk Operations',
        html: `
            <div class="list-group text-left">
                <a href="#" class="list-group-item list-group-item-action" onclick="bulkGenerate()">
                    <i class="fas fa-brain text-primary"></i> Bulk Generate Questions
                </a>
                <a href="#" class="list-group-item list-group-item-action" onclick="bulkValidateQuestions()">
                    <i class="fas fa-check-double text-success"></i> Bulk Validate Questions
                </a>
                <a href="#" class="list-group-item list-group-item-action" onclick="bulkAnalyze()">
                    <i class="fas fa-chart-line text-info"></i> Bulk Analyze Quizzes
                </a>
            </div>
        `,
        showConfirmButton: false,
        showCloseButton: true
    });
}
</script>
@endpush
@endsection