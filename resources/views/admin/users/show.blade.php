@extends('layouts.admin')
@section('title', 'View User')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-circle mr-2"></i>User Profile
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit mr-1"></i> Edit Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Left Column - Profile Card --}}
        <div class="col-lg-4 mb-4">
            {{-- Profile Overview --}}
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    {{-- Profile Picture --}}
                    <div class="mb-4">
                        @if($user->hasProfilePicture())
                            <img src="{{ $user->profile_picture_url }}" 
                                class="rounded-circle shadow" 
                                style="width: 150px; height: 150px; object-fit: cover;" 
                                alt="{{ $user->full_name }}">
                        @else
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center shadow" 
                                style="width: 150px; height: 150px;">
                                <span class="text-white" style="font-size: 3rem; font-weight: bold;">
                                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Name and Username --}}
                    <h3 class="mb-2">{{ $user->full_name }}</h3>
                    <p class="text-muted mb-3">
                        {{ $user->username }}
                    </p>
                    
                    {{-- Role Badge --}}
                    <div class="mb-3">
                        <span class="badge badge-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'instructor' ? 'success' : 'info') }} px-4 py-2" style="font-size: 0.9rem;">
                            <i class="fas fa-{{ $user->role === 'admin' ? 'user-shield' : ($user->role === 'instructor' ? 'chalkboard-teacher' : 'user-graduate') }} mr-1"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    
                    {{-- Status Badge --}}
                    <span class="badge badge-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'secondary' : 'danger') }} px-4 py-2" style="font-size: 0.9rem;">
                        <i class="fas fa-circle mr-1" style="font-size: 0.6rem;"></i>
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>

            {{-- Account Information --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle mr-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Email Address</small>
                        <p class="mb-0">
                            <i class="fas fa-envelope text-primary mr-2"></i>
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </p>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Account Created</small>
                        <p class="mb-0">
                            <i class="far fa-calendar-plus text-success mr-2"></i>
                            {{ $user->created_at->format('F d, Y') }}
                            <br>
                            <small class="text-muted ml-4">{{ $user->created_at->diffForHumans() }}</small>
                        </p>
                    </div>
                    
                    <hr>
                    
                    @if($user->last_login_at)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Last Login</small>
                        <p class="mb-0">
                            <i class="fas fa-sign-in-alt text-info mr-2"></i>
                            {{ $user->last_login_at->format('F d, Y g:i A') }}
                            <br>
                            <small class="text-muted ml-4">{{ $user->last_login_at->diffForHumans() }}</small>
                        </p>
                    </div>
                    
                    <hr>
                    @endif
                    
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Password Status</small>
                        <p class="mb-0">
                            @if($user->must_change_password)
                                <span class="badge badge-warning">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Must Change Password
                                </span>
                            @else
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Password Set
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Details --}}
        <div class="col-lg-8">
            {{-- Profile Information --}}
            @if($user->profile)
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-card mr-2"></i>Personal Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">First Name</small>
                            <p class="font-weight-bold mb-0">{{ $user->profile->first_name }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Middle Name</small>
                            <p class="font-weight-bold mb-0">{{ $user->profile->middle_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Last Name</small>
                            <p class="font-weight-bold mb-0">{{ $user->profile->last_name }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1">Phone Number</small>
                            <p class="mb-0">
                                @if($user->profile->phone)
                                    <i class="fas fa-phone text-success mr-2"></i>{{ $user->profile->phone }}
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Admin-specific details --}}
                    @if($user->isAdmin())
                    <hr class="my-4">
                    <h6 class="font-weight-bold text-primary mb-3">
                        <i class="fas fa-user-shield mr-2"></i>Administrator Details
                    </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block mb-1">Position</small>
                            <p class="font-weight-bold mb-0">{{ $user->admin->position ?? 'Not specified' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block mb-1">Office Location</small>
                            <p class="font-weight-bold mb-0">{{ $user->admin->office ?? 'Not specified' }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Instructor-specific details --}}
                    @if($user->isInstructor())
                    <hr class="my-4">
                    <h6 class="font-weight-bold text-success mb-3">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>Instructor Details
                    </h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Employee ID</small>
                            <p class="font-weight-bold mb-0">{{ $user->instructor->employee_id }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Department</small>
                            <p class="font-weight-bold mb-0">{{ $user->instructor->department ?? 'Not assigned' }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Hire Date</small>
                            <p class="font-weight-bold mb-0">
                                {{ $user->instructor->hire_date ? $user->instructor->hire_date->format('M d, Y') : 'Not specified' }}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <small class="text-muted d-block mb-1">Specialization</small>
                            <p class="font-weight-bold mb-0">{{ $user->instructor->specialization->name ?? 'Not assigned' }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Student-specific details --}}
                    @if($user->isStudent())
                    <hr class="my-4">
                    <h6 class="font-weight-bold text-info mb-3">
                        <i class="fas fa-user-graduate mr-2"></i>Student Details
                    </h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Student Number</small>
                            <p class="font-weight-bold mb-0">{{ $user->student->student_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Year Level</small>
                            <p class="font-weight-bold mb-0">{{ $user->student->year_level }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-1">Admission Date</small>
                            <p class="font-weight-bold mb-0">
                                {{ $user->student->admission_date ? $user->student->admission_date->format('M d, Y') : 'Not specified' }}
                            </p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <small class="text-muted d-block mb-1">Course</small>
                            <p class="font-weight-bold mb-0">{{ $user->student->course->course_name ?? 'Not enrolled' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <small class="text-muted d-block mb-1">Address</small>
                            <p class="mb-0">{{ $user->student->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Recent Activities --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-2"></i>Recent Activities
                    </h6>
                    <span class="badge badge-primary badge-pill">{{ $user->auditLogs->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Action</th>
                                    <th class="border-0">Details</th>
                                    <th class="border-0">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->auditLogs->take(10) as $log)
                                <tr>
                                    <td>
                                        <span class="badge badge-primary">
                                            {{ str_replace('_', ' ', ucwords($log->action)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $log->model_type ? class_basename($log->model_type) : 'System Action' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $log->created_at->format('M d, Y g:i A') }}
                                            <span class="text-muted">({{ $log->created_at->diffForHumans() }})</span>
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No recent activities found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell mr-2"></i>Recent Notifications
                    </h6>
                    <span class="badge badge-primary badge-pill">
                        {{ $user->notifications->whereNull('read_at')->count() }} unread
                    </span>
                </div>
                <div class="card-body">
                    @forelse($user->notifications->sortByDesc('created_at')->take(5) as $notification)
                    <div class="alert alert-{{ $notification->type === 'info' ? 'info' : ($notification->type === 'success' ? 'success' : ($notification->type === 'warning' ? 'warning' : 'danger')) }} 
                                {{ $notification->read_at ? 'bg-light border' : '' }} 
                                mb-3 shadow-sm">
                        <div class="d-flex">
                            <div class="mr-3">
                                @if($notification->type === 'info')
                                    <i class="fas fa-info-circle fa-lg"></i>
                                @elseif($notification->type === 'success')
                                    <i class="fas fa-check-circle fa-lg"></i>
                                @elseif($notification->type === 'warning')
                                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                                @else
                                    <i class="fas fa-exclamation-circle fa-lg"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0 font-weight-bold">{{ $notification->title }}</h6>
                                    @if(!$notification->read_at)
                                        <span class="badge badge-danger">New</span>
                                    @endif
                                </div>
                                <p class="mb-2">{{ $notification->message }}</p>
                                <small class="text-muted">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $notification->created_at->format('M d, Y g:i A') }}
                                    <span>({{ $notification->created_at->diffForHumans() }})</span>
                                    @if($notification->read_at)
                                        <span class="ml-2">• Read {{ $notification->read_at->diffForHumans() }}</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-bell-slash fa-3x mb-3 d-block"></i>
                        <p class="mb-0">No notifications yet</p>
                    </div>
                    @endforelse
                    
                    @if($user->notifications->count() > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.notifications.index', ['user_id' => $user->id]) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye mr-1"></i>
                            View All {{ $user->notifications->count() }} Notifications
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleStatus(userId) {
        if (confirm('Are you sure you want to toggle this user\'s status? This will change their account status and may affect their access to the system.')) {
            // Create a form and submit via POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/users/${userId}/toggle-status`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush