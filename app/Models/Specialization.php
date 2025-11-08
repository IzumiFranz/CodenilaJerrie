<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function instructors()
    {
        return $this->hasMany(Instructor::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    // Helper Methods
    public function getQualifiedInstructorsCount(): int
    {
        return $this->instructors()->whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->count();
    }
}