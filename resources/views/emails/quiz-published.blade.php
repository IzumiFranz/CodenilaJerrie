@component('mail::message')
# New Quiz Available

Hello {{ $quiz->subject->students->first()->full_name ?? 'Student' }},

A new quiz has been published in your course!

## {{ $quiz->title }}

**Subject:** {{ $quiz->subject->subject_name }}  
**Instructor:** {{ $quiz->instructor->full_name }}

### Quiz Details:
- **Questions:** {{ $quiz->questions->count() }}
- **Time Limit:** {{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'Unlimited' }}
- **Passing Score:** {{ $quiz->passing_score }}%
- **Max Attempts:** {{ $quiz->max_attempts }}

@if($quiz->available_from)
**Available from:** {{ $quiz->available_from->format('F d, Y h:i A') }}
@endif

@if($quiz->available_until)
**Available until:** {{ $quiz->available_until->format('F d, Y h:i A') }}
@endif

@component('mail::button', ['url' => route('student.quizzes.show', $quiz)])
View Quiz
@endcomponent

Good luck with your quiz!

Thanks,<br>
{{ config('app.name') }}
@endcomponent