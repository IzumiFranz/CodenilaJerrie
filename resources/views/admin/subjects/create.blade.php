@extends('layouts.admin')

@section('title', 'Create Subject')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book-open mr-2"></i>Create New Subject</h1>
    <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to List
    </a>
</div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subject Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subjects.store') }}" method="POST">
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
                                        {{ old('course_id', $selectedCourse ?? '') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_code }} - {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="specialization_id">Specialization (Optional)</label>
                            <select name="specialization_id" 
                                    id="specialization_id" 
                                    class="form-control @error('specialization_id') is-invalid @enderror">
                                <option value="">-- None (General) --</option>
                                @foreach($specializations as $spec)
                                    <option value="{{ $spec->id }}" {{ old('specialization_id') == $spec->id ? 'selected' : '' }}>
                                        {{ $spec->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialization_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">If specified, only instructors with this specialization can teach this subject</small>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject_code">Subject Code <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="subject_code" 
                                           id="subject_code" 
                                           class="form-control @error('subject_code') is-invalid @enderror" 
                                           value="{{ old('subject_code') }}" 
                                           placeholder="e.g., CS101, MATH201"
                                           required>
                                    @error('subject_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="units">Units <span class="text-danger">*</span></label>
                                    <select name="units" 
                                            id="units" 
                                            class="form-control @error('units') is-invalid @enderror" 
                                            required>
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ old('units', 3) == $i ? 'selected' : '' }}>
                                                {{ $i }} Unit{{ $i > 1 ? 's' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('units')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject_name">Subject Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="subject_name" 
                                   id="subject_name" 
                                   class="form-control @error('subject_name') is-invalid @enderror" 
                                   value="{{ old('subject_name') }}" 
                                   placeholder="e.g., Introduction to Programming"
                                   required>
                            @error('subject_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Brief description of the subject...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

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
                            <small class="form-text text-muted">Active subjects are visible and can be assigned to instructors</small>
                        </div>

                        <hr>

                        <div class="form-group text-right">
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection