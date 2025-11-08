<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'choice_id',
        'answer_text',
        'is_correct',
        'points_earned',
        'instructor_feedback',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'points_earned' => 'decimal:2',
        ];
    }

    // Relationships
    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }

    public function choice()
    {
        return $this->belongsTo(Choice::class);
    }

    // Helper Methods
    public function gradeAutomatically(): void
    {
        $question = $this->question;

        if ($question->isMultipleChoice() || $question->isTrueFalse()) {
            $correctChoice = $question->getCorrectChoice();
            
            if ($correctChoice && $this->choice_id === $correctChoice->id) {
                $this->is_correct = true;
                $this->points_earned = $question->points;
            } else {
                $this->is_correct = false;
                $this->points_earned = 0;
            }
        }

        $this->save();
    }

    public function needsManualGrading(): bool
    {
        return $this->question->isEssay() || $this->question->isIdentification();
    }
}