@component('mail::message')
# 🚨 System Error Alert

Hello Admin,

A system error has occurred in **{{ config('app.name') }}**. Please review the details below.

@component('mail::panel')
**Exception:** {{ $exceptionClass }}  
**Message:** {{ $message }}  
**File:** {{ $file }}  
**Line:** {{ $line }}  
@endcomponent

## 🔍 Next Steps

- Check the logs for more details.  
- Investigate the root cause of the exception.  
- Apply fixes if necessary to prevent further errors.

@component('mail::button', ['url' => route('admin.audit-logs.index'), 'color' => 'danger'])
View Audit Logs
@endcomponent

Thanks,<br>
{{ config('app.name') }} System
@endcomponent
