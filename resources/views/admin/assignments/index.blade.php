@extends('layouts.admin')

@section('title', 'Teaching Assignments')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chalkboard-teacher mr-2"></i>Teaching Assignments</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.assignments.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Create Assignment
        </a>
        <a href="{{ route('admin.assignments.trashed') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash mr-1"></i> Trash
        </a>
    </div>
</div>

    @livewire('assignment-table')
@endsection