<?php

namespace App\Livewire;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogTable extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $action = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 50;
    
    protected $queryString = ['search', 'action', 'dateFrom', 'dateTo'];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingAction()
    {
        $this->resetPage();
    }
    
    public function updatingDateFrom()
    {
        $this->resetPage();
    }
    
    public function updatingDateTo()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $query = AuditLog::with('user');
        
        if ($this->search) {
            $query->where('action', 'like', "%{$this->search}%")
                  ->orWhere('model_type', 'like', "%{$this->search}%")
                  ->orWhereHas('user', function($q) {
                      $q->where('username', 'like', "%{$this->search}%");
                  });
        }
        
        if ($this->action) {
            $query->where('action', $this->action);
        }
        
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        
        return view('livewire.audit-log-table', compact('logs', 'actions'));
    }
}