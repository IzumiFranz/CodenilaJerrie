@extends('layouts.admin')

@section('title', 'Subjects Management')

@php
    $pageTitle = 'Subjects Management';
    $pageActions = '
        <a href="' . route('admin.subjects.create') . '" class="btn btn-primary btn-sm mr-2">
            <i class="fas fa-plus"></i> Add Subject
        </a>
        <a href="' . route('admin.subjects.trashed') . '" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash"></i> Trash
        </a>
    ';
@endphp

@section('content')
    @livewire('subject-table')
@endsection