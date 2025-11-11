<?php

namespace App\Livewire;

use App\Models\Section;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class SectionTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $courseId = '';
    public $yearLevel = '';
    public $perPage = 20;

    protected $queryString = ['search', 'courseId', 'yearLevel'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCourseId()
    {
        $this->resetPage();
    }

    public function updatingYearLevel()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Section::with('course');

        if ($this->search) {
            $query->where('section_name', 'like', "%{$this->search}%");
        }

        if ($this->courseId) {
            $query->where('course_id', $this->courseId);
        }

        if ($this->yearLevel) {
            $query->where('year_level', $this->yearLevel);
        }

        $sections = $query->orderBy('course_id')
            ->orderBy('year_level')
            ->orderBy('section_name')
            ->paginate($this->perPage);

        $courses = Course::where('is_active', true)->get();

        return view('livewire.section-table', compact('sections', 'courses'));
    }
}
