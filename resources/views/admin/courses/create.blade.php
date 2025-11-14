@extends('layouts.admin')

@section('title', 'Create Course')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book mr-2"></i>Create New Course</h1>
    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to List
    </a>
</div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Course Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.courses.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="course_code">Course Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="course_code" 
                                   id="course_code" 
                                   class="form-control @error('course_code') is-invalid @enderror" 
                                   value="{{ old('course_code') }}" 
                                   placeholder="e.g., BSIT, BSCS, BSBA"
                                   required>
                            @error('course_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Unique identifier for the course (e.g., BSIT, BSCS)</small>
                        </div>

                        <div class="form-group">
                            <label for="course_name">Course Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="course_name" 
                                   id="course_name" 
                                   class="form-control @error('course_name') is-invalid @enderror" 
                                   value="{{ old('course_name') }}" 
                                   placeholder="e.g., Bachelor of Science in Information Technology"
                                   required>
                            @error('course_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Brief description of the course...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="max_years">Maximum Years <span class="text-danger">*</span></label>
                            <select name="max_years" 
                                    id="max_years" 
                                    class="form-control @error('max_years') is-invalid @enderror" 
                                    required>
                                <option value="">-- Select Years --</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('max_years', 4) == $i ? 'selected' : '' }}>
                                        {{ $i }} Year{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                            @error('max_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Duration of the course program</small>
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
                            <small class="form-text text-muted">Active courses are visible to users and can accept enrollments</small>
                        </div>

                        <hr>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> After creating the course, you can add subjects and sections to it.
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Course
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
        // Auto-generate course code from course name
        $('#course_name').on('input', function() {
            if ($('#course_code').val() === '') {
                let courseName = $(this).val();
                let words = courseName.split(' ');
                let code = '';
                
                words.forEach(function(word) {
                    if (word.length > 0) {
                        code += word.charAt(0).toUpperCase();
                    }
                });
                
                $('#course_code').val(code);
            }
        });
    });
</script>
@endpush