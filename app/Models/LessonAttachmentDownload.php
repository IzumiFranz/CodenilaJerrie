<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonAttachmentDownload extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'lesson_attachment_id',
        'student_id',
        'downloaded_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    /**
     * Get the attachment that was downloaded.
     */
    public function attachment()
    {
        return $this->belongsTo(LessonAttachment::class, 'lesson_attachment_id');
    }

    /**
     * Get the student who downloaded.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}