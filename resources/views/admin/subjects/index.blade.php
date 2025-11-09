@extends('layouts.admin')
@section('title', 'Manage Subjects')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book-open mr-2"></i>Manage Subjects</h1>
    <div class="btn-group">
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Add Subject
        </a>
        <a href="{{ route('admin.subjects.trashed') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash-restore mr-1"></i> Trash
        </a>
    </div>
</div>

@livewire('subject-table')
@endsection