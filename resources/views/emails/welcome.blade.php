@component('mail::message')
# Welcome to {{ config('app.name') }}!

Hello **{{ $user->profile->first_name ?? $user->username }}**,

Your account has been successfully created. Welcome to our Learning Management System!

---

## 🔐 Your Login Credentials

@component('mail::panel')
**Your Username:** `{{ $user->username }}`  
**Temporary Password:** `{{ $password }}`  
**Account Type:** {{ $roleLabel }}  
**Email:** {{ $user->email }}
@endcomponent

@component('mail::button', ['url' => $loginUrl, 'color' => 'success'])
Login to Your Account
@endcomponent

---

## ⚠️ Important Security Notice

@component('mail::panel')
🔒 **You MUST change your password on first login**

For your security, you will be required to create a new password when you first access the system. This is a mandatory security measure.

**Please keep your credentials safe and do not share them with anyone.**
@endcomponent

---

## 🚀 Getting Started - 4 Simple Steps

**Step 1:** Click the "Login to Your Account" button above

**Step 2:** Enter your username: `{{ $user->username }}`

**Step 3:** Enter your temporary password: `{{ $password }}`

**Step 4:** Create a strong new password (minimum 8 characters)

---

## 📚 What's Next?

@if($user->role === 'student')
### As a Student, you can:
- ✅ View your enrolled courses and subjects
- ✅ Access learning materials and lessons
- ✅ Take quizzes and view your results
- ✅ Track your academic progress
- ✅ Submit feedback to instructors

@elseif($user->role === 'instructor')
### As an Instructor, you can:
- ✅ Create and manage lessons
- ✅ Build question banks with AI assistance
- ✅ Create and publish quizzes
- ✅ Grade student submissions
- ✅ Track student performance
- ✅ Generate questions using AI

@elseif($user->role === 'admin')
### As an Administrator, you can:
- ✅ Manage all users (students, instructors, admins)
- ✅ Configure courses and subjects
- ✅ Manage enrollments and sections
- ✅ View system analytics
- ✅ Handle feedback and support requests
- ✅ Monitor system activities

@endif

---

## 💡 Need Help?

If you have any questions or encounter any issues:

📧 **Email Support:** support@{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'quizlms.com' }}  
📱 **Contact:** Your academic coordinator  
📖 **Help Center:** Visit the help section in your dashboard

---

## 🔒 Security Tips

- ✅ Choose a strong password (mix of letters, numbers, symbols)
- ✅ Never share your login credentials
- ✅ Log out after each session on shared computers
- ✅ Contact support immediately if you suspect unauthorized access

---

We're excited to have you on board!

Best regards,  
**{{ config('app.name') }} Team**

---

<small style="color: #6c757d;">
This email was sent to {{ $user->email }} because an account was created for you.  
If you did not request this account, please contact your administrator immediately.  
Account created on: {{ now()->format('F d, Y h:i A') }}
</small>
@endcomponent