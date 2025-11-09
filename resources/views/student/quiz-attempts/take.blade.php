@extends('layouts.student')

@section('title', 'Taking Quiz')

@section('content')
<!-- Quiz Header -->
<div class="card shadow mb-4 border-left-primary">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-2">{{ $attempt->quiz->title }}</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-book mr-1"></i>{{ $attempt->quiz->subject->subject_name }} | 
                    <i class="fas fa-user mr-1"></i>{{ $attempt->quiz->instructor->full_name }}
                </p>
            </div>
            <div class="col-md-4 text-right">
                @if($remainingTime !== null)
                <div class="alert alert-warning mb-0" id="timer-container">
                    <i class="fas fa-clock mr-2"></i>
                    <strong>Time Remaining:</strong>
                    <span id="timer" class="font-weight-bold" data-remaining="{{ $remainingTime }}">
                        {{ gmdate('H:i:s', $remainingTime * 60) }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quiz Instructions -->
@if($attempt->quiz->instructions)
<div class="alert alert-info">
    <h5><i class="fas fa-info-circle mr-2"></i>Instructions</h5>
    {!! nl2br(e($attempt->quiz->instructions)) !!}
</div>
@endif

<!-- Quiz Form -->
<form id="quiz-form" method="POST" action="{{ route('student.quiz-attempts.submit', $attempt) }}">
    @csrf
    
    <!-- Questions -->
    @foreach($questions as $index => $question)
    <div class="card shadow mb-4 question-card" id="question-{{ $question->id }}">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <span class="badge badge-primary">Question {{ $index + 1 }}</span>
                    <span class="badge badge-info">{{ $question->points }} {{ Str::plural('point', $question->points) }}</span>
                    @if($question->difficulty)
                    <span class="badge badge-secondary">{{ ucfirst($question->difficulty) }}</span>
                    @endif
                </h5>
                <span class="auto-save-indicator" id="save-indicator-{{ $question->id }}" style="display: none;">
                    <i class="fas fa-check-circle text-success"></i> Saved
                </span>
            </div>
        </div>
        <div class="card-body">
            <!-- Question Text -->
            <div class="mb-4">
                <p class="lead">{!! nl2br(e($question->question_text)) !!}</p>
            </div>

            @php
                $existingAnswer = $existingAnswers->get($question->id);
            @endphp

            <!-- Multiple Choice / True-False -->
            @if($question->isMultipleChoice() || $question->isTrueFalse())
            <div class="choices-container">
                @foreach($question->choices as $choice)
                <div class="custom-control custom-radio mb-3">
                    <input type="radio" 
                           class="custom-control-input answer-input" 
                           id="choice-{{ $choice->id }}" 
                           name="answers[{{ $question->id }}]" 
                           value="{{ $choice->id }}"
                           data-question-id="{{ $question->id }}"
                           {{ $existingAnswer && $existingAnswer->choice_id == $choice->id ? 'checked' : '' }}>
                    <label class="custom-control-label" for="choice-{{ $choice->id }}">
                        {{ $choice->choice_text }}
                    </label>
                </div>
                @endforeach
            </div>

            <!-- Identification -->
            @elseif($question->isIdentification())
            <div class="form-group">
                <input type="text" 
                       class="form-control form-control-lg answer-input" 
                       name="answers[{{ $question->id }}]"
                       data-question-id="{{ $question->id }}"
                       placeholder="Type your answer here..."
                       value="{{ $existingAnswer ? $existingAnswer->answer_text : '' }}">
            </div>

            <!-- Essay -->
            @elseif($question->isEssay())
            <div class="form-group">
                <textarea class="form-control answer-input" 
                          name="answers[{{ $question->id }}]"
                          data-question-id="{{ $question->id }}"
                          rows="8" 
                          placeholder="Type your essay answer here...">{{ $existingAnswer ? $existingAnswer->answer_text : '' }}</textarea>
                <small class="form-text text-muted">
                    <span class="char-count" data-for="{{ $question->id }}">0</span> characters
                </small>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <!-- Navigation -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 text-muted">
                        <i class="fas fa-question-circle mr-2"></i>
                        <strong>Total Questions:</strong> {{ $questions->count() }}
                    </p>
                    <p class="mb-0 text-muted">
                        <i class="fas fa-star mr-2"></i>
                        <strong>Total Points:</strong> {{ $attempt->total_points }}
                    </p>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" id="review-btn">
                        <i class="fas fa-eye mr-1"></i>Review Answers
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-paper-plane mr-1"></i>Submit Quiz
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                    Submit Quiz?
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit this quiz?</p>
                <div id="unanswered-warning" class="alert alert-warning" style="display: none;">
                    <strong>Warning:</strong> You have <span id="unanswered-count">0</span> unanswered question(s).
                </div>
                <p class="text-muted mb-0">Once submitted, you cannot change your answers.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-submit">
                    <i class="fas fa-check mr-1"></i>Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-list-check mr-2"></i>Review Your Answers
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="review-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .question-card {
        scroll-margin-top: 100px;
    }
    
    .auto-save-indicator {
        font-size: 0.875rem;
        animation: fadeIn 0.3s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .timer-critical {
        animation: blink 1s infinite;
    }
    
    @keyframes blink {
        0%, 50%, 100% { opacity: 1; }
        25%, 75% { opacity: 0.7; }
    }
</style>
@endpush

@push('scripts')
<script>
let autoSaveTimeout;
const AUTOSAVE_DELAY = 2000; // 2 seconds

$(document).ready(function() {
    // Timer functionality
    @if($remainingTime !== null)
    let remainingSeconds = {{ $remainingTime * 60 }};
    const timerElement = $('#timer');
    const timerContainer = $('#timer-container');
    
    const timerInterval = setInterval(function() {
        remainingSeconds--;
        
        if (remainingSeconds <= 0) {
            clearInterval(timerInterval);
            alert('Time is up! The quiz will be submitted automatically.');
            $('#quiz-form').submit();
            return;
        }
        
        // Format time
        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = remainingSeconds % 60;
        
        timerElement.text(
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0')
        );
        
        // Warning when 5 minutes left
        if (remainingSeconds <= 300) {
            timerContainer.removeClass('alert-warning').addClass('alert-danger timer-critical');
        }
    }, 1000);
    @endif
    
    // Auto-save functionality
    $('.answer-input').on('change input', function() {
        clearTimeout(autoSaveTimeout);
        const $input = $(this);
        const questionId = $input.data('question-id');
        
        autoSaveTimeout = setTimeout(function() {
            saveAnswer(questionId);
        }, AUTOSAVE_DELAY);
    });
    
    // Character counter for essays
    $('textarea.answer-input').on('input', function() {
        const questionId = $(this).data('question-id');
        const charCount = $(this).val().length;
        $(`.char-count[data-for="${questionId}"]`).text(charCount);
    });
    
    // Initialize character counters
    $('textarea.answer-input').trigger('input');
    
    // Save answer function
    function saveAnswer(questionId) {
        const $questionCard = $(`#question-${questionId}`);
        const $inputs = $questionCard.find('.answer-input');
        let answerData = {
            _token: '{{ csrf_token() }}',
            question_id: questionId
        };
        
        // Get answer value
        const $radioInput = $inputs.filter(':checked');
        if ($radioInput.length) {
            answerData.choice_id = $radioInput.val();
        } else {
            answerData.answer_text = $inputs.val();
        }
        
        // Show saving indicator
        const $indicator = $(`#save-indicator-${questionId}`);
        $indicator.html('<i class="fas fa-spinner fa-spin"></i> Saving...').show();
        
        // Save via AJAX
        $.ajax({
            url: '{{ route("student.quiz-attempts.save-answer", $attempt) }}',
            method: 'POST',
            data: answerData,
            success: function(response) {
                $indicator.html('<i class="fas fa-check-circle text-success"></i> Saved').show();
                setTimeout(function() {
                    $indicator.fadeOut();
                }, 2000);
            },
            error: function(xhr) {
                $indicator.html('<i class="fas fa-exclamation-circle text-danger"></i> Error').show();
                console.error('Save error:', xhr);
            }
        });
    }
    
    // Submit button handler
    $('#submit-btn').click(function(e) {
        e.preventDefault();
        
        // Check for unanswered questions
        const totalQuestions = $('.question-card').length;
        let answeredCount = 0;
        
        $('.question-card').each(function() {
            const $card = $(this);
            const hasRadioAnswer = $card.find('.answer-input:checked').length > 0;
            const hasTextAnswer = $card.find('.answer-input').filter('input[type="text"], textarea').val().trim().length > 0;
            
            if (hasRadioAnswer || hasTextAnswer) {
                answeredCount++;
            }
        });
        
        const unansweredCount = totalQuestions - answeredCount;
        
        if (unansweredCount > 0) {
            $('#unanswered-count').text(unansweredCount);
            $('#unanswered-warning').show();
        } else {
            $('#unanswered-warning').hide();
        }
        
        $('#submitModal').modal('show');
    });
    
    // Confirm submit
    $('#confirm-submit').click(function() {
        $('#submitModal').modal('hide');
        $('#quiz-form').submit();
    });
    
    // Review button
    $('#review-btn').click(function() {
        let reviewHtml = '<div class="list-group">';
        
        $('.question-card').each(function(index) {
            const $card = $(this);
            const questionId = $card.attr('id').replace('question-', '');
            const questionNum = index + 1;
            const hasAnswer = $card.find('.answer-input:checked').length > 0 || 
                             $card.find('.answer-input').filter('input[type="text"], textarea').val().trim().length > 0;
            
            reviewHtml += `
                <a href="#${$card.attr('id')}" class="list-group-item list-group-item-action" data-dismiss="modal">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Question ${questionNum}</span>
                        ${hasAnswer ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-circle text-muted"></i>'}
                    </div>
                </a>
            `;
        });
        
        reviewHtml += '</div>';
        $('#review-content').html(reviewHtml);
        $('#reviewModal').modal('show');
    });
    
    // Prevent accidental page leave
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = 'Are you sure you want to leave? Your progress may be lost.';
        return e.returnValue;
    });
    
    // Remove warning on form submit
    $('#quiz-form').submit(function() {
        window.removeEventListener('beforeunload', function() {});
    });
});
</script>
@endpush