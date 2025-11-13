<div>
    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Search feedback by user, subject or message...">
                </div>
                <div class="col-md-3 mb-3">
                    <select wire:model.live="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="responded">Responded</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <select wire:model.live="rating" class="form-control">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-1 mb-3">
                    <button wire:click="$refresh" class="btn btn-secondary btn-block">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Cards -->
    <div class="row">
        @forelse($feedback as $item)
            <div class="col-12 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if($item->user->profile_picture)
                                <img src="{{ asset('storage/' . $item->user->profile_picture) }}" 
                                    class="rounded-circle mr-2" 
                                    style="width: 40px; height: 40px; object-fit: cover;"
                                    alt="Avatar">
                            @else
                                <div class="rounded-circle bg-primary text-white mr-2 d-flex align-items-center justify-content-center" 
                                    style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($item->user->username, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <strong>{{ $item->user->username }}</strong>
                                <br><small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div>
                            @if($item->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-success">Responded</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Rating -->
                        @if($item->rating)
                            <div class="mb-2">
                                <div class="text-warning" style="font-size: 1.2rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $item->rating)
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        @endif

                        <!-- Related Item -->
                        @if($item->feedbackable)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-link"></i> 
                                    Related to: 
                                    <span class="badge badge-info">{{ class_basename($item->feedbackable_type) }}</span>
                                    @if($item->feedbackable_type == 'App\Models\Lesson')
                                        {{ $item->feedbackable->title }}
                                    @elseif($item->feedbackable_type == 'App\Models\Quiz')
                                        {{ $item->feedbackable->title }}
                                    @endif
                                </small>
                            </div>
                        @endif

                        <!-- Message -->
                        <p class="mb-3"><strong>Subject:</strong> {{ $item->subject }}</p>
                        <p class="mb-3">{{ $item->message }}</p>

                        <!-- Admin Response -->
                        @if($item->response)
                            <div class="alert alert-info mb-0">
                                <strong><i class="fas fa-reply"></i> Admin Response:</strong>
                                <p class="mb-0 mt-2">{{ $item->response }}</p>
                                <small class="text-muted">
                                    Responded {{ \Carbon\Carbon::parse($item->updated_at)->diffForHumans() }}
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-top d-flex justify-content-between">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-user"></i> {{ $item->user->email }}
                            </small>
                        </div>
                        <div>
                            <a href="{{ route('admin.feedback.show', $item) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            @if($item->status !== 'responded')
                                <button wire:click="markAsResolved({{ $item->id }})" 
                                    wire:confirm="Mark this feedback as responded?"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i> Mark Responded
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Feedback Found</h5>
                        <p class="text-muted mb-0">There are no feedback submissions matching your filters.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($feedback->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $feedback->links() }}
        </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading class="text-center mt-3">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="text-muted mt-2">Loading feedback...</p>
    </div>
</div>