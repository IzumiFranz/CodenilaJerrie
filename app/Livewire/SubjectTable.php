<?php

namespace App\Livewire;

use App\Models\Subject;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class SubjectTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $courseFilter = '';
    public $yearLevelFilter = '';
    public $statusFilter = '';
    public $sortBy = 'subject_name';
    public $sortOrder = 'asc';
    public $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'courseFilter' => ['except' => ''],
        'yearLevelFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCourseFilter()
    {
        $this->resetPage();
    }

    public function updatingYearLevelFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortOrder = 'asc';
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->courseFilter = '';
        $this->yearLevelFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Subject::with(['course', 'specialization']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('subject_code', 'like', "%{$this->search}%")
                  ->orWhere('subject_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->courseFilter) {
            $query->where('course_id', $this->courseFilter);
        }

        if ($this->yearLevelFilter) {
            $query->where('year_level', $this->yearLevelFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $query->orderBy($this->sortBy, $this->sortOrder);

        $subjects = $query->paginate($this->perPage);
        $courses = Course::where('is_active', true)->get();

        return view('livewire.subject-table', [
            'subjects' => $subjects,
            'courses' => $courses,
        ]);
    }
}