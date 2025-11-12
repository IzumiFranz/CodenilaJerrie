<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Quiz LMS Instructor</title>

    <!-- SB Admin 2 CSS -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/instructor-custom.css') }}" rel="stylesheet">



    <style>
        .sidebar { background: linear-gradient(180deg, #1cc88a 10%, #13855c 100%); }
        .sidebar .nav-item .nav-link { color: rgba(255, 255, 255, 0.8); }
        .sidebar .nav-item .nav-link:hover { color: #fff; }
        .sidebar .nav-item.active .nav-link { color: #fff; font-weight: 700; }
        .sidebar-brand { color: #fff !important; }
        .btn-primary { background-color: #1cc88a; border-color: #1cc88a; }
        .btn-primary:hover { background-color: #17a673; border-color: #17a673; }
        .text-primary { color: #1cc88a !important; }
        .border-left-primary { border-left: 0.25rem solid #1cc88a !important; }
    </style>

    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('instructor.dashboard') }}">
                <div class="sidebar-brand-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="sidebar-brand-text mx-3">Instructor</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('instructor.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">

            <li class="nav-item {{ request()->routeIs('instructor.ai.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('instructor.ai.index') }}">
                    <i class="fas fa-fw fa-robot"></i>
                    <span>AI Assistant</span>
                    @php
                        $pendingJobs = \App\Models\AIJob::where('user_id', auth()->id())
                            ->whereIn('status', ['pending', 'processing'])
                            ->count();
                    @endphp
                    @if($pendingJobs > 0)
                        <span class="badge badge-warning badge-counter">{{ $pendingJobs }}</span>
                    @endif
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Content</div>

            <li class="nav-item {{ request()->routeIs('instructor.lessons.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('instructor.lessons.index') }}">
                    <i class="fas fa-fw fa-book"></i><span>Lessons</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('instructor.question-bank.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('instructor.question-bank.index') }}">
                    <i class="fas fa-fw fa-question-circle"></i><span>Question Bank</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('instructor.quizzes.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('instructor.quizzes.index') }}">
                    <i class="fas fa-fw fa-clipboard-list"></i><span>Quizzes</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Students</div>

            <li class="nav-item {{ request()->routeIs('instructor.student-progress.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('instructor.student-progress.index') }}">
                    <i class="fas fa-fw fa-chart-line"></i><span>Student Progress</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-bell fa-fw"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="badge badge-danger badge-counter">{{ auth()->user()->unreadNotifications->count() }}</span>
                                @endif
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                <h6 class="dropdown-header">Notifications</h6>
                                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-{{ $notification->type }}">
                                                <i class="fas fa-info text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                                            <span class="font-weight-bold">{{ $notification->title }}</span>
                                        </div>
                                    </a>
                                @empty
                                    <a class="dropdown-item text-center small text-gray-500" href="#">No notifications</a>
                                @endforelse
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->full_name }}</span>
                                <img class="img-profile rounded-circle" src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->full_name) }}">
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
                <div class="container-fluid">
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

                    @yield('content')
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Quiz LMS {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

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
    
    <script>
    function cancelSchedule(id, type) {
        if (confirm('Are you sure you want to cancel the scheduled publish?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = type === 'lesson' 
                ? `/instructor/lessons/${id}/schedule`
                : `/instructor/quizzes/${id}/schedule`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Validate unpublish date is after publish date
    $(document).ready(function() {
        $('input[name="scheduled_publish_at"]').on('change', function() {
            const publishDate = new Date($(this).val());
            const unpublishInput = $('input[name="scheduled_unpublish_at"]');
            const currentUnpublish = new Date(unpublishInput.val());
            
            // Set minimum unpublish date to 1 hour after publish date
            if (publishDate) {
                const minUnpublish = new Date(publishDate.getTime() + 60 * 60 * 1000);
                unpublishInput.attr('min', minUnpublish.toISOString().slice(0, 16));
                
                // Clear unpublish if it's before the new publish date
                if (currentUnpublish && currentUnpublish <= publishDate) {
                    unpublishInput.val('');
                }
            }
        });
    });
    </script>
    @stack('scripts')
</body>
</html>