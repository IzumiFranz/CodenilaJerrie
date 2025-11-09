@extends('layouts.instructor')
@section('title', 'Quiz Results')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-bar mr-2"></i>Quiz Results: {{ $quiz->title }}</h1>
    <div>
        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> View Quiz
        </a>
        <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Attempts</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attempts->total() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Passed</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ $attempts->where('percentage', '>=', $quiz->passing_score)->count() }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Average Score</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ $attempts->avg('percentage') ? number_format($attempts->avg('percentage'), 1) : '0' }}%
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pass Rate</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ $attempts->count() > 0 ? number_format(($attempts->where('percentage', '>=', $quiz->passing_score)->count() / $attempts->count()) * 100, 1) : '0' }}%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Student Attempts</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Student Number</th>
                        <th>Attempt</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Status</th>
                        <th>Time Spent</th>
                        <th>Completed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $attempt)
                    <tr>
                        <td>
                            <strong>{{ $attempt->student->full_name }}</strong>
                        </td>
                        <td>{{ $attempt->student->student_number }}</td>
                        <td>
                            <span class="badge badge-secondary">{{ $attempt->attempt_number }}/{{ $quiz->max_attempts }}</span>
                        </td>
                        <td>{{ $attempt->score }}/{{ $attempt->total_points }}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $attempt->percentage >= $quiz->passing_score ? 'success' : 'danger' }}" 
                                     style="width: {{ $attempt->percentage }}%">
                                    {{ number_format($attempt->percentage, 1) }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($attempt->percentage >= $quiz->passing_score)
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Passed
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i> Failed
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($attempt->started_at && $attempt->completed_at)
                                {{ $attempt->started_at->diffForHumans($attempt->completed_at, true) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y h:i A') : '-' }}</td>
                        <td>
                            <a href="{{ route('student.quiz-attempts.results', $attempt) }}" 
                               class="btn btn-sm btn-info" target="_blank" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            No attempts yet. Students haven't taken this quiz.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $attempts->links() }}</div>
    </div>
</div>

@endsection