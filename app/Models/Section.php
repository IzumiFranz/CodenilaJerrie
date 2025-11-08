<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'section_name',
        'year_level',
        'max_students',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'year_level' => 'integer',
            'max_students' => 'integer',
        ];
    }

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot('academic_year', 'semester', 'status', 'enrollment_date')
            ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(InstructorSubjectSection::class);
    }

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class, 'instructor_subject_section')
            ->withPivot('subject_id', 'academic_year', 'semester')
            ->withTimestamps();
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'instructor_subject_section')
            ->withPivot('instructor_id', 'academic_year', 'semester')
            ->withTimestamps();
    }

    // Helper Methods
    public function getEnrolledStudentsCount(string $academicYear, string $semester): int
    {
        return $this->enrollments()
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'enrolled')
            ->count();
    }

    public function hasAvailableSlots(string $academicYear, string $semester): bool
    {
        $enrolled = $this->getEnrolledStudentsCount($academicYear, $semester);
        return $enrolled < $this->max_students;
    }

    public function getFullNameAttribute(): string
    {
        return $this->course->course_code . ' ' . $this->year_level . '-' . $this->section_name;
    }
}