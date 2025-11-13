@extends('layouts.admin')
@section('title', 'View User')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user mr-2"></i>User Details</h1>
    <div class="btn-group">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                @if($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" class="img-fluid rounded-circle mb-3" style="max-width: 200px;" alt="Profile">
                @else
                    <img src="{{ asset('img/undraw_profile.svg') }}" class="img-fluid rounded-circle mb-3" style="max-width: 200px;" alt="Profile">
                @endif
                
                <h4>{{ $user->full_name }}</h4>
                <p class="text-muted">@{{ $user->username }}</p>
                
                <div class="mb-3">
                    <span class="badge badge-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'instructor' ? 'success' : 'info') }} badge-pill px-3 py-2">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                
                <span class="badge badge-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'secondary' : 'danger') }} badge-pill px-3 py-2">
                    {{ ucfirst($user->status) }}
                </span>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Email:</strong><br>{{ $user->email }}</p>
                <p><strong>Created:</strong><br>{{ $user->created_at->format('M d, Y h:i A') }}</p>
                <p><strong>Last Updated:</strong><br>{{ $user->updated_at->format('M d, Y h:i A') }}</p>
                @if($user->last_login_at)
                <p><strong>Last Login:</strong><br>{{ $user->last_login_at->diffForHumans() }}</p>
                @endif
                <p><strong>Must Change Password:</strong><br>
                    <span class="badge badge-{{ $user->must_change_password ? 'warning' : 'success' }}">
                        {{ $user->must_change_password ? 'Yes' : 'No' }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if($user->profile)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>First Name:</strong> {{ $user->profile->first_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Last Name:</strong> {{ $user->profile->last_name }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Middle Name:</strong> {{ $user->profile->middle_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Phone:</strong> {{ $user->profile->phone ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($user->isAdmin())
                <hr>
                <h6 class="font-weight-bold">Admin Details</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Position:</strong> {{ $user->admin->position ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Office:</strong> {{ $user->admin->office ?? 'N/A' }}</p>
                    </div>
                </div>
                @endif

                @if($user->isInstructor())
                <hr>
                <h6 class="font-weight-bold">Instructor Details</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Employee ID:</strong> {{ $user->instructor->employee_id }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Department:</strong> {{ $user->instructor->department ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Specialization:</strong> {{ $user->instructor->specialization->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Hire Date:</strong> {{ $user->instructor->hire_date?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
                @endif

                @if($user->isStudent())
                <hr>
                <h6 class="font-weight-bold">Student Details</h6>
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Student Number:</strong> {{ $user->student->student_number }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Course:</strong> {{ $user->student->course->course_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Year Level:</strong> {{ $user->student->year_level }}</p>
                    </div>
                </div>
                <p><strong>Address:</strong> {{ $user->student->address ?? 'N/A' }}</p>
                <p><strong>Admission Date:</strong> {{ $user->student->admission_date?->format('M d, Y') ?? 'N/A' }}</p>
                @endif
            </div>
        </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Details</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->auditLogs->take(10) as $log)
                            <tr>
                                <td><span class="badge badge-primary">{{ $log->action }}</span></td>
                                <td>{{ $log->model_type ? class_basename($log->model_type) : 'System' }}</td>
                                <td>{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No recent activities</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Notifications</h6>
            </div>
            <div class="card-body">
                @forelse($user->notifications->take(5) as $notification)
                <div class="mb-3 p-3 border-left-{{ $notification->type }} {{ $notification->read_at ? 'bg-light' : 'bg-white' }}" style="border-left: 3px solid;">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1">{{ $notification->title }}</h6>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0 text-sm">{{ Str::limit($notification->message, 100) }}</p>
                    @if($notification->read_at)
                    <small class="text-muted">Read {{ $notification->read_at->diffForHumans() }}</small>
                    @else
                    <span class="badge badge-primary">Unread</span>
                    @endif
                </div>
                @empty
                <p class="text-center text-muted mb-0">No notifications</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function toggleStatus(userId) {
        if (confirm('Are you sure you want to toggle this user\'s status?')) {
            window.location.href = `/admin/users/${userId}/toggle-status`;
        }
    }
</script>
@endsection