<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Student Dashboard') | {{ config('app.name', 'Quiz LMS') }}</title>

    <!-- Fonts & Icons -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- SB Admin 2 CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    @livewireStyles

    <style>
    /* FIXED LAYOUT - Proper Scrolling */
    html, body {
        height: 100%;
        margin: 0;
    }

    body {
        overflow-x: hidden;
        overflow-y: auto;
    }

    #wrapper {
        display: flex;
        min-height: 100vh; /* Changed from height: 100vh */
    }

    /* Fixed Sidebar */
    #accordionSidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 14rem;
        height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 1030;
        transition: margin-left 0.3s ease;
    }

    /* Scrollbar for sidebar */
    #accordionSidebar::-webkit-scrollbar {
        width: 6px;
    }
    #accordionSidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }
    #accordionSidebar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
    }

    /* Content Wrapper */
    #content-wrapper {
        margin-left: 14rem;
        width: calc(100% - 14rem);
        min-height: 100vh; /* Changed from height: 100vh */
        display: flex;
        flex-direction: column;
        transition: margin-left 0.3s ease, width 0.3s ease;
    }

    /* Sticky Topbar */
    .topbar {
        position: sticky;
        top: 0;
        z-index: 1020;
        flex-shrink: 0;
    }

    /* Content Area */
    #content {
        flex: 1 0 auto; /* Changed from flex: 1 */
        padding-bottom: 2rem;
        width: 100%;
    }

    /* Footer - Sticky at bottom */
    .sticky-footer {
        flex-shrink: 0;
        margin-top: auto;
    }

    /* Container Fluid */
    .container-fluid {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        width: 100%;
    }

    /* Student Theme - Blue/Cyan */
    .sidebar {
        background: linear-gradient(180deg, #36b9cc 10%, #2c9faf 100%);
    }

    .sidebar .nav-item .nav-link {
        color: rgba(255, 255, 255, 0.8);
        transition: all 0.3s;
    }

    .sidebar .nav-item .nav-link:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar .nav-item.active .nav-link {
        color: #fff;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #36b9cc 0%, #4e73df 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2c9faf 0%, #3d5ec1 100%);
        transform: translateY(-2px);
    }

    .icon-circle {
        height: 2.5rem;
        width: 2.5rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .badge-counter {
        position: absolute;
        transform: scale(0.7);
        transform-origin: top right;
        right: 0.25rem;
        margin-top: -0.25rem;
    }

    .timer-warning {
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        #accordionSidebar {
            margin-left: -14rem;
        }
        #content-wrapper {
            margin-left: 0;
            width: 100%;
        }
        #accordionSidebar.toggled {
            margin-left: 0;
        }
    }

    /* Sidebar Toggled State */
    body.sidebar-toggled #accordionSidebar {
        width: 6.5rem;
    }
    body.sidebar-toggled #content-wrapper {
        margin-left: 6.5rem;
        width: calc(100% - 6.5rem);
    }
    body.sidebar-toggled #accordionSidebar .sidebar-brand-text {
        display: none;
    }
</style>
    @stack('styles')
</head>

<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('student.dashboard') }}">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="sidebar-brand-text mx-3">Quiz LMS</div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">Learning</div>

        <li class="nav-item {{ request()->routeIs('student.lessons.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.lessons.index') }}">
                <i class="fas fa-fw fa-book-reader"></i>
                <span>Lessons</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('student.quizzes.*', 'student.quiz-attempts.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.quizzes.index') }}">
                <i class="fas fa-fw fa-pen"></i>
                <span>Quizzes</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">Performance</div>

        <li class="nav-item {{ request()->routeIs('student.progress.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.progress.index') }}">
                <i class="fas fa-fw fa-chart-line"></i>
                <span>My Progress</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">Support</div>

        <li class="nav-item {{ request()->routeIs('student.feedback.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.feedback.index') }}">
                <i class="fas fa-fw fa-comment-dots"></i>
                <span>Feedback</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('student.notifications.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.notifications.index') }}">
                <i class="fas fa-fw fa-bell"></i>
                <span>Notifications</span>
                @php
                    $unreadCount = Auth::check() && Auth::user()->notifications ? Auth::user()->notifications()->where('read_at', false)->count() : 0;
                @endphp
                @if($unreadCount > 0)
                    <span class="badge badge-danger badge-counter ml-1">{{ $unreadCount }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('student.settings.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.settings.index') }}">
                <i class="fas fa-fw fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search..." aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            <ul class="navbar-nav ml-auto">
                @php
                    $unreadCount = Auth::check() && Auth::user()->notifications ? Auth::user()->notifications()->where('read_at', false)->count() : 0;
                    $recentNotifications = Auth::check() && Auth::user()->notifications ? Auth::user()->notifications()->orderBy('created_at', 'desc')->limit(5)->get() : collect();
                @endphp

                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" data-toggle="dropdown">
                        <i class="fas fa-bell fa-fw"></i>
                        @if($unreadCount > 0)
                            <span class="badge badge-danger badge-counter">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" style="max-width: 380px; min-width: 320px;">
                        <h6 class="dropdown-header bg-info text-white">
                            <i class="fas fa-bell"></i> Notifications
                        </h6>
                        
                        <div style="max-height: 400px; overflow-y: auto;">
                            @forelse($recentNotifications as $notification)
                                <a class="dropdown-item d-flex align-items-center {{ !$notification->read_at ? 'bg-light' : '' }}" 
                                   href="{{ route('student.notifications.mark-read', $notification) }}">
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
                                        <span class="font-weight-bold">{{ Str::limit($notification->title ?? 'Notification', 50) }}</span>
                                        @if(!$notification->read_at)
                                            <span class="badge badge-info badge-sm ml-1">New</span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="dropdown-item text-center py-3">
                                    <i class="fas fa-bell-slash text-muted"></i>
                                    <p class="mb-0 text-muted">No notifications</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <a class="dropdown-item text-center small text-gray-500 bg-light" href="{{ route('student.notifications.index') }}">
                            <i class="fas fa-eye"></i> View All Notifications
                        </a>
                    </div>
                </li>

                <div class="topbar-divider d-none d-sm-block"></div>

                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            {{ Auth::user()->full_name ?? Auth::user()->username }}
                        </span>
                        @if(Auth::user()->profile_picture)
                            <img class="img-profile rounded-circle" src="{{ asset('storage/' . Auth::user()->profile_picture) }}">
                        @else
                            <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}">
                        @endif
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                        </a>
                        <a class="dropdown-item" href="{{ route('profile.change-password') }}">
                            <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i> Change Password
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- SCROLLABLE Page Content -->
        <div id="content">
            <div class="container-fluid">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Validation Errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Quiz LMS {{ date('Y') }}</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Scroll to Top -->
<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">Select "Logout" to end your session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

@livewireScripts

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    setTimeout(() => $('.alert').fadeOut('slow'), 5000);

    // Service Worker for PWA
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('✅ Service Worker registered'))
            .catch(err => console.error('❌ SW registration failed:', err));
    }
</script>

@stack('scripts')
</body>
</html>