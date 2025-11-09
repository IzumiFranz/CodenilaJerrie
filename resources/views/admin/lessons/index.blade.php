@extends('layouts.admin')
@section('title', 'Manage Lessons')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book-reader mr-2"></i>Manage Lessons</h1>
</div>

@livewire('lesson-table')
@endsection