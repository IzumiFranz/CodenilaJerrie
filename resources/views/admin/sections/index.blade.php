@extends('layouts.admin')
@section('title', 'Manage Sections')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users-class mr-2"></i>Manage Sections</h1>
    <div class="btn-group">
        <a href="{{ route('admin.sections.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Add Section
        </a>
        <a href="{{ route('admin.sections.trashed') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash-restore mr-1"></i> Trash
        </a>
    </div>
</div>

@livewire('section-table')
@endsection