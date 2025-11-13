@extends('layouts.admin')

@section('title', 'Create Section')

@php
    $pageTitle = 'Create New Section';
    $pageActions = '<a href="' . route('admin.sections.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to List</a>';
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Section Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sections.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="course_id">Course <span class="text-danger">*</span></label>
                            <select name="course_id" 
                                    id="course_id" 
                                    class="form-control @error('course_id') is-invalid @enderror" 
                                    required>
                                <option value="">-- Select Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" 
                                        {{ old('course_id', request('course')) == $course->id ? 'selected' : '' }}>
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
                                        <option value="">-- Select Year Level --</option>
                                        @for($i = 1; $i <= 6; $i++)
                                            <option value="{{ $i }}" {{ old('year_level') == $i ? 'selected' : '' }}>
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
                                           value="{{ old('section_name') }}" 
                                           placeholder="e.g., A, B, 1-A, CS-101"
                                           required>
                                    @error('section_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Section identifier (e.g., A, B, 1-A)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="max_students">Maximum Students <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="max_students" 
                                   id="max_students" 
                                   class="form-control @error('max_students') is-invalid @enderror" 
                                   value="{{ old('max_students', 40) }}" 
                                   min="1" 
                                   max="100" 
                                   required>
                            @error('max_students')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum number of students that can be enrolled in this section (1-100)</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       name="is_active" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Active Status
                                </label>
                            </div>
                            <small class="form-text text-muted">Active sections are visible and can accept enrollments</small>
                        </div>

                        <hr>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> The section name must be unique within the same course and year level combination.
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Section
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
        // Update max students based on course selection (optional enhancement)
        $('#course_id').on('change', function() {
            // You can add logic here to adjust default max_students based on course
        });
    });
</script>
@endpush