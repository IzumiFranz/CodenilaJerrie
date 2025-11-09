@extends('layouts.admin')
@section('title', 'Create Assignment')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chalkboard-teacher mr-2"></i>Create Assignment</h1>
    <a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.assignments.store') }}" method="POST" data-validate>
            @csrf
            
            <div class="form-group">
                <label class="form-label">Instructor <span class="text-danger">*</span></label>
                <select name="instructor_id" class="form-control @error('instructor_id') is-invalid @enderror" required>
                    <option value="">Select Instructor</option>
                    @foreach($instructors as $instructor)
                    <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                        {{ $instructor->full_name }} 
                        @if($instructor->specialization)
                        ({{ $instructor->specialization->name }})
                        @endif
                    </option>
                    @endforeach
                </select>
                @error('instructor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-control" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                            <option value="">Select Subject</option>
                        </select>
                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Section <span class="text-danger">*</span></label>
                <select name="section_id" class="form-control @error('section_id') is-invalid @enderror" required>
                    <option value="">Select Section</option>
                </select>
                @error('section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select name="academic_year" class="form-control" required>
                            @foreach($academicYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-control" required>
                            <option value="1st">1st Semester</option>
                            <option value="2nd">2nd Semester</option>
                            <option value="summer">Summer</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Create Assignment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection