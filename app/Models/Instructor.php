<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization_id',
        'employee_id',
        'first_name',
        'last_name',
        'middle_name',
        'department',
        'phone',
        'hire_date',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function assignments()
    {
        return $this->hasMany(InstructorSubjectSection::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'instructor_subject_section')
            ->withPivot('section_id', 'academic_year', 'semester')
            ->withTimestamps();
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'instructor_subject_section')
            ->withPivot('subject_id', 'academic_year', 'semester')
            ->withTimestamps();
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function questionBank()
    {
        return $this->hasMany(QuestionBank::class);
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

    public function canTeachSubject(Subject $subject): bool
    {
        return $this->specialization_id === $subject->specialization_id;
    }
}
