<?php

namespace App\Livewire;

use Livewire\Component;

class SearchFilter extends Component
{
    public $search = '';
    public $filters = [];
    public $placeholder = 'Search...';

    public function updatedSearch()
    {
        $this->dispatch('search-updated', search: $this->search);
    }

    public function updatedFilters()
    {
        $this->dispatch('filters-updated', filters: $this->filters);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filters = [];
        $this->dispatch('filters-cleared');
    }

    public function render()
    {
        return view('livewire.search-filter');
    }
}