<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorSubjectSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'instructor_subject_section';

    protected $fillable = [
        'instructor_id',
        'subject_id',
        'section_id',
        'academic_year',
        'semester',
    ];

    // Relationships
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Helper Methods
    public function getAssignmentNameAttribute(): string
    {
        return $this->instructor->full_name . ' - ' . 
               $this->subject->subject_name . ' - ' . 
               $this->section->full_name;
    }

    public function getEnrolledStudentsCount(): int
    {
        return $this->section->getEnrolledStudentsCount($this->academic_year, $this->semester);
    }
}