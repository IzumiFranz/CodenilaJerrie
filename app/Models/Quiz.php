<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'subject_id',
        'title',
        'description',
        'instructions',
        'time_limit',
        'passing_score',
        'max_attempts',
        'randomize_questions',
        'randomize_choices',
        'show_results',
        'show_answers',
        'is_published',
        'available_from',
        'available_until',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'time_limit' => 'integer',
            'passing_score' => 'decimal:2',
            'max_attempts' => 'integer',
            'randomize_questions' => 'boolean',
            'randomize_choices' => 'boolean',
            'show_results' => 'boolean',
            'show_answers' => 'boolean',
            'is_published' => 'boolean',
            'available_from' => 'datetime',
            'available_until' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    // Relationships
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->belongsToMany(QuestionBank::class, 'quiz_question')
            ->withPivot('order')
            ->orderBy('quiz_question.order')
            ->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Helper Methods
    public function togglePublish(): void
    {
        $this->is_published = !$this->is_published;
        $this->published_at = $this->is_published ? now() : null;
        $this->save();
    }

    public function isAvailable(): bool
    {
        if (!$this->is_published) {
            return false;
        }

        $now = now();

        if ($this->available_from && $now->lt($this->available_from)) {
            return false;
        }

        if ($this->available_until && $now->gt($this->available_until)) {
            return false;
        }

        return true;
    }

    public function getTotalPoints(): float
    {
        return $this->questions->sum('points');
    }

    public function getQuestionsCount(): int
    {
        return $this->questions->count();
    }

    public function studentCanTakeQuiz(Student $student): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $attemptCount = $this->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'completed')
            ->count();

        return $attemptCount < $this->max_attempts;
    }

    public function getStudentAttempts(Student $student)
    {
        return $this->attempts()
            ->where('student_id', $student->id)
            ->orderBy('attempt_number', 'desc')
            ->get();
    }

    public function getAverageScore(): float
    {
        return $this->attempts()
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;
    }

    public function getPassingRate(): float
    {
        $total = $this->attempts()->where('status', 'completed')->count();
        if ($total === 0) {
            return 0;
        }

        $passed = $this->attempts()
            ->where('status', 'completed')
            ->where('percentage', '>=', $this->passing_score)
            ->count();

        return round(($passed / $total) * 100, 2);
    }
}