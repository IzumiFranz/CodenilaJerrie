@extends('layouts.admin')
@section('title', 'Manage Feedback')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-comments mr-2"></i>Manage Feedback</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search feedback..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="rating" class="form-control">
                        <option value="">All Ratings</option>
                        @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} ⭐</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

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
                        <strong>{{ $item->user->full_name }}</strong>
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
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
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
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <p class="mb-0">No feedback found</p>
        </div>
        @endforelse

        <div class="mt-3">
            {{ $feedback->links() }}
        </div>
    </div>
</div>
@livewire('feedback-table')
@endsection