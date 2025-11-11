@component('mail::message')
# Welcome to {{ config('app.name') }}!

Hello **{{ $user->full_name }}**,

Your account has been successfully created by {{ $createdBy->full_name }}. Welcome to our Learning Management System!

@component('mail::panel')
## 🔐 Your Login Credentials

**Username:** `{{ $user->username }}`  
**Temporary Password:** `{{ $password }}`  
**Role:** {{ ucfirst($user->role) }}  
**Email:** {{ $user->email }}
@endcomponent

## 🚨 Important Security Notice

@component('mail::panel')
⚠️ **You MUST change your password on first login**

For security reasons, this temporary password expires after your first successful login. You will be automatically prompted to create a new, secure password.
@endcomponent

## 🚀 Getting Started

Follow these simple steps to access your account:

### Step 1: Login
Click the button below to go to the login page.

@component('mail::button', ['url' => route('login')])
Login to Your Account
@endcomponent

### Step 2: Enter Your Credentials
- **Username:** {{ $user->username }}
- **Password:** {{ $password }}

### Step 3: Change Your Password
You'll be immediately prompted to create a new password. Choose a strong password that:
- Is at least 8 characters long
- Contains uppercase and lowercase letters
- Includes numbers
- Has special characters (@, #, $, etc.)

### Step 4: Complete Your Profile
After changing your password, complete your profile information.

## 📚 What's Available

As a **{{ ucfirst($user->role) }}**, you have access to:

@if($user->role === 'student')
- 📖 View and download lessons
- 📝 Take quizzes and view results
- 📊 Track your progress
- 💬 Submit feedback
- 🎯 Access your enrolled courses
@elseif($user->role === 'instructor')
- 📝 Create and manage lessons
- 📋 Create and publish quizzes
- 📊 View student progress
- 💬 Respond to feedback
- 🎓 Manage your courses
@elseif($user->role === 'admin')
- 👥 Manage users and roles
- 📚 Manage courses and subjects
- 📊 View system analytics
- ⚙️ Configure system settings
- 📧 Send notifications
@endif

## 📱 Access Information

**Login URL:** {{ url('/login') }}  
**Support Email:** support@{{ config('app.url') }}  
**System Status:** Active

## 🔒 Security Tips

To keep your account secure:

1. ✅ **Never share** your password with anyone
2. ✅ **Change your password** regularly
3. ✅ **Logout** when using shared computers
4. ✅ **Report** suspicious activity immediately
5. ✅ **Use** a strong, unique password

## 📞 Need Help?

If you have any questions or need assistance:

- 📧 Email our support team
- 💬 Contact your {{ $user->role === 'student' ? 'instructor' : 'administrator' }}
- 📖 Visit the help center
- 🎥 Watch tutorial videos

@component('mail::button', ['url' => route('login'), 'color' => 'success'])
Get Started Now
@endcomponent

## 📋 Account Summary

@component('mail::table')
| Field | Value |
|:------|:------|
| Username | {{ $user->username }} |
| Email | {{ $user->email }} |
| Role | {{ ucfirst($user->role) }} |
| Status | Active |
| Created | {{ $user->created_at->format('F d, Y h:i A') }} |
| Must Change Password | Yes |
@endcomponent

---

We're excited to have you on board! If you have any questions, don't hesitate to reach out.

Thanks,<br>
{{ config('app.name') }} Team

---

<small style="color: #666;">
**Note:** This email contains sensitive information. Please keep it confidential and delete it after successfully logging in and changing your password.
</small>
@endcomponent