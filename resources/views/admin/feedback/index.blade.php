@extends('layouts.admin')

@section('title', 'Feedback Management')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-comments mr-2"></i> Feedback Management</h1>
</div>

{{-- 🔍 Filter Section --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.feedback.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search user or comment..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-control">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ str_repeat('★', $i) }} ({{ $i }})
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.feedback.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- 📊 Statistics Section --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $feedback->where('status', 'pending')->count() }}</div>
                </div>
                <i class="fas fa-clock fa-2x text-gray-300"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Reviewed</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $feedback->where('status', 'reviewed')->count() }}</div>
                </div>
                <i class="fas fa-eye fa-2x text-gray-300"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Resolved</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $feedback->where('status', 'resolved')->count() }}</div>
                </div>
                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Avg Rating</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $feedback->avg('rating') ? number_format($feedback->avg('rating'), 1) : 'N/A' }}
                        @if($feedback->avg('rating'))
                            <small class="text-warning">★</small>
                        @endif
                    </div>
                </div>
                <i class="fas fa-star fa-2x text-gray-300"></i>
            </div>
        </div>
    </div>
</div>

{{-- 🗒 Feedback List --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Feedback List</h6>
    </div>
    <div class="card-body">
        @forelse($feedback as $item)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <strong>{{ $item->user->full_name ?? $item->user->username }}</strong>
                            <span class="text-muted">- {{ $item->created_at->diffForHumans() }}</span>
                        </div>
                        <div>
                            @if($item->rating)
                                <span class="badge badge-warning">{{ str_repeat('⭐', $item->rating) }}</span>
                            @endif
                            <span class="badge badge-{{ $item->status === 'resolved' ? 'success' : ($item->status === 'reviewed' ? 'info' : 'warning') }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                    </div>

                    <p class="mb-2">{{ $item->comment }}</p>

                    @if($item->admin_response)
                        <div class="alert alert-info mb-2">
                            <strong>Admin Response:</strong>
                            <p class="mb-0">{{ $item->admin_response }}</p>
                            <small class="text-muted">Responded {{ $item->responded_at->diffForHumans() }}</small>
                        </div>
                    @endif

                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.feedback.show', $item) }}" class="btn btn-info">
                            <i class="fas fa-eye mr-1"></i> View
                        </a>

                        @if(!$item->admin_response)
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#respondModal{{ $item->id }}">
                                <i class="fas fa-reply mr-1"></i> Respond
                            </button>
                        @endif

                        @if($item->status !== 'resolved')
                            <form action="{{ route('admin.feedback.update-status', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="resolved">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check mr-1"></i> Mark Resolved
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Respond Modal --}}
            <div class="modal fade" id="respondModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Respond to Feedback</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form action="{{ route('admin.feedback.respond', $item) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Your Response</label>
                                    <textarea name="admin_response" class="form-control" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane mr-1"></i> Send Response
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <p class="mb-0 text-muted">No feedback found</p>
            </div>
        @endforelse

        <div class="mt-3">
            {{ $feedback->links() }}
        </div>
    </div>
</div>

{{-- 💬 Optional Livewire Component --}}
@livewire('feedback-table')
@endsection
