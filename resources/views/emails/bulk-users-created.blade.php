@component('mail::message')
# Bulk User Creation Complete

Hello {{ auth()->user()->full_name ?? 'Administrator' }},

The bulk user creation process has been completed successfully!

@component('mail::panel')
## Summary

**Total Users Created:** {{ $totalCreated }}  
**Role:** {{ ucfirst($role) }}  
**Created By:** {{ auth()->user()->username }}  
**Date:** {{ now()->format('F d, Y h:i A') }}
@endcomponent

## 📋 What's Included

A CSV file containing all user credentials is attached to this email. The file includes:

- ✅ Full Name
- ✅ Username (auto-generated)
- ✅ Email Address
- ✅ Temporary Password
- ✅ Login URL
- ✅ Account Status

## 🔐 Important Security Notes

@component('mail::panel')
⚠️ **CONFIDENTIAL INFORMATION**

This email contains sensitive login credentials. Please:

1. **Store securely** - Save the CSV file in a secure location
2. **Distribute carefully** - Share credentials individually with each user
3. **Delete when done** - Remove the CSV after distributing credentials
4. **Change passwords** - Users will be prompted to change passwords on first login
@endcomponent

## 📧 Next Steps

### For {{ ucfirst($role) }}s:

1. **Download the attached CSV file**
2. **Review the credentials** for accuracy
3. **Distribute credentials** to each user individually via secure channel
4. **Inform users** that they must change their password on first login

### For Users:

Each user should:
1. Visit the login page at: {{ url('/login') }}
2. Use their assigned username and temporary password
3. Change their password immediately upon first login
4. Complete their profile information

## 📊 User List Preview

Below is a preview of the first 10 users created:

@component('mail::table')
| Name | Username | Email |
|:-----|:---------|:------|
@foreach(array_slice($users, 0, 10) as $user)
| {{ $user['name'] }} | {{ $user['username'] }} | {{ $user['email'] }} |
@endforeach
@if(count($users) > 10)
| ... | ... | ... |
| **+{{ count($users) - 10 }} more users** | | |
@endif
@endcomponent

## 🔗 Quick Actions

@component('mail::button', ['url' => route('admin.users.index')])
View All Users
@endcomponent

## 📞 Need Help?

If you encounter any issues or have questions:

- Check the user management documentation
- Contact technical support
- Review the system logs for any errors

---

**Important Reminders:**

✅ All users have been set to "Active" status  
✅ Users MUST change password on first login  
✅ Email verification may be required (check settings)  
✅ Audit logs have been created for this bulk operation

## 🗑️ Cleanup

This email will be automatically deleted after 7 days for security purposes. Please ensure you have saved the CSV file before then.

Thanks,<br>
{{ config('app.name') }} System

---

<small style="color: #666;">
**Bulk Operation ID:** {{ md5($totalCreated . now()->timestamp) }}<br>
**Generated:** {{ now()->format('Y-m-d H:i:s') }}<br>
**IP Address:** {{ request()->ip() }}
</small>
@endcomponent