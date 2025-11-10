<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'action_url',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper Methods
    public function markAsRead(): void
    {
        
        if (!$this->read_at) {
            $this->read_at = now();
            $this->save();
        }
    }

    public function markAsUnread(): void
    {
        $this->read_at = null;
        $this->save();
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}