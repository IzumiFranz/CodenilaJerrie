@extends('layouts.student')

@section('title', 'Feedback Details')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.feedback.index') }}">Feedback</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-comment-dots mr-2"></i>Feedback Details
                    </h5>
                    <span class="badge badge-{{ $feedback->status === 'resolved' ? 'success' : ($feedback->status === 'reviewed' ? 'info' : 'warning') }}">
                        {{ ucfirst($feedback->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Feedback Info -->
                <div class="mb-4">
                    <h6 class="font-weight-bold text-muted mb-3">Feedback Information</h6>
                    
                    <p class="mb-2">
                        <strong>Submitted:</strong> {{ $feedback->created_at->format('F d, Y \a\t h:i A') }}
                        <span class="text-muted">({{ $feedback->created_at->diffForHumans() }})</span>
                    </p>
                    
                    @if($feedback->feedbackable)
                    <p class="mb-2">
                        <strong>Related to:</strong><br>
                        @if($feedback->feedbackable_type === 'App\Models\Quiz')
                            <span class="badge badge-success">Quiz</span> {{ $feedback->feedbackable->title }}
                        @elseif($feedback->feedbackable_type === 'App\Models\Lesson')
                            <span class="badge badge-primary">Lesson</span> {{ $feedback->feedbackable->title }}
                        @endif
                    </p>
                    @endif
                    
                    @if($feedback->rating)
                    <p class="mb-2">
                        <strong>Rating:</strong><br>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        ({{ $feedback->rating }}/5)
                    </p>
                    @endif
                </div>

                <hr>

                <!-- Your Feedback -->
                <div class="mb-4">
                    <h6 class="font-weight-bold text-muted mb-3">Your Feedback</h6>
                    <div class="p-3 bg-light rounded">
                        {!! nl2br(e($feedback->comment)) !!}
                    </div>
                </div>

                <!-- Admin Response -->
                @if($feedback->admin_response)
                <hr>
                <div class="alert alert-success">
                    <h6 class="font-weight-bold">
                        <i class="fas fa-reply mr-2"></i>Administrator Response
                    </h6>
                    <p class="mb-2 small text-muted">
                        Responded: {{ $feedback->responded_at->format('F d, Y \a\t h:i A') }}
                    </p>
                    <div class="mt-2">
                        {!! nl2br(e($feedback->admin_response)) !!}
                    </div>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-clock mr-2"></i>
                    Your feedback is being reviewed. You will be notified when there is a response.
                </div>
                @endif
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('student.feedback.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Feedback List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection