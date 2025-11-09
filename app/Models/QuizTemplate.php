<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizTemplate extends Model
{
    protected $fillable = [
        'instructor_id', 'name', 'description', 'time_limit', 'passing_score',
        'max_attempts', 'randomize_questions', 'randomize_choices',
        'show_results', 'show_correct_answers', 'allow_review_mode',
        'allow_practice_mode', 'is_shared'
    ];

    protected $casts = [
        'randomize_questions' => 'boolean',
        'randomize_choices' => 'boolean',
        'show_results' => 'boolean',
        'show_correct_answers' => 'boolean',
        'allow_review_mode' => 'boolean',
        'allow_practice_mode' => 'boolean',
        'is_shared' => 'boolean',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function applyToQuiz()
    {
        return [
            'time_limit' => $this->time_limit,
            'passing_score' => $this->passing_score,
            'max_attempts' => $this->max_attempts,
            'randomize_questions' => $this->randomize_questions,
            'randomize_choices' => $this->randomize_choices,
            'show_results' => $this->show_results,
            'show_correct_answers' => $this->show_correct_answers,
            'allow_review_mode' => $this->allow_review_mode,
            'allow_practice_mode' => $this->allow_practice_mode,
        ];
    }
}