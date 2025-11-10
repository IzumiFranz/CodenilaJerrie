<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonView extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'student_id',
        'viewed_at',
        'duration_seconds',
        'completed',
        'completed_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'completed_at' => 'datetime',
            'completed' => 'boolean',
            'duration_seconds' => 'integer',
        ];
    }

    // Relationships
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Helper Methods
    public function markCompleted(): void
    {
        $this->completed = true;
        $this->completed_at = now();
        $this->save();
    }

    public function getDurationFormatted(): string
    {
        if (!$this->duration_seconds) {
            return '0 seconds';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        if ($minutes > 60) {
            $hours = floor($minutes / 60);
            $minutes = $minutes % 60;
            return "{$hours}h {$minutes}m {$seconds}s";
        }

        return $minutes > 0 ? "{$minutes}m {$seconds}s" : "{$seconds}s";
    }

    // Static Methods
    public static function recordView(Lesson $lesson, Student $student, ?int $duration = null): self
    {
        return self::create([
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'viewed_at' => now(),
            'duration_seconds' => $duration,
            'ip_address' => request()->ip(),
        ]);
    }

    public static function getStudentProgress(Student $student, Lesson $lesson)
    {
        return self::where('lesson_id', $lesson->id)
            ->where('student_id', $student->id)
            ->orderBy('viewed_at', 'desc')
            ->first();
    }

    public static function getLessonStats(Lesson $lesson): array
    {
        $views = self::where('lesson_id', $lesson->id)->get();
        
        return [
            'total_views' => $views->count(),
            'unique_students' => $views->pluck('student_id')->unique()->count(),
            'completed_count' => $views->where('completed', true)->count(),
            'average_duration' => $views->avg('duration_seconds'),
            'completion_rate' => $views->count() > 0 
                ? round(($views->where('completed', true)->count() / $views->pluck('student_id')->unique()->count()) * 100, 2)
                : 0,
        ];
    }
}