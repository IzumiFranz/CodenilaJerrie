<?php

namespace App\Livewire;

use App\Models\InstructorSubjectSection;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class AssignmentTable extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $courseId = '';
    public $academicYear = '';
    public $semester = '';
    public $perPage = 20;
    
    protected $queryString = ['search', 'courseId', 'academicYear', 'semester'];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingCourseId()
    {
        $this->resetPage();
    }
    
    public function deleteAssignment($assignmentId)
    {
        $assignment = InstructorSubjectSection::findOrFail($assignmentId);
        $assignment->delete();
        
        session()->flash('success', 'Assignment deleted successfully.');
        $this->dispatch('assignment-updated');
    }
    
    public function render()
    {
        $query = InstructorSubjectSection::with(['instructor.user', 'subject', 'section.course']);
        
        if ($this->search) {
            $query->whereHas('instructor', function($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('employee_id', 'like', "%{$this->search}%");
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
        
        $assignments = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $courses = Course::where('is_active', true)->get();
        
        return view('livewire.assignment-table', compact('assignments', 'courses'));
    }
}
