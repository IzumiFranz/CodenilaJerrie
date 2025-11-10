<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Student') - Quiz LMS</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <!-- Custom student theme styles -->
    <link href="{{ asset('css/student-custom.css') }}" rel="stylesheet">
    <style>
        :root {
            --student-primary: #36b9cc;
            --student-primary-dark: #2c9faf;
            --student-secondary: #4e73df;
        }
        
        .bg-gradient-primary {
            background-color: var(--student-primary);
            background-image: linear-gradient(180deg, var(--student-primary) 10%, var(--student-primary-dark) 100%);
        }
        
        .sidebar-brand {
            background-color: var(--student-primary-dark) !important;
        }
        
        .btn-primary {
            background-color: var(--student-primary);
            border-color: var(--student-primary);
        }
        
        .btn-primary:hover {
            background-color: var(--student-primary-dark);
            border-color: var(--student-primary-dark);
        }
        
        .border-left-primary {
            border-left-color: var(--student-primary) !important;
        }
        
        .text-primary {
            color: var(--student-primary) !important;
        }

        .quiz-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .quiz-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .lesson-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .lesson-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .timer-warning {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

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

    @stack('styles')
    @livewireStyles
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-info sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('student.dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Quiz LMS</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Learning
            </div>

            <!-- Nav Item - Lessons -->
            <li class="nav-item {{ request()->routeIs('student.lessons.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.lessons.index') }}">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Lessons</span>
                </a>
            </li>

            <!-- Nav Item - Quizzes -->
            <li class="nav-item {{ request()->routeIs('student.quizzes.*') || request()->routeIs('student.quiz-attempts.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.quizzes.index') }}">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Quizzes</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Performance
            </div>

            <!-- 🆕 Nav Item - Progress & Grades (NEW!) -->
            <li class="nav-item {{ request()->routeIs('student.progress.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.progress.index') }}">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>My Progress</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Support
            </div>

            <!-- Nav Item - Feedback -->
            <li class="nav-item {{ request()->routeIs('student.feedback.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.feedback.index') }}">
                    <i class="fas fa-fw fa-comment-dots"></i>
                    <span>Feedback</span>
                </a>
            </li>

            <!-- 🆕 Nav Item - Notifications (NEW!) -->
            <li class="nav-item {{ request()->routeIs('student.notifications.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.notifications.index') }}">
                    <i class="fas fa-fw fa-bell"></i>
                    <span>Notifications</span>
                    @if($unreadCount > 0)
                        <span class="badge badge-danger badge-counter ml-2">{{ $unreadCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Nav Item - Settings -->
            <li class="nav-item {{ request()->routeIs('student.settings.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.settings.index') }}">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Alerts -->
                      <!-- Topbar -->
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
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

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ Auth::user()->student->full_name }}
                                </span>
                                @if(Auth::user()->profile_picture)
                                    <img class="img-profile rounded-circle" 
                                        src="{{ Storage::url(Auth::user()->profile_picture) }}"
                                        alt="Profile">
                                @else
                                    <img class="img-profile rounded-circle" 
                                        src="{{ asset('img/undraw_profile.svg') }}"
                                        alt="Default Profile">
                                @endif
                            </a>
                            
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('profile.change-password') }}">
                                    <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Change Password
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                    </nav>
                    <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Quiz LMS {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
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

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    @stack('scripts')
    @livewireScripts
    <script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(reg => console.log('Service Worker registered'))
        .catch(err => console.log('Service Worker registration failed'));
}
</script>
</body>
</html>