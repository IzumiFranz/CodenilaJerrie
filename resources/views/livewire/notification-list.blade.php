<div class="dropdown no-arrow">
    <!-- Notification Bell Icon -->
    <a class="nav-link dropdown-toggle" 
        href="#" 
        id="alertsDropdown" 
        role="button" 
        data-toggle="dropdown" 
        aria-haspopup="true" 
        aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        @if($unreadCount > 0)
            <span class="badge badge-danger badge-counter">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </a>

    <!-- Dropdown Menu -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" 
        style="width: 380px; max-height: 500px;">
        
        <!-- Header -->
        <h6 class="dropdown-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-bell"></i> Notifications
            </span>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" 
                    class="btn btn-link btn-sm text-primary p-0" 
                    style="font-size: 0.75rem;">
                    <i class="fas fa-check-double"></i> Mark all as read
                </button>
            @endif
        </h6>

        <!-- Notifications List -->
        <div style="max-height: 400px; overflow-y: auto;">
            @forelse($notifications as $notification)
                <a class="dropdown-item d-flex align-items-start py-3 {{ $notification->read_at ? '' : 'bg-light' }}" 
                    href="{{ $notification->action_url ?? '#' }}"
                    wire:click="markAsRead({{ $notification->id }})">
                    
                    <!-- Icon -->
                    <div class="mr-3 flex-shrink-0">
                        <div class="icon-circle bg-{{ $notification->type }}" style="width: 40px; height: 40px;">
                            @if($notification->type == 'info')
                                <i class="fas fa-info-circle text-white"></i>
                            @elseif($notification->type == 'success')
                                <i class="fas fa-check-circle text-white"></i>
                            @elseif($notification->type == 'warning')
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            @else
                                <i class="fas fa-exclamation-circle text-white"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <strong class="text-gray-800" style="font-size: 0.9rem;">
                                {{ $notification->title }}
                            </strong>
                            @if(!$notification->read_at)
                                <span class="badge badge-primary badge-sm ml-2">New</span>
                            @endif
                        </div>
                        
                        <div class="text-gray-700 mb-1" style="font-size: 0.85rem;">
                            {{ Str::limit($notification->message, 100) }}
                        </div>
                        
                        <div class="small text-gray-500">
                            <i class="far fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash text-muted fa-3x mb-3"></i>
                    <p class="text-muted mb-0">No notifications yet</p>
                    <small class="text-muted">You're all caught up!</small>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($notifications->count() > 0)
            <a class="dropdown-item text-center small text-gray-500 py-2" 
                href="{{ route('admin.notifications.index') }}">
                <i class="fas fa-list"></i> Show All Notifications
            </a>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="position-absolute" style="top: 10px; right: 10px;">
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>

@push('styles')
<style>
    .icon-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fc;
    }
    
    .dropdown-item.bg-light {
        background-color: #e3f2fd !important;
        border-left: 3px solid #2196f3;
    }
    
    .badge-counter {
        position: absolute;
        transform: scale(0.7);
        transform-origin: top right;
        right: 0.25rem;
        margin-top: -0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-refresh notifications every 30 seconds
    setInterval(function() {
        @this.call('loadNotifications');
    }, 30000);

    // Listen for notification events
    window.addEventListener('notification-received', event => {
        @this.call('loadNotifications');
    });
</script>
@endpush