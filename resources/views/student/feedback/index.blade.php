@extends('layouts.student')

@section('title', 'My Feedback')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-comment-dots mr-2"></i>My Feedback</h1>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="feedbackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="list-tab" data-bs-toggle="tab" data-bs-target="#listTab" type="button" role="tab">View Feedback</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="submit-tab" data-bs-toggle="tab" data-bs-target="#submitTab" type="button" role="tab">Submit Feedback</button>
        </li>
    </ul>

    <div class="tab-content" id="feedbackTabsContent">
        <!-- Feedback List -->
        <div class="tab-pane fade show active" id="listTab" role="tabpanel">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-primary border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Feedback</h6>
                            <h3 class="mb-0">{{ $totalFeedback }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-warning border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h3 class="mb-0">{{ $pendingFeedback }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-success border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Responded</h6>
                            <h3 class="mb-0">{{ $respondedFeedback }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-info border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Avg. Rating</h6>
                            <h3 class="mb-0">{{ number_format($averageRating, 1) }} <small class="text-muted">/5</small></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('student.feedback.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="type" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Types</option>
                                    <option value="quiz" {{ request('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="lesson" {{ request('type') == 'lesson' ? 'selected' : '' }}>Lesson</option>
                                    <option value="instructor" {{ request('type') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                                    <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="status" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="responded" {{ request('status') == 'responded' ? 'selected' : '' }}>Responded</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="rating" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Ratings</option>
                                    @for($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Feedback Cards -->
            <div class="row">
                @forelse($feedbacks as $feedback)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100 hover-card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <span class="badge bg-{{ $feedback->type === 'quiz' ? 'primary' : ($feedback->type === 'lesson' ? 'info' : ($feedback->type === 'instructor' ? 'success' : 'secondary')) }}">
                                <i class="fas fa-{{ $feedback->type === 'quiz' ? 'clipboard-list' : ($feedback->type === 'lesson' ? 'book' : ($feedback->type === 'instructor' ? 'chalkboard-teacher' : 'comment')) }} me-1"></i>
                                {{ ucfirst($feedback->type) }}
                            </span>
                            <span class="badge bg-{{ $feedback->status === 'pending' ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $feedback->status === 'pending' ? 'clock' : 'check-circle' }} me-1"></i>
                                {{ ucfirst($feedback->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if($feedback->rating)
                            <div class="mb-2">
                                @for($i=1;$i<=5;$i++)
                                    <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <span class="ms-2 text-muted small">({{ $feedback->rating }}/5)</span>
                            </div>
                            @endif
                            <h5 class="card-title mb-1">{{ $feedback->subject }}</h5>
                            @if($feedback->feedbackable)
                            <p class="text-muted small mb-2"><i class="fas fa-link me-1"></i> About: {{ $feedback->feedbackable->title ?? $feedback->feedbackable->name }}</p>
                            @endif
                            <p class="text-muted mb-2">{{ Str::limit($feedback->message, 100) }}</p>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <a href="{{ route('student.feedback.show', $feedback->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-comments text-muted fs-1 mb-3"></i>
                    <h4>No Feedback Found</h4>
                    <p class="text-muted">You haven't submitted any feedback yet.</p>
                </div>
                @endforelse
            </div>
            <div class="d-flex justify-content-center mt-4">{{ $feedbacks->links() }}</div>
        </div>

        <!-- Submit Feedback Tab -->
        <div class="tab-pane fade" id="submitTab" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('student.feedback.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="feedbackType" class="form-label">Feedback Type</label>
                            <select name="feedbackable_type" class="form-select @error('feedbackable_type') is-invalid @enderror" id="feedbackType">
                                <option value="">General</option>
                                <option value="App\Models\Quiz" {{ old('feedbackable_type')=='App\Models\Quiz'?'selected':'' }}>Quiz</option>
                                <option value="App\Models\Lesson" {{ old('feedbackable_type')=='App\Models\Lesson'?'selected':'' }}>Lesson</option>
                            </select>
                            @error('feedbackable_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating (Optional)</label>
                            <input type="number" min="1" max="5" class="form-control @error('rating') is-invalid @enderror" name="rating" id="rating" value="{{ old('rating') }}">
                            @error('rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Your Feedback</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" name="comment" id="comment" rows="4">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>                            
<form method="POST" action="{{ route('student.feedback.store') }}" id="feedbackForm">
                                @csrf

                                <!-- Feedback Type -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Feedback Type <span class="text-danger">*</span></label>
                                    <div class="row g-2">
                                        @foreach(['quiz'=>'primary','lesson'=>'info','instructor'=>'success','general'=>'secondary'] as $type => $color)
                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check" name="type" id="type_{{ $type }}" value="{{ $type }}" {{ old('type') == $type ? 'checked' : '' }} required>
                                                <label class="btn btn-outline-{{ $color }} w-100" for="type_{{ $type }}">
                                                    <i class="fas fa-{{ $type === 'quiz' ? 'clipboard-list' : ($type === 'lesson' ? 'book' : ($type === 'instructor' ? 'chalkboard-teacher' : 'comment')) }} d-block fs-3 mb-2"></i>
                                                    {{ ucfirst($type) }} Feedback
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>

                                <!-- Related Items -->
                                <div id="relatedItemSection" style="display: none;">
                                    <div class="mb-3" id="quizSelection" style="display: none;">
                                        <label for="quiz_id" class="form-label fw-bold">Select Quiz</label>
                                        <select name="quiz_id" id="quiz_id" class="form-select">
                                            <option value="">-- Choose a quiz --</option>
                                            @foreach($quizzes as $quiz)
                                                <option value="{{ $quiz->id }}" {{ old('quiz_id') == $quiz->id ? 'selected' : '' }}>{{ $quiz->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('quiz_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="mb-3" id="lessonSelection" style="display: none;">
                                        <label for="lesson_id" class="form-label fw-bold">Select Lesson</label>
                                        <select name="lesson_id" id="lesson_id" class="form-select">
                                            <option value="">-- Choose a lesson --</option>
                                            @foreach($lessons as $lesson)
                                                <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('lesson_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="mb-3" id="instructorSelection" style="display: none;">
                                        <label for="instructor_id" class="form-label fw-bold">Select Instructor</label>
                                        <select name="instructor_id" id="instructor_id" class="form-select">
                                            <option value="">-- Choose an instructor --</option>
                                            @foreach($instructors as $instructor)
                                                <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('instructor_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <!-- Rating -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rating <span class="text-danger">*</span></label>
                                    <input type="hidden" name="rating" id="rating" value="{{ old('rating', 5) }}" required>
                                    <div class="star-rating mb-1">
                                        @for($i=1;$i<=5;$i++)
                                            <i class="fas fa-star star" data-value="{{ $i }}" style="font-size:2rem; cursor:pointer;"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted">Click stars to rate (1-5)</small>
                                    @error('rating')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>

                                <!-- Subject -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="Brief summary" required>
                                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <!-- Message -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Feedback <span class="text-danger">*</span></label>
                                    <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" rows="6" placeholder="Enter your feedback..." required>{{ old('message') }}</textarea>
                                    <small class="text-muted">Minimum 20 characters</small>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <!-- Anonymous -->
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="is_anonymous" class="form-check-input" id="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_anonymous">Submit anonymously</label>
                                </div>

                                <!-- Buttons -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane me-2"></i>Submit Feedback</button>
                                    <a href="{{ route('student.feedback.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-2"></i>Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-lightbulb me-2 text-warning"></i>Guidelines</h5>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li>Be specific and constructive</li>
                                <li>Focus on your learning experience</li>
                                <li>Provide examples if possible</li>
                                <li>Be respectful</li>
                                <li>Suggest improvements</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>How We Use Your Feedback</h5>
                        </div>
                        <div class="card-body">
                            <ul class="small mb-0">
                                <li>Improve course content</li>
                                <li>Enhance teaching methods</li>
                                <li>Fix technical issues</li>
                                <li>Better learning experience</li>
                                <li>Recognize excellent instructors</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card shadow-sm border-success">
                        <div class="card-body">
                            <h6 class="text-success"><i class="fas fa-shield-alt me-2"></i>Privacy Note</h6>
                            <p class="small text-muted mb-0">Your feedback is confidential. Anonymous submissions hide your identity.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.hover-card { transition: transform 0.2s, box-shadow 0.2s; }
.hover-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
.star-rating .star { color: #ddd; transition: color 0.2s; }
.star-rating .star.active, .star-rating .star:hover { color: #ffc107; }
.btn-check:checked + .btn-outline-primary,
.btn-check:checked + .btn-outline-info,
.btn-check:checked + .btn-outline-success,
.btn-check:checked + .btn-outline-secondary {
    background-color: currentColor;
    color: #fff;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Open submit tab if needed
    @if($errors->any())
        var submitTab = new bootstrap.Tab(document.querySelector('#submit-tab'));
        submitTab.show();
    @endif
    @if($openSubmitTab)
        var submitTab = new bootstrap.Tab(document.querySelector('#submit-tab'));
        submitTab.show();
    @endif

    // Feedback type selection show/hide
    const types = ['quiz', 'lesson', 'instructor'];
    const relatedSection = document.getElementById('relatedItemSection');

    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            relatedSection.style.display = types.includes(this.value) ? 'block' : 'none';
            types.forEach(t => document.getElementById(t+'Selection').style.display = t === this.value ? 'block' : 'none');
        });
        // Trigger initial display on load
        if(radio.checked) radio.dispatchEvent(new Event('change'));
    });

    // Star rating
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('rating');
    stars.forEach(star => {
        star.addEventListener('click', function(){
            ratingInput.value = this.dataset.value;
            stars.forEach(s => s.classList.toggle('active', s.dataset.value <= this.dataset.value));
        });
        // Highlight old value
        if(star.dataset.value <= ratingInput.value) star.classList.add('active');
    });
});
</script>
@endpush
@endsection
