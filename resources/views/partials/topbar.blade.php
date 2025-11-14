nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search (Optional) -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" 
                   placeholder="Search..." aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Notifications Dropdown -->
        @php
            $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
            $recentNotifications = auth()->user()->notifications()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        @endphp

        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                @if($unreadCount > 0)
                    <span class="badge badge-danger badge-counter">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </a>
            
            <!-- Dropdown - Notifications -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <h6 class="dropdown-header bg-primary text-white">
                    <i class="fas fa-bell"></i> Notifications
                </h6>
                
                <div class="notification-list">
                    @forelse($recentNotifications as $notification)
                        <a class="dropdown-item d-flex align-items-center {{ !$notification->is_read ? 'bg-light' : '' }}" 
                           href="{{ route(auth()->user()->role . '.notifications.mark-read', $notification) }}">
                            <div class="mr-3">
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
                                    <span class="badge badge-info badge-sm ml-1">New</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="dropdown-item text-center py-4">
                            <i class="fas fa-bell-slash fa-2x text-gray-300 mb-2"></i>
                            <p class="mb-0 text-muted">No notifications</p>
                        </div>
                    @endforelse
                </div>
                
                <a class="dropdown-item text-center small text-gray-500 bg-light" 
                   href="{{ route(auth()->user()->role . '.notifications.index') }}">
                    <i class="fas fa-eye"></i> View All Notifications
                </a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ auth()->user()->full_name ?? auth()->user()->username }}
                </span>
                @if(auth()->user()->profile_picture)
                    <img class="img-profile rounded-circle" 
                         src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
                         alt="Profile">
                @else
                    <img class="img-profile rounded-circle" 
                         src="{{ asset('img/undraw_profile.svg') }}" 
                         alt="Default Profile">
                @endif
            </a>
            
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="{{ route('profile.change-password') }}">
                    <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
                    Change Password
                </a>
                @if(!auth()->user()->isStudent())
                <a class="dropdown-item" href="{{ route(auth()->user()->role . '.settings.index') }}">
                    <i class="fas fa-cog fa-sm fa-fw mr-2 text-gray-400"></i>
                    Settings
                </a>
                @endif
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>
