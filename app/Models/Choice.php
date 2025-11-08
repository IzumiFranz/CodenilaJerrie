<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'choice_text',
        'is_correct',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'order' => 'integer',
        ];
    }

    // Relationships
    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }

    public function quizAnswers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    // Helper Methods
    public function markAsCorrect(): void
    {
        // Mark this choice as correct and others as incorrect
        $this->question->choices()->update(['is_correct' => false]);
        $this->is_correct = true;
        $this->save();
    }
}