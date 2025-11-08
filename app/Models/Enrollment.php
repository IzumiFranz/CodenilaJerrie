<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'section_id',
        'academic_year',
        'semester',
        'status',
        'enrollment_date',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
        ];
    }

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'enrolled';
    }

    public function drop(): void
    {
        $this->status = 'dropped';
        $this->save();
    }

    public function complete(): void
    {
        $this->status = 'completed';
        $this->save();
    }
}
