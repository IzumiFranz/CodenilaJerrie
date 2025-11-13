<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Quiz LMS') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts & Icons (LOCAL) -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- SB Admin 2 CSS (LOCAL) -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- DataTables (Optional - if using tables) -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    @livewireStyles

    <style>
        /* =====================================================================
           LAYOUT STRUCTURE - Fixed Sidebar, Scrollable Content
           ===================================================================== */
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        #wrapper {
            display: flex;
            height: 100vh;
        }

        /* Sidebar - Fixed Position, Full Height */
        #accordionSidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 14rem;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1030;
            transition: all 0.3s ease;
        }

        /* Content Wrapper - Adjust for sidebar */
        #content-wrapper {
            margin-left: 14rem;
            width: calc(100% - 14rem);
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* Topbar - Sticky at top */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            flex-shrink: 0;
        }

        /* Main Content - Scrollable */
        #content {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 2rem;
            scroll-behavior: smooth;
        }

        /* Footer - Sticky at bottom */
        .sticky-footer {
            flex-shrink: 0;
        }

        /* Smooth Scrollbars */
        #content::-webkit-scrollbar,
        #accordionSidebar::-webkit-scrollbar {
            width: 8px;
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

        #content::-webkit-scrollbar-thumb:hover,
        #accordionSidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        /* =====================================================================
           ROLE-BASED COLOR THEMES
           ===================================================================== */
        
        /* ADMIN THEME - Purple/Indigo Gradient */
        @if(auth()->check() && auth()->user()->isAdmin())
        .sidebar-brand,
        .bg-gradient-primary,
        .sidebar {
            background: linear-gradient(180deg, #667eea 10%, #764ba2 100%) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63397d 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }
        
        .border-left-primary {
            border-left: 0.25rem solid #667eea !important;
        }
        
        .text-primary {
            color: #667eea !important;
        }
        
        .icon-circle.bg-primary {
            background-color: #667eea !important;
        }

        .sidebar-brand-icon {
            color: #fff;
        }
        @endif

        /* INSTRUCTOR THEME - Green/Emerald Gradient */
        @if(auth()->check() && auth()->user()->isInstructor())
        .sidebar-brand,
        .bg-gradient-primary,
        .sidebar {
            background: linear-gradient(180deg, #1cc88a 10%, #13855c 100%) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1cc88a 0%, #28a745 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #17a673 0%, #218838 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(28, 200, 138, 0.3);
        }
        
        .border-left-primary,
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        
        .text-primary {
            color: #1cc88a !important;
        }
        
        .icon-circle.bg-primary {
            background-color: #1cc88a !important;
        }

        .sidebar-brand-icon {
            color: #fff;
        }

        /* AI Assistant Badge */
        .ai-badge {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #13855c;
            font-weight: 700;
        }
        @endif

        /* STUDENT THEME - Blue/Cyan Gradient */
        @if(auth()->check() && auth()->user()->isStudent())
        .sidebar-brand,
        .bg-gradient-primary,
        .sidebar {
            background: linear-gradient(180deg, #36b9cc 10%, #2c9faf 100%) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #36b9cc 0%, #4e73df 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2c9faf 0%, #3d5ec1 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(54, 185, 204, 0.3);
        }
        
        .border-left-primary,
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        
        .text-primary {
            color: #36b9cc !important;
        }
        
        .icon-circle.bg-primary {
            background-color: #36b9cc !important;
        }

        .sidebar-brand-icon {
            color: #fff;
        }
        @endif

        /* =====================================================================
           COMMON STYLES
           ===================================================================== */
        
        /* Sidebar Styles */
        .sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-item .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-item.active .nav-link {
            color: #fff;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            z-index: 1;
        }

        .sidebar-brand-icon {
            font-size: 2rem;
        }

        .sidebar-brand-text {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }

        /* Card Hover Effects */
        .card-stats {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .card-stats:hover {
            transform: translateY(-8px);
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
        }

        /* Icon Circles */
        .icon-circle {
            height: 2.5rem;
            width: 2.5rem;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Badge Counter */
        .badge-counter {
            position: absolute;
            transform: scale(0.7);
            transform-origin: top right;
            right: 0.25rem;
            margin-top: -0.25rem;
            font-size: 0.65rem;
            padding: 0.25rem 0.4rem;
        }

        /* Notification Dropdown */
        .dropdown-list {
            max-width: 380px;
            min-width: 320px;
            max-height: 450px;
            overflow: hidden;
        }

        .notification-list {
            max-height: 350px;
            overflow-y: auto;
        }

        .dropdown-list .dropdown-item {
            white-space: normal;
            padding: 1rem;
            border-bottom: 1px solid #e3e6f0;
            transition: all 0.3s;
        }

        .dropdown-list .dropdown-item:hover {
            background-color: #f8f9fc !important;
            transform: translateX(5px);
        }

        .dropdown-list .dropdown-item:last-child {
            border-bottom: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #858796;
        }

        .empty-state i {
            font-size: 3.5rem;
            opacity: 0.3;
            margin-bottom: 1.5rem;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #4e73df;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            color: #fff;
            margin-top: 1rem;
            font-size: 1.1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Timer Warning (for quiz countdown) */
        .timer-warning {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Profile Image */
        .img-profile {
            height: 2rem;
            width: 2rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            #accordionSidebar {
                margin-left: -14rem;
            }

            #accordionSidebar.toggled {
                margin-left: 0;
            }

            #content-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .sidebar-brand-text {
                display: none;
            }
        }

        /* Sidebar Collapsed State */
        body.sidebar-toggled #accordionSidebar {
            width: 4.5rem;
        }

        body.sidebar-toggled #content-wrapper {
            margin-left: 4.5rem;
            width: calc(100% - 4.5rem);
        }

        body.sidebar-toggled .sidebar-brand-text {
            display: none;
        }

        /* Print Styles */
        @media print {
            #accordionSidebar,
            .topbar,
            .sticky-footer,
            .scroll-to-top {
                display: none !important;
            }

            #content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                @include('partials.topbar')

                <!-- Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    @hasSection('page-header')
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">
                                @yield('page-header')
                            </h1>
                            @hasSection('page-actions')
                                <div>
                                    @yield('page-actions')
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Flash Messages -->
                    @include('partials.alerts')

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            @include('partials.footer')
        </div>
    </div>

    <!-- Scroll to Top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Modals -->
    @include('partials.modals')

    <!-- Scripts (LOCAL) -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    @livewireScripts

    <script>
        // =====================================================================
        // CSRF Token Setup for AJAX
        // =====================================================================
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // =====================================================================
        // Auto-hide Flash Messages
        // =====================================================================
        setTimeout(() => {
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, 5000);

        // =====================================================================
        // Delete Confirmation Handler
        // =====================================================================
        let deleteForm = null;
        $('[data-confirm]').on('click', function(e) {
            e.preventDefault();
            deleteForm = $(this).closest('form');
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').on('click', function() {
            if (deleteForm) {
                deleteForm.submit();
            }
        });

        // =====================================================================
        // Form Validation
        // =====================================================================
        document.querySelectorAll('[data-validate]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        // =====================================================================
        // Tooltips & Popovers
        // =====================================================================
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        });

        // =====================================================================
        // Service Worker Registration (PWA - Student Only)
        // =====================================================================
        @if(auth()->check() && auth()->user()->isStudent())
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('✅ Service Worker registered'))
                .catch(err => console.error('❌ Service Worker failed:', err));
        }
        @endif

        // =====================================================================
        // Notification Polling (Real-time updates)
        // =====================================================================
        @if(auth()->check())
        function checkNotifications() {
            fetch('{{ route(auth()->user()->role . ".notifications.count") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        $('.badge-counter').text(data.count > 9 ? '9+' : data.count).show();
                    } else {
                        $('.badge-counter').hide();
                    }
                })
                .catch(err => console.error('Notification check failed:', err));
        }

        // Check every 30 seconds
        setInterval(checkNotifications, 30000);
        @endif
    </script>

    @stack('scripts')
</body>
</html>
