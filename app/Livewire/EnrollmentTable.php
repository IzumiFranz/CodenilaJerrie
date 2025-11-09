<?php

namespace App\Livewire;

use App\Models\Enrollment;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class EnrollmentTable extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $courseId = '';
    public $academicYear = '';
    public $semester = '';
    public $status = '';
    public $perPage = 20;
    
    protected $queryString = ['search', 'courseId', 'academicYear', 'semester', 'status'];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingCourseId()
    {
        $this->resetPage();
    }
    
    public function updatingAcademicYear()
    {
        $this->resetPage();
    }
    
    public function updatingSemester()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
    }
    
    public function dropEnrollment($enrollmentId)
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $enrollment->drop();
        
        session()->flash('success', 'Student dropped successfully.');
        $this->dispatch('enrollment-updated');
    }
    
    public function render()
    {
        $query = Enrollment::with(['student.user', 'student.course', 'section.course']);
        
        if ($this->search) {
            $query->whereHas('student', function($q) {
                $q->where('student_number', 'like', "%{$this->search}%")
                  ->orWhere('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%");
            });
        }
        
        if ($this->courseId) {
            $query->whereHas('section', function($q) {
                $q->where('course_id', $this->courseId);
            });
        }
        
        if ($this->academicYear) {
            $query->where('academic_year', $this->academicYear);
        }
        
        if ($this->semester) {
            $query->where('semester', $this->semester);
        }
        
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        $enrollments = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $courses = Course::where('is_active', true)->get();
        
        return view('livewire.enrollment-table', compact('enrollments', 'courses'));
    }
}