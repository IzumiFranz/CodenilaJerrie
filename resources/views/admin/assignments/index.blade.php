@extends('layouts.admin')
@section('title', 'Instructor Assignments')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chalkboard-teacher mr-2"></i>Instructor Assignments</h1>
    <a href="{{ route('admin.assignments.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus mr-1"></i> Create Assignment
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search instructor..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="course_id" class="form-control">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="academic_year" class="form-control">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="semester" class="form-control">
                        <option value="">All Semesters</option>
                        <option value="1st" {{ request('semester') == '1st' ? 'selected' : '' }}>1st</option>
                        <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>2nd</option>
                        <option value="summer" {{ request('semester') == 'summer' ? 'selected' : '' }}>Summer</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Assignments List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Instructor</th>
                        <th>Subject</th>
                        <th>Section</th>
                        <th>Academic Year</th>
                        <th>Semester</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->instructor->full_name }}</td>
                        <td>{{ $assignment->subject->subject_name }}</td>
                        <td>{{ $assignment->section->full_name }}</td>
                        <td>{{ $assignment->academic_year }}</td>
                        <td>{{ $assignment->semester }}</td>
                        <td>{{ $assignment->getEnrolledStudentsCount() }}</td>
                        <td>
                            <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this assignment?">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <p class="mb-0">No assignments found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $assignments->links() }}
        </div>
    </div>
</div>
@livewire('assignment-table')
@endsection