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
        'scheduled_publish_at',
        'scheduled_unpublish_at',
        'auto_publish',
        'word_count',
        'read_time_minutes',
        'file_name',
    ];
    
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'publish_at' => 'datetime',
            'order' => 'integer',
            'view_count' => 'integer',
            'scheduled_publish_at' => 'datetime',
            'scheduled_unpublish_at' => 'datetime',
            'auto_publish' => 'boolean',
            'word_count' => 'integer',
            'read_time_minutes' => 'integer',
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

    public function isAvailableToStudent($student)
    {
        if (!$this->is_published) return false;
        
        // Check if student is enrolled in any section where this subject is taught
        $sectionIds = $student->enrollments()
            ->where('status', 'enrolled')
            ->pluck('section_id');
        
        return \DB::table('instructor_subject_section')
            ->whereIn('section_id', $sectionIds)
            ->where('subject_id', $this->subject_id)
            ->exists();
    }

    public function views()
    {
        return $this->hasMany(LessonView::class);
    }

    public function viewsByStudent(Student $student)
    {
        return $this->views()->where('student_id', $student->id);
    }

    public function getViewStats(): array
    {
        return LessonView::getLessonStats($this);
    }

    public function hasBeenViewedBy(Student $student): bool
    {
        return $this->viewsByStudent($student)->exists();
    }

    public function isCompletedBy(Student $student): bool
    {
        return $this->viewsByStudent($student)->where('completed', true)->exists();
    }

    public function prerequisite()
    {
        return $this->belongsTo(Lesson::class, 'prerequisite_lesson_id');
    }

    public function attachments()
    {
        return $this->hasMany(LessonAttachment::class);
    }

    public function visibleAttachments()
    {
        return $this->attachments()->where('is_visible', true);
    }

    /**
     * Get attachment count.
     */
    public function getAttachmentCountAttribute(): int
    {
        return $this->attachments()->count();
    }

    /**
     * Get total attachment size.
     */
    public function getTotalAttachmentSizeAttribute(): int
    {
        return $this->attachments()->sum('file_size');
    }

    /**
     * Get formatted total attachment size.
     */
    public function getFormattedAttachmentSizeAttribute(): string
    {
        $bytes = $this->total_attachment_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    // Helpers
    public function getReadTimeAttribute()
    {
        if ($this->word_count > 0) {
            return ceil($this->word_count / 200); // 200 words per minute
        }
        return 0;
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

    public function calculateWordCount(): void
    {
        $text = strip_tags($this->content);
        $this->word_count = str_word_count($text);
        $this->save();
    }

    public function calculateReadTime(): void
    {
        if (!$this->word_count) {
            $this->calculateWordCount();
        }
        
        // Average reading speed: 200-250 words per minute
        $this->read_time_minutes = max(1, ceil($this->word_count / 225));
        $this->save();
    }
}