@extends('layouts.admin')

@section('title', 'Edit Subject')

@php
    $pageTitle = 'Edit Subject: ' . $subject->subject_name;
    $pageActions = '<a href="' . route('admin.subjects.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to List</a>';
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subject Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
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
                                        {{ old('course_id', $subject->course_id) == $course->id ? 'selected' : '' }}>
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
                                    <option value="{{ $spec->id }}" 
                                        {{ old('specialization_id', $subject->specialization_id) == $spec->id ? 'selected' : '' }}>
                                        {{ $spec->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialization_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                           value="{{ old('subject_code', $subject->subject_code) }}" 
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
                                            <option value="{{ $i }}" {{ old('units', $subject->units) == $i ? 'selected' : '' }}>
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
                                   value="{{ old('subject_name', $subject->subject_name) }}" 
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
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $subject->description) }}</textarea>
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
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('year_level', $subject->year_level) == $i ? 'selected' : '' }}>
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
                                       {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
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
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Lessons</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subject->getPublishedLessonsCount() }}/{{ $subject->lessons()->count() }}</div>
                                        <small class="text-muted">Published/Total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Quizzes</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subject->getPublishedQuizzesCount() }}/{{ $subject->quizzes()->count() }}</div>
                                        <small class="text-muted">Published/Total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Questions</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subject->getTotalQuestionsCount() }}</div>
                                        <small class="text-muted">In Question Bank</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($subject->assignments()->count() > 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                This subject has {{ $subject->assignments()->count() }} active assignment(s) to instructors.
                            </div>
                        @endif

                        <div class="form-group text-right">
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection