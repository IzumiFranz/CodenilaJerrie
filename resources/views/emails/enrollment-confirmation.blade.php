@component('mail::message')
# Enrollment Confirmation

Hello {{ $enrollment->student->full_name }},

You have been successfully enrolled in the following section:

## Enrollment Details

**Section:** {{ $enrollment->section->full_name }}  
**Course:** {{ $enrollment->section->course->course_name }}  
**Academic Year:** {{ $enrollment->academic_year }}  
**Semester:** {{ $enrollment->semester }}  
**Enrollment Date:** {{ $enrollment->enrollment_date->format('F d, Y') }}

### Enrolled Subjects:
@foreach($enrollment->section->subjects as $subject)
- {{ $subject->subject_name }} ({{ $subject->subject_code }}) - {{ $subject->units }} unit(s)
@endforeach

@component('mail::button', ['url' => route('student.dashboard')])
View Dashboard
@endcomponent

If you have any questions, please contact your academic advisor.

Thanks,<br>
{{ config('app.name') }}
@endcomponent