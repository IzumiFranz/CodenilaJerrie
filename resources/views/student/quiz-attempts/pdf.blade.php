<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quiz Result - {{ $attempt->quiz->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #36b9cc;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #36b9cc;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .score-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .score-card .score {
            font-size: 48px;
            font-weight: bold;
            color: {{ $attempt->isPassed() ? '#1cc88a' : '#e74a3b' }};
        }
        .score-card .status {
            font-size: 24px;
            color: {{ $attempt->isPassed() ? '#1cc88a' : '#e74a3b' }};
            margin: 10px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table th,
        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .details-table th {
            background-color: #36b9cc;
            color: white;
            font-weight: bold;
        }
        .details-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .question-section {
            margin: 30px 0;
            page-break-inside: avoid;
        }
        .question-header {
            background: #36b9cc;
            color: white;
            padding: 10px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .question-body {
            border-left: 4px solid #36b9cc;
            padding-left: 15px;
            margin: 10px 0;
        }
        .choice {
            padding: 8px;
            margin: 5px 0;
            border-radius: 3px;
        }
        .choice.correct {
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .choice.incorrect {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .choice.neutral {
            background: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @page {
            margin: 2cm;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Quiz Result Certificate</h1>
        <p><strong>{{ $attempt->quiz->title }}</strong></p>
        <p>{{ $attempt->quiz->subject->subject_name }} ({{ $attempt->quiz->subject->subject_code }})</p>
        <p>{{ $attempt->quiz->subject->course->course_name }}</p>
    </div>

    <!-- Student Info -->
    <table class="details-table">
        <tr>
            <th>Student Name</th>
            <td>{{ $attempt->student->full_name }}</td>
        </tr>
        <tr>
            <th>Student Number</th>
            <td>{{ $attempt->student->student_number }}</td>
        </tr>
        <tr>
            <th>Instructor</th>
            <td>{{ $attempt->quiz->instructor->full_name }}</td>
        </tr>
        <tr>
            <th>Date Completed</th>
            <td>{{ $attempt->completed_at->format('F d, Y h:i A') }}</td>
        </tr>
    </table>

    <!-- Score Card -->
    <div class="score-card">
        <div class="score">{{ number_format($attempt->percentage, 1) }}%</div>
        <div class="status">{{ $attempt->isPassed() ? '✓ PASSED' : '✗ NOT PASSED' }}</div>
        <p><strong>Score:</strong> {{ $attempt->score }}/{{ $attempt->total_points }} points</p>
        <p><strong>Time Spent:</strong> {{ $attempt->getTimeSpentFormatted() }}</p>
        <p><strong>Attempt:</strong> {{ $attempt->attempt_number }}/{{ $attempt->quiz->max_attempts }}</p>
    </div>

    <!-- Summary -->
    <h2 style="color: #36b9cc;">Summary</h2>
    <table class="details-table">
        <tr>
            <th>Total Questions</th>
            <td>{{ $attempt->quiz->questions->count() }}</td>
        </tr>
        <tr>
            <th>Correct Answers</th>
            <td style="color: #1cc88a;">{{ $attempt->answers->where('is_correct', true)->count() }}</td>
        </tr>
        <tr>
            <th>Incorrect Answers</th>
            <td style="color: #e74a3b;">{{ $attempt->answers->where('is_correct', false)->count() }}</td>
        </tr>
        <tr>
            <th>Passing Score</th>
            <td>{{ $attempt->quiz->passing_score }}%</td>
        </tr>
    </table>

    <!-- Questions (if show_answers is enabled) -->
    @if($attempt->quiz->show_answers)
    <h2 style="color: #36b9cc; page-break-before: always;">Answer Review</h2>
    
    @foreach($attempt->quiz->questions()->orderBy('quiz_question.order')->get() as $index => $question)
        @php
            $answer = $attempt->answers->where('question_id', $question->id)->first();
        @endphp
        
        <div class="question-section">
            <div class="question-header">
                Question {{ $index + 1 }} of {{ $attempt->quiz->questions->count() }}
                ({{ $answer ? $answer->points_earned : 0 }}/{{ $question->points }} points)
            </div>
            
            <div class="question-body">
                <p><strong>{{ $question->question_text }}</strong></p>
                
                @if($question->isMultipleChoice() || $question->isTrueFalse())
                    @foreach($question->choices as $choice)
                        <div class="choice {{ $choice->is_correct ? 'correct' : ($answer && $answer->choice_id == $choice->id ? 'incorrect' : 'neutral') }}">
                            {{ $choice->choice_text }}
                            @if($choice->is_correct)
                                <strong>(Correct Answer)</strong>
                            @endif
                            @if($answer && $answer->choice_id == $choice->id)
                                <strong>(Your Answer)</strong>
                            @endif
                        </div>
                    @endforeach
                @elseif($question->isIdentification() || $question->isEssay())
                    <div class="choice {{ $answer && $answer->is_correct ? 'correct' : 'incorrect' }}">
                        <strong>Your Answer:</strong><br>
                        {{ $answer ? $answer->answer_text : 'No answer provided' }}
                    </div>
                @endif
            </div>
        </div>
    @endforeach
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This is an official document generated by Quiz LMS</p>
        <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
        <p>Document ID: {{ $attempt->id }}-{{ md5($attempt->id . $attempt->completed_at) }}</p>
    </div>
</body>
</html>