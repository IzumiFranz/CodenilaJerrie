<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Quiz & Learning Management System">
    <meta name="author" content="">
    <title>Forgot Password - Quiz LMS</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        .forgot-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .forgot-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .forgot-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .forgot-logo i {
            font-size: 2.5rem;
            color: white;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body class="forgot-container">
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
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="card forgot-card">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="forgot-logo">
                            <i class="fas fa-key"></i>
                        </div>

                        <!-- Title -->
                        <div class="text-center mb-4">
                            <h1 class="h3 text-gray-900 mb-2">Forgot Your Password?</h1>
                            <p class="text-muted small">No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Whoops!</strong> There were some problems with your input.
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Forgot Password Form -->
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="Enter your email address" 
                                           required 
                                           autofocus>
                                </div>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-submit btn-block mt-4">
                                <i class="fas fa-paper-plane mr-2"></i>Email Password Reset Link
                            </button>
                        </form>

                        <hr class="my-4">

                        <!-- Back to Login Link -->
                        <div class="text-center">
                            <a class="small text-primary" href="{{ route('login') }}">
                                <i class="fas fa-arrow-left mr-1"></i>Back to Login
                            </a>
                        </div>

                        <!-- Help Text -->
                        <div class="text-center mt-3">
                            <div class="alert alert-info small mb-0" role="alert">
                                <i class="fas fa-info-circle mr-2"></i>
                                If you don't receive an email within a few minutes, please check your spam folder or contact the administrator.
                            </div>
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
</body>
</html>