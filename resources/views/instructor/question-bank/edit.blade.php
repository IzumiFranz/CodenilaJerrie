@extends('layouts.instructor')
@section('title', isset($questionBank) ? 'Edit Question' : 'Create Question')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-question-circle mr-2"></i>{{ isset($questionBank) ? 'Edit Question' : 'Create Question' }}
    </h1>
    <a href="{{ route('instructor.question-bank.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <form action="{{ isset($questionBank) ? route('instructor.question-bank.update', $questionBank) : route('instructor.question-bank.store') }}" 
              method="POST" id="questionForm">
            @csrf
            @if(isset($questionBank))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" 
                                    {{ (old('subject_id', $questionBank->subject_id ?? $selectedSubject) == $subject->id) ? 'selected' : '' }}>
                                {{ $subject->subject_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Question Type <span class="text-danger">*</span></label>
                        <select name="type" id="questionType" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="multiple_choice" {{ old('type', $questionBank->type ?? '') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ old('type', $questionBank->type ?? '') == 'true_false' ? 'selected' : '' }}>True/False</option>
                            <option value="identification" {{ old('type', $questionBank->type ?? '') == 'identification' ? 'selected' : '' }}>Identification</option>
                            <option value="essay" {{ old('type', $questionBank->type ?? '') == 'essay' ? 'selected' : '' }}>Essay</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Question Text <span class="text-danger">*</span></label>
                <textarea name="question_text" class="form-control @error('question_text') is-invalid @enderror" 
                          rows="3" required>{{ old('question_text', $questionBank->question_text ?? '') }}</textarea>
                @error('question_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Multiple Choice Choices -->
            <div id="multipleChoiceSection" style="display:none;">
                <div class="form-group">
                    <label>Choices <span class="text-danger">*</span></label>
                    <div id="choices-container">
                        @if(isset($questionBank) && $questionBank->type == 'multiple_choice')
                            @foreach($questionBank->choices as $index => $choice)
                            <div class="choice-input mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="choices[{{ $index }}][is_correct]" value="1" 
                                                   {{ $choice->is_correct ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <input type="text" name="choices[{{ $index }}][text]" class="form-control" 
                                           value="{{ $choice->choice_text }}" placeholder="Choice {{ $index + 1 }}" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-choice"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="choice-input mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="choices[0][is_correct]" value="1">
                                        </div>
                                    </div>
                                    <input type="text" name="choices[0][text]" class="form-control" placeholder="Choice 1">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-choice"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="choice-input mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="choices[1][is_correct]" value="1">
                                        </div>
                                    </div>
                                    <input type="text" name="choices[1][text]" class="form-control" placeholder="Choice 2">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-choice"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="add-choice" class="btn btn-sm btn-info mt-2">
                        <i class="fas fa-plus"></i> Add Choice
                    </button>
                    <small class="form-text text-muted">Check the box for correct answer(s)</small>
                </div>
            </div>

            <!-- True/False -->
            <div id="trueFalseSection" style="display:none;">
                <div class="form-group">
                    <label>Correct Answer <span class="text-danger">*</span></label>
                    <select name="correct_answer" class="form-control">
                        <option value="true" {{ old('correct_answer', isset($questionBank) && $questionBank->choices->where('choice_text', 'True')->first()?->is_correct ? 'true' : '') == 'true' ? 'selected' : '' }}>True</option>
                        <option value="false" {{ old('correct_answer', isset($questionBank) && $questionBank->choices->where('choice_text', 'False')->first()?->is_correct ? 'false' : '') == 'false' ? 'selected' : '' }}>False</option>
                    </select>
                </div>
            </div>

            <!-- Identification -->
            <div id="identificationSection" style="display:none;">
                <div class="form-group">
                    <label>Correct Answer <span class="text-danger">*</span></label>
                    <input type="text" name="identification_answer" class="form-control" 
                           value="{{ old('identification_answer', isset($questionBank) && $questionBank->type == 'identification' ? $questionBank->choices->first()?->choice_text : '') }}" 
                           placeholder="Enter correct answer">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Points <span class="text-danger">*</span></label>
                        <input type="number" name="points" class="form-control @error('points') is-invalid @enderror" 
                               value="{{ old('points', $questionBank->points ?? 1) }}" min="1" max="100" required>
                        @error('points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Difficulty <span class="text-danger">*</span></label>
                        <select name="difficulty" class="form-control @error('difficulty') is-invalid @enderror" required>
                            <option value="easy" {{ old('difficulty', $questionBank->difficulty ?? '') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('difficulty', $questionBank->difficulty ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('difficulty', $questionBank->difficulty ?? '') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                        @error('difficulty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Bloom's Level</label>
                        <select name="blooms_level" class="form-control">
                            <option value="">Select Level</option>
                            <option value="remember" {{ old('blooms_level', $questionBank->blooms_level ?? '') == 'remember' ? 'selected' : '' }}>Remember</option>
                            <option value="understand" {{ old('blooms_level', $questionBank->blooms_level ?? '') == 'understand' ? 'selected' : '' }}>Understand</option>
                            <option value="apply" {{ old('blooms_level', $questionBank->blooms_level ?? '') == 'apply' ? 'selected' : '' }}>Apply</option>
                            <option value="analyze" {{ old('blooms_level', $questionBank->blooms_level ?? '') == 'analyze' ? 'selected' : '' }}>Analyze</option>
                            <option value="evaluate" {{ old('blooms_level', $questionBank->blooms_level ?? '') == 'evaluate' ? 'selected' : '' }}>Evaluate</option>
                            <option value="create" {{ old('blooms_level', $questionBank->blooms_level ?? '') == 'create' ? 'selected' : '' }}>Create</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Explanation (Optional)</label>
                <textarea name="explanation" class="form-control" rows="2" 
                          placeholder="Explanation for correct answer">{{ old('explanation', $questionBank->explanation ?? '') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i>{{ isset($questionBank) ? 'Update' : 'Create' }} Question
                </button>
                <a href="{{ route('instructor.question-bank.index') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide sections based on question type
    function updateQuestionType() {
        let type = $('#questionType').val();
        $('#multipleChoiceSection, #trueFalseSection, #identificationSection').hide();
        
        if (type === 'multiple_choice') {
            $('#multipleChoiceSection').show();
        } else if (type === 'true_false') {
            $('#trueFalseSection').show();
        } else if (type === 'identification') {
            $('#identificationSection').show();
        }
    }

    $('#questionType').change(updateQuestionType);
    updateQuestionType(); // Initialize on load

    // Add choice
    $('#add-choice').click(function() {
        let choiceNum = $('.choice-input').length;
        let html = `
            <div class="choice-input mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="checkbox" name="choices[${choiceNum}][is_correct]" value="1">
                        </div>
                    </div>
                    <input type="text" name="choices[${choiceNum}][text]" class="form-control" placeholder="Choice ${choiceNum + 1}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-choice"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        `;
        $('#choices-container').append(html);
    });

    // Remove choice
    $(document).on('click', '.remove-choice', function() {
        if ($('.choice-input').length > 2) {
            $(this).closest('.choice-input').remove();
        } else {
            alert('You need at least 2 choices');
        }
    });
});
</script>
@endpush