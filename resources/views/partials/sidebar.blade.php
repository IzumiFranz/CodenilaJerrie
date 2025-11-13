<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center flex-column" 
       href="{{ route(auth()->user()->role . '.dashboard') }}">
        <div class="sidebar-brand-icon">
            @if(auth()->user()->isAdmin())
                <i class="fas fa-user-shield"></i>
            @elseif(auth()->user()->isInstructor())
                <i class="fas fa-chalkboard-teacher"></i>
            @else
                <i class="fas fa-graduation-cap"></i>
            @endif
        </div>
        <div class="sidebar-brand-text">
            @if(auth()->user()->isAdmin())
                Admin Panel
            @elseif(auth()->user()->isInstructor())
                Instructor
            @else
                Quiz LMS
            @endif
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs(auth()->user()->role . '.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route(auth()->user()->role . '.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    @if(auth()->user()->isAdmin())
        <!-- ====================================================================
             ADMIN SIDEBAR - System Management
             ==================================================================== -->
        <div class="sidebar-heading">User Management</div>

        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Users</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Academic Setup</div>

        <li class="nav-item {{ request()->routeIs('admin.specializations.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.specializations.index') }}">
                <i class="fas fa-fw fa-certificate"></i>
                <span>Specializations</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.courses.index') }}">
                <i class="fas fa-fw fa-book"></i>
                <span>Courses</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.subjects.index') }}">
                <i class="fas fa-fw fa-book-open"></i>
                <span>Subjects</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.sections.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.sections.index') }}">
                <i class="fas fa-fw fa-chalkboard"></i>
                <span>Sections</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.enrollments.index') }}">
                <i class="fas fa-fw fa-user-graduate"></i>
                <span>Enrollments</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.assignments.index') }}">
                <i class="fas fa-fw fa-chalkboard-teacher"></i>
                <span>Instructor Assignments</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Content</div>

        <li class="nav-item {{ request()->routeIs('admin.lessons.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.lessons.index') }}">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Lessons</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.quizzes.index') }}">
                <i class="fas fa-fw fa-clipboard-check"></i>
                <span>Quizzes</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">System</div>

        <li class="nav-item {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.feedback.index') }}">
                <i class="fas fa-fw fa-comments"></i>
                <span>Feedback</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.notifications.index') }}">
                <i class="fas fa-fw fa-bell"></i>
                <span>Notifications</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.audit-logs.index') }}">
                <i class="fas fa-fw fa-history"></i>
                <span>Audit Logs</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.settings.index') }}">
                <i class="fas fa-fw fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>

    @elseif(auth()->user()->isInstructor())
        <!-- ====================================================================
             INSTRUCTOR SIDEBAR - Teaching & AI Tools
             ==================================================================== -->
        
        <!-- AI Assistant - Featured -->
        <li class="nav-item {{ request()->routeIs('instructor.ai.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('instructor.ai.index') }}">
                <i class="fas fa-fw fa-robot"></i>
                <span>AI Assistant</span>
                @php
                    $pendingJobs = \App\Models\AIJob::where('user_id', auth()->id())
                        ->whereIn('status', ['pending', 'processing'])
                        ->count();
                @endphp
                @if($pendingJobs > 0)
                    <span class="badge ai-badge badge-counter ml-1">{{ $pendingJobs }}</span>
                @endif
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Teaching Content</div>

        <li class="nav-item {{ request()->routeIs('instructor.lessons.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('instructor.lessons.index') }}">
                <i class="fas fa-fw fa-book"></i>
                <span>My Lessons</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('instructor.question-bank.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('instructor.question-bank.index') }}">
                <i class="fas fa-fw fa-question-circle"></i>
                <span>Question Bank</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('instructor.quizzes.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('instructor.quizzes.index') }}">
                <i class="fas fa-fw fa-clipboard-list"></i>
                <span>My Quizzes</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Students & Analytics</div>

        <li class="nav-item {{ request()->routeIs('instructor.student-progress.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('instructor.student-progress.index') }}">
                <i class="fas fa-fw fa-chart-line"></i>
                <span>Student Progress</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('instructor.settings.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('instructor.settings.index') }}">
                <i class="fas fa-fw fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>

    @else
        <!-- ====================================================================
             STUDENT SIDEBAR - Learning & Progress
             ==================================================================== -->
        <div class="sidebar-heading">My Learning</div>

        <li class="nav-item {{ request()->routeIs('student.lessons.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.lessons.index') }}">
                <i class="fas fa-fw fa-book-reader"></i>
                <span>Lessons</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('student.quizzes.*', 'student.quiz-attempts.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.quizzes.index') }}">
                <i class="fas fa-fw fa-pen"></i>
                <span>Quizzes</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Performance</div>

        <li class="nav-item {{ request()->routeIs('student.progress.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.progress.index') }}">
                <i class="fas fa-fw fa-chart-line"></i>
                <span>My Progress</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Support</div>

        <li class="nav-item {{ request()->routeIs('student.feedback.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.feedback.index') }}">
                <i class="fas fa-fw fa-comment-dots"></i>
                <span>Feedback</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('student.notifications.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.notifications.index') }}">
                <i class="fas fa-fw fa-bell"></i>
                <span>Notifications</span>
                @php
                    $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="badge badge-danger badge-counter ml-1">{{ $unreadCount }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('student.settings.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('student.settings.index') }}">
                <i class="fas fa-fw fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>