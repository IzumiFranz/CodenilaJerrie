<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Quiz & Learning Management System">
    <meta name="author" content="">
    <title>Welcome - Quiz LMS</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
        }

        /* Section fade-in animation */
        section, .feature-card, .stats-section, .about-section {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.7s ease;
        }
        section.visible, .feature-card.visible, .stats-section.visible, .about-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Navbar smooth transition on scroll */
        .navbar {
            transition: background 0.4s, padding 0.3s;
        }
        .navbar.scrolled {
            background: rgba(0, 0, 0, 0.6) !important;
            backdrop-filter: blur(8px);
            padding: 0.75rem 0 !important;
        }
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,101.3C1248,85,1344,75,1392,69.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            animation: wave 20s linear infinite;
        }
        .scrollable-hidden-scrollbar {
            overflow: scroll; /* Enable scrolling */
            -ms-overflow-style: none; /* For Internet Explorer and Edge */
            scrollbar-width: none; /* For Firefox */
        }

        .scrollable-hidden-scrollbar::-webkit-scrollbar {
            display: none; /* For Chrome, Safari, and Opera */
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

        @keyframes wave {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .logo-large {
            width: 150px;
            height: 150px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }
        .logo-large i {
            font-size: 5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2);
        }
        .feature-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
        }
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .feature-icon i {
            font-size: 2rem;
            color: white;
        }
        .stats-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            margin: 3rem 0;
        }
        .stat-item {
            text-align: center;
            color: white;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
        }
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }
    </style>
</head>

<body class="hero-section scrollable-hidden-scrollbar">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(0, 0, 0, 0.1);">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-graduation-cap mr-2"></i>Quiz LMS
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route(auth()->user()->role . '.dashboard') }}">
                                <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt mr-1"></i>Login
                            </a>
                        </li>
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link btn btn-light btn-sm ml-2 px-3" href="{{ route('register') }}" style="color: black;">
                                <i class="fas fa-user-plus mr-1" ></i>Register
                            </a>
                        </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="container hero-content">
        <div class="row align-items-center" style="min-height: 80vh;">
            <div class="col-lg-6 text-center text-lg-left text-white mb-5 mb-lg-0">
                <div class="logo-large d-lg-none">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="display-3 font-weight-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">
                    Quiz & Learning Management System
                </h1>
                <p class="lead mb-5" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                    Empower education with AI-powered quizzes, comprehensive learning materials, and real-time progress tracking. The future of learning starts here.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start">
                    @auth
                        <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="btn btn-light btn-hero mb-3 mb-sm-0 mr-sm-3">
                            <i class="fas fa-tachometer-alt mr-2"></i>Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light btn-hero mb-3 mb-sm-0 mr-sm-3">
                            <i class="fas fa-sign-in-alt mr-2"></i>Get Started
                        </a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-hero">
                            <i class="fas fa-user-plus mr-2"></i>Sign Up Free
                        </a>
                        @endif
                    @endauth
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="logo-large" style="width: 300px; height: 300px;">
                    <i class="fas fa-graduation-cap" style="font-size: 10rem;"></i>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="row">
                <div class="col-md-3 col-6 mb-3 mb-md-0">
                    <div class="stat-item">
                        <span class="stat-number"><i class="fas fa-users"></i></span>
                        <span class="stat-label">Students & Instructors</span>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3 mb-md-0">
                    <div class="stat-item">
                        <span class="stat-number"><i class="fas fa-book"></i></span>
                        <span class="stat-label">Interactive Lessons</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number"><i class="fas fa-question-circle"></i></span>
                        <span class="stat-label">AI-Generated Quizzes</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number"><i class="fas fa-chart-line"></i></span>
                        <span class="stat-label">Real-time Analytics</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" style="background: white; padding: 5rem 0;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 font-weight-bold mb-3">Powerful Features</h2>
                <p class="lead text-muted">Everything you need for modern education</p>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4 class="text-center mb-3">AI-Powered Quiz Generation</h4>
                        <p class="text-muted text-center">Automatically generate high-quality quizzes from your lesson materials using advanced AI technology.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <h4 class="text-center mb-3">Interactive Learning</h4>
                        <p class="text-muted text-center">Rich multimedia lessons with file attachments, videos, and interactive content to engage students.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4 class="text-center mb-3">Real-time Analytics</h4>
                        <p class="text-muted text-center">Track student progress, quiz performance, and identify areas needing improvement instantly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h4 class="text-center mb-3">Auto-Grading System</h4>
                        <p class="text-muted text-center">Save time with automatic grading for multiple-choice and true/false questions.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h4 class="text-center mb-3">Question Bank</h4>
                        <p class="text-muted text-center">Build and manage a comprehensive question repository with difficulty levels and tags.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h4 class="text-center mb-3">Role-Based Access</h4>
                        <p class="text-muted text-center">Secure system with distinct roles for admins, instructors, and students.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div id="about" style="background: #f8f9fc; padding: 5rem 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="display-4 font-weight-bold mb-4">About Quiz LMS</h2>
                    <p class="lead mb-4">A comprehensive learning management system designed to streamline education with cutting-edge technology.</p>
                    <p class="text-muted mb-4">Our platform combines traditional learning methods with modern AI capabilities to create an engaging and effective educational experience. Whether you're an administrator managing courses, an instructor creating content, or a student learning new concepts, Quiz LMS provides the tools you need to succeed.</p>
                    <div class="row mb-4">
                        <div class="col-6">
                            <h5><i class="fas fa-check-circle text-success mr-2"></i>Easy to Use</h5>
                            <p class="text-muted small">Intuitive interface for all user types</p>
                        </div>
                        <div class="col-6">
                            <h5><i class="fas fa-check-circle text-success mr-2"></i>Secure</h5>
                            <p class="text-muted small">Bank-level security for your data</p>
                        </div>
                        <div class="col-6">
                            <h5><i class="fas fa-check-circle text-success mr-2"></i>Scalable</h5>
                            <p class="text-muted small">Grows with your institution</p>
                        </div>
                        <div class="col-6">
                            <h5><i class="fas fa-check-circle text-success mr-2"></i>24/7 Support</h5>
                            <p class="text-muted small">Always here to help you</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5 text-center">
                            <i class="fas fa-graduation-cap" style="font-size: 8rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                            <h3 class="mt-4 mb-3">Ready to Transform Education?</h3>
                            <p class="text-muted mb-4">Join thousands of educators and students already using Quiz LMS</p>
                            @guest
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-rocket mr-2"></i>Get Started Now
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #2d3748; color: white; padding: 3rem 0;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3"><i class="fas fa-graduation-cap mr-2"></i>Quiz LMS</h5>
                    <p class="text-white-50">Empowering education through innovative technology and AI-powered learning solutions.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-white-50">Features</a></li>
                        <li><a href="#about" class="text-white-50">About</a></li>
                        <li><a href="{{ route('login') }}" class="text-white-50">Login</a></li>
                        @if (Route::has('register'))
                        <li><a href="{{ route('register') }}" class="text-white-50">Register</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Contact</h5>
                    <p class="text-white-50 mb-2"><i class="fas fa-envelope mr-2"></i>quizlms.edu@gmail.com</p>
                    <p class="text-white-50 mb-2"><i class="fas fa-phone mr-2"></i>(+63) 936 - 617 - 4944</p>
                    <p class="text-white-50"><i class="fas fa-map-marker-alt mr-2"></i>Bulihan, City of Malolos, Bulacan, Philippines</p>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center text-white-50">
                <p class="mb-0">&copy; {{ date('Y') }} Quiz LMS. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Smooth scroll -->
    <script>
         // Navbar shrink on scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });

    // Section fade-in animation
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('visible');
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('section, .feature-card, .stats-section, .about-section')
        .forEach(el => observer.observe(el));
        $(document).ready(function(){
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if( target.length ) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 70
                    }, 1000);
                }
            });
        });
    </script>
</body>
</html>