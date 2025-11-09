<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'subject_id',
        'title',
        'content',
        'file_path',
        'file_type',
        'order',
        'is_published',
        'published_at',
        'view_count',
    ];
    
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'publish_at' => 'datetime',
            'order' => 'integer',
            'view_count' => 'integer',
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

    // Helper Methods
    public function togglePublish(): void
    {
        $this->is_published = !$this->is_published;
        $this->published_at = $this->is_published ? now() : null;
        $this->save();
    }

    public function views()
    {
        return $this->hasMany(LessonView::class);
    }

    public function prerequisite()
    {
        return $this->belongsTo(Lesson::class, 'prerequisite_lesson_id');
    }

    public function attachments()
    {
        return $this->hasMany(LessonAttachment::class);
    }

    // Helpers
    public function getReadTimeAttribute()
    {
        if ($this->word_count > 0) {
            return ceil($this->word_count / 200); // 200 words per minute
        }
        return 0;
    }

    public function calculateWordCount()
    {
        $text = strip_tags($this->content);
        $this->word_count = str_word_count($text);
        $this->save();
    }

    public function incrementViewCount()
    {
    $this->increment('view_count');
}

public function isViewedBy($studentId)
{
    return $this->views()->where('student_id', $studentId)->exists();
}

public function getViewPercentage($sectionId)
{
    $totalStudents = \App\Models\Enrollment::where('section_id', $sectionId)
        ->where('status', 'enrolled')
        ->count();
    
    if ($totalStudents == 0) return 0;
    
    $viewedCount = $this->views()->count();
    return round(($viewedCount / $totalStudents) * 100, 1);
}

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }

    public function deleteFile(): void
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
    }
}