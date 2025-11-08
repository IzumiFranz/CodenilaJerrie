<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_code',
        'course_name',
        'description',
        'max_years',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'max_years' => 'integer',
        ];
    }

    // Relationships
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Helper Methods
    public function getSubjectsByYearLevel(int $yearLevel)
    {
        return $this->subjects()->where('year_level', $yearLevel)->get();
    }

    public function getSectionsByYearLevel(int $yearLevel)
    {
        return $this->sections()->where('year_level', $yearLevel)->get();
    }

    public function getActiveStudentsCount(): int
    {
        return $this->students()->whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->count();
    }
}