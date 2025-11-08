<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'attempt_number',
        'score',
        'total_points',
        'percentage',
        'status',
        'started_at',
        'completed_at',
        'time_spent',
        'question_order',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'score' => 'decimal:2',
            'total_points' => 'decimal:2',
            'percentage' => 'decimal:2',
            'time_spent' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'question_order' => 'array',
        ];
    }

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }

    // Helper Methods
    public function start(): void
    {
        $this->status = 'in_progress';
        $this->started_at = now();
        $this->save();
    }

    public function complete(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        
        if ($this->started_at) {
            $this->time_spent = $this->started_at->diffInSeconds($this->completed_at);
        }
        
        $this->calculateScore();
        $this->save();
    }

    public function calculateScore(): void
    {
        $totalPoints = $this->answers->sum('points_earned');
        $maxPoints = $this->quiz->getTotalPoints();

        $this->score = $totalPoints;
        $this->total_points = $maxPoints;
        $this->percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 2) : 0;
    }

    public function isPassed(): bool
    {
        return $this->percentage >= $this->quiz->passing_score;
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getTimeSpentFormatted(): string
    {
        if (!$this->time_spent) {
            return '0 minutes';
        }

        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;

        return $minutes > 0 ? "{$minutes} min {$seconds} sec" : "{$seconds} sec";
    }

    public function getRemainingTime(): ?int
    {
        if (!$this->quiz->time_limit || !$this->started_at || $this->isCompleted()) {
            return null;
        }

        $elapsed = now()->diffInMinutes($this->started_at);
        $remaining = $this->quiz->time_limit - $elapsed;

        return max(0, $remaining);
    }
}