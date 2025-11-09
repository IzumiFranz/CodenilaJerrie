<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionRevision extends Model
{
    protected $fillable = [
        'question_bank_id', 'revised_by', 'question_text', 'choices',
        'type', 'points', 'difficulty', 'blooms_level', 'explanation', 'revision_note'
    ];

    protected $casts = [
        'choices' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'revised_by');
    }
}