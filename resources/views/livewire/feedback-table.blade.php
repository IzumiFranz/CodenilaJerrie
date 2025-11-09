<div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search feedback...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="rating" class="form-control">
                        <option value="">All Ratings</option>
                        @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }} ⭐</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-1">
                    <button wire:click="$refresh" class="btn btn-secondary btn-block">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Feedback ({{ $feedback->total() }})</h6>
            <div wire:loading class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
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
                        @if($item->status !== 'resolved')
                        <button wire:click="markAsResolved({{ $item->id }})" class="btn btn-success">
                            <i class="fas fa-check mr-1"></i> Mark Resolved
                        </button>
                        @endif
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
</div>