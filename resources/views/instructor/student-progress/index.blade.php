@extends('layouts.instructor')
@section('title', 'Student Progress')
@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-line mr-2"></i>Student Progress</h1>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row">
                <div class="col-md-4">
                    <label>Academic Year</label>
                    <select name="academic_year" class="form-control">
                        @foreach($academicYears as $year)
                        <option value="{{ $year }}" {{ request('academic_year', $currentAcademicYear) == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Semester</label>
                    <select name="semester" class="form-control">
                        <option value="1st" {{ request('semester', $currentSemester) == '1st' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2nd" {{ request('semester', $currentSemester) == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                        <option value="summer" {{ request('semester', $currentSemester) == 'summer' ? 'selected' : '' }}>Summer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Subject</label>
                    <select name="subject_id" class="form-control">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Teaching Assignments -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Your Teaching Assignments</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Section</th>
                        <th>Course</th>
                        <th>Students Enrolled</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    <tr>
                        <td><strong>{{ $assignment->subject->subject_name }}</strong></td>
                        <td>{{ $assignment->section->section_name }}</td>
                        <td>{{ $assignment->section->course->course_name }}</td>
                        <td>
                            @php
                                $enrolledCount = \App\Models\Enrollment::where('section_id', $assignment->section_id)
                                    ->where('academic_year', request('academic_year', $currentAcademicYear))
                                    ->where('semester', request('semester', $currentSemester))
                                    ->where('status', 'enrolled')
                                    ->count();
                            @endphp
                            <span class="badge badge-info">{{ $enrolledCount }} students</span>
                        </td>
                        <td>
                            <a href="{{ route('instructor.student-progress.export', ['section' => $assignment->section_id, 'academic_year' => request('academic_year', $currentAcademicYear), 'semester' => request('semester', $currentSemester)]) }}" 
                               class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No teaching assignments found for selected period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Students per Assignment -->
@foreach($assignments as $assignment)
<div class="card shadow mt-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">
            {{ $assignment->subject->subject_name }} - {{ $assignment->section->section_name }}
        </h6>
        <a href="{{ route('instructor.student-progress.export', ['section' => $assignment->section_id, 'academic_year' => request('academic_year', $currentAcademicYear), 'semester' => request('semester', $currentSemester)]) }}" 
           class="btn btn-sm btn-success">
            <i class="fas fa-download"></i> Export
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Student Number</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $students = \App\Models\Student::whereHas('enrollments', function($q) use ($assignment, $currentAcademicYear, $currentSemester) {
                            $q->where('section_id', $assignment->section_id)
                              ->where('academic_year', request('academic_year', $currentAcademicYear))
                              ->where('semester', request('semester', $currentSemester))
                              ->where('status', 'enrolled');
                        })->with('user', 'course')->get();
                    @endphp
                    
                    @forelse($students as $student)
                    <tr>
                        <td>{{ $student->student_number }}</td>
                        <td><strong>{{ $student->full_name }}</strong></td>
                        <td>{{ $student->course->course_name }}</td>
                        <td>
                            <a href="{{ route('instructor.student-progress.show', ['student' => $student->id, 'academic_year' => request('academic_year', $currentAcademicYear), 'semester' => request('semester', $currentSemester)]) }}" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View Progress
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">No students enrolled</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

@endsection