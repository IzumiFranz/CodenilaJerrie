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
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog"></i> Generation Parameters
                    </h6>
                </div>
                <div class="card-body">
                    @if($job->job_type == 'generate_questions')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-hashtag text-primary"></i> Number of Questions:</strong>
                                <div class="alert alert-light border mt-1">
                                    <h4 class="mb-0 text-primary">{{ $job->parameters['count'] ?? 'N/A' }}</h4>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-sliders-h text-warning"></i> Difficulty Level:</strong>
                                <div class="alert alert-light border mt-1">
                                    <span class="badge badge-{{ $job->parameters['difficulty'] == 'easy' ? 'success' : ($job->parameters['difficulty'] == 'medium' ? 'warning' : 'danger') }} badge-lg">
                                        {{ ucfirst($job->parameters['difficulty'] ?? 'N/A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-list-check text-info"></i> Question Types:</strong>
                            <div class="mt-2">
                                @if(isset($job->parameters['types']) && is_array($job->parameters['types']))
                                    @foreach($job->parameters['types'] as $type)
                                        <span class="badge badge-info mr-2 mb-2">
                                            <i class="fas fa-{{ $type == 'multiple_choice' ? 'list' : ($type == 'true_false' ? 'check-circle' : ($type == 'identification' ? 'keyboard' : 'align-left')) }}"></i>
                                            {{ str_replace('_', ' ', ucfirst($type)) }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-book text-success"></i> Selected Lessons:</strong>
                            <div class="mt-2">
                                @if(isset($job->parameters['lesson_ids']) && is_array($job->parameters['lesson_ids']))
                                    @php
                                        $lessons = \App\Models\Lesson::whereIn('id', $job->parameters['lesson_ids'])
                                            ->get(['id', 'title', 'order']);
                                    @endphp
                                    @if($lessons->count() > 0)
                                        <ul class="list-group">
                                            @foreach($lessons->sortBy('order') as $lesson)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-alt text-primary mr-2"></i>
                                                        <strong>{{ $lesson->title }}</strong>
                                                        @if($lesson->order)
                                                            <span class="badge badge-secondary ml-2">Order: {{ $lesson->order }}</span>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('instructor.lessons.show', $lesson) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="View Lesson">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Lessons not found (IDs: {{ implode(', ', $job->parameters['lesson_ids']) }})
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">No lessons selected</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle"></i>
                            <strong>What are Parameters?</strong><br>
                            These are the settings you configured when creating this AI job. They tell the AI:
                            <ul class="mb-0 mt-2">
                                <li>How many questions to generate</li>
                                <li>What difficulty level to use</li>
                                <li>Which question types to create</li>
                                <li>Which lessons to analyze for content</li>
                            </ul>
                        </div>
                    @elseif($job->job_type == 'validate_question')
                        <div class="mb-3">
                            <strong><i class="fas fa-question-circle text-primary"></i> Question ID:</strong>
                            <div class="alert alert-light border mt-1">
                                <a href="{{ route('instructor.question-bank.show', $job->parameters['question_id'] ?? '#') }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Question #{{ $job->parameters['question_id'] ?? 'N/A' }}
                                </a>
                            </div>
                        </div>
                    @elseif($job->job_type == 'analyze_quiz')
                        <div class="mb-3">
                            <strong><i class="fas fa-clipboard-list text-primary"></i> Quiz ID:</strong>
                            <div class="alert alert-light border mt-1">
                                <a href="{{ route('instructor.quizzes.show', $job->parameters['quiz_id'] ?? '#') }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Quiz #{{ $job->parameters['quiz_id'] ?? 'N/A' }}
                                </a>
                            </div>
                        </div>
                    @else
                        <pre class="bg-light p-3 rounded">{{ json_encode($job->parameters, JSON_PRETTY_PRINT) }}</pre>
                    @endif
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
                    @if($job->job_type == 'generate_questions' && isset($job->result['count']))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Successfully generated <strong>{{ $job->result['count'] }}</strong> questions!
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <a href="{{ route('instructor.question-bank.index', ['subject' => $job->subject_id]) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-list"></i> View in Question Bank
                            </a>
                            @if($generatedQuestions->count() > 0)
                                <button type="button" class="btn btn-info" onclick="scrollToQuestions()">
                                    <i class="fas fa-arrow-down"></i> View Generated Questions Below
                                </button>
                            @endif
                        </div>
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

            <!-- Generated Questions -->
            @if($job->job_type == 'generate_questions' && $generatedQuestions->count() > 0)
            <div class="card shadow mb-4" id="generated-questions">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-question-circle"></i> Generated Questions ({{ $generatedQuestions->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($generatedQuestions as $index => $question)
                    <div class="card mb-4 border-left-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge badge-primary">Question {{ $index + 1 }}</span>
                                    <span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($question->type)) }}</span>
                                    <span class="badge badge-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($question->difficulty) }}
                                    </span>
                                    @if($question->bloom_level)
                                        <span class="badge badge-secondary">{{ ucfirst($question->bloom_level) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="badge badge-dark">{{ $question->points }} points</span>
                                    <a href="{{ route('instructor.question-bank.show', $question) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="View Full Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('instructor.question-bank.edit', $question) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Edit Question">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="alert alert-light border mb-3">
                                <h5 class="mb-0">{{ $question->question_text }}</h5>
                            </div>

                            @if($question->type == 'multiple_choice' && $question->choices->count() > 0)
                                <h6 class="text-primary mb-2"><i class="fas fa-list mr-2"></i>Answer Choices</h6>
                                <div class="list-group mb-3">
                                    @foreach($question->choices->sortBy('order') as $choice)
                                    <div class="list-group-item {{ $choice->is_correct ? 'list-group-item-success' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @if($choice->is_correct)
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                @else
                                                    <i class="far fa-circle text-muted mr-2"></i>
                                                @endif
                                                <strong>{{ chr(64 + $choice->order) }}.</strong> {{ $choice->choice_text }}
                                            </div>
                                                @if($choice->is_correct)
                                                    <span class="badge badge-success">Correct</span>
                                                @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @elseif($question->type == 'true_false' && $question->choices->count() > 0)
                                <h6 class="text-primary mb-2"><i class="fas fa-check-circle mr-2"></i>Correct Answer</h6>
                                <div class="alert alert-success mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-{{ $question->choices->where('is_correct', true)->first()->choice_text == 'True' ? 'check' : 'times' }}-circle mr-2"></i>
                                        {{ $question->choices->where('is_correct', true)->first()->choice_text }}
                                    </h5>
                                </div>
                            @elseif($question->type == 'identification' && $question->choices->count() > 0)
                                <h6 class="text-primary mb-2"><i class="fas fa-keyboard mr-2"></i>Correct Answer</h6>
                                <div class="alert alert-success mb-3">
                                    <h5 class="mb-0">{{ $question->choices->first()->choice_text }}</h5>
                                </div>
                            @elseif($question->type == 'essay')
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-pen mr-2"></i>
                                    <strong>Essay Question</strong> - This question requires a written response and will be graded manually.
                                </div>
                            @endif

                            @if($question->explanation)
                                <div class="mt-3">
                                    <h6 class="text-info"><i class="fas fa-info-circle mr-2"></i>Explanation</h6>
                                    <div class="alert alert-info mb-0">
                                        {{ $question->explanation }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
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
    let statusCheckInterval = setInterval(() => {
        $.get('{{ route("instructor.ai.check-status", $job) }}', function(data) {
            if (data.status !== 'processing') {
                clearInterval(statusCheckInterval);
                location.reload();
            }
        }).fail(function() {
            clearInterval(statusCheckInterval);
            location.reload();
        });
    }, 3000); // Check every 3 seconds
@endif

// Scroll to generated questions
function scrollToQuestions() {
    const element = document.getElementById('generated-questions');
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>
@endpush
@endsection