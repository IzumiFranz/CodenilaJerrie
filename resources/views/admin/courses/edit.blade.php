@extends('layouts.admin')

@section('title', 'Edit Course')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit mr-2"></i>Edit Course: {{ $course->course_name }}</h1>
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
                    <form action="{{ route('admin.courses.update', $course) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="course_code">Course Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="course_code" 
                                   id="course_code" 
                                   class="form-control @error('course_code') is-invalid @enderror" 
                                   value="{{ old('course_code', $course->course_code) }}" 
                                   required>
                            @error('course_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Unique identifier for the course</small>
                        </div>

                        <div class="form-group">
                            <label for="course_name">Course Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="course_name" 
                                   id="course_name" 
                                   class="form-control @error('course_name') is-invalid @enderror" 
                                   value="{{ old('course_name', $course->course_name) }}" 
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
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $course->description) }}</textarea>
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
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('max_years', $course->max_years) == $i ? 'selected' : '' }}>
                                        {{ $i }} Year{{ $i > 1 ? 's' : '' }}
                                    </option>
                                @endfor
                            </select>
                            @error('max_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       name="is_active" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       value="1" 
                                       {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Active Status
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                @if($course->is_active)
                                    <span class="text-success">This course is currently active</span>
                                @else
                                    <span class="text-danger">This course is currently inactive</span>
                                @endif
                            </small>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Subjects</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $course->subjects()->count() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sections</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $course->sections()->count() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Students</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $course->students()->count() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        @if($course->students()->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> This course has enrolled students. Changes may affect existing enrollments.
                            </div>
                        @endif

                        <div class="form-group text-right">
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection