@extends('layouts.admin')

@section('title', 'View Specialization')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-eye mr-2"></i>Specialization Details: {{ $specialization->name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.specializations.edit', $specialization) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <a href="{{ route('admin.specializations.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>
    <div class="row">
        {{-- Specialization Information --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Specialization Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 text-primary">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h5 class="font-weight-bold mt-3">{{ $specialization->code }}</h5>
                        <p class="text-muted">{{ $specialization->name }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        @if($specialization->is_active)
                            <span class="badge badge-success badge-lg">Active</span>
                        @else
                            <span class="badge badge-secondary badge-lg">Inactive</span>
                        @endif
                    </div>

                    @if($specialization->description)
                        <hr>
                        <div>
                            <strong>Description:</strong><br>
                            <p class="text-muted">{{ $specialization->description }}</p>
                        </div>
                    @endif

                    <hr>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $specialization->created_at->format('M d, Y') }}
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

        {{-- Specialization Content --}}
        <div class="col-lg-8">
            {{-- Stats Cards --}}
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Instructors</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $specialization->instructors()->count() }}
                                    </div>
                                    <div class="text-xs text-muted">
                                        Active: {{ $specialization->getQualifiedInstructorsCount() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Related Subjects</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $specialization->subjects()->count() }}
                                    </div>
                                    <div class="text-xs text-muted">
                                        Requiring this specialization
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-book-open fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Instructors with this Specialization --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Instructors with this Specialization</h6>
                </div>
                <div class="card-body">
                    @if($specialization->instructors()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($specialization->instructors as $instructor)
                                        <tr>
                                            <td>{{ $instructor->employee_id }}</td>
                                            <td>{{ $instructor->full_name }}</td>
                                            <td>{{ $instructor->department ?? 'N/A' }}</td>
                                            <td>
                                                @if($instructor->user->status === 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @elseif($instructor->user->status === 'inactive')
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @else
                                                    <span class="badge badge-danger">Suspended</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $instructor->user) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Instructor">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No instructors with this specialization yet</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Subjects Requiring this Specialization --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subjects Requiring this Specialization</h6>
                </div>
                <div class="card-body">
                    @if($specialization->subjects()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Name</th>
                                        <th>Course</th>
                                        <th>Year Level</th>
                                        <th>Units</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($specialization->subjects as $subject)
                                        <tr>
                                            <td><strong>{{ $subject->subject_code }}</strong></td>
                                            <td>{{ $subject->subject_name }}</td>
                                            <td>{{ $subject->course->course_code }}</td>
                                            <td>Year {{ $subject->year_level }}</td>
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
                                                   title="View Subject">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Edit Subject">
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
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No subjects require this specialization yet</p>
                            <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Subject
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
                    <p>Are you sure you want to delete <strong>{{ $specialization->name }}</strong>?</p>
                    @if($specialization->instructors()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This specialization has {{ $specialization->instructors()->count() }} assigned instructor(s). 
                            Deleting it will remove the specialization from these instructors.
                        </div>
                    @endif
                    @if($specialization->subjects()->count() > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            {{ $specialization->subjects()->count() }} subject(s) require this specialization. 
                            They will become general subjects after deletion.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.specializations.destroy', $specialization) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Specialization</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection