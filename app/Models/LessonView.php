<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonView extends Model
{
    protected $fillable = [
        'lesson_id', 'student_id', 'viewed_at', 'time_spent', 'completed'
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'completed' => 'boolean',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}