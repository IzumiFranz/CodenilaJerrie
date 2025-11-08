@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('page-header', 'Dashboard')

@section('page-actions')
    <a href="{{ route('admin.users.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Add New User
    </a>
@endsection

@section('content')

<!-- Content Row - Statistics Cards -->
<div class="row">

    <!-- Total Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 card-stats">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Instructors Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 card-stats">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Instructors</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeInstructors ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrolled Students Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 card-stats">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Enrolled Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $enrolledStudents ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Courses Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 card-stats">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Active Courses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCourses ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Content Row - Charts and Tables -->
<div class="row">

    <!-- Quick Actions Card -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.users.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-plus text-primary"></i> Add New User
                    </a>
                    <a href="{{ route('admin.courses.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle text-success"></i> Create Course
                    </a>
                    <a href="{{ route('admin.subjects.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-book-open text-info"></i> Add Subject
                    </a>
                    <a href="{{ route('admin.sections.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-users-class text-warning"></i> Create Section
                    </a>
                    <a href="{{ route('admin.enrollments.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-check text-danger"></i> Enroll Student
                    </a>
                    <a href="{{ route('admin.assignments.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-chalkboard-teacher text-secondary"></i> Assign Instructor
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Card -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($recentActivities ?? [] as $activity)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $activity->action }}</h6>
                            <small>{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ $activity->user->full_name ?? 'System' }}</p>
                        <small class="text-muted">{{ $activity->model_type }}</small>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">No recent activities</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Content Row - Additional Info -->
<div class="row">

    <!-- Pending Feedback Card -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pending Feedback</h6>
                <span class="badge badge-danger badge-pill">{{ $pendingFeedback ?? 0 }}</span>
            </div>
            <div class="card-body">
                <p class="mb-3">You have {{ $pendingFeedback ?? 0 }} feedback items awaiting response.</p>
                <a href="{{ route('admin.feedback.index') }}" class="btn btn-primary btn-sm btn-block">
                    View All Feedback
                </a>
            </div>
        </div>
    </div>

    <!-- System Status Card -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Status</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="font-weight-bold">Database</small>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <small class="font-weight-bold">Storage</small>
                    <div class="progress">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 45%"></div>
                    </div>
                </div>
                <div>
                    <small class="font-weight-bold">Server</small>
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 65%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Statistics Card -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Statistics</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Admins:</span>
                        <strong>{{ $adminCount ?? 0 }}</strong>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Instructors:</span>
                        <strong>{{ $instructorCount ?? 0 }}</strong>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Students:</span>
                        <strong>{{ $studentCount ?? 0 }}</strong>
                    </div>
                </div>
                <hr>
                <div>
                    <div class="d-flex justify-content-between">
                        <span>Active:</span>
                        <strong class="text-success">{{ $activeUsersCount ?? 0 }}</strong>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between">
                        <span>Inactive:</span>
                        <strong class="text-warning">{{ $inactiveUsersCount ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<!-- Chart.js (LOCAL) -->
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

<script>
// Example Chart - You can add actual data from controller
// var ctx = document.getElementById("myChart");
// var myChart = new Chart(ctx, {
//     type: 'line',
//     data: {
//         labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
//         datasets: [{
//             label: "Users",
//             data: [10, 20, 30, 40, 50, 60],
//             backgroundColor: 'rgba(78, 115, 223, 0.05)',
//             borderColor: 'rgba(78, 115, 223, 1)',
//         }]
//     }
// });
</script>
@endpush