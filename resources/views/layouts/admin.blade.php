<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') | {{ config('app.name', 'Quiz LMS') }}</title>

    <!-- Fonts & Icons -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- SB Admin 2 CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- DataTables -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <!-- Custom Admin Styles -->
    <link href="{{ asset('css/admin-custom.css') }}" rel="stylesheet">

    @livewireStyles

    <style>
        /* --- Layout Fixes --- */
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        #wrapper {
            display: flex;
            height: 100%;
        }

        #accordionSidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            overflow-y: auto;
            z-index: 1030;
        }

        #content-wrapper {
            margin-left: 14rem; /* width of sidebar */
            width: calc(100% - 14rem);
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        #content {
            flex: 1 1 auto;
            overflow-y: auto;
            padding-bottom: 1rem;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        /* Sidebar colors */
        .sidebar {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }

        .sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }

        .sidebar .nav-item .nav-link:hover {
            color: #fff;
        }

        .sidebar .nav-item.active .nav-link {
            color: #fff;
            font-weight: 700;
        }

        /* Misc */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #858796;
        }

        .empty-state i {
            font-size: 3rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        .icon-circle {
            height: 2.5rem;
            width: 2.5rem;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4e73df;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        /* Smooth scrolling */
        #content-wrapper, #accordionSidebar {
            scroll-behavior: smooth;
        }

        /* Hide scrollbars but allow scrolling */
        #content-wrapper::-webkit-scrollbar,
        #accordionSidebar::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        #content-wrapper,
        #accordionSidebar {
            -ms-overflow-style: none;  /* IE/Edge */
            scrollbar-width: thin;     /* Firefox */
        }

        /* Optional: hover to show a thin scrollbar */
        #content-wrapper:hover::-webkit-scrollbar,
        #accordionSidebar:hover::-webkit-scrollbar {
            width: 6px;
        }

        #content-wrapper::-webkit-scrollbar-thumb,
        #accordionSidebar::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
            border-radius: 3px;
        }


        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            #content-wrapper {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>

    @stack('styles')
</head>

<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="sidebar-brand-text mx-3">Admin Panel</div>
        </a>

        <hr class="sidebar-divider my-0">

        <!-- Dashboard -->
        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <!-- User Management -->
        <div class="sidebar-heading">User Management</div>

        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Users</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <!-- Academic Management -->
        <div class="sidebar-heading">Academic Management</div>

        <li class="nav-item {{ request()->routeIs('admin.specializations.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.specializations.index') }}">
                <i class="fas fa-fw fa-certificate"></i>
                <span>Specializations</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.courses.index') }}">
                <i class="fas fa-fw fa-book"></i>
                <span>Courses</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.subjects.index') }}">
                <i class="fas fa-fw fa-book-open"></i>
                <span>Subjects</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.sections.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.sections.index') }}">
                <i class="fas fa-fw fa-chalkboard"></i>
                <span>Sections</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.enrollments.index') }}">
                <i class="fas fa-fw fa-user-graduate"></i>
                <span>Enrollments</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.assignments.index') }}">
                <i class="fas fa-fw fa-chalkboard-teacher"></i>
                <span>Assignments</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <!-- Content -->
        <div class="sidebar-heading">Content</div>

        <li class="nav-item {{ request()->routeIs('admin.lessons.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.lessons.index') }}">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Lessons</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.quizzes.index') }}">
                <i class="fas fa-fw fa-clipboard-check"></i>
                <span>Quizzes</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <!-- System -->
        <div class="sidebar-heading">System</div>

        <li class="nav-item {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.feedback.index') }}">
                <i class="fas fa-fw fa-comments"></i>
                <span>Feedback</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.notifications.index') }}">
                <i class="fas fa-fw fa-bell"></i>
                <span>Notifications</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.audit-logs.index') }}">
                <i class="fas fa-fw fa-history"></i>
                <span>Audit Logs</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <ul class="navbar-nav ml-auto">
                <!-- Alerts -->
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" data-toggle="dropdown">
                        <i class="fas fa-bell fa-fw"></i>
                        @if($unreadCount > 0)
                         <span class="badge badge-danger badge-counter">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif

                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                        <h6 class="dropdown-header">Alerts Center</h6>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">{{ now()->format('F d, Y') }}</div>
                                <span class="font-weight-bold">New user registered!</span>
                            </div>
                        </a>
                        <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                    </div>
                </li>

                <div class="topbar-divider d-none d-sm-block"></div>

                <ul class="navbar-nav ml-auto">

                        <!-- 🆕 Nav Item - Notifications -->
                        @php
                            $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
                            $recentNotifications = Auth::user()->notifications()
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
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

                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- User Info -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->full_name }}</span>
                        @if(auth()->user()->profile_picture)
                            <img class="img-profile rounded-circle" src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile">
                        @else
                            <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}" alt="Profile">
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

        <!-- Page Content -->
        <div id="content" class="p-4">
            @include('partials.alerts')
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white mt-auto">
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

@include('partials.modals')

<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/admin-custom.js') }}"></script>
@livewireScripts

<script>
    // Auto-hide alerts after 5s
    setTimeout(() => $('.alert').fadeOut('slow'), 5000);

    // Delete confirmation handler
    let deleteForm = null;
    $('[data-confirm]').on('click', function(e) {
        e.preventDefault();
        deleteForm = $(this).closest('form');
        $('#deleteModal').modal('show');
    });
    $('#confirmDelete').on('click', function() {
        if (deleteForm) deleteForm.submit();
    });
</script>

@stack('scripts')
</body>
</html>
