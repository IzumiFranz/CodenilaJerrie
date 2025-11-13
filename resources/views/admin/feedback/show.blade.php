@extends('layouts.admin')

@section('title', 'Feedback Details')

@php
    $pageTitle = 'Feedback Details';
    $pageActions = '
        <a href="' . route('admin.feedback.index') . '" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>';
@endphp

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Feedback Details -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Feedback Information</h6>
                @if($feedback->status == 'pending')
                    <span class="badge badge-warning">Pending</span>
                @else
                    <span class="badge badge-success">Responded</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>User:</strong>
                    </div>
                    <div class="col-md-9">
                        <div>
                            <strong>{{ $feedback->user->username }}</strong>
                            <span class="badge badge-{{ $feedback->user->role == 'admin' ? 'danger' : ($feedback->user->role == 'instructor' ? 'success' : 'info') }}">
                                {{ ucfirst($feedback->user->role) }}
                            </span>
                        </div>
                        <small class="text-muted">{{ $feedback->user->email }}</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Rating:</strong>
                    </div>
                    <div class="col-md-9">
                        <div class="text-warning" style="font-size: 1.5rem;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $feedback->rating)
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <small class="text-muted">({{ $feedback->rating }} out of 5)</small>
                    </div>
                </div>

                @if($feedback->feedbackable)
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Related To:</strong>
                        </div>
                        <div class="col-md-9">
                            <span class="badge badge-info">{{ class_basename($feedback->feedbackable_type) }}</span>
                            @if($feedback->feedbackable_type == 'App\\Models\\Lesson')
                                <br><small>Lesson: {{ $feedback->feedbackable->title }}</small>
                            @elseif($feedback->feedbackable_type == 'App\\Models\\Quiz')
                                <br><small>Quiz: {{ $feedback->feedbackable->title }}</small>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Subject:</strong>
                    </div>
                    <div class="col-md-9">
                        <div class="border rounded p-3 bg-light">
                            <strong>{{ $feedback->subject }}</strong>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Message:</strong>
                    </div>
                    <div class="col-md-9">
                        <div class="border rounded p-3 bg-light">
                            {{ $feedback->message }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Submitted:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $feedback->created_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $feedback->created_at->diffForHumans() }})</small>
                    </div>
                </div>

                @if($feedback->response)
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Admin Response:</strong>
                        </div>
                        <div class="col-md-9">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-reply"></i> {{ $feedback->response }}
                            </div>
                            @if($feedback->updated_at != $feedback->created_at)
                                <small class="text-muted">Responded: {{ $feedback->updated_at->format('M d, Y H:i') }}</small>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Respond to Feedback -->
        @if(!$feedback->response || $feedback->status != 'responded')
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-reply"></i> 
                        {{ $feedback->response ? 'Update Response' : 'Respond to Feedback' }}
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.feedback.respond', $feedback) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group">
                            <label for="response">Your Response <span class="text-danger">*</span></label>
                            <textarea name="response" id="response" 
                                class="form-control @error('response') is-invalid @enderror" 
                                rows="5" 
                                placeholder="Write your response to the user..."
                                required>{{ old('response', $feedback->response) }}</textarea>
                            @error('response')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> This response will be sent to the user via email
                            </small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.feedback.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Send Response
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.feedback.update-status', $feedback) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="form-group">
                        <label for="status">Change Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending" {{ $feedback->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="responded" {{ $feedback->status == 'responded' ? 'selected' : '' }}>Responded</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- User Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    @if($feedback->user->profile_picture)
                        <img src="{{ asset('storage/' . $feedback->user->profile_picture) }}" 
                            alt="{{ $feedback->user->username }}" 
                            class="rounded-circle" 
                            style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                            style="width: 100px; height: 100px; font-size: 2rem;">
                            {{ strtoupper(substr($feedback->user->username, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="text-center">
                    <h5 class="mb-1">{{ $feedback->user->username }}</h5>
                    <p class="text-muted mb-2">{{ $feedback->user->email }}</p>
                    <span class="badge badge-{{ $feedback->user->role == 'admin' ? 'danger' : ($feedback->user->role == 'instructor' ? 'success' : 'info') }}">
                        {{ ucfirst($feedback->user->role) }}
                    </span>
                </div>

                <hr>

                <div class="small">
                    <div class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge badge-{{ $feedback->user->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($feedback->user->status) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Member Since:</strong><br>
                        {{ $feedback->user->created_at->format('M d, Y') }}
                    </div>
                    <div>
                        <strong>Total Feedback:</strong><br>
                        {{ $feedback->user->feedback()->count() }} submissions
                    </div>
                </div>

                <hr>

                <a href="{{ route('admin.users.show', $feedback->user) }}" class="btn btn-sm btn-outline-primary btn-block">
                    <i class="fas fa-user"></i> View User Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection