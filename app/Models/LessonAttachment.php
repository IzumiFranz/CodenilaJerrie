<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LessonAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lesson_id',
        'original_filename',
        'stored_filename',
        'file_path',
        'mime_type',
        'file_size',
        'file_extension',
        'description',
        'display_order',
        'is_visible',
        'download_count',
        'uploaded_by',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Get the lesson that owns the attachment.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the user who uploaded the attachment.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get download records.
     */
    public function downloads()
    {
        return $this->hasMany(LessonAttachmentDownload::class);
    }

    /**
     * Get file size in human-readable format.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    /**
     * Get file icon class based on file type.
     */
    public function getFileIconAttribute(): string
    {
        $iconMap = [
            'pdf' => 'bi-file-pdf text-danger',
            'doc' => 'bi-file-word text-primary',
            'docx' => 'bi-file-word text-primary',
            'xls' => 'bi-file-excel text-success',
            'xlsx' => 'bi-file-excel text-success',
            'ppt' => 'bi-file-ppt text-warning',
            'pptx' => 'bi-file-ppt text-warning',
            'zip' => 'bi-file-zip text-secondary',
            'rar' => 'bi-file-zip text-secondary',
            'jpg' => 'bi-file-image text-info',
            'jpeg' => 'bi-file-image text-info',
            'png' => 'bi-file-image text-info',
            'gif' => 'bi-file-image text-info',
            'mp4' => 'bi-file-play text-danger',
            'mp3' => 'bi-file-music text-purple',
            'txt' => 'bi-file-text text-dark',
        ];

        return $iconMap[$this->file_extension] ?? 'bi-file-earmark text-secondary';
    }

    /**
     * Check if file is an image.
     */
    public function isImage(): bool
    {
        return in_array($this->file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    /**
     * Check if file is a document.
     */
    public function isDocument(): bool
    {
        return in_array($this->file_extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt']);
    }

    /**
     * Check if file is a video.
     */
    public function isVideo(): bool
    {
        return in_array($this->file_extension, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm']);
    }

    /**
     * Get full file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Increment download count.
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Record a download by a student.
     */
    public function recordDownload(int $studentId): void
    {
        LessonAttachmentDownload::create([
            'lesson_attachment_id' => $this->id,
            'student_id' => $studentId,
            'downloaded_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->incrementDownloadCount();
    }

    /**
     * Check if student has downloaded this attachment.
     */
    public function hasBeenDownloadedBy(int $studentId): bool
    {
        return $this->downloads()
            ->where('student_id', $studentId)
            ->exists();
    }

    /**
     * Delete file from storage.
     */
    public function deleteFile(): bool
    {
        if (Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->delete($this->file_path);
        }
        return true;
    }

    /**
     * Boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            // Delete file from storage when attachment is deleted
            $attachment->deleteFile();
        });
    }
}