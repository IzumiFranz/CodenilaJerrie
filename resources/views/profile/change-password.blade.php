<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Quiz & Learning Management System">
    <meta name="author" content="">
    <title>Change Password - Quiz LMS</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        .change-password-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .change-password-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .password-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .password-logo i {
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
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }
        .password-requirements {
            font-size: 0.875rem;
        }
        .password-requirements li {
            margin-bottom: 0.25rem;
        }
        .password-requirements .valid {
            color: #28a745;
        }
        .password-requirements .invalid {
            color: #dc3545;
        }
    </style>
</head>

<body class="change-password-container">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-xl-6 col-lg-7 col-md-9">
                <div class="change-password-card card">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="password-logo">
                            <i class="fas fa-key"></i>
                        </div>

                        <!-- Title -->
                        <div class="text-center mb-4">
                            <h1 class="h3 text-gray-900 mb-2">Change Your Password</h1>
                            @if(auth()->user()->must_change_password)
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Action Required:</strong> You must change your password before continuing. This is your first time logging in.
                                </div>
                            @else
                                <p class="text-muted">Update your password to keep your account secure</p>
                            @endif
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

                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                {{ session('warning') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <strong>Whoops!</strong> There were some problems.
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

                        <!-- User Info -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        @if(auth()->user()->profile_picture)
                                            <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
                                                 class="rounded-circle" 
                                                 width="50" 
                                                 height="50" 
                                                 alt="Profile">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ auth()->user()->full_name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user-tag mr-1"></i>{{ ucfirst(auth()->user()->role) }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-at mr-1"></i>{{ auth()->user()->username }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Change Password Form -->
                        <form method="POST" action="{{ route('profile.update-password') }}" id="changePasswordForm">
                            @csrf

                            <!-- Current Password (only if not first login) -->
                            @if(!auth()->user()->must_change_password)
                            <div class="form-group">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" 
                                           name="current_password" 
                                           placeholder="Enter current password" 
                                           required>
                                </div>
                                @error('current_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            @endif

                            <!-- New Password -->
                            <div class="form-group">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-key"></i>
                                        </span>
                                    </div>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter new password" 
                                           required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="password-strength" id="passwordStrength"></div>
                                @error('password')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Password Requirements -->
                            <div class="card mb-3">
                                <div class="card-body p-3">
                                    <small class="text-muted d-block mb-2"><strong>Password Requirements:</strong></small>
                                    <ul class="password-requirements mb-0 pl-3">
                                        <li id="req-length" class="invalid">
                                            <i class="fas fa-times-circle"></i> At least 8 characters
                                        </li>
                                        <li id="req-uppercase" class="invalid">
                                            <i class="fas fa-times-circle"></i> One uppercase letter
                                        </li>
                                        <li id="req-lowercase" class="invalid">
                                            <i class="fas fa-times-circle"></i> One lowercase letter
                                        </li>
                                        <li id="req-number" class="invalid">
                                            <i class="fas fa-times-circle"></i> One number
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Confirm new password" 
                                           required>
                                </div>
                                <small class="form-text" id="matchMessage"></small>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-submit btn-block mt-4" id="submitBtn" disabled>
                                <i class="fas fa-check-circle mr-2"></i>Change Password
                            </button>
                        </form>

                        @if(!auth()->user()->must_change_password)
                        <hr class="my-4">

                        <!-- Back Link -->
                        <div class="text-center">
                            <a class="small text-primary" href="{{ route(auth()->user()->role . '.dashboard') }}">
                                <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
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

    <!-- Password Validation Script -->
    <script>
        $(document).ready(function() {
            var requirements = {
                length: false,
                uppercase: false,
                lowercase: false,
                number: false
            };

            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                var passwordField = $('#password');
                var icon = $('#toggleIcon');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Password strength and requirements checker
            $('#password').on('input', function() {
                var password = $(this).val();
                var strength = 0;
                var strengthBar = $('#passwordStrength');
                
                // Check requirements
                requirements.length = password.length >= 8;
                updateRequirement('req-length', requirements.length);
                if (requirements.length) strength++;
                
                requirements.uppercase = /[A-Z]/.test(password);
                updateRequirement('req-uppercase', requirements.uppercase);
                if (requirements.uppercase) strength++;
                
                requirements.lowercase = /[a-z]/.test(password);
                updateRequirement('req-lowercase', requirements.lowercase);
                if (requirements.lowercase) strength++;
                
                requirements.number = /[0-9]/.test(password);
                updateRequirement('req-number', requirements.number);
                if (requirements.number) strength++;
                
                // Update strength bar
                strengthBar.removeClass('bg-danger bg-warning bg-info bg-success');
                
                if (strength === 0) {
                    strengthBar.css('width', '0%');
                } else if (strength <= 2) {
                    strengthBar.addClass('bg-danger').css('width', '33%');
                } else if (strength === 3) {
                    strengthBar.addClass('bg-warning').css('width', '66%');
                } else {
                    strengthBar.addClass('bg-success').css('width', '100%');
                }

                checkFormValidity();
            });

            // Check password confirmation match
            $('#password_confirmation').on('input', function() {
                checkPasswordMatch();
                checkFormValidity();
            });

            function updateRequirement(id, isValid) {
                var element = $('#' + id);
                if (isValid) {
                    element.removeClass('invalid').addClass('valid');
                    element.find('i').removeClass('fa-times-circle').addClass('fa-check-circle');
                } else {
                    element.removeClass('valid').addClass('invalid');
                    element.find('i').removeClass('fa-check-circle').addClass('fa-times-circle');
                }
            }

            function checkPasswordMatch() {
                var password = $('#password').val();
                var confirmation = $('#password_confirmation').val();
                var message = $('#matchMessage');

                if (confirmation.length === 0) {
                    message.text('').removeClass('text-danger text-success');
                    return false;
                }

                if (password === confirmation) {
                    message.text('✓ Passwords match').removeClass('text-danger').addClass('text-success');
                    return true;
                } else {
                    message.text('✗ Passwords do not match').removeClass('text-success').addClass('text-danger');
                    return false;
                }
            }

            function checkFormValidity() {
                var allRequirementsMet = requirements.length && 
                                        requirements.uppercase && 
                                        requirements.lowercase && 
                                        requirements.number;
                
                var passwordsMatch = $('#password').val() === $('#password_confirmation').val() && 
                                    $('#password_confirmation').val().length > 0;

                if (allRequirementsMet && passwordsMatch) {
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#submitBtn').prop('disabled', true);
                }
            }
        });
    </script>
</body>
</html>