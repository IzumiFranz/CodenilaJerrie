@extends('layouts.admin')

@section('title', 'Edit Enrollment')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit mr-2"></i>Edit Enrollment</h1>
    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to List
    </a>
</div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Enrollment Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.enrollments.update', $enrollment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Student Information (Read-only) --}}
                        <div class="alert alert-info">
                            <strong>Student:</strong> {{ $enrollment->student->student_number }} - {{ $enrollment->student->full_name }}<br>
                            <strong>Course:</strong> {{ $enrollment->student->course->course_code ?? 'N/A' }} - {{ $enrollment->student->course->course_name ?? 'N/A' }}<br>
                            <strong>Year Level:</strong> Year {{ $enrollment->student->year_level }}
                        </div>

                        <hr>

                        {{-- Academic Period --}}
                        <h6 class="font-weight-bold text-primary mb-3">Academic Period</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="academic_year">Academic Year <span class="text-danger">*</span></label>
                                    <select name="academic_year" 
                                            id="academic_year" 
                                            class="form-control @error('academic_year') is-invalid @enderror" 
                                            required>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year }}" {{ old('academic_year', $enrollment->academic_year) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="semester">Semester <span class="text-danger">*</span></label>
                                    <select name="semester" 
                                            id="semester" 
                                            class="form-control @error('semester') is-invalid @enderror" 
                                            required>
                                        <option value="1st" {{ old('semester', $enrollment->semester) === '1st' ? 'selected' : '' }}>1st Semester</option>
                                        <option value="2nd" {{ old('semester', $enrollment->semester) === '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                        <option value="summer" {{ old('semester', $enrollment->semester) === 'summer' ? 'selected' : '' }}>Summer</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Section Selection --}}
                        <h6 class="font-weight-bold text-primary mb-3">Section Assignment</h6>

                        <div class="form-group">
                            <label for="section_id">Section <span class="text-danger">*</span></label>
                            <select name="section_id" 
                                    id="section_id" 
                                    class="form-control @error('section_id') is-invalid @enderror" 
                                    required>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" 
                                        {{ old('section_id', $enrollment->section_id) == $section->id ? 'selected' : '' }}>
                                        {{ $section->full_name }} (Year {{ $section->year_level }}) - Max: {{ $section->max_students }} students
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="form-group">
                            <label for="status">Enrollment Status <span class="text-danger">*</span></label>
                            <select name="status" 
                                    id="status" 
                                    class="form-control @error('status') is-invalid @enderror" 
                                    required>
                                <option value="enrolled" {{ old('status', $enrollment->status) === 'enrolled' ? 'selected' : '' }}>
                                    Enrolled
                                </option>
                                <option value="dropped" {{ old('status', $enrollment->status) === 'dropped' ? 'selected' : '' }}>
                                    Dropped
                                </option>
                                <option value="completed" {{ old('status', $enrollment->status) === 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <strong>Enrolled:</strong> Currently enrolled and active<br>
                                <strong>Dropped:</strong> Student has dropped this enrollment<br>
                                <strong>Completed:</strong> Student has completed this enrollment
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="enrollment_date">Enrollment Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="date" 
                                       name="enrollment_date" 
                                       id="enrollment_date" 
                                       class="form-control @error('enrollment_date') is-invalid @enderror" 
                                       value="{{ old('enrollment_date', $enrollment->enrollment_date->format('Y-m-d')) }}" 
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                            @error('enrollment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        {{-- Enrollment History --}}
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="font-weight-bold">Enrollment History</h6>
                                <p class="mb-1"><strong>Original Enrollment Date:</strong> {{ $enrollment->enrollment_date->format('M d, Y') }}</p>
                                <p class="mb-1"><strong>Created:</strong> {{ $enrollment->created_at->format('M d, Y h:i A') }}</p>
                                <p class="mb-0"><strong>Last Updated:</strong> {{ $enrollment->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        @if($enrollment->status === 'enrolled')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> Changing the section or status will affect the student's current enrollment.
                            </div>
                        @endif

                        <div class="form-group text-right">
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            @if($enrollment->status === 'enrolled')
                                <button type="button" 
                                        class="btn btn-warning" 
                                        data-toggle="modal" 
                                        data-target="#dropModal">
                                    <i class="fas fa-user-minus"></i> Drop Student
                                </button>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Enrollment
                            </button>
                        </div>
                    </form>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Show warning when changing status
        $('#status').on('change', function() {
            const status = $(this).val();
            if (status === 'dropped') {
                alert('Note: Dropping a student will remove them from the active enrollment list.');
            } else if (status === 'completed') {
                alert('Note: Marking as completed indicates the student has finished this enrollment period.');
            }
        });
    });
</script>
@endpush