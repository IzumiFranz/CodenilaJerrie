@extends('layouts.student')

@section('title', 'Submit Feedback')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.feedback.index') }}">Feedback</a></li>
        <li class="breadcrumb-item active">Submit</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-comment-dots mr-2"></i>Submit Feedback
                </h5>
            </div>
            <div class="card-body">
                @if($feedbackable)
                <div class="alert alert-info">
                    <strong>Feedback for:</strong><br>
                    @if($feedbackableType === 'quiz')
                        <i class="fas fa-clipboard-list mr-1"></i>Quiz: {{ $feedbackable->title }}
                    @elseif($feedbackableType === 'lesson')
                        <i class="fas fa-book-open mr-1"></i>Lesson: {{ $feedbackable->title }}
                    @endif
                </div>
                @endif

                <form method="POST" action="{{ route('student.feedback.store') }}">
                    @csrf
                    
                    @if($feedbackable)
                    <input type="hidden" name="feedbackable_type" value="{{ get_class($feedbackable) }}">
                    <input type="hidden" name="feedbackable_id" value="{{ $feedbackable->id }}">
                    @endif

                    @if(!$feedbackable)
                    <div class="form-group">
                        <label for="feedbackable_type">Feedback Type</label>
                        <select class="form-control @error('feedbackable_type') is-invalid @enderror" 
                                id="feedbackable_type" name="feedbackable_type">
                            <option value="">General Feedback</option>
                            <option value="App\Models\Quiz">About a Quiz</option>
                            <option value="App\Models\Lesson">About a Lesson</option>
                        </select>
                        @error('feedbackable_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="rating">Rating (Optional)</label>
                        <div class="star-rating">
                            <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}">
                            <div id="star-rating-display">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star star-icon text-muted" data-rating="{{ $i }}" style="font-size: 2rem; cursor: pointer;"></i>
                                @endfor
                            </div>
                        </div>
                        <small class="form-text text-muted">Click on stars to rate</small>
                    </div>

                    <div class="form-group">
                        <label for="comment">Your Feedback <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" 
                                  id="comment" 
                                  name="comment" 
                                  rows="8" 
                                  required 
                                  placeholder="Please share your thoughts, suggestions, or concerns...">{{ old('comment') }}</textarea>
                        <small class="form-text text-muted">
                            <span id="char-count">0</span>/2000 characters
                        </small>
                        @error('comment')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Your feedback helps us improve the learning experience. It will be reviewed by the administration.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('student.feedback.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Feedback
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
    // Star rating
    let selectedRating = {{ old('rating', 0) }};
    
    $('.star-icon').on('click', function() {
        selectedRating = $(this).data('rating');
        $('#rating-input').val(selectedRating);
        updateStars();
    });
    
    $('.star-icon').on('mouseenter', function() {
        let hoverRating = $(this).data('rating');
        $('.star-icon').each(function() {
            if ($(this).data('rating') <= hoverRating) {
                $(this).removeClass('text-muted').addClass('text-warning');
            } else {
                $(this).removeClass('text-warning').addClass('text-muted');
            }
        });
    });
    
    $('#star-rating-display').on('mouseleave', function() {
        updateStars();
    });
    
    function updateStars() {
        $('.star-icon').each(function() {
            if ($(this).data('rating') <= selectedRating) {
                $(this).removeClass('text-muted').addClass('text-warning');
            } else {
                $(this).removeClass('text-warning').addClass('text-muted');
            }
        });
    }
    
    // Character counter
    $('#comment').on('input', function() {
        let count = $(this).val().length;
        $('#char-count').text(count);
        if (count > 2000) {
            $('#char-count').addClass('text-danger');
        } else {
            $('#char-count').removeClass('text-danger');
        }
    });
    
    // Initialize
    updateStars();
    $('#comment').trigger('input');
});
</script>
@endpush