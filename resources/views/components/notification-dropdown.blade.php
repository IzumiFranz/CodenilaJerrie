<!-- Notifications Dropdown -->
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Counter - Unread Notifications -->
        @if($unreadCount > 0)
            <span class="badge badge-danger badge-counter">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </a>
    
    <!-- Dropdown - Notifications -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
         aria-labelledby="alertsDropdown" style="max-width: 380px; min-width: 320px;">
        <h6 class="dropdown-header bg-info text-white">
            <i class="fas fa-bell"></i> Notifications
        </h6>
        
        <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
            @forelse($recentNotifications as $notification)
                <a class="dropdown-item d-flex align-items-center {{ !$notification->is_read ? 'bg-light' : '' }}" 
                   href="{{ route('student.notifications.mark-read', $notification) }}">
                    <div class="me-3">
                        @if($notification->type === 'info')
                            <div class="icon-circle bg-info">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                        @elseif($notification->type === 'success')
                            <div class="icon-circle bg-success">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                        @elseif($notification->type === 'warning')
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        @else
                            <div class="icon-circle bg-danger">
                                <i class="fas fa-exclamation-circle text-white"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                        <span class="font-weight-bold">{{ Str::limit($notification->title, 50) }}</span>
                        @if(!$notification->is_read)
                            <span class="badge bg-info ms-1">New</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="dropdown-item text-center py-3">
                    <i class="fas fa-bell-slash text-muted"></i>
                    <p class="mb-0 text-muted">No new notifications</p>
                </div>
            @endforelse
        </div>
        
        <a class="dropdown-item text-center small text-gray-500 bg-light" href="{{ route('student.notifications.index') }}">
            <i class="fas fa-eye"></i> View All Notifications
        </a>
    </div>
</li>

<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dropdown-list {
    position: relative;
}

.dropdown-list .dropdown-item {
    white-space: normal;
    padding: 1rem;
    border-bottom: 1px solid #e3e6f0;
    transition: all 0.3s;
}

.dropdown-list .dropdown-item:hover {
    background-color: #f8f9fc !important;
}

.dropdown-list .dropdown-item:last-child {
    border-bottom: none;
}

.badge-counter {
    position: absolute;
    transform: scale(0.7);
    transform-origin: top right;
    right: 0.25rem;
    margin-top: -0.25rem;
}
</style>