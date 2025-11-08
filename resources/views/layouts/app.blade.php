<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - Quiz LMS</title>

    <!-- Custom fonts - Font Awesome (LOCAL) -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    
    <!-- Google Fonts (HTTPS - Safe) -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS (LOCAL) -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- SB Admin 2 CSS (LOCAL) -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Role-Based Custom CSS -->
    <style>
        /* Admin Theme - Purple Gradient */
        @if(auth()->user()->isAdmin())
        .sidebar-brand,
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
        .sidebar .nav-item.active .nav-link {
            background: rgba(102, 126, 234, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63397d 100%);
        }
        .border-left-primary {
            border-left: 0.25rem solid #667eea !important;
        }
        .text-primary {
            color: #667eea !important;
        }
        @endif

        /* Instructor Theme - Green */
        @if(auth()->user()->isInstructor())
        .sidebar-brand,
        .bg-gradient-primary {
            background: linear-gradient(135deg, #1cc88a 0%, #28a745 100%) !important;
        }
        .sidebar .nav-item.active .nav-link {
            background: rgba(28, 200, 138, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #1cc88a 0%, #28a745 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #17a673 0%, #218838 100%);
        }
        .border-left-primary {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        .text-primary {
            color: #1cc88a !important;
        }
        @endif

        /* Student Theme - Blue/Cyan */
        @if(auth()->user()->isStudent())
        .sidebar-brand,
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%) !important;
        }
        .sidebar .nav-item.active .nav-link {
            background: rgba(78, 115, 223, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3d5ec1 0%, #2a96a5 100%);
        }
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        .text-primary {
            color: #4e73df !important;
        }
        @endif

        /* Common Styles */
        .card-stats {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
        }
        .sidebar .nav-item .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }
    </style>

    @stack('styles')
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('partials.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('partials.topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    @hasSection('page-header')
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">@yield('page-header')</h1>
                            @hasSection('page-actions')
                                <div>
                                    @yield('page-actions')
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Flash Messages -->
                    @include('components.alert')

                    <!-- Page Content -->
                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('partials.footer')
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
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery (LOCAL) -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap Bundle JS (LOCAL) -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- jQuery Easing (LOCAL) -->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- SB Admin 2 JS (LOCAL) -->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Custom Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // CSRF Token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
