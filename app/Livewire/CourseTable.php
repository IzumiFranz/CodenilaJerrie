<?php

namespace App\Livewire;

use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class CourseTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'course_name';
    public $sortOrder = 'asc';
    public $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
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
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Course::withCount(['subjects', 'sections', 'students']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('course_code', 'like', "%{$this->search}%")
                  ->orWhere('course_name', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $query->orderBy($this->sortBy, $this->sortOrder);

        $courses = $query->paginate($this->perPage);

        return view('livewire.course-table', [
            'courses' => $courses,
        ]);
    }
}