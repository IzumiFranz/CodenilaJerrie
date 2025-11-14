<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'status',
        'profile_picture',
        'must_change_password',
        'last_login_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    // Relationships
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function receivedFeedback()
    {
        return $this->morphMany(Feedback::class, 'feedbackable');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function AIJobs()
    {
        return $this->hasMany(AIJob::class);
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    // Helper Methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getFullNameAttribute(): string
    {
        if ($this->isAdmin() && $this->admin) {
            return trim($this->admin->first_name . ' ' . $this->admin->last_name);
        }
        if ($this->isInstructor() && $this->instructor) {
            return trim($this->instructor->first_name . ' ' . $this->instructor->last_name);
        }
        if ($this->isStudent() && $this->student) {
            return trim($this->student->first_name . ' ' . $this->student->last_name);
        }
        return $this->username;
    }

    /**
     * Get the profile attribute (accessor)
     * This returns the actual profile model instance
     * Note: For eager loading, use with(['admin', 'instructor', 'student']) instead of with('profile')
     */
    public function getProfileAttribute()
    {
        if ($this->isAdmin()) {
            return $this->admin;
        }
        if ($this->isInstructor()) {
            return $this->instructor;
        }
        if ($this->isStudent()) {
            return $this->student;
        }
        return null;
    }
    
    public function hasAccessToLesson($lessonId)
    {
        // Implement your access logic here
        // For now, return true if user is a student
        return $this->role === 'student';
    }

    public function getProfilePictureUrlAttribute()
    {
        // Check if user has direct profile picture
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        // Check role-specific profile picture
        if ($this->isStudent() && $this->student && $this->student->profile_picture) {
            return asset('storage/' . $this->student->profile_picture);
        }

        if ($this->isInstructor() && $this->instructor && $this->instructor->profile_picture) {
            return asset('storage/' . $this->instructor->profile_picture);
        }

        if ($this->isAdmin() && $this->admin && $this->admin->profile_picture) {
            return asset('storage/' . $this->admin->profile_picture);
        }

        // Return default placeholder
        return asset('img/undraw_profile.svg');
    }

    /**
     * Check if user has a profile picture
     */
    public function hasProfilePicture()
    {
        return $this->profile_picture 
            || ($this->isStudent() && $this->student && $this->student->profile_picture)
            || ($this->isInstructor() && $this->instructor && $this->instructor->profile_picture)
            || ($this->isAdmin() && $this->admin && $this->admin->profile_picture);
    }
}
