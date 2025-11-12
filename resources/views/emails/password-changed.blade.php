@component('mail::message')
# Password Successfully Changed

Hello **{{ $user->full_name }}**,

Your password has been successfully changed.

---

## ✅ Confirmation Details

@component('mail::panel')
**Account:** {{ $user->username }}  
**Email:** {{ $user->email }}  
**Changed On:** {{ now()->format('F d, Y h:i A') }}  
**IP Address:** {{ request()->ip() }}
@endcomponent

---

## 🔒 Security Information

If you made this change, no further action is needed. Your account is now secured with your new password.

---

## ⚠️ Did You Make This Change?

If you **did not** change your password, your account may have been compromised.

**Take immediate action:**

1. Click the button below to reset your password
2. Contact support immediately
3. Review your recent account activity

@component('mail::button', ['url' => route('password.request'), 'color' => 'danger'])
Reset Password Now
@endcomponent

---

## 💡 Security Tips

- Use a unique password for this account
- Never share your password with anyone
- Enable two-factor authentication (if available)
- Log out from shared devices
- Contact support if you notice suspicious activity

---

Stay safe!

Best regards,  
**{{ config('app.name') }} Team**

---

<small style="color: #6c757d;">
This is an automated security notification.  
If you need assistance, contact support at support@{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'quizlms.com' }}
</small>
@endcomponent