<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'specialization_id',
        'subject_code',
        'subject_name',
        'description',
        'year_level',
        'units',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'year_level' => 'integer',
            'units' => 'integer',
        ];
    }

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function assignments()
    {
        return $this->hasMany(InstructorSubjectSection::class);
    }

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class, 'instructor_subject_section')
            ->withPivot('section_id', 'academic_year', 'semester')
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

    public function aiJobs()
    {
        return $this->hasMany(AIJob::class);
    }

    // Helper Methods
    public function getQualifiedInstructors()
    {
        return Instructor::where('specialization_id', $this->specialization_id)
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->with('user')
            ->get();
    }

    public function getPublishedLessonsCount(): int
    {
        return $this->lessons()->where('is_published', true)->count();
    }

    public function getPublishedQuizzesCount(): int
    {
        return $this->quizzes()->where('is_published', true)->count();
    }

    public function getTotalQuestionsCount(): int
    {
        return $this->questionBank()->count();
    }
}
