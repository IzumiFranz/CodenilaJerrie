@component('mail::message')
# Response to Your Feedback

Hello {{ $feedback->user->full_name }},

An administrator has responded to your feedback.

## Your Feedback
**Submitted:** {{ $feedback->created_at->format('F d, Y h:i A') }}

@if($feedback->feedbackable)
**Related to:**
@if($feedback->feedbackable_type === 'App\Models\Quiz')
Quiz: {{ $feedback->feedbackable->title }}
@elseif($feedback->feedbackable_type === 'App\Models\Lesson')
Lesson: {{ $feedback->feedbackable->title }}
@endif
@endif

@if($feedback->rating)
**Rating:** {{ str_repeat('⭐', $feedback->rating) }}
@endif

**Subject:** {{ $feedback->subject }}

**Your Message:**
{{ $feedback->message }}

---

## Administrator Response

@component('mail::panel')
{{ $feedback->response }}
@endcomponent

**Responded:** {{ $feedback->responded_at->format('F d, Y h:i A') }}

@component('mail::button', ['url' => route('student.feedback.show', $feedback)])
View Feedback
@endcomponent

Thank you for helping us improve!

Thanks,<br>
{{ config('app.name') }}
@endcomponent