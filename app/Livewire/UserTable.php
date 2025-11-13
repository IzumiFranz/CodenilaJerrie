<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortOrder' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
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
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->sortBy = 'created_at';
        $this->sortOrder = 'desc';
        $this->resetPage();
    }

    public function render()
    {
        $query = User::with(['admin', 'instructor', 'student']);

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('username', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhereHas('admin', function($q) {
                      $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('instructor', function($q) {
                      $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('employee_id', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('student', function($q) {
                      $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('student_number', 'like', "%{$this->search}%");
                  });
            });
        }

        // Role Filter
        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        // Status Filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        $users = $query->paginate($this->perPage);

        return view('livewire.user-table', [
            'users' => $users,
        ]);
    }
}