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
        'status',
        'available_from',
        'available_until',
        'published_at',
        'publish_at',
        'scheduled_publish_at',
        'scheduled_unpublish_at',
        'auto_publish',
        'difficulty_level',
        'estimated_duration',
        'allow_practice_mode',
        'allow_review_mode',
        'show_correct_in_review',
        'show_explanation_in_review',
        'review_available_after',
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
            'publish_at' => 'datetime',
            'scheduled_publish_at' => 'datetime',
            'scheduled_unpublish_at' => 'datetime',
            'auto_publish' => 'boolean',
            'estimated_duration' => 'integer',
            'allow_practice_mode' => 'boolean',
            'allow_review_mode' => 'boolean',
            'show_correct_in_review' => 'boolean',
            'show_explanation_in_review' => 'boolean',
            'review_available_after' => 'integer',
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

    public function feedbacks()
    {
        return $this->morphMany(Feedback::class, 'feedbackable');
    }
    
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
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

    public function template()
    {
        return $this->belongsTo(QuizTemplate::class, 'template_id');
    }

    // Helpers
    public function getAverageDifficulty()
    {
        $difficulties = ['easy' => 1, 'medium' => 2, 'hard' => 3];
        $avg = $this->questions->avg(function($q) use ($difficulties) {
            return $difficulties[$q->difficulty] ?? 2;
        });
        
        if ($avg <= 1.5) return 'easy';
        if ($avg >= 2.5) return 'hard';
        return 'medium';
    }

    public function isPracticeMode($attemptId = null)
    {
        if (!$this->allow_practice_mode) return false;
        
        if ($attemptId) {
            $attempt = $this->attempts()->find($attemptId);
            return $attempt && $attempt->is_practice;
        }
        
        return false;
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

    public function isScheduledForPublish(): bool
    {
        return $this->auto_publish && 
            $this->scheduled_publish_at && 
            !$this->is_published;
    }

    public function shouldAutoPublish(): bool
    {
        if (!$this->isScheduledForPublish()) {
            return false;
        }
        
        return now()->gte($this->scheduled_publish_at);
    }

    public function shouldAutoUnpublish(): bool
    {
        if (!$this->is_published || !$this->scheduled_unpublish_at) {
            return false;
        }
        
        return now()->gte($this->scheduled_unpublish_at);
    }

    public function calculateDifficultyLevel(): void
    {
        if ($this->questions->count() === 0) {
            $this->difficulty_level = null;
            $this->save();
            return;
        }
        
        $difficulties = $this->questions->pluck('difficulty');
        $counts = $difficulties->countBy();
        
        $easy = $counts->get('easy', 0);
        $medium = $counts->get('medium', 0);
        $hard = $counts->get('hard', 0);
        $total = $this->questions->count();
        
        // Calculate weighted average
        $score = ($easy * 1 + $medium * 2 + $hard * 3) / $total;
        
        if ($score <= 1.5) {
            $this->difficulty_level = 'easy';
        } elseif ($score <= 2.5) {
            $this->difficulty_level = 'medium';
        } else {
            $this->difficulty_level = 'hard';
        }
        
        $this->save();
    }

    public function calculateEstimatedDuration(): void
    {
        if ($this->questions->count() === 0) {
            $this->estimated_duration = null;
            $this->save();
            return;
        }
        
        // Estimate: 1-2 minutes per multiple choice, 2-3 for others
        $duration = 0;
        foreach ($this->questions as $question) {
            switch ($question->type) {
                case 'multiple_choice':
                case 'true_false':
                    $duration += 1.5; // minutes
                    break;
                case 'identification':
                    $duration += 2;
                    break;
                case 'essay':
                    $duration += 5;
                    break;
            }
        }
        
        $this->estimated_duration = max(5, ceil($duration));
        $this->save();
    }
    public function canReview(QuizAttempt $attempt): bool
    {
        if (!$this->allow_review || !$attempt->isCompleted()) {
            return false;
        }
        
        if ($this->review_available_after) {
            $minutesSinceCompletion = $attempt->completed_at->diffInMinutes(now());
            return $minutesSinceCompletion >= $this->review_available_after;
        }
        
        return true;
    }

    public function isAvailableToStudent($student)
    {
        if (!$this->is_published) return false;
        
        // Check date availability
        $now = now();
        if ($this->available_from && $now < $this->available_from) return false;
        if ($this->available_until && $now > $this->available_until) return false;
        
        // Check enrollment
        $sectionIds = $student->enrollments()
            ->where('status', 'enrolled')
            ->pluck('section_id');
        
        return \DB::table('instructor_subject_section')
            ->whereIn('section_id', $sectionIds)
            ->where('subject_id', $this->subject_id)
            ->exists();
    }
}