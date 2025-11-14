@extends('layouts.student')

@section('title', 'Available Quizzes')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-clipboard-list mr-2"></i>Available Quizzes</h1>
            <p class="text-muted mb-0">Test your knowledge and track your progress</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('student.quizzes.index') }}" id="filterForm" class="mb-0">
                <div class="form-row">
                    <div class="col-md-4 mb-2">
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                            <input type="text" name="search" class="form-control" placeholder="Search quizzes..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2 mb-2">
                        <select name="subject_id" id="subject_id" class="form-control">
                            <option value="">All Subjects</option>
                            @foreach($enrolledSubjects ?? $subjects ?? [] as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_code ?? $subject->subject_name ?? $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <select name="difficulty" id="difficulty" class="form-control">
                            <option value="">All Difficulties</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary btn-block mr-2">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('student.quizzes.index') }}" class="btn btn-light btn-block" title="Clear filters">
                                <i class="fas fa-eraser"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-2">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <small class="text-muted">Total Quizzes</small>
                        <h4 class="mb-0">{{ $totalQuizzes ?? $quizzes->total() ?? 0 }}</h4>
                    </div>
                    <div class="text-primary display-4"><i class="fas fa-clipboard-list"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card shadow-sm border-left-success">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <small class="text-muted">Completed</small>
                        <h4 class="mb-0">{{ $completedQuizzes ?? 0 }}</h4>
                    </div>
                    <div class="text-success display-4"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card shadow-sm border-left-warning">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <small class="text-muted">In Progress</small>
                        <h4 class="mb-0">{{ $inProgressQuizzes ?? 0 }}</h4>
                    </div>
                    <div class="text-warning display-4"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <div class="card shadow-sm border-left-info">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <small class="text-muted">Average Score</small>
                        <h4 class="mb-0">{{ isset($averageScore) ? number_format($averageScore,1).'%' : '—' }}</h4>
                    </div>
                    <div class="text-info display-4"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quizzes Grid -->
    @if($quizzes->count() > 0)
    <div class="row">
        @foreach($quizzes as $quiz)
        @php
            // Resolve most recent attempt by current user
            $userAttempt = $quiz->attempts->where('user_id', auth()->id())->sortByDesc('created_at')->first();
            $difficultyClass = $quiz->difficulty === 'easy' ? 'success' : ($quiz->difficulty === 'medium' ? 'warning' : 'danger');
            $statusBadge = $quiz->isAvailable() ? ['label'=>'Available','class'=>'success'] : ($quiz->available_from && $quiz->available_from->isFuture() ? ['label'=>'Upcoming','class'=>'warning'] : ['label'=>'Expired','class'=>'danger']);
        @endphp

        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card quiz-card h-100 shadow-sm {{ !$quiz->isAvailable() ? 'border-secondary' : 'border-left-primary' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="small">
                        <span class="badge badge-{{ $difficultyClass }}">{{ ucfirst($quiz->difficulty ?? 'Medium') }}</span>
                        <strong class="ml-2">{{ Str::limit($quiz->title, 40) }}</strong>
                    </span>

                    <span class="small">
                        <span class="badge badge-{{ $statusBadge['class'] }}">
                            <i class="fas fa-{{ $statusBadge['class'] === 'success' ? 'check-circle' : ($statusBadge['class'] === 'warning' ? 'clock' : 'times-circle') }} mr-1"></i>
                            {{ $statusBadge['label'] }}
                        </span>
                    </span>
                </div>

                <div class="card-body">
                    @if($quiz->description)
                        <p class="text-muted small mb-2">{{ Str::limit($quiz->description, 110) }}</p>
                    @endif

                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <i class="fas fa-question-circle text-primary"></i>
                            <p class="mb-0 small"><strong>{{ $quiz->questions_count }}</strong></p>
                            <p class="mb-0 text-muted" style="font-size:0.7rem;">Questions</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-clock text-warning"></i>
                            <p class="mb-0 small"><strong>{{ $quiz->duration ?? ($quiz->time_limit ?? '—') }}</strong></p>
                            <p class="mb-0 text-muted" style="font-size:0.7rem;">Minutes</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-star text-success"></i>
                            <p class="mb-0 small"><strong>{{ $quiz->passing_score }}%</strong></p>
                            <p class="mb-0 text-muted" style="font-size:0.7rem;">To Pass</p>
                        </div>
                    </div>

                    {{-- student attempt info --}}
                    @if($userAttempt)
                        @if($userAttempt->status === 'completed')
                        <div class="alert alert-success py-2 mb-2 small">
                            <i class="fas fa-trophy mr-1"></i>
                            Score: <strong>{{ number_format($userAttempt->score,1) }}%</strong>
                            @if($userAttempt->score >= $quiz->passing_score)
                                <span class="text-success ml-2">(Passed)</span>
                            @else
                                <span class="text-danger ml-2">(Failed)</span>
                            @endif
                        </div>
                        @else
                        <div class="alert alert-warning py-2 mb-2 small">
                            <i class="fas fa-info-circle mr-1"></i>
                            Progress: <strong>{{ $userAttempt->answers_count ?? 0 }}/{{ $quiz->questions_count }}</strong> answered
                        </div>
                        @endif
                    @else
                        <p class="text-muted small mb-2"><i class="fas fa-info-circle mr-1"></i>You haven't attempted this quiz yet.</p>
                    @endif

                    {{-- availability windows --}}
                    @if($quiz->available_from || $quiz->available_until)
                    <hr class="my-2">
                    <div class="small text-muted">
                        @if($quiz->available_from)
                        <div><i class="far fa-calendar-plus mr-1"></i>From: {{ $quiz->available_from->format('M d, Y h:i A') }}</div>
                        @endif
                        @if($quiz->available_until)
                        <div><i class="far fa-calendar-times mr-1"></i>Until: {{ $quiz->available_until->format('M d, Y h:i A') }}
                            @if($quiz->available_until->isFuture() && $quiz->isAvailable())
                                <span class="text-danger">({{ $quiz->available_until->diffForHumans() }})</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="card-footer bg-white">
                    @if($userAttempt)
                        @if($userAttempt->status === 'completed')
                        <div class="d-grid">
                            <a href="{{ route('student.quiz-attempts.results', $userAttempt->id) }}" class="btn btn-outline-primary btn-sm mb-2">
                                <i class="fas fa-chart-bar mr-1"></i>View Results
                            </a>
                            @if($quiz->allow_retake)
                            <a href="{{ route('student.quizzes.show', $quiz->id) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-redo mr-1"></i>Retake Quiz
                            </a>
                            @endif
                        </div>
                        @else
                        <div class="d-grid">
                            <a href="{{ route('student.quiz-attempts.take', $userAttempt->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-play mr-1"></i>Continue Quiz
                            </a>
                        </div>
                        @endif
                    @else
                    <div class="d-grid">
                        <a href="{{ route('student.quizzes.show', $quiz->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-play mr-1"></i>Start Quiz
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if(method_exists($quizzes, 'links'))
    <div class="d-flex justify-content-center mt-4">
        {{ $quizzes->links() }}
    </div>
    @endif

    @else
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-clipboard-list text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Quizzes Found</h4>
            <p class="text-muted">There are no quizzes matching your criteria.</p>
            @if(request()->hasAny(['search', 'subject_id', 'difficulty', 'status']))
            <a href="{{ route('student.quizzes.index') }}" class="btn btn-primary">Clear Filters</a>
            @endif
        </div>
    </div>
    @endif

    <!-- Legend -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3"><i class="fas fa-info-circle mr-2"></i>Status Legend</h6>
                    <div class="row">
                        <div class="col-md-4 mb-2"><span class="badge badge-success mr-2">Available</span>Quiz is currently available</div>
                        <div class="col-md-4 mb-2"><span class="badge badge-warning mr-2">Upcoming</span>Quiz will open in the future</div>
                        <div class="col-md-4 mb-2"><span class="badge badge-danger mr-2">Expired</span>Quiz deadline has passed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .quiz-card { transition: transform 0.18s, box-shadow 0.18s; }
    .quiz-card:hover { transform: translateY(-6px); box-shadow: 0 0.8rem 1.4rem rgba(0,0,0,0.12) !important; }
    .border-left-primary { border-left: .35rem solid #4e73df !important; }
    .border-left-success { border-left: .35rem solid #1cc88a !important; }
    .border-left-warning { border-left: .35rem solid #f6c23e !important; }
    @media print { .btn, .input-group, .card-header, .quiz-card:hover { display: none !important; } }
</style>
@endpush

@push('scripts')
<script>
$(function(){
    // auto-submit when changing filters (except search)
    $('#subject_id, #difficulty, #status').change(function(){ $('#filterForm').submit(); });

    // tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // small hover shadow toggle for accessibility (keyboard)
    $('.quiz-card').on('focusin', function(){ $(this).addClass('shadow-lg'); }).on('focusout', function(){ $(this).removeClass('shadow-lg'); });
});
</script>
@endpush
