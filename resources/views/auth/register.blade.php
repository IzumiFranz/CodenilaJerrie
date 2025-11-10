<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Quiz & Learning Management System">
    <meta name="author" content="">
    <title>Register - Quiz LMS</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        .register-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .register-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .register-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .register-logo i {
            font-size: 2.5rem;
            color: white;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }
    </style>
</head>
<body class="register-container">
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
                            <a class="nav-link btn btn-light btn-sm ml-2 px-3" href="" style="color: black;">
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
        <div class="row justify-content-center align-items-center">
            <div class="col-xl-6 col-lg-7 col-md-9">
                <div class="card register-card my-4">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="register-logo">
                            <i class="fas fa-user-plus"></i>
                        </div>

                        <!-- Title -->
                        <div class="text-center mb-4">
                            <h1 class="h3 text-gray-900 mb-2">Create Account</h1>
                            <p class="text-muted">Join Quiz & Learning Management System</p>
                        </div>

                        <!-- Registration Disabled Notice (Optional) -->
                        @if(config('app.disable_registration', false))
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle mr-2"></i>
                                Public registration is currently disabled. Please contact the administrator to create an account.
                            </div>
                            <div class="text-center mt-4">
                                <a href="{{ route('login') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-arrow-left mr-2"></i>Back to Login
                                </a>
                            </div>
                        @else
                            <!-- Registration Form -->
                            <form method="POST" action="{{ route('register') }}" id="registrationForm">
                                @csrf

                                <div class="row">
                                    <!-- First Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('first_name') is-invalid @enderror" 
                                                   id="first_name" 
                                                   name="first_name" 
                                                   value="{{ old('first_name') }}" 
                                                   placeholder="Enter first name" 
                                                   required 
                                                   autofocus>
                                            @error('first_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('last_name') is-invalid @enderror" 
                                                   id="last_name" 
                                                   name="last_name" 
                                                   value="{{ old('last_name') }}" 
                                                   placeholder="Enter last name" 
                                                   required>
                                            @error('last_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Middle Name (Optional) -->
                                <div class="form-group">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" 
                                           class="form-control @error('middle_name') is-invalid @enderror" 
                                           id="middle_name" 
                                           name="middle_name" 
                                           value="{{ old('middle_name') }}" 
                                           placeholder="Enter middle name (optional)">
                                    @error('middle_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="Enter email address" 
                                           required>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Username (Auto-generated suggestion) -->
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username') }}" 
                                           placeholder="Choose a username" 
                                           required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Username will be auto-generated if left empty
                                    </small>
                                    @error('username')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="row">
                                    <!-- Password -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password <span class="text-danger">*</span></label>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Enter password" 
                                                   required>
                                            <div class="password-strength" id="passwordStrength"></div>
                                            <small class="form-text text-muted">
                                                Min. 8 characters, include uppercase, lowercase, and number
                                            </small>
                                            @error('password')
                                                <small class="text-danger d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   placeholder="Confirm password" 
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" 
                                               class="custom-control-input @error('terms') is-invalid @enderror" 
                                               id="terms" 
                                               name="terms" 
                                               required>
                                        <label class="custom-control-label" for="terms">
                                            I agree to the <a href="#" data-toggle="modal" data-target="#termsModal">Terms and Conditions</a>
                                        </label>
                                    </div>
                                    @error('terms')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary btn-register btn-block mt-4">
                                    <i class="fas fa-user-plus mr-2"></i>Register Account
                                </button>
                            </form>

                            <hr class="my-4">

                            <!-- Login Link -->
                            <div class="text-center">
                                <a class="small text-primary" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt mr-1"></i>Already have an account? Login!
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4 mb-4">
                    <p class="text-white small">
                        &copy; {{ date('Y') }} Quiz LMS. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>1. Acceptance of Terms</h6>
                    <p>By accessing and using Quiz LMS, you accept and agree to be bound by the terms and provision of this agreement.</p>
                    
                    <h6>2. User Accounts</h6>
                    <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
                    
                    <h6>3. Privacy Policy</h6>
                    <p>Your privacy is important to us. All personal information collected will be handled according to our Privacy Policy.</p>
                    
                    <h6>4. Academic Integrity</h6>
                    <p>Users must maintain academic integrity when taking quizzes and submitting assignments. Any form of cheating or plagiarism is strictly prohibited.</p>
                    
                    <h6>5. Code of Conduct</h6>
                    <p>Users must behave respectfully and professionally when interacting with other users and content within the system.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

    <!-- Password Strength Indicator -->
    <script>
        $(document).ready(function() {
            // Password strength indicator
            $('#password').on('input', function() {
                var password = $(this).val();
                var strength = 0;
                var strengthBar = $('#passwordStrength');
                
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;
                
                strengthBar.removeClass('bg-danger bg-warning bg-info bg-success');
                
                switch(strength) {
                    case 0:
                    case 1:
                        strengthBar.addClass('bg-danger').css('width', '25%');
                        break;
                    case 2:
                    case 3:
                        strengthBar.addClass('bg-warning').css('width', '50%');
                        break;
                    case 4:
                        strengthBar.addClass('bg-info').css('width', '75%');
                        break;
                    case 5:
                        strengthBar.addClass('bg-success').css('width', '100%');
                        break;
                }
            });

            // Auto-generate username suggestion from name
            $('#first_name, #last_name').on('blur', function() {
                if ($('#username').val() === '') {
                    var firstName = $('#first_name').val().toLowerCase().replace(/\s+/g, '');
                    var lastName = $('#last_name').val().toLowerCase().replace(/\s+/g, '');
                    if (firstName && lastName) {
                        var suggestion = firstName + '.' + lastName;
                        $('#username').val(suggestion);
                    }
                }
            });
        });
    </script>
</body>
</html>