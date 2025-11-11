<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonAttachmentService
{
    /**
     * Allowed file types and their MIME types.
     */
    private const ALLOWED_TYPES = [
        // Documents
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'ppt' => ['application/vnd.ms-powerpoint'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'txt' => ['text/plain'],
        
        // Images
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
        'svg' => ['image/svg+xml'],
        
        // Archives
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'rar' => ['application/x-rar-compressed'],
        
        // Media
        'mp4' => ['video/mp4'],
        'mp3' => ['audio/mpeg'],
    ];

    /**
     * Maximum file size in bytes (50MB).
     */
    private const MAX_FILE_SIZE = 52428800;

    /**
     * Upload an attachment to a lesson.
     */
    public function upload(Lesson $lesson, UploadedFile $file, int $uploadedBy, ?string $description = null): LessonAttachment
    {
        // Validate file
        $this->validateFile($file);

        // Generate unique filename
        $extension = strtolower($file->getClientOriginalExtension());
        $storedFilename = Str::uuid() . '.' . $extension;
        
        // Store file
        $path = $file->storeAs(
            'lesson-attachments/' . $lesson->id,
            $storedFilename,
            'public'
        );

        // Get next display order
        $displayOrder = $lesson->attachments()->max('display_order') + 1;

        // Create attachment record
        $attachment = LessonAttachment::create([
            'lesson_id' => $lesson->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_extension' => $extension,
            'description' => $description,
            'display_order' => $displayOrder,
            'uploaded_by' => $uploadedBy,
        ]);

        return $attachment;
    }

    /**
     * Upload multiple attachments.
     */
    public function uploadMultiple(Lesson $lesson, array $files, int $uploadedBy, array $descriptions = []): array
    {
        $attachments = [];

        foreach ($files as $index => $file) {
            $description = $descriptions[$index] ?? null;
            $attachments[] = $this->upload($lesson, $file, $uploadedBy, $description);
        }

        return $attachments;
    }

    /**
     * Validate uploaded file.
     */
    private function validateFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('File size exceeds maximum allowed size of 50MB.');
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!isset(self::ALLOWED_TYPES[$extension])) {
            throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', array_keys(self::ALLOWED_TYPES)));
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_TYPES[$extension])) {
            throw new \Exception('Invalid file MIME type.');
        }
    }

    /**
     * Delete an attachment.
     */
    public function delete(LessonAttachment $attachment): bool
    {
        return $attachment->delete();
    }

    /**
     * Update attachment order.
     */
    public function reorder(Lesson $lesson, array $orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            $lesson->attachments()
                ->where('id', $id)
                ->update(['display_order' => $order]);
        }
    }

    /**
     * Toggle attachment visibility.
     */
    public function toggleVisibility(LessonAttachment $attachment): LessonAttachment
    {
        $attachment->update([
            'is_visible' => !$attachment->is_visible
        ]);

        return $attachment->fresh();
    }

    /**
     * Update attachment description.
     */
    public function updateDescription(LessonAttachment $attachment, ?string $description): LessonAttachment
    {
        $attachment->update(['description' => $description]);
        return $attachment->fresh();
    }

    /**
     * Get attachment statistics for a lesson.
     */
    public function getStatistics(Lesson $lesson): array
    {
        $attachments = $lesson->attachments;

        return [
            'total_count' => $attachments->count(),
            'total_size' => $attachments->sum('file_size'),
            'total_downloads' => $attachments->sum('download_count'),
            'visible_count' => $attachments->where('is_visible', true)->count(),
            'by_type' => $attachments->groupBy('file_extension')->map->count(),
        ];
    }

    /**
     * Get allowed file extensions.
     */
    public static function getAllowedExtensions(): array
    {
        return array_keys(self::ALLOWED_TYPES);
    }

    /**
     * Get max file size in MB.
     */
    public static function getMaxFileSizeMB(): int
    {
        return self::MAX_FILE_SIZE / 1048576;
    }
}