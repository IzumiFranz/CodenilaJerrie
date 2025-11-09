<?php

namespace App\Livewire;

use App\Models\Feedback;
use Livewire\Component;
use Livewire\WithPagination;

class FeedbackTable extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $search = '';
    public $status = '';
    public $rating = '';
    public $perPage = 20;
    
    protected $queryString = ['search', 'status', 'rating'];
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
    }
    
    public function updatingRating()
    {
        $this->resetPage();
    }
    
    public function markAsResolved($feedbackId)
    {
        $feedback = Feedback::findOrFail($feedbackId);
        $feedback->update(['status' => 'resolved']);
        
        session()->flash('success', 'Feedback marked as resolved.');
        $this->dispatch('feedback-updated');
    }
    
    public function render()
    {
        $query = Feedback::with('user');
        
        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('username', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })->orWhere('comment', 'like', "%{$this->search}%");
        }
        
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        if ($this->rating) {
            $query->where('rating', $this->rating);
        }
        
        $feedback = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        
        return view('livewire.feedback-table', compact('feedback'));
    }
}