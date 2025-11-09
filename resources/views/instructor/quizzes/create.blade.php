@extends('layouts.instructor')
@section('title', isset($quiz) ? 'Edit Quiz' : 'Create Quiz')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-clipboard-list mr-2"></i>{{ isset($quiz) ? 'Edit Quiz' : 'Create Quiz' }}
    </h1>
    <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <form action="{{ isset($quiz) ? route('instructor.quizzes.update', $quiz) : route('instructor.quizzes.store') }}" 
              method="POST">
            @csrf
            @if(isset($quiz))
                @method('PUT')
            @endif
            @if(isset($template))
                <div class="alert alert-info">
                    <i class="fas fa-layer-group mr-2"></i>
                    Using template: <strong>{{ $template->name }}</strong>
                </div>
            @endif

            <input type="number" name="time_limit" value="{{ old('time_limit', $template->time_limit ?? 60) }}">
            <input type="number" name="passing_score" value="{{ old('passing_score', $template->passing_score ?? 60) }}">

            <!-- Basic Information -->
            <h5 class="text-success mb-3"><i class="fas fa-info-circle mr-2"></i>Basic Information</h5>
            
            <div class="form-group">
                <label>Subject <span class="text-danger">*</span></label>
                <select name="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" 
                            {{ (old('subject_id', $quiz->subject_id ?? $selectedSubject) == $subject->id) ? 'selected' : '' }}>
                        {{ $subject->subject_name }}
                    </option>
                    @endforeach
                </select>
                @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Quiz Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                       value="{{ old('title', $quiz->title ?? '') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                          rows="3" placeholder="Optional description for students">{{ old('description', $quiz->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr>

            <!-- Quiz Settings -->
            <h5 class="text-success mb-3"><i class="fas fa-cog mr-2"></i>Quiz Settings</h5>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Time Limit (minutes) <span class="text-danger">*</span></label>
                        <input type="number" name="time_limit" class="form-control @error('time_limit') is-invalid @enderror" 
                               value="{{ old('time_limit', $quiz->time_limit ?? 60) }}" min="1" max="300" required>
                        <small class="form-text text-muted">1-300 minutes</small>
                        @error('time_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Passing Score (%) <span class="text-danger">*</span></label>
                        <input type="number" name="passing_score" class="form-control @error('passing_score') is-invalid @enderror" 
                               value="{{ old('passing_score', $quiz->passing_score ?? 60) }}" min="1" max="100" required>
                        <small class="form-text text-muted">1-100%</small>
                        @error('passing_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Max Attempts <span class="text-danger">*</span></label>
                        <input type="number" name="max_attempts" class="form-control @error('max_attempts') is-invalid @enderror" 
                               value="{{ old('max_attempts', $quiz->max_attempts ?? 3) }}" min="1" max="10" required>
                        <small class="form-text text-muted">1-10 attempts</small>
                        @error('max_attempts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <hr>

            <!-- Display Options -->
            <h5 class="text-success mb-3"><i class="fas fa-eye mr-2"></i>Display Options</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="randomize_questions" 
                               name="randomize_questions" value="1" 
                               {{ old('randomize_questions', $quiz->randomize_questions ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="randomize_questions">
                            <strong>Randomize Questions</strong>
                            <br><small class="text-muted">Shuffle question order for each student</small>
                        </label>
                    </div>

                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="randomize_choices" 
                               name="randomize_choices" value="1" 
                               {{ old('randomize_choices', $quiz->randomize_choices ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="randomize_choices">
                            <strong>Randomize Choices</strong>
                            <br><small class="text-muted">Shuffle multiple choice options</small>
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="show_results" 
                               name="show_results" value="1" 
                               {{ old('show_results', $quiz->show_results ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="show_results">
                            <strong>Show Results</strong>
                            <br><small class="text-muted">Display score after submission</small>
                        </label>
                    </div>

                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="show_correct_answers" 
                               name="show_correct_answers" value="1" 
                               {{ old('show_correct_answers', $quiz->show_correct_answers ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="show_correct_answers">
                            <strong>Show Correct Answers</strong>
                            <br><small class="text-muted">Let students review answers</small>
                        </label>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Availability -->
            <h5 class="text-success mb-3"><i class="fas fa-calendar mr-2"></i>Availability</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Available From</label>
                        <input type="datetime-local" name="available_from" class="form-control @error('available_from') is-invalid @enderror" 
                               value="{{ old('available_from', isset($quiz) && $quiz->available_from ? $quiz->available_from->format('Y-m-d\TH:i') : '') }}">
                        <small class="form-text text-muted">Leave empty for immediate availability</small>
                        @error('available_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Available Until</label>
                        <input type="datetime-local" name="available_until" class="form-control @error('available_until') is-invalid @enderror" 
                               value="{{ old('available_until', isset($quiz) && $quiz->available_until ? $quiz->available_until->format('Y-m-d\TH:i') : '') }}">
                        <small class="form-text text-muted">Leave empty for no end date</small>
                        @error('available_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <hr>

            <!-- Publish -->
            <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" class="custom-control-input" id="is_published" 
                       name="is_published" value="1" 
                       {{ old('is_published', $quiz->is_published ?? false) ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_published">
                    <strong>Publish Quiz (visible to students)</strong>
                </label>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i>{{ isset($quiz) ? 'Update Quiz' : 'Create Quiz' }}
                </button>
                @if(isset($quiz))
                    <a href="{{ route('instructor.quizzes.questions', $quiz) }}" class="btn btn-primary ml-2">
                        <i class="fas fa-tasks mr-2"></i>Manage Questions
                    </a>
                @endif
                <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection