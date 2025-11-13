@extends('layouts.admin')

@section('title', 'View Section')

@php
    $pageTitle = 'Section Details: ' . $section->full_name;
    $pageActions = '
        <a href="' . route('admin.sections.edit', $section) . '" class="btn btn-warning btn-sm mr-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="' . route('admin.enrollments.create', ['section' => $section->id]) . '" class="btn btn-success btn-sm mr-2">
            <i class="fas fa-user-plus"></i> Enroll Students
        </a>
        <a href="' . route('admin.sections.index') . '" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    ';
@endphp

@section('content')
    <div class="row">
        {{-- Section Information --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Section Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 text-primary">
                            <i class="fas fa-users-class"></i>
                        </div>
                        <h5 class="font-weight-bold mt-3">{{ $section->section_name }}</h5>
                        <p class="text-muted">Year {{ $section->year_level }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Course:</strong><br>
                        <a href="{{ route('admin.courses.show', $section->course) }}">
                            {{ $section->course->course_code }} - {{ $section->course->course_name }}
                        </a>
                    </div>

                    <div class="mb-3">
                        <strong>Full Name:</strong><br>
                        {{ $section->full_name }}
                    </div>

                    <div class="mb-3">
                        <strong>Capacity:</strong><br>
                        {{ $enrolledCount }} / {{ $section->max_students }} students
                        @if($section->hasAvailableSlots($currentAcademicYear, $currentSemester))
                            <br><span class="badge badge-success">Available Slots</span>
                        @else
                            <br><span class="badge badge-danger">Full</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        @if($section->is_active)
                            <span class="badge badge-success badge-lg">Active</span>
                        @else
                            <span class="badge badge-secondary badge-lg">Inactive</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Academic Period:</strong><br>
                        {{ $currentSemester }} Semester {{ $currentAcademicYear }}
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $section->created_at->format('M d, Y') }}
                    </div>

                    <hr>

                    <div class="btn-group d-flex" role="group">
                        <button type="button" 
                                class="btn btn-sm btn-outline-danger" 
                                data-toggle="modal" 
                                data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Content --}}
        <div class="col-lg-8">
            {{-- Stats Cards --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Enrolled Students</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $enrolledCount }}</div>
                                    <div class="text-xs text-muted">
                                        {{ round(($enrolledCount / $section->max_students) * 100) }}% capacity
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Instructors</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $section->assignments()->count() }}</div>
                                    <div class="text-xs text-muted">Teaching subjects</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Subjects</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $section->subjects()->distinct()->count() }}</div>
                                    <div class="text-xs text-muted">Assigned</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-book-open fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enrolled Students --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Enrolled Students</h6>
                    <a href="{{ route('admin.enrollments.create', ['section' => $section->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Enroll Student
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $enrollments = $section->enrollments()
                            ->with('student.user', 'student.course')
                            ->where('academic_year', $currentAcademicYear)
                            ->where('semester', $currentSemester)
                            ->where('status', 'enrolled')
                            ->get();
                    @endphp

                    @if($enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Student Number</th>
                                        <th>Name</th>
                                        <th>Course</th>
                                        <th>Year Level</th>
                                        <th>Enrollment Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td>{{ $enrollment->student->student_number }}</td>
                                            <td>{{ $enrollment->student->full_name }}</td>
                                            <td>{{ $enrollment->student->course->course_code ?? 'N/A' }}</td>
                                            <td>Year {{ $enrollment->student->year_level }}</td>
                                            <td>{{ $enrollment->enrollment_date->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $enrollment->student->user) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Student">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.enrollments.show', $enrollment) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="View Enrollment">
                                                    <i class="fas fa-file-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No students enrolled yet</p>
                            <a href="{{ route('admin.enrollments.create', ['section' => $section->id]) }}" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Enroll First Student
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Teaching Assignments --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Teaching Assignments</h6>
                    <a href="{{ route('admin.assignments.create', ['section' => $section->id]) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Assign Instructor
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $assignments = $section->assignments()
                            ->with('instructor.user', 'subject')
                            ->where('academic_year', $currentAcademicYear)
                            ->where('semester', $currentSemester)
                            ->get();
                    @endphp

                    @if($assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Instructor</th>
                                        <th>Employee ID</th>
                                        <th>Specialization</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr>
                                            <td>
                                                {{ $assignment->subject->subject_code }} - {{ $assignment->subject->subject_name }}
                                                <br>
                                                <small class="text-muted">{{ $assignment->subject->units }} units</small>
                                            </td>
                                            <td>{{ $assignment->instructor->full_name }}</td>
                                            <td>{{ $assignment->instructor->employee_id }}</td>
                                            <td>
                                                @if($assignment->instructor->specialization)
                                                    {{ $assignment->instructor->specialization->name }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $assignment->instructor->user) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Instructor">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('admin.assignments.destroy', $assignment) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to remove this assignment?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Remove Assignment">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No instructors assigned yet</p>
                            <a href="{{ route('admin.assignments.create', ['section' => $section->id]) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Assign First Instructor
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $section->full_name }}</strong>?</p>
                    @if($enrolledCount > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This section has {{ $enrolledCount }} enrolled student(s). 
                            Deleting this section will affect existing enrollments.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Section</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection