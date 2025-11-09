@extends('layouts.admin')
@section('title', 'Create Course')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book mr-2"></i>Create Course</h1>
    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.courses.store') }}" method="POST" data-validate>
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Course Code <span class="text-danger">*</span></label>
                        <input type="text" name="course_code" class="form-control @error('course_code') is-invalid @enderror" value="{{ old('course_code') }}" required>
                        @error('course_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Max Years <span class="text-danger">*</span></label>
                        <select name="max_years" class="form-control" required>
                            @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('max_years', 4) == $i ? 'selected' : '' }}>{{ $i }} Year(s)</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Course Name <span class="text-danger">*</span></label>
                <input type="text" name="course_name" class="form-control @error('course_name') is-invalid @enderror" value="{{ old('course_name') }}" required>
                @error('course_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Create Course
                </button>
            </div>
        </form>
    </div>
</div>
@endsection