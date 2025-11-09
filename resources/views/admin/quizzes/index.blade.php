@extends('layouts.admin')
@section('title', 'Manage Quizzes')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clipboard-list mr-2"></i>Manage Quizzes</h1>
</div>

@livewire('quiz-table')
@endsection