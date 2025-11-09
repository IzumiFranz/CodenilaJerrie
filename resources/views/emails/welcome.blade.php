@component('mail::message')
# Welcome to Quiz LMS!

Hello {{ $user->full_name }},

Your account has been created successfully. Welcome to our Learning Management System!

## Your Login Credentials

**Username:** {{ $user->username }}  
**Temporary Password:** {{ $password }}  
**Role:** {{ ucfirst($user->role) }}

@component('mail::panel')
⚠️ **Important:** For security reasons, you will be required to change your password upon first login.
@endcomponent

@component('mail::button', ['url' => route('login')])
Login Now
@endcomponent

## Getting Started

1. Click the button above to login
2. Use your username and temporary password
3. You'll be prompted to create a new password
4. Explore your dashboard and start learning!

### Need Help?

If you have any questions or need assistance:
- Contact your instructor
- Visit the help center
- Email support@quizlms.com

We're excited to have you on board!

Thanks,<br>
{{ config('app.name') }}
@endcomponent