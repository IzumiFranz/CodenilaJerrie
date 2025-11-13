@extends('layouts.admin')

@section('title', 'View Course')

@php
    $pageTitle = 'Course Details: ' . $course->course_name;
    $pageActions = '
        <a href="' . route('admin.courses.edit', $course) . '" class="btn btn-warning btn-sm mr-2">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="' . route('admin.subjects.create', ['course' => $course->id]) . '" class="btn btn-success btn-sm mr-2">
            <i class="fas fa-plus"></i> Add Subject
        </a>
        <a href="' . route('admin.sections.create', ['course' => $course->id]) . '" class="btn btn-info btn-sm mr-2">
            <i class="fas fa-plus"></i> Add Section
        </a>
        <a href="' . route('admin.courses.index') . '" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    ';
@endphp

@section('content')
    <div class="row">
        {{-- Course Information --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Course Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 text-primary">
                            <i class="fas fa-book"></i>
                        </div>
                        <h5 class="font-weight-bold mt-3">{{ $course->course_code }}</h5>
                        <p class="text-muted">{{ $course->course_name }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        @if($course->is_active)
                            <span class="badge badge-success badge-lg">Active</span>
                        @else
                            <span class="badge badge-secondary badge-lg">Inactive</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Duration:</strong><br>
                        {{ $course->max_years }} Year{{ $course->max_years > 1 ? 's' : '' }}
                    </div>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $course->created_at->format('M d, Y') }}
                    </div>

                    @if($course->description)
                        <hr>
                        <div>
                            <strong>Description:</strong><br>
                            <p class="text-muted">{{ $course->description }}</p>
                        </div>
                    @endif

                    <hr>

                    <div class="btn-group d-flex" role="group">
                        <button type="button" 
                                class="btn btn-sm btn-outline-primary" 
                                onclick="toggleStatus({{ $course->id }})">
                            <i class="fas fa-toggle-on"></i> Toggle Status
                        </button>
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

        {{-- Course Statistics --}}
        <div class="col-lg-8">
            {{-- Stats Cards --}}
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Subjects</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $course->subjects()->count() }}</div>
                                    <div class="text-xs text-muted">
                                        Active: {{ $course->subjects()->where('is_active', true)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-book-open fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Sections</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $course->sections()->count() }}</div>
                                    <div class="text-xs text-muted">
                                        Active: {{ $course->sections()->where('is_active', true)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users-class fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Students</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $course->students()->count() }}</div>
                                    <div class="text-xs text-muted">
                                        Active: {{ $course->getActiveStudentsCount() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subjects by Year Level --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subjects by Year Level</h6>
                </div>
                <div class="card-body">
                    @for($year = 1; $year <= $course->max_years; $year++)
                        @php
                            $yearSubjects = $course->getSubjectsByYearLevel($year);
                        @endphp
                        <div class="mb-3">
                            <h6 class="font-weight-bold">
                                Year {{ $year }}
                                <span class="badge badge-primary">{{ $yearSubjects->count() }} Subjects</span>
                            </h6>
                            @if($yearSubjects->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Subject Name</th>
                                                <th>Units</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($yearSubjects as $subject)
                                                <tr>
                                                    <td>{{ $subject->subject_code }}</td>
                                                    <td>{{ $subject->subject_name }}</td>
                                                    <td>{{ $subject->units }}</td>
                                                    <td>
                                                        @if($subject->is_active)
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.subjects.show', $subject) }}" 
                                                           class="btn btn-sm btn-info" 
                                                           title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                                           class="btn btn-sm btn-warning" 
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">No subjects for this year level yet.</p>
                            @endif
                        </div>
                        @if($year < $course->max_years)
                            <hr>
                        @endif
                    @endfor
                </div>
            </div>

            {{-- Sections --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Course Sections</h6>
                </div>
                <div class="card-body">
                    @if($course->sections()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Section Name</th>
                                        <th>Year Level</th>
                                        <th>Max Students</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->sections as $section)
                                        <tr>
                                            <td>{{ $section->section_name }}</td>
                                            <td>Year {{ $section->year_level }}</td>
                                            <td>{{ $section->max_students }}</td>
                                            <td>
                                                @if($section->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.sections.show', $section) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.sections.edit', $section) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users-class fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No sections created yet</p>
                            <a href="{{ route('admin.sections.create', ['course' => $course->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Section
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
                    <p>Are you sure you want to delete <strong>{{ $course->course_name }}</strong>?</p>
                    @if($course->students()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This course has {{ $course->students()->count() }} enrolled student(s). 
                            Deleting this course may affect existing enrollments.
                        </div>
                    @else
                        <p class="text-muted">The course will be moved to trash and can be restored later.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleStatus(courseId) {
        if (confirm('Are you sure you want to toggle this course\'s status?')) {
            window.location.href = `/admin/courses/${courseId}/toggle-status`;
        }
    }
</script>
@endpush