
@extends('layouts.admin')

@section('title', 'Sections Management')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chalkboard mr-2"></i>Sections Management</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.sections.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Add Section
        </a>
        <a href="{{ route('admin.sections.trashed') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash mr-1"></i> Trash
        </a>
    </div>
</div>
    @livewire('section-table')
@endsection