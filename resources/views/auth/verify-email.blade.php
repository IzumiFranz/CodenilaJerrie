<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Quiz & Learning Management System">
    <meta name="author" content="">
    <title>Verify Email - Quiz LMS</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        .verify-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .verify-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .verify-logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: pulse 2s infinite;
        }
        .verify-logo i {
            font-size: 3rem;
            color: white;
        }
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
            }
        }
        .btn-verify {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-verify:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .email-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="verify-container">
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
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-xl-6 col-lg-7 col-md-9">
                <div class="card verify-card">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="verify-logo">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>

                        <!-- Title -->
                        <div class="text-center mb-4">
                            <h1 class="h3 text-gray-900 mb-2">Verify Your Email</h1>
                            <p class="text-muted">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?</p>
                        </div>

                        <!-- Status Messages -->
                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>Success!</strong> A new verification link has been sent to your email address.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Email Address Display -->
                        <div class="card bg-light mb-4">
                            <div class="card-body text-center">
                                <i class="fas fa-envelope email-icon"></i>
                                <p class="mb-0"><strong>Email sent to:</strong></p>
                                <p class="text-primary mb-0 h5">{{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        <!-- Resend Verification Email Form -->
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-verify btn-block">
                                <i class="fas fa-paper-plane mr-2"></i>Resend Verification Email
                            </button>
                        </form>

                        <hr class="my-4">

                        <!-- Instructions -->
                        <div class="alert alert-info mb-4" role="alert">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-info-circle mr-2"></i>What to do next?
                            </h6>
                            <ol class="mb-0 pl-3">
                                <li>Check your email inbox for a verification email from Quiz LMS</li>
                                <li>Click the verification link in the email</li>
                                <li>If you don't see the email, check your spam or junk folder</li>
                                <li>Click "Resend Verification Email" if you need a new link</li>
                            </ol>
                        </div>

                        <!-- Help Section -->
                        <div class="card border-warning mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Didn't receive the email?
                                </h6>
                                <p class="card-text small mb-2">If you haven't received the verification email after a few minutes:</p>
                                <ul class="small mb-0">
                                    <li>Check your spam or junk mail folder</li>
                                    <li>Make sure {{ auth()->user()->email }} is correct</li>
                                    <li>Click the "Resend Verification Email" button above</li>
                                    <li>Contact support if the problem persists</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Logout Form -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>

                        <!-- Support Contact -->
                        <div class="text-center mt-4">
                            <p class="small text-muted mb-0">
                                <i class="fas fa-question-circle mr-1"></i>
                                Need help? <a href="mailto:support@quizlms.com">Contact Support</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-white small">
                        &copy; {{ date('Y') }} Quiz LMS. All Rights Reserved.
                    </p>
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

    <!-- Auto-hide success message after 5 seconds -->
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 5000);
        });
    </script>
</body>
</html>