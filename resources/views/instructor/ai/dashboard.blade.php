@extends('layouts.instructor')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-robot fa-fw"></i> AI Assistant Dashboard
        </h1>
        <div>
            <a href="{{ route('instructor.ai.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-history"></i> View All Jobs
            </a>
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
                                Question Generation
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_generated'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-600">Questions Created</div>
                            @if(isset($stats['monthly_generated']) && $stats['monthly_generated'] > 0)
                                <div class="text-xs text-muted mt-1">This month: {{ $stats['monthly_generated'] }}</div>
                            @endif
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
                                Validations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_validated'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-600">Questions Checked</div>
                            @if(isset($stats['avg_validation_score']) && $stats['avg_validation_score'] > 0)
                                <div class="text-xs text-muted mt-1">Avg Score: {{ number_format($stats['avg_validation_score'], 1) }}%</div>
                            @endif
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
                                Quiz Analysis
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_analyzed'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-600">Quizzes Analyzed</div>
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
                                This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['monthly_jobs'] ?? 0 }}
                            </div>
                            <div class="text-xs text-gray-600">AI Requests</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick AI Actions -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-magic"></i> Quick AI Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <button type="button" class="btn btn-primary btn-block btn-lg" data-toggle="modal" data-target="#aiGenerateModal">
                                <i class="fas fa-plus-circle"></i> Generate Questions
                            </button>
                            <small class="text-muted d-block mt-2">
                                Create quiz questions from your lessons using AI
                            </small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('instructor.question-bank.index') }}?ai_validate=1" class="btn btn-success btn-block btn-lg">
                                <i class="fas fa-check-double"></i> Validate Questions
                            </a>
                            <small class="text-muted d-block mt-2">
                                Check quality and get improvement suggestions
                            </small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-info btn-block btn-lg">
                                <i class="fas fa-chart-bar"></i> Analyze Quizzes
                            </a>
                            <small class="text-muted d-block mt-2">
                                Get insights on quiz performance and difficulty
                            </small>
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

@include('instructor.question-bank.partials.ai-generate-modal', ['subjects' => $subjects ?? collect()])

@push('scripts')
<script>
// Auto-refresh if there are processing jobs
@if(isset($hasProcessingJobs) && $hasProcessingJobs)
    let refreshInterval = setInterval(() => {
        // Check job statuses via AJAX instead of full page reload
        let processingJobIds = {{ $recentJobs->where('status', 'processing')->pluck('id')->toJson() }};
        
        if (processingJobIds.length > 0) {
            let stillProcessing = false;
            // Poll each processing job
            processingJobIds.forEach(jobId => {
                $.get(`/instructor/ai/job/${jobId}/status`, function(data) {
                    if (data.status === 'processing') {
                        stillProcessing = true;
                    } else {
                        // Job completed or failed, reload page
                        clearInterval(refreshInterval);
                        location.reload();
                    }
                }).fail(function() {
                    clearInterval(refreshInterval);
                    location.reload();
                });
            });
            
            if (!stillProcessing && processingJobIds.length > 0) {
                clearInterval(refreshInterval);
                location.reload();
            }
        } else {
            clearInterval(refreshInterval);
            location.reload();
        }
    }, 5000); // Check every 5 seconds
@endif
</script>
@endpush
@endsection