@extends('layouts.admin')

@section('title', 'View Enrollment')

@php
    $pageTitle = 'Enrollment Details';
    $pageActions = '
        <a href="' . route('admin.enrollments.edit', $enrollment) . '" class="btn btn-warning btn-sm mr-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="' . route('admin.enrollments.index') . '" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    ';
@endphp

@section('content')
    <div class="row">
        {{-- Student & Enrollment Info --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Student Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img class="img-profile rounded-circle" 
                             style="width: 100px; height: 100px;" 
                             src="{{ $enrollment->student->user->profile_picture ? asset('storage/' . $enrollment->student->user->profile_picture) : asset('img/undraw_profile.svg') }}">
                        <h5 class="font-weight-bold mt-3">{{ $enrollment->student->full_name }}</h5>
                        <p class="text-muted mb-1">{{ $enrollment->student->student_number }}</p>
                        <p class="text-muted mb-0">{{ $enrollment->student->user->email }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Course:</strong><br>
                        @if($enrollment->student->course)
                            <a href="{{ route('admin.courses.show', $enrollment->student->course) }}">
                                {{ $enrollment->student->course->course_code }} - {{ $enrollment->student->course->course_name }}
                            </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Year Level:</strong><br>
                        Year {{ $enrollment->student->year_level }}
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        @if($enrollment->status === 'enrolled')
                            <span class="badge badge-success badge-lg">Enrolled</span>
                        @elseif($enrollment->status === 'dropped')
                            <span class="badge badge-warning badge-lg">Dropped</span>
                        @else
                            <span class="badge badge-info badge-lg">Completed</span>
                        @endif
                    </div>

                    <hr>

                    <div class="text-center">
                        <a href="{{ route('admin.users.show', $enrollment->student->user) }}" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-user"></i> View Full Profile
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Enrollment Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Academic Year:</strong><br>
                        {{ $enrollment->academic_year }}
                    </div>

                    <div class="mb-3">
                        <strong>Semester:</strong><br>
                        {{ ucfirst($enrollment->semester) }} Semester
                    </div>

                    <div class="mb-3">
                        <strong>Enrollment Date:</strong><br>
                        {{ $enrollment->enrollment_date->format('F d, Y') }}
                    </div>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $enrollment->created_at->format('M d, Y h:i A') }}
                    </div>

                    @if($enrollment->updated_at != $enrollment->created_at)
                        <div class="mb-3">
                            <strong>Last Updated:</strong><br>
                            {{ $enrollment->updated_at->format('M d, Y h:i A') }}
                        </div>
                    @endif

                    <hr>

                    @if($enrollment->status === 'enrolled')
                        <button type="button" 
                                class="btn btn-warning btn-sm btn-block" 
                                data-toggle="modal" 
                                data-target="#dropModal">
                            <i class="fas fa-user-minus"></i> Drop Student
                        </button>
                    @endif

                    <button type="button" 
                            class="btn btn-danger btn-sm btn-block" 
                            data-toggle="modal" 
                            data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Delete Enrollment
                    </button>
                </div>
            </div>
        </div>

        {{-- Section & Subjects Info --}}
        <div class="col-lg-8">
            {{-- Section Information --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Section Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Section:</strong> {{ $enrollment->section->full_name }}</p>
                            <p><strong>Course:</strong> {{ $enrollment->section->course->course_name }}</p>
                            <p><strong>Year Level:</strong> Year {{ $enrollment->section->year_level }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Capacity:</strong> {{ $enrollment->section->getEnrolledStudentsCount($enrollment->academic_year, $enrollment->semester) }}/{{ $enrollment->section->max_students }} students</p>
                            <p><strong>Status:</strong> 
                                @if($enrollment->section->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('admin.sections.show', $enrollment->section) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View Section Details
                        </a>
                    </div>
                </div>
            </div>

            {{-- Subjects for this Section --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assigned Subjects</h6>
                </div>
                <div class="card-body">
                    @php
                        $assignments = $enrollment->section->assignments()
                            ->with('subject', 'instructor.user')
                            ->where('academic_year', $enrollment->academic_year)
                            ->where('semester', $enrollment->semester)
                            ->get();
                    @endphp

                    @if($assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Name</th>
                                        <th>Units</th>
                                        <th>Instructor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr>
                                            <td><strong>{{ $assignment->subject->subject_code }}</strong></td>
                                            <td>{{ $assignment->subject->subject_name }}</td>
                                            <td>{{ $assignment->subject->units }}</td>
                                            <td>{{ $assignment->instructor->full_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right"><strong>Total Units:</strong></td>
                                        <td colspan="2"><strong>{{ $assignments->sum(function($a) { return $a->subject->units; }) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No subjects assigned to this section yet</p>
                            <a href="{{ route('admin.assignments.create', ['section' => $enrollment->section->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Assign Subjects
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Other Students in Same Section --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Classmates</h6>
                </div>
                <div class="card-body">
                    @php
                        $classmates = $enrollment->section->enrollments()
                            ->with('student.user')
                            ->where('academic_year', $enrollment->academic_year)
                            ->where('semester', $enrollment->semester)
                            ->where('status', 'enrolled')
                            ->where('id', '!=', $enrollment->id)
                            ->get();
                    @endphp

                    @if($classmates->count() > 0)
                        <p class="text-muted">{{ $classmates->count() }} other student(s) in this section:</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Student Number</th>
                                        <th>Name</th>
                                        <th>Year Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classmates->take(10) as $classmate)
                                        <tr>
                                            <td>{{ $classmate->student->student_number }}</td>
                                            <td>{{ $classmate->student->full_name }}</td>
                                            <td>Year {{ $classmate->student->year_level }}</td>
                                            <td>
                                                <a href="{{ route('admin.enrollments.show', $classmate) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($classmates->count() > 10)
                            <p class="text-muted text-center mt-2">
                                Showing 10 of {{ $classmates->count() }} classmates. 
                                <a href="{{ route('admin.sections.show', $enrollment->section) }}">View all</a>
                            </p>
                        @endif
                    @else
                        <p class="text-muted text-center">No other students enrolled in this section</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Drop Confirmation Modal --}}
    <div class="modal fade" id="dropModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Confirm Drop</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to drop <strong>{{ $enrollment->student->full_name }}</strong> from <strong>{{ $enrollment->section->full_name }}</strong>?</p>
                    <p class="text-muted">This will change the enrollment status to "Dropped".</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.enrollments.drop', $enrollment) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">Drop Student</button>
                    </form>
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
                    <p>Are you sure you want to delete this enrollment record?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action will move the enrollment to trash.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Enrollment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection