@extends('layouts.student')

@section('title', 'Quiz Results')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('student.quizzes.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Quizzes
            </a>
            <h1 class="h3 mb-0">{{ $attempt->quiz->title }} - Results</h1>
        </div>
        <div>
            <a href="{{ route('student.quiz-attempts.review', $attempt->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-1"></i> Review Answers
            </a>
            <a href="{{ route('student.quiz-attempts.pdf', $attempt->id) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Result Summary -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 result-card {{ $attempt->score >= $attempt->quiz->passing_score ? 'border-success' : 'border-danger' }}">
                <div class="card-body text-center py-5">
                    <div class="result-icon mb-3 {{ $attempt->score >= $attempt->quiz->passing_score ? 'text-success' : 'text-danger' }}">
                        <i class="fas {{ $attempt->score >= $attempt->quiz->passing_score ? 'fa-check-circle' : 'fa-times-circle' }}" style="font-size:5rem"></i>
                    </div>
                    <h2 class="{{ $attempt->score >= $attempt->quiz->passing_score ? 'text-success' : 'text-danger' }} mb-2">
                        {{ $attempt->score >= $attempt->quiz->passing_score ? 'Congratulations! You Passed!' : 'You Did Not Pass' }}
                    </h2>
                    <div class="display-1 fw-bold my-4" style="color: {{ $attempt->score >= $attempt->quiz->passing_score ? '#28a745' : '#dc3545' }}">
                        {{ number_format($attempt->score, 1) }}%
                    </div>
                    <p class="lead text-muted mb-4">You needed {{ $attempt->quiz->passing_score }}% to pass</p>
                    @if($attempt->score < $attempt->quiz->passing_score && $attempt->quiz->allow_retake)
                        <a href="{{ route('student.quizzes.show', $attempt->quiz->id) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-redo me-2"></i>Try Again
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        @php
            $stats = [
                ['title'=>'Correct Answers','value'=>"$attempt->correct_answers/$attempt->total_questions",'icon'=>'fa-check-circle','color'=>'primary'],
                ['title'=>'Wrong Answers','value'=>$attempt->wrong_answers,'icon'=>'fa-times-circle','color'=>'danger'],
                ['title'=>'Time Taken','value'=>"$attempt->time_taken min",'icon'=>'fa-clock','color'=>'warning'],
                ['title'=>'Accuracy','value'=>number_format(($attempt->correct_answers/$attempt->total_questions)*100,1).'%','icon'=>'fa-bullseye','color'=>'info']
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm border-start border-4 border-{{ $stat['color'] }} h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ $stat['title'] }}</h6>
                            <h3 class="mb-0 text-{{ $stat['color'] }}">{{ $stat['value'] }}</h3>
                        </div>
                        <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }} fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <!-- Performance Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Performance Breakdown</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Progress Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-success"></i>Your Progress</h5>
                </div>
                <div class="card-body">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Question-by-Question Performance -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list-check me-2 text-info"></i>Question-by-Question Performance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="80">#</th>
                                    <th>Question</th>
                                    <th width="150">Your Answer</th>
                                    <th width="150">Correct Answer</th>
                                    <th width="100" class="text-center">Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempt->answers as $i => $answer)
                                <tr>
                                    <td><strong>#{{ $i + 1 }}</strong></td>
                                    <td>{{ Str::limit(strip_tags($answer->question->question_text), 60) }}</td>
                                    <td>
                                        @if($answer->selected_option)
                                            <span class="badge bg-light text-dark">{{ $answer->selected_option }}</span>
                                        @else
                                            <span class="text-muted">Not answered</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-success">{{ $answer->question->correct_answer }}</span></td>
                                    <td class="text-center">
                                        <i class="fas {{ $answer->is_correct ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} fs-4"></i>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="mb-3">What would you like to do next?</h5>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="{{ route('student.quiz-attempts.review', $attempt->id) }}" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i> Review Answers in Detail
                        </a>
                        @if($attempt->score < $attempt->quiz->passing_score && $attempt->quiz->allow_retake)
                        <a href="{{ route('student.quizzes.show', $attempt->quiz->id) }}" class="btn btn-primary">
                            <i class="fas fa-redo me-1"></i> Retake Quiz
                        </a>
                        @endif
                        <a href="{{ route('student.quizzes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-1"></i> Browse More Quizzes
                        </a>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-1"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
.result-card { border-top:5px solid; animation:slideDown 0.5s ease-out; }
@keyframes slideDown { from {opacity:0; transform:translateY(-20px);} to {opacity:1; transform:translateY(0);} }
.result-icon { animation:scaleIn 0.6s ease-out; }
@keyframes scaleIn { from {opacity:0; transform:scale(0.5);} to {opacity:1; transform:scale(1);} }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Performance Chart
new Chart(document.getElementById('performanceChart'), {
    type:'doughnut',
    data:{labels:['Correct','Wrong'], datasets:[{data:[{{ $attempt->correct_answers }},{{ $attempt->wrong_answers }}], backgroundColor:['#28a745','#dc3545'], borderWidth:2, borderColor:'#fff'}]},
    options:{responsive:true, maintainAspectRatio:true, plugins:{legend:{position:'bottom'}}}
});

// Progress Chart
new Chart(document.getElementById('progressChart'), {
    type:'line',
    data:{
        labels:{!! json_encode($previousAttempts->pluck('created_at')->map(fn($d)=>$d->format('M d'))) !!},
        datasets:[
            {label:'Score %', data:{!! json_encode($previousAttempts->pluck('score')) !!}, borderColor:'#007bff', backgroundColor:'rgba(0,123,255,0.1)', tension:0.4, fill:true, pointRadius:5, pointHoverRadius:7},
            {label:'Passing Score', data:Array({{ $previousAttempts->count() }}).fill({{ $attempt->quiz->passing_score }}), borderColor:'#28a745', borderDash:[5,5], fill:false, pointRadius:0}
        ]
    },
    options:{responsive:true, maintainAspectRatio:true, scales:{y:{beginAtZero:true,max:100,ticks:{callback:v=>v+'%'}}}, plugins:{legend:{position:'bottom'}}}
});
</script>
@endpush
@endsection
