@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <div class="quick-actions">
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user-plus fa-sm text-white-50"></i> Add User
        </a>
        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-sm btn-success shadow-sm">
            <i class="fas fa-user-graduate fa-sm text-white-50"></i> Enroll Student
        </a>
    </div>
</div>

<!-- Content Row - User Statistics -->
<div class="row">
    <!-- Total Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeUsers }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructors Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Instructors</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $instructorCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $studentCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Academic Statistics -->
<div class="row">
    <!-- Courses Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Courses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCourses }}</div>
                        <div class="text-xs text-muted">{{ $activeCourses }} active</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Subjects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSubjects }}</div>
                        <div class="text-xs text-muted">{{ $activeSubjects }} active</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book-open fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sections Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Sections</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSections }}</div>
                        <div class="text-xs text-muted">{{ $activeSections }} active</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollments Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Enrollments ({{ $currentSemester }})</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeEnrollments }}</div>
                        <div class="text-xs text-muted">{{ $currentAcademicYear }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Content & Quiz Statistics -->
<div class="row">
    <!-- Lessons Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Lessons</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLessons }}</div>
                        <div class="text-xs text-muted">{{ $publishedLessons }} published</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quizzes Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Quizzes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalQuizzes }}</div>
                        <div class="text-xs text-muted">{{ $publishedQuizzes }} published</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Attempts Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Quiz Attempts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedAttempts }}</div>
                        <div class="text-xs text-muted">{{ $inProgressAttempts }} in progress</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Score Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Average Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageScore, 2) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Tables -->
<div class="row">
    <!-- Recent Activities -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Model</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $log)
                            <tr>
                                <td>{{ $log->user ? $log->user->username : 'System' }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $log->action }}</span>
                                </td>
                                <td>{{ $log->model_type ? class_basename($log->model_type) : '-' }}</td>
                                <td>{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No recent activities</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($recentUsers as $user)
                <div class="d-flex align-items-center mb-3">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" class="avatar mr-3" alt="Avatar">
                    @else
                        <img src="{{ asset('img/undraw_profile.svg') }}" class="avatar mr-3" alt="Avatar">
                    @endif
                    <div class="flex-grow-1">
                        <div class="font-weight-bold">{{ $user->full_name }}</div>
                        <div class="text-xs text-muted">{{ $user->email }}</div>
                    </div>
                    <span class="badge badge-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'instructor' ? 'success' : 'info') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                @empty
                <p class="text-center text-muted">No recent users</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Pending Feedback -->
@if($pendingFeedback > 0)
<div class="row">
    <div class="col-12">
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            You have <strong>{{ $pendingFeedback }}</strong> pending feedback to review.
            <a href="{{ route('admin.feedback.index') }}" class="alert-link">View Feedback</a>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Additional dashboard scripts if needed
    $(document).ready(function() {
        // Refresh stats every 5 minutes
        setInterval(function() {
            // You can implement auto-refresh logic here
        }, 300000);
    });
</script>
@endpush