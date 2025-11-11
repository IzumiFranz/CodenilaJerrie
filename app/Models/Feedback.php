<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'message',
        'rating',
        'status',
        'is_anonymous',
        'feedbackable_type',
        'feedbackable_id',
        'response',
        'response_by_id',
        'responded_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackable()
    {
        return $this->morphTo();
    }

    // Helper Methods
    public function respond(string $response): void
    {
        $this->admin_response = $response;
        $this->responded_at = now();
        $this->status = 'resolved';
        $this->save();
    }

    public function markAsReviewed(): void
    {
        $this->status = 'reviewed';
        $this->save();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
