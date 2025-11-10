<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Quiz & Learning Management System">
    <meta name="author" content="">
    <title>Login - Quiz LMS</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .login-logo i {
            font-size: 2.5rem;
            color: white;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body class="login-container">
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
                            <a class="nav-link" href="">
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
                <div class="card login-card">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="login-logo">
                            <i class="fas fa-graduation-cap"></i>
                        </div>

                        <!-- Title -->
                        <div class="text-center mb-4">
                            <h1 class="h3 text-gray-900 mb-2">Welcome Back!</h1>
                            <p class="text-muted">Quiz & Learning Management System</p>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Error Messages -->
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Username -->
                            <div class="form-group">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </div>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username') }}" 
                                           placeholder="Enter username" 
                                           required 
                                           autofocus>
                                </div>
                                @error('username')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter password" 
                                           required>
                                </div>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="form-group">
                                <div class="custom-control custom-checkbox small">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="remember_me" 
                                           name="remember">
                                    <label class="custom-control-label" for="remember_me">
                                        Remember Me
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-login btn-block">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </button>
                        </form>

                        <hr class="my-4">

                        <!-- Links -->
                        <div class="text-center">
                            <a class="small text-primary" href="{{ route('password.request') }}">
                                <i class="fas fa-key mr-1"></i>Forgot Password?
                            </a>
                        </div>

                        @if (Route::has('register'))
                        <div class="text-center mt-2">
                            <a class="small text-primary" href="{{ route('register') }}">
                                <i class="fas fa-user-plus mr-1"></i>Create an Account!
                            </a>
                        </div>
                        @endif
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