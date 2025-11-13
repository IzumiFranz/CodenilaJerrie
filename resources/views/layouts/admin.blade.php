<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') | {{ config('app.name', 'Quiz LMS') }}</title>

    <!-- Fonts & Icons -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- SB Admin 2 CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- DataTables -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    @livewireStyles

    <style>
        /* Layout Structure - Fixed Sidebar with Scrolling */
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
            min-height: 100vh;
        }

        #accordionSidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 14rem;
            height: 100vh;
            overflow-y: auto;
            z-index: 1030;
            transition: all 0.3s ease;
        }

        #content-wrapper {
            margin-left: 14rem;
            width: calc(100% - 14rem);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        #content {
            flex: 1 0 auto;
            padding-bottom: 2rem;
            width: 100%;
        }
        .container-fluid {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .sticky-footer {
            flex-shrink: 0;
            margin-top: auto;
        }

        /* Smooth Scrollbars */
        #content::-webkit-scrollbar,
        #accordionSidebar::-webkit-scrollbar {
            width: 6px;
        }

        #content::-webkit-scrollbar-track,
        #accordionSidebar::-webkit-scrollbar-track {
            background: #f8f9fc;
        }

        #content::-webkit-scrollbar-thumb,
        #accordionSidebar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }

        /* Admin Theme - Purple Gradient */
        .sidebar {
            background: linear-gradient(180deg, #667eea 10%, #764ba2 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63397d 100%);
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
                <i class="fas fa-user-shield"></i>
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

        <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.settings.index') }}">
                <i class="fas fa-fw fa-cog"></i>
                <span>Settings</span>
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
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <ul class="navbar-nav ml-auto">
                <!-- Notifications -->
                @php
                    $unreadCount = Auth::check() && Auth::user()->notifications ? Auth::user()->notifications()->where('read_at', false)->count() : 0;
                @endphp
                
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" data-toggle="dropdown">
                        <i class="fas fa-bell fa-fw"></i>
                        @if($unreadCount > 0)
                            <span class="badge badge-danger badge-counter">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                        <h6 class="dropdown-header">Notifications</h6>
                        <a class="dropdown-item text-center small text-gray-500" href="{{ route('admin.notifications.index') }}">
                            Show All Notifications
                        </a>
                    </div>
                </li>

                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- User Info -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->full_name ?? Auth::user()->username }}</span>
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
                        <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cog fa-sm fa-fw mr-2 text-gray-400"></i> Settings
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button class="close text-white" type="button" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">Are you sure you want to delete this item?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
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

    let deleteForm = null;
    $('[data-confirm]').on('click', function(e) {
        e.preventDefault();
        deleteForm = $(this).closest('form');
        $('#deleteModal').modal('show');
    });

    $('#confirmDelete').on('click', function() {
        if (deleteForm) deleteForm.submit();
    });

    // Cancel Schedule Function
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
</script>

@stack('scripts')
</body>
</html>