<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz Results - {{ $attempt->quiz->title }}</title>
<style>
/* Reset & base */
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; }
h1,h2,h3,h4,h5,h6 { margin-bottom: 10px; }

/* Header */
.header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #007bff; }
.header h1 { color: #007bff; font-size: 28px; }
.header .subtitle { color: #6c757d; font-size: 14px; }

/* Result box */
.result-box {
    background: {{ $attempt->isPassed() ? '#d4edda' : '#f8d7da' }};
    border: 2px solid {{ $attempt->isPassed() ? '#28a745' : '#dc3545' }};
    border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 30px;
}
.result-box h2 { color: {{ $attempt->isPassed() ? '#28a745' : '#dc3545' }}; font-size: 24px; margin-bottom: 10px; }
.result-box .score { font-size: 48px; font-weight: bold; color: {{ $attempt->isPassed() ? '#28a745' : '#dc3545' }}; }

/* Stats grid */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
.stat-card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; text-align: center; }
.stat-card h3 { font-size: 28px; margin: 10px 0 5px; }
.stat-card p { color: #6c757d; font-size: 12px; text-transform: uppercase; }

/* Info section */
.info-section { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 30px; }
.info-section h3 { color: #007bff; margin-bottom: 10px; }
.info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dee2e6; }
.info-row:last-child { border-bottom: none; }
.info-label { font-weight: bold; color: #495057; }

/* Question review */
.question-container { margin-bottom: 25px; page-break-inside: avoid; }
.question-header { color: white; padding: 10px 15px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
.question-header.correct { background: #28a745; }
.question-header.wrong { background: #dc3545; }
.question-body { border: 1px solid #dee2e6; border-top: none; padding: 15px; border-radius: 0 0 8px 8px; }
.question-text { font-weight: bold; margin-bottom: 15px; font-size: 14px; }
.options { margin-top: 10px; }
.option { padding: 10px; margin-bottom: 8px; border: 1px solid #dee2e6; border-radius: 5px; display: flex; align-items: center; }
.option.correct { background: #d4edda; border-color: #28a745; }
.option.wrong { background: #f8d7da; border-color: #dc3545; }
.option-label { display: inline-block; width: 30px; height: 30px; border-radius: 50%; text-align: center; line-height: 30px; margin-right: 10px; font-weight: bold; }
.option.correct .option-label { background: #28a745; color: white; }
.option.wrong .option-label { background: #dc3545; color: white; }
.option .option-label { background: #6c757d; color: white; }
.explanation { background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; padding: 10px; margin-top: 15px; }
.explanation strong { color: #0c5460; }

/* Footer */
.footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #dee2e6; text-align: center; color: #6c757d; font-size: 12px; }

/* Print */
@media print {
    body { padding: 15px; }
    .question-container { page-break-inside: avoid; }
    .header, .footer { page-break-after: avoid; }
}
</style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>{{ $attempt->quiz->title }}</h1>
    <div class="subtitle">Quiz Results Report</div>
    <div class="subtitle">Generated on {{ now()->format('F d, Y H:i') }}</div>
</div>

<!-- Result Box -->
<div class="result-box">
    <h2>{{ $attempt->isPassed() ? '✓ PASSED' : '✗ NOT PASSED' }}</h2>
    <div class="score">{{ number_format($attempt->score, 1) }}%</div>
    <p>Score: {{ $attempt->score }}/{{ $attempt->total_points }} points</p>
    <p>Passing Score Required: {{ $attempt->quiz->passing_score }}%</p>
    <p>Time Spent: {{ $attempt->getTimeSpentFormatted() }}</p>
    <p>Attempt: {{ $attempt->attempt_number }}/{{ $attempt->quiz->max_attempts }}</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <p>Total Questions</p>
        <h3>{{ $attempt->total_questions }}</h3>
    </div>
    <div class="stat-card">
        <p>Correct Answers</p>
        <h3 style="color: #28a745;">{{ $attempt->correct_answers }}</h3>
    </div>
    <div class="stat-card">
        <p>Wrong Answers</p>
        <h3 style="color: #dc3545;">{{ $attempt->wrong_answers }}</h3>
    </div>
    <div class="stat-card">
        <p>Time Taken</p>
        <h3 style="color: #ffc107;">{{ $attempt->time_taken }}</h3>
        <p style="font-size: 10px;">minutes</p>
    </div>
</div>

<!-- Quiz Info -->
<div class="info-section">
    <h3>Quiz Information</h3>
    <div class="info-row"><span class="info-label">Student Name:</span><span>{{ $attempt->student->full_name }}</span></div>
    <div class="info-row"><span class="info-label">Student Number:</span><span>{{ $attempt->student->student_number }}</span></div>
    <div class="info-row"><span class="info-label">Instructor:</span><span>{{ $attempt->quiz->instructor->full_name }}</span></div>
    <div class="info-row"><span class="info-label">Quiz Duration:</span><span>{{ $attempt->quiz->duration }} minutes</span></div>
    <div class="info-row"><span class="info-label">Attempt Date:</span><span>{{ $attempt->created_at->format('F d, Y H:i') }}</span></div>
    <div class="info-row"><span class="info-label">Completion Date:</span><span>{{ $attempt->completed_at ? $attempt->completed_at->format('F d, Y H:i') : 'N/A' }}</span></div>
</div>

<!-- Detailed Answer Review -->
<h2 style="color: #007bff; margin-bottom: 20px;">Detailed Answer Review</h2>

@foreach($attempt->answers as $index => $answer)
<div class="question-container">
    <div class="question-header {{ $answer->is_correct ? 'correct' : 'wrong' }}">
        <span><strong>Question {{ $index + 1 }}</strong> of {{ $attempt->total_questions }}</span>
        <span>{{ $answer->is_correct ? '✓ Correct' : '✗ Wrong' }}</span>
    </div>
    <div class="question-body">
        <div class="question-text">{{ strip_tags($answer->question->question_text) }}</div>

        <div class="options">
            @php
                $options = ['A','B','C','D'];
                $answerOptions = [
                    'A' => $answer->question->option_a,
                    'B' => $answer->question->option_b,
                    'C' => $answer->question->option_c,
                    'D' => $answer->question->option_d,
                ];
            @endphp
            @foreach($options as $option)
                @if(!empty($answerOptions[$option]))
                <div class="option {{ $option === $answer->question->correct_answer ? 'correct' : ($option === $answer->selected_option ? 'wrong' : '') }}">
                    <span class="option-label">{{ $option }}</span>
                    <span>{{ $answerOptions[$option] }}
                        @if($option === $answer->question->correct_answer) <strong>(Correct Answer)</strong> 
                        @elseif($option === $answer->selected_option) <strong>(Your Answer)</strong> @endif
                    </span>
                </div>
                @endif
            @endforeach
        </div>

        @if($answer->question->explanation)
        <div class="explanation"><strong>Explanation:</strong> {{ $answer->question->explanation }}</div>
        @endif
    </div>
</div>
@endforeach

<!-- Footer -->
<div class="footer">
    <p>This is an official quiz results report generated by the Quiz LMS System</p>
    <p>© {{ now()->year }} Quiz LMS. All rights reserved.</p>
    <p>For questions, contact instructor: {{ $attempt->quiz->instructor->email }}</p>
</div>
</body>
</html>
