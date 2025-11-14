@extends('layouts.admin')

@section('title', 'View Subject')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-eye mr-2"></i>Subject Details: {{ $subject->subject_name }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>
    <div class="row">
        {{-- Subject Information --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Subject Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 text-primary">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h5 class="font-weight-bold mt-3">{{ $subject->subject_code }}</h5>
                        <p class="text-muted">{{ $subject->subject_name }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Course:</strong><br>
                        <a href="{{ route('admin.courses.show', $subject->course) }}">
                            {{ $subject->course->course_code }} - {{ $subject->course->course_name }}
                        </a>
                    </div>

                    <div class="mb-3">
                        <strong>Year Level:</strong><br>
                        Year {{ $subject->year_level }}
                    </div>

                    <div class="mb-3">
                        <strong>Units:</strong><br>
                        {{ $subject->units }} Unit{{ $subject->units > 1 ? 's' : '' }}
                    </div>

                    <div class="mb-3">
                        <strong>Specialization:</strong><br>
                        @if($subject->specialization)
                            <a href="{{ route('admin.specializations.show', $subject->specialization) }}">
                                {{ $subject->specialization->name }}
                            </a>
                        @else
                            <span class="text-muted">None (General)</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        @if($subject->is_active)
                            <span class="badge badge-success badge-lg">Active</span>
                        @else
                            <span class="badge badge-secondary badge-lg">Inactive</span>
                        @endif
                    </div>

                    @if($subject->description)
                        <hr>
                        <div>
                            <strong>Description:</strong><br>
                            <p class="text-muted">{{ $subject->description }}</p>
                        </div>
                    @endif

                    <hr>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $subject->created_at->format('M d, Y') }}
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

        {{-- Subject Content & Stats --}}
        <div class="col-lg-8">
            {{-- Stats Cards --}}
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Lessons</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $subject->lessons()->count() }}
                                    </div>
                                    <small class="text-muted">Published: {{ $subject->getPublishedLessonsCount() }}</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Quizzes</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $subject->quizzes()->count() }}
                                    </div>
                                    <small class="text-muted">Published: {{ $subject->getPublishedQuizzesCount() }}</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Questions</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $subject->getTotalQuestionsCount() }}
                                    </div>
                                    <small class="text-muted">In Bank</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Instructors</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $subject->assignments()->count() }}
                                    </div>
                                    <small class="text-muted">Assigned</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Qualified Instructors --}}
            @if($subject->specialization_id)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Qualified Instructors</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $qualifiedInstructors = $subject->getQualifiedInstructors();
                        @endphp
                        @if($qualifiedInstructors->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>Specialization</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($qualifiedInstructors as $instructor)
                                            <tr>
                                                <td>{{ $instructor->employee_id }}</td>
                                                <td>{{ $instructor->full_name }}</td>
                                                <td>{{ $instructor->specialization->name }}</td>
                                                <td>
                                                    @if($instructor->user->status === 'active')
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($instructor->user->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No qualified instructors found for this specialization.</p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Current Assignments --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Assignments</h6>
                </div>
                <div class="card-body">
                    @if($subject->assignments()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Instructor</th>
                                        <th>Section</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subject->assignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->instructor->full_name }}</td>
                                            <td>{{ $assignment->section->full_name }}</td>
                                            <td>{{ $assignment->academic_year }}</td>
                                            <td>{{ ucfirst($assignment->semester) }} Semester</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No assignments yet</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Lessons --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Lessons</h6>
                </div>
                <div class="card-body">
                    @if($subject->lessons()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Instructor</th>
                                        <th>Status</th>
                                        <th>Views</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subject->lessons()->orderBy('created_at', 'desc')->limit(5)->get() as $lesson)
                                        <tr>
                                            <td>{{ $lesson->title }}</td>
                                            <td>{{ $lesson->instructor->full_name }}</td>
                                            <td>
                                                @if($lesson->is_published)
                                                    <span class="badge badge-success">Published</span>
                                                @else
                                                    <span class="badge badge-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td>{{ $lesson->view_count }}</td>
                                            <td>{{ $lesson->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No lessons created yet</p>
                    @endif
                </div>
            </div>

            {{-- Recent Quizzes --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Quizzes</h6>
                </div>
                <div class="card-body">
                    @if($subject->quizzes()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Instructor</th>
                                        <th>Questions</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subject->quizzes()->orderBy('created_at', 'desc')->limit(5)->get() as $quiz)
                                        <tr>
                                            <td>{{ $quiz->title }}</td>
                                            <td>{{ $quiz->instructor->full_name }}</td>
                                            <td>{{ $quiz->getQuestionsCount() }}</td>
                                            <td>
                                                @if($quiz->is_published)
                                                    <span class="badge badge-success">Published</span>
                                                @else
                                                    <span class="badge badge-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td>{{ $quiz->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No quizzes created yet</p>
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
                    <p>Are you sure you want to delete <strong>{{ $subject->subject_name }}</strong>?</p>
                    @if($subject->assignments()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            This subject has active assignments and may contain lessons and quizzes.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Subject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection