@extends('layouts.admin')

@section('title', 'Manage Courses')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book mr-2"></i>Manage Courses</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.courses.trashed') }}" class="btn btn-warning btn-sm">
            <i class="fas fa-trash-restore mr-1"></i> Trash
        </a>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Add Course
        </a>
    </div>
</div>

<!-- Filters (optional: can also be handled by Livewire inside the table) -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Search courses...">
            </div>
            <div class="col-md-3 mb-3">
                <select wire:model="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <select wire:model="perPage" class="form-control">
                    <option value="10">10 per page</option>
                    <option value="20">20 per page</option>
                    <option value="50">50 per page</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Livewire Courses Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            Courses List
        </h6>
        <div wire:loading class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <div class="card-body">
        @livewire('course-table')
    </div>
</div>
@endsection
