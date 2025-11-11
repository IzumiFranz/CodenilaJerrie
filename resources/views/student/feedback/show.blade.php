@extends('layouts.student')

@section('title', 'Feedback Details')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('student.feedback.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Feedback List
        </a>
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-comment-dots me-2"></i>Feedback Details</h1>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Feedback Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <div>
                        <span class="badge bg-{{ $feedback->type === 'quiz' ? 'primary' : ($feedback->type === 'lesson' ? 'info' : ($feedback->type === 'instructor' ? 'success' : 'secondary')) }} me-2">
                            <i class="fas fa-{{ $feedback->type === 'quiz' ? 'clipboard-list' : ($feedback->type === 'lesson' ? 'book' : ($feedback->type === 'instructor' ? 'chalkboard-teacher' : 'comment')) }} me-1"></i>
                            {{ ucfirst($feedback->type) }}
                        </span>
                        <span class="badge bg-{{ $feedback->status === 'pending' ? 'warning' : 'success' }}">
                            <i class="fas fa-{{ $feedback->status === 'pending' ? 'clock' : 'check-circle' }} me-1"></i>
                            {{ ucfirst($feedback->status) }}
                        </span>
                    </div>
                    @if($feedback->status === 'pending')
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Rating -->
                    @if($feedback->rating)
                    <div class="mb-4 text-center py-3 bg-light rounded">
                        <h6 class="text-muted mb-2">Your Rating</h6>
                        <div class="fs-3">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                        </div>
                        <p class="mb-0 mt-2"><strong>{{ $feedback->rating }}</strong> out of 5 stars</p>
                    </div>
                    @endif

                    <!-- Subject -->
                    <h4 class="mb-3">{{ $feedback->subject }}</h4>

                    <!-- Related Item -->
                    @if($feedback->feedbackable)
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-link me-2"></i>Related {{ ucfirst($feedback->type) }}
                        </h6>
                        <p class="mb-0"><strong>{{ $feedback->feedbackable->title ?? $feedback->feedbackable->name }}</strong></p>
                        @if($feedback->type === 'quiz' || $feedback->type === 'lesson')
                        <a href="{{ route('student.' . $feedback->type . 's.show', $feedback->feedbackable->id) }}" 
                           class="btn btn-sm btn-outline-info mt-2">
                            <i class="fas fa-eye me-1"></i> View {{ ucfirst($feedback->type) }}
                        </a>
                        @endif
                    </div>
                    @endif

                    <!-- Feedback Message -->
                    <div class="mb-4">
                        <h5 class="mb-3">Your Feedback</h5>
                        <div class="border-start border-primary border-4 ps-3 py-2">
                            <p class="mb-0" style="white-space: pre-line;">{{ $feedback->message }}</p>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="row text-muted small border-top pt-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-calendar me-2"></i>
                                <strong>Submitted:</strong> {{ $feedback->created_at->format('F d, Y H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-user me-2"></i>
                                <strong>Submitted by:</strong> 
                                {{ $feedback->is_anonymous ? 'Anonymous' : $feedback->user->name }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response Card -->
            @if($feedback->status === 'responded' && $feedback->response)
            <div class="card shadow-sm border-success mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-reply me-2"></i>Instructor Response
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <img src="{{ $feedback->response_by->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($feedback->response_by->name ?? 'Instructor') }}" 
                             alt="{{ $feedback->response_by->name ?? 'Instructor' }}" 
                             class="rounded-circle me-3" width="50" height="50">
                        <div>
                            <h6 class="mb-0">{{ $feedback->response_by->name ?? 'Instructor' }}</h6>
                            <small class="text-muted">{{ $feedback->responded_at ? $feedback->responded_at->format('F d, Y H:i') : '' }}</small>
                        </div>
                    </div>
                    <div class="border-start border-success border-4 ps-3 py-2">
                        <p class="mb-0" style="white-space: pre-line;">{{ $feedback->response }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm border-warning mb-4">
                <div class="card-body text-center py-4">
                    <i class="fas fa-clock text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-2">Awaiting Response</h5>
                    <p class="text-muted mb-0">Your feedback has been received. An instructor will respond soon.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2 text-secondary"></i>Actions</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <button onclick="window.print()" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="fas fa-print me-2"></i>Print Feedback
                    </button>
                    @if($feedback->status === 'pending')
                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Delete Feedback
                    </button>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-info"></i>Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Feedback Submitted</h6>
                                    <p class="text-muted small mb-0">{{ $feedback->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        @if($feedback->status === 'responded')
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-marker bg-success"></div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Response Received</h6>
                                    <p class="text-muted small mb-0">{{ $feedback->responded_at ? $feedback->responded_at->format('M d, Y H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="ms-3">
                                    <h6 class="mb-1">Pending Response</h6>
                                    <p class="text-muted small mb-0">Waiting for instructor</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Guidelines -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2 text-warning"></i>Guidelines</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li>Be specific and constructive</li>
                        <li>Focus on your learning experience</li>
                        <li>Provide examples if possible</li>
                        <li>Be respectful</li>
                        <li>Suggest improvements</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this feedback?</p>
                <p class="text-danger mb-0"><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('student.feedback.destroy', $feedback->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.hover-card { transition: transform 0.2s, box-shadow 0.2s; }
.hover-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
.timeline-item { position: relative; margin-bottom: 1rem; }
.timeline-marker { width: 16px; height: 16px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px currentColor; }
.timeline-item:not(:last-child)::before { content: ''; position: absolute; left: 7px; top: 24px; bottom: -16px; width: 2px; background: #dee2e6; }
@media print { .btn, .modal, nav, .card-header, .timeline { display: none !important; } .card { border: none !important; box-shadow: none !important; } }
</style>
@endpush
@endsection
