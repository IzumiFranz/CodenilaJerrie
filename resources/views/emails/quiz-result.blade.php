@component('mail::message')
# Quiz Result: {{ $attempt->isPassed() ? 'Passed!' : 'Completed' }}

Hello {{ $attempt->student->full_name }},

Your quiz results are now available.

## {{ $attempt->quiz->title }}

@component('mail::panel')
### Your Score: {{ number_format($attempt->percentage, 1) }}%

**Points Earned:** {{ $attempt->score }}/{{ $attempt->total_points }}  
**Status:** @if($attempt->isPassed()) ✅ Passed @else ❌ Not Passed @endif  
**Time Spent:** {{ $attempt->getTimeSpentFormatted() }}  
**Attempt:** {{ $attempt->attempt_number }}/{{ $attempt->quiz->max_attempts }}
@endcomponent

@if($attempt->isPassed())
🎉 **Congratulations!** You passed the quiz with a score of {{ number_format($attempt->percentage, 1) }}%!
@else
The passing score for this quiz is {{ $attempt->quiz->passing_score }}%. 

@if($attempt->quiz->studentCanTakeQuiz($attempt->student))
You have {{ $attempt->quiz->max_attempts - $attempt->attempt_number }} attempt(s) remaining. Keep studying and try again!
@endif
@endif

@component('mail::button', ['url' => route('student.quiz-attempts.results', $attempt)])
View Detailed Results
@endcomponent

@if($attempt->quiz->show_answers)
@component('mail::button', ['url' => route('student.quiz-attempts.review', $attempt), 'color' => 'success'])
Review Your Answers
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent