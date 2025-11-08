<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'student_number',
        'first_name',
        'last_name',
        'middle_name',
        'year_level',
        'phone',
        'address',
        'admission_date',
    ];

    protected function casts(): array
    {
        return [
            'admission_date' => 'date',
            'year_level' => 'integer',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'enrollments')
            ->withPivot('academic_year', 'semester', 'status', 'enrollment_date')
            ->withTimestamps();
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Helper Methods
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . substr($this->middle_name, 0, 1) . '.';
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }

    public function isEnrolledInSection(Section $section, string $academicYear, string $semester): bool
    {
        return $this->enrollments()
            ->where('section_id', $section->id)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'enrolled')
            ->exists();
    }

    public function getEnrolledSubjects(string $academicYear, string $semester)
    {
        return $this->sections()
            ->wherePivot('academic_year', $academicYear)
            ->wherePivot('semester', $semester)
            ->wherePivot('status', 'enrolled')
            ->with('subjects')
            ->get()
            ->pluck('subjects')
            ->flatten()
            ->unique('id');
    }
}