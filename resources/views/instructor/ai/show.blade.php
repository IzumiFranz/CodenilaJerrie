@extends('layouts.instructor')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-robot fa-fw"></i> AI Job Details
        </h1>
        <a href="{{ route('instructor.ai.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Job Information Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Job Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Job ID:</th>
                            <td>#{{ $job->id }}</td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td>
                                @switch($job->job_type)
                                    @case('generate_questions')
                                        <span class="badge badge-primary">Question Generation</span>
                                        @break
                                    @case('validate_question')
                                        <span class="badge badge-success">Question Validation</span>
                                        @break
                                    @case('analyze_quiz')
                                        <span class="badge badge-info">Quiz Analysis</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>{{ $job->subject->subject_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @switch($job->status)
                                    @case('pending')
                                        <span class="badge badge-secondary">Pending</span>
                                        @break
                                    @case('processing')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-spinner fa-spin"></i> Processing
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-success">Completed</span>
                                        @break
                                    @case('failed')
                                        <span class="badge badge-danger">Failed</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $job->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Started:</th>
                            <td>{{ $job->started_at ? $job->started_at->format('M d, Y h:i A') : 'Not yet' }}</td>
                        </tr>
                        <tr>
                            <th>Completed:</th>
                            <td>{{ $job->completed_at ? $job->completed_at->format('M d, Y h:i A') : 'Not yet' }}</td>
                        </tr>
                        @if($job->started_at && $job->completed_at)
                        <tr>
                            <th>Duration:</th>
                            <td>{{ $job->started_at->diffInSeconds($job->completed_at) }} seconds</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Parameters -->
            @if($job->parameters)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Parameters</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded">{{ json_encode($job->parameters, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif

            <!-- Results -->
            @if($job->result)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-check-circle"></i> Results
                    </h6>
                </div>
                <div class="card-body">
                    @if($job->job_type == 'generate_questions' && isset($job->result['questions']))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Successfully generated <strong>{{ count($job->result['questions']) }}</strong> questions!
                        </div>
                        <a href="{{ route('instructor.question-bank.index', ['subject' => $job->subject_id]) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-eye"></i> View Generated Questions
                        </a>
                    @elseif($job->job_type == 'validate_question')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h2 class="display-4">{{ $job->result['validation']['quality_score'] ?? 'N/A' }}</h2>
                                        <p class="text-muted">Quality Score</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h2 class="display-4">{{ $job->result['validation']['clarity_score'] ?? 'N/A' }}</h2>
                                        <p class="text-muted">Clarity Score</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(isset($job->result['validation']['suggestions']))
                            <h6 class="mt-4">Suggestions:</h6>
                            <ul>
                                @foreach($job->result['validation']['suggestions'] as $suggestion)
                                    <li>{{ $suggestion }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @elseif($job->job_type == 'analyze_quiz')
                        <div class="alert alert-info">
                            <i class="fas fa-chart-line"></i>
                            Quiz analysis completed successfully!
                        </div>
                        @if(isset($job->result['analysis']))
                            <h6>Overall Assessment:</h6>
                            <p class="lead">{{ ucfirst($job->result['analysis']['overall_difficulty'] ?? 'N/A') }}</p>
                            
                            @if(isset($job->result['analysis']['recommendations']))
                                <h6 class="mt-3">Recommendations:</h6>
                                <ul>
                                    @foreach($job->result['analysis']['recommendations'] as $recommendation)
                                        <li>{{ $recommendation }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        @endif
                    @else
                        <pre class="bg-light p-3 rounded">{{ json_encode($job->result, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                </div>
            </div>
            @endif

            <!-- Error Message -->
            @if($job->error_message)
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-exclamation-triangle"></i> Error
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ $job->error_message }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $job->created_at ? 'completed' : '' }}">
                            <i class="fas fa-plus-circle"></i>
                            <div class="timeline-content">
                                <h6>Created</h6>
                                <small>{{ $job->created_at->format('M d, h:i A') }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $job->started_at ? 'completed' : '' }}">
                            <i class="fas fa-play-circle"></i>
                            <div class="timeline-content">
                                <h6>Started</h6>
                                <small>{{ $job->started_at ? $job->started_at->format('M d, h:i A') : 'Waiting...' }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $job->completed_at ? 'completed' : ($job->status == 'failed' ? 'failed' : '') }}">
                            <i class="fas {{ $job->status == 'failed' ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                            <div class="timeline-content">
                                <h6>{{ $job->status == 'failed' ? 'Failed' : 'Completed' }}</h6>
                                <small>{{ $job->completed_at ? $job->completed_at->format('M d, h:i A') : 'In progress...' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($job->status == 'processing')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="sr-only">Processing...</span>
                    </div>
                    <p class="text-muted">Job is being processed...</p>
                    <small>This page will auto-refresh</small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-item i {
    position: absolute;
    left: -28px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    color: #d1d3e2;
    font-size: 20px;
}

.timeline-item.completed i {
    color: #1cc88a;
}

.timeline-item.failed i {
    color: #e74a3b;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-content small {
    color: #858796;
}
</style>
@endpush

@push('scripts')
<script>
// Auto-refresh if job is still processing
@if($job->status == 'processing')
    setTimeout(() => {
        location.reload();
    }, 5000); // Refresh every 5 seconds
@endif
</script>
@endpush
@endsection