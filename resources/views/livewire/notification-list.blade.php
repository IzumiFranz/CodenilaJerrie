<div class="dropdown no-arrow">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown">
        <i class="fas fa-bell fa-fw"></i>
        @if($unreadCount > 0)
        <span class="badge badge-danger badge-counter">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" style="width: 350px;">
        <h6 class="dropdown-header">
            Notifications
            @if($unreadCount > 0)
            <button wire:click="markAllAsRead" class="btn btn-link btn-sm float-right p-0">
                Mark all as read
            </button>
            @endif
        </h6>
        
        <div style="max-height: 400px; overflow-y: auto;">
            @forelse($notifications as $notification)
            <a class="dropdown-item d-flex align-items-center {{ $notification->isUnread() ? 'bg-light' : '' }}" 
               href="{{ $notification->action_url }}"
               wire:click="markAsRead({{ $notification->id }})">
                <div class="mr-3">
                    <div class="icon-circle bg-{{ $notification->type }}">
                        <i class="fas fa-{{ $notification->type === 'danger' ? 'exclamation-circle' : ($notification->type === 'warning' ? 'exclamation-triangle' : ($notification->type === 'success' ? 'check-circle' : 'info-circle')) }} text-white"></i>
                    </div>
                </div>
                <div>
                    <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                    <strong>{{ $notification->title }}</strong>
                    <p class="mb-0 text-truncate" style="max-width: 250px;">{{ $notification->message }}</p>
                </div>
            </a>
            @empty
            <div class="text-center py-3">
                <i class="fas fa-bell-slash text-muted fa-2x"></i>
                <p class="text-muted mb-0 mt-2">No notifications</p>
            </div>
            @endforelse
        </div>
        
        <a class="dropdown-item text-center small text-gray-500" href="#">Show All Notifications</a>
    </div>
</div>