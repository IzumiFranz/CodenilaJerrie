@component('mail::message')
# Account Access Suspended

Hello **{{ $user->full_name }}**,

Your account access has been temporarily suspended.

---

## ⚠️ Account Status

@component('mail::panel')
**Username:** {{ $user->username }}  
**Status:** Suspended  
**Suspended On:** {{ now()->format('F d, Y h:i A') }}  
@if(isset($reason))
**Reason:** {{ $reason }}
@endif
@endcomponent

---

## 📞 Contact Support

If you believe this is a mistake or need clarification, please contact:

📧 **Email:** support@{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'quizlms.com' }}  
📱 **Phone:** Contact your administrator

---

Best regards,  
**{{ config('app.name') }} Team**
@endcomponent