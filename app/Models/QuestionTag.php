<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuestionTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'subject_id',
        'name',
        'slug',
        'color',
        'description',
        'question_count',
    ];

    protected function casts(): array
    {
        return [
            'question_count' => 'integer',
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
        return $this->belongsToMany(QuestionBank::class, 'question_tag_pivot')
            ->withTimestamps();
    }

    // Helper Methods
    public function updateQuestionCount(): void
    {
        $this->question_count = $this->questions()->count();
        $this->save();
    }

    public static function generateSlug(string $name): string
    {
        return Str::slug($name);
    }

    public function getColorBadgeClass(): string
    {
        // Convert hex color to Bootstrap badge class
        $brightness = $this->getColorBrightness();
        return $brightness > 128 ? 'text-dark' : 'text-white';
    }

    private function getColorBrightness(): int
    {
        $hex = ltrim($this->color, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return (int)(($r * 299 + $g * 587 + $b * 114) / 1000);
    }

    // Scopes
    public function scopeForInstructor($query, int $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopeForSubject($query, ?int $subjectId)
    {
        if ($subjectId) {
            return $query->where('subject_id', $subjectId);
        }
        return $query;
    }

    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderBy('question_count', 'desc')->limit($limit);
    }
}