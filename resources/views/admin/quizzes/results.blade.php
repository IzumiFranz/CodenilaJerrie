@extends('layouts.admin')

@section('title', 'Quiz Results')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-bar mr-2"></i>Quiz Results: {{ $quiz->title }}</h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Print
        </button>
        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Quiz
        </a>
    </div>
</div>
<!-- Overall Statistics -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Attempts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attempts->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Average Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $attempts->count() > 0 ? number_format($attempts->avg('percentage'), 1) : 0 }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Highest Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $attempts->count() > 0 ? number_format($attempts->max('percentage'), 1) : 0 }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pass Rate</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @if($attempts->count() > 0)
                                {{ number_format(($attempts->where('percentage', '>=', $quiz->passing_score)->count() / $attempts->count()) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Student Results</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Student Number</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Status</th>
                        <th>Time Taken</th>
                        <th>Completed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $attempt)
                        <tr>
                            <td>
                                <strong>{{ $attempt->student->first_name }} {{ $attempt->student->last_name }}</strong>
                            </td>
                            <td>{{ $attempt->student->student_number }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $attempt->score }}/{{ $quiz->questions->sum('points') }}</span>
                            </td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar 
                                        @if($attempt->percentage >= $quiz->passing_score) bg-success 
                                        @elseif($attempt->percentage >= 50) bg-warning 
                                        @else bg-danger 
                                        @endif" 
                                        role="progressbar" 
                                        style="width: {{ $attempt->percentage }}%"
                                        aria-valuenow="{{ $attempt->percentage }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                        {{ number_format($attempt->percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($attempt->percentage >= $quiz->passing_score)
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Passed</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Failed</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->started_at && $attempt->completed_at)
                                    @php
                                        $duration = \Carbon\Carbon::parse($attempt->started_at)->diffInMinutes($attempt->completed_at);
                                    @endphp
                                    <i class="fas fa-clock"></i> {{ $duration }} min
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $attempt->completed_at ? \Carbon\Carbon::parse($attempt->completed_at)->format('M d, Y H:i') : 'Not completed' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailsModal{{ $attempt->id }}">
                                    <i class="fas fa-eye"></i> Details
                                </button>
                            </td>
                        </tr>

                        <!-- Details Modal -->
                        <div class="modal fade" id="detailsModal{{ $attempt->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title">
                                            Quiz Attempt Details - {{ $attempt->student->first_name }} {{ $attempt->student->last_name }}
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Score:</strong> {{ $attempt->score }}/{{ $quiz->questions->sum('points') }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Percentage:</strong> {{ number_format($attempt->percentage, 1) }}%
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Started:</strong> {{ \Carbon\Carbon::parse($attempt->started_at)->format('M d, Y H:i') }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Completed:</strong> {{ \Carbon\Carbon::parse($attempt->completed_at)->format('M d, Y H:i') }}
                                            </div>
                                        </div>

                                        <h6 class="border-bottom pb-2 mb-3">Answers:</h6>
                                        @foreach($attempt->answers as $index => $answer)
                                            <div class="border-bottom pb-2 mb-2">
                                                <p class="mb-1">
                                                    <strong>Q{{ $index + 1 }}:</strong> {{ $answer->question->question_text }}
                                                </p>
                                                <p class="mb-1">
                                                    <strong>Student Answer:</strong>
                                                    @if($answer->choice)
                                                        {{ $answer->choice->choice_text }}
                                                    @else
                                                        {{ $answer->answer_text }}
                                                    @endif
                                                    @if($answer->is_correct)
                                                        <span class="badge badge-success"><i class="fas fa-check"></i> Correct</span>
                                                    @else
                                                        <span class="badge badge-danger"><i class="fas fa-times"></i> Incorrect</span>
                                                    @endif
                                                </p>
                                                <p class="mb-0">
                                                    <strong>Points:</strong> {{ $answer->points_earned }}/{{ $answer->question->points }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No completed attempts yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $attempts->links() }}
        </div>
    </div>
</div>
@endsection