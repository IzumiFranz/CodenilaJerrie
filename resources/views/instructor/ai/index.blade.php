@extends('layouts.instructor')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-robot fa-fw"></i> AI Assistant Dashboard
        </h1>
    </div>

    <!-- AI Feature Cards -->
    <div class="row mb-4">
        <!-- Question Generation Card -->
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
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-brain fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Validation Card -->
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
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quiz Analysis Card -->
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

        <!-- API Usage Card -->
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

    <!-- Quick Actions -->
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
                            <a href="{{ route('instructor.question-bank.index') }}?ai_generate=1" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-plus-circle"></i> Generate Questions
                            </a>
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
                            <a href="{{ route('instructor.quizzes.index') }}?ai_analyze=1" class="btn btn-info btn-block btn-lg">
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

    <!-- Filter and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history"></i> AI Job History
            </h6>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <select name="type" class="form-control">
                            <option value="">All Job Types</option>
                            <option value="generate_questions" {{ request('type') == 'generate_questions' ? 'selected' : '' }}>
                                Question Generation
                            </option>
                            <option value="validate_question" {{ request('type') == 'validate_question' ? 'selected' : '' }}>
                                Question Validation
                            </option>
                            <option value="analyze_quiz" {{ request('type') == 'analyze_quiz' ? 'selected' : '' }}>
                                Quiz Analysis
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('instructor.ai.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Jobs Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Started</th>
                            <th>Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                        <tr>
                            <td>#{{ $job->id }}</td>
                            <td>
                                @switch($job->job_type)
                                    @case('generate_questions')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-brain"></i> Generate
                                        </span>
                                        @break
                                    @case('validate_question')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Validate
                                        </span>
                                        @break
                                    @case('analyze_quiz')
                                        <span class="badge badge-info">
                                            <i class="fas fa-chart-line"></i> Analyze
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($job->subject)
                                    {{ $job->subject->subject_name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @switch($job->status)
                                    @case('pending')
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                        @break
                                    @case('processing')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-spinner fa-spin"></i> Processing
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Completed
                                        </span>
                                        @break
                                    @case('failed')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle"></i> Failed
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($job->status == 'processing')
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                             role="progressbar" style="width: 50%"></div>
                                    </div>
                                @elseif($job->status == 'completed')
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                    </div>
                                @elseif($job->status == 'failed')
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"></div>
                                    </div>
                                @else
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($job->started_at)
                                    <small>{{ $job->started_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">Not started</span>
                                @endif
                            </td>
                            <td>
                                @if($job->completed_at)
                                    <small>{{ $job->completed_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">-</span>
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
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-robot fa-3x text-gray-300 mb-3"></i>
                                <p class="text-muted">No AI jobs yet. Start by generating questions or validating existing ones!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($jobs->hasPages())
                <div class="mt-3">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-refresh processing jobs with smart polling
let processingJobs = {{ $jobs->where('status', 'processing')->pluck('id')->toJson() }};

if (processingJobs.length > 0) {
    let refreshInterval = setInterval(() => {
        let stillProcessing = false;
        
        // Poll each processing job
        processingJobs.forEach(jobId => {
            $.get(`/instructor/ai/job/${jobId}/status`, function(data) {
                if (data.status === 'processing') {
                    stillProcessing = true;
                } else {
                    // Job completed or failed, reload page
                    clearInterval(refreshInterval);
                    location.reload();
                }
            }).fail(function() {
                // On error, reload page
                clearInterval(refreshInterval);
                location.reload();
            });
        });
        
        if (!stillProcessing) {
            clearInterval(refreshInterval);
            location.reload();
        }
    }, 5000); // Check every 5 seconds
}
</script>
@endpush
@endsection