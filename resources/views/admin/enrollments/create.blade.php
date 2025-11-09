@extends('layouts.admin')
@section('title', 'Enroll Student')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-graduate mr-2"></i>Enroll Student</h1>
    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to List
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Single Enrollment</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.enrollments.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Student <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                            <option value="">Select Student</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->student_number }} - {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-control" required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
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

                    <div class="form-group">
                        <label class="form-label">Enrollment Date <span class="text-danger">*</span></label>
                        <input type="date" name="enrollment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Enroll Student
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Bulk Enrollment</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.enrollments.bulk-enroll') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Upload a CSV file with student numbers and section IDs.
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

                    <div class="form-group">
                        <label class="form-label">CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <small class="form-text text-muted">Format: student_number, section_id</small>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-upload mr-1"></i> Bulk Enroll
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection