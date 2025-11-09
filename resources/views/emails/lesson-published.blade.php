@component('mail::message')
# New Lesson Available

Hello Student,

A new lesson has been published in your course!

## {{ $lesson->title }}

**Subject:** {{ $lesson->subject->subject_name }}  
**Instructor:** {{ $lesson->instructor->full_name }}  
**Published:** {{ $lesson->published_at->format('F d, Y h:i A') }}

@if($lesson->hasFile())
📎 This lesson includes a downloadable file ({{ strtoupper($lesson->file_type) }})
@endif

@component('mail::button', ['url' => route('student.lessons.show', $lesson)])
View Lesson
@endcomponent

Happy learning!

Thanks,<br>
{{ config('app.name') }}
@endcomponent