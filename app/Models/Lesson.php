<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
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