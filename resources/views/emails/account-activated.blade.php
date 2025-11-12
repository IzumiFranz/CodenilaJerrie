@component('mail::message')
# Your Account Has Been Activated

Hello **{{ $user->full_name }}**,

Good news! Your account has been activated and you can now access the system.

---

## ✅ Account Status

@component('mail::panel')
**Username:** {{ $user->username }}  
**Status:** Active ✅  
**Activated On:** {{ now()->format('F d, Y h:i A') }}
@endcomponent

@component('mail::button', ['url' => route('login'), 'color' => 'success'])
Login to Your Account
@endcomponent

---

## 🚀 What's Next?

You can now:
- Access your dashboard
- View your courses and materials
- Participate in quizzes and activities
- Submit assignments

---

If you have any questions, please contact your administrator.

Welcome back!

Best regards,  
**{{ config('app.name') }} Team**
@endcomponent
