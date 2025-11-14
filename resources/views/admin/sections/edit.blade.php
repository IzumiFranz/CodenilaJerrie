@extends('layouts.admin')

@section('title', 'Edit Section')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit mr-2"></i>Edit Section: {{ $section->full_name }}</h1>
    <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to List
    </a>
</div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Section Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sections.update', $section) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="course_id">Course <span class="text-danger">*</span></label>
                            <select name="course_id" 
                                    id="course_id" 
                                    class="form-control @error('course_id') is-invalid @enderror" 
                                    required>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" 
                                        {{ old('course_id', $section->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_code }} - {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="year_level">Year Level <span class="text-danger">*</span></label>
                                    <select name="year_level" 
                                            id="year_level" 
                                            class="form-control @error('year_level') is-invalid @enderror" 
                                            required>
                                        @for($i = 1; $i <= 6; $i++)
                                            <option value="{{ $i }}" {{ old('year_level', $section->year_level) == $i ? 'selected' : '' }}>
                                                Year {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('year_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="section_name">Section Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="section_name" 
                                           id="section_name" 
                                           class="form-control @error('section_name') is-invalid @enderror" 
                                           value="{{ old('section_name', $section->section_name) }}" 
                                           required>
                                    @error('section_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="max_students">Maximum Students <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="max_students" 
                                   id="max_students" 
                                   class="form-control @error('max_students') is-invalid @enderror" 
                                   value="{{ old('max_students', $section->max_students) }}" 
                                   min="1" 
                                   max="100" 
                                   required>
                            @error('max_students')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @php
                                $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
                                $currentSemester = now()->month >= 6 && now()->month <= 10 ? '1st' : (now()->month >= 11 || now()->month <= 3 ? '2nd' : 'summer');
                                $enrolledCount = $section->getEnrolledStudentsCount($currentAcademicYear, $currentSemester);
                            @endphp
                            <small class="form-text text-muted">
                                Currently enrolled: <strong>{{ $enrolledCount }}</strong> students
                                @if($enrolledCount > 0)
                                    (Cannot set max below current enrollment count)
                                @endif
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       name="is_active" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       value="1" 
                                       {{ old('is_active', $section->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Active Status
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Enrollments</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $section->enrollments()->where('status', 'enrolled')->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Assignments</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $section->assignments()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Capacity</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            {{ $enrolledCount }}/{{ $section->max_students }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($section->enrollments()->where('status', 'enrolled')->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> This section has enrolled students. Changes may affect existing enrollments.
                            </div>
                        @endif

                        <div class="form-group text-right">
                            <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Section
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Validation for max_students
        const enrolledCount = {{ $enrolledCount }};
        $('#max_students').on('input', function() {
            const value = parseInt($(this).val());
            if (value < enrolledCount) {
                $(this).addClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
                $(this).after('<div class="invalid-feedback" style="display: block;">Cannot set max students below current enrollment count (' + enrolledCount + ')</div>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').remove();
            }
        });
    });
</script>
@endpush