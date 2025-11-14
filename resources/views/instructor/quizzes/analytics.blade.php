@extends('layouts.instructor')
@section('title', 'Quiz Analytics')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-line mr-2"></i>Analytics: {{ $quiz->title }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye mr-1"></i> View Quiz
        </a>
        <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>

<!-- Statistics Summary -->
<div class="row mb-4">
    @php
        $attempts = $quiz->attempts()->where('status', 'completed')->get();
        $totalAttempts = $attempts->count();
        $averageScore = $totalAttempts > 0 ? $attempts->avg('percentage') : 0;
        $passRate = $totalAttempts > 0 ? ($attempts->where('percentage', '>=', $quiz->passing_score)->count() / $totalAttempts * 100) : 0;
        $averageTime = $totalAttempts > 0 ? $attempts->avg(function($a) {
            return $a->started_at && $a->completed_at ? $a->started_at->diffInMinutes($a->completed_at) : 0;
        }) : 0;
    @endphp

    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Attempts</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAttempts }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Average Score</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageScore, 1) }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pass Rate</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($passRate, 1) }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg Time</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageTime, 1) }} min</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Score Distribution -->
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Score Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="scoreDistributionChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Pass/Fail Rate -->
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Pass vs Fail</h6>
            </div>
            <div class="card-body">
                <canvas id="passFailChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Question Performance -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Question Performance Analysis</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Type</th>
                        <th>Correct Answers</th>
                        <th>Success Rate</th>
                        <th>Difficulty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quiz->questions->sortBy('pivot.order') as $question)
                    @php
                        // Calculate question statistics
                        $questionAnswers = \App\Models\QuizAnswer::where('question_id', $question->id)
                            ->whereHas('attempt', function($q) use ($quiz) {
                                $q->where('quiz_id', $quiz->id)->where('status', 'completed');
                            })->get();
                        
                        $totalAnswers = $questionAnswers->count();
                        $correctAnswers = $questionAnswers->where('is_correct', true)->count();
                        $successRate = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers * 100) : 0;
                        
                        // Determine if question needs review
                        $needsReview = $successRate < 30 || $successRate > 95;
                    @endphp
                    <tr class="{{ $needsReview ? 'table-warning' : '' }}">
                        <td>
                            <strong>Q{{ $question->pivot->order }}:</strong>
                            {{ Str::limit($question->question_text, 60) }}
                        </td>
                        <td>
                            <span class="badge badge-info">
                                {{ str_replace('_', ' ', ucfirst($question->type)) }}
                            </span>
                        </td>
                        <td>{{ $correctAnswers }}/{{ $totalAnswers }}</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $successRate >= 70 ? 'success' : ($successRate >= 50 ? 'warning' : 'danger') }}" 
                                     style="width: {{ $successRate }}%">
                                    {{ number_format($successRate, 1) }}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                        </td>
                        <td>
                            @if($successRate < 30)
                                <span class="badge badge-danger">Too Hard</span>
                            @elseif($successRate > 95)
                                <span class="badge badge-info">Too Easy</span>
                            @else
                                <span class="badge badge-success">Good</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // SCORE DISTRIBUTION CHART
    @php
        // Group scores into ranges
        $scoreRanges = [
            '0-20' => 0, '21-40' => 0, '41-60' => 0, '61-80' => 0, '81-100' => 0
        ];
        
        foreach($attempts as $attempt) {
            $score = $attempt->percentage;
            if ($score <= 20) $scoreRanges['0-20']++;
            elseif ($score <= 40) $scoreRanges['21-40']++;
            elseif ($score <= 60) $scoreRanges['41-60']++;
            elseif ($score <= 80) $scoreRanges['61-80']++;
            else $scoreRanges['81-100']++;
        }
    @endphp

    const scoreDistributionCtx = document.getElementById('scoreDistributionChart').getContext('2d');
    new Chart(scoreDistributionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($scoreRanges)) !!},
            datasets: [{
                label: 'Number of Students',
                data: {!! json_encode(array_values($scoreRanges)) !!},
                backgroundColor: [
                    'rgba(220, 53, 69, 0.7)',   // Red for 0-20
                    'rgba(255, 193, 7, 0.7)',   // Orange for 21-40
                    'rgba(255, 193, 7, 0.7)',   // Yellow for 41-60
                    'rgba(23, 162, 184, 0.7)',  // Cyan for 61-80
                    'rgba(40, 167, 69, 0.7)'    // Green for 81-100
                ],
                borderColor: [
                    'rgba(220, 53, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(40, 167, 69, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'How many students scored in each range'
                }
            }
        }
    });

    // PASS/FAIL PIE CHART
    @php
        $passedCount = $attempts->where('percentage', '>=', $quiz->passing_score)->count();
        $failedCount = $totalAttempts - $passedCount;
    @endphp

    const passFailCtx = document.getElementById('passFailChart').getContext('2d');
    new Chart(passFailCtx, {
        type: 'doughnut',
        data: {
            labels: ['Passed', 'Failed'],
            datasets: [{
                data: [{{ $passedCount }}, {{ $failedCount }}],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',   // Green
                    'rgba(220, 53, 69, 0.8)'    // Red
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Pass Rate: {{ number_format($passRate, 1) }}%'
                }
            }
        }
    });
});
</script>
@endpush