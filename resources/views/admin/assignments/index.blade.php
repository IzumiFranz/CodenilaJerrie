@extends('layouts.admin')

@section('title', 'Teaching Assignments')

@php
    $pageTitle = 'Teaching Assignments';
    $pageActions = '
        <a href="' . route('admin.assignments.create') . '" class="btn btn-primary btn-sm mr-2">
            <i class="fas fa-plus"></i> Create Assignment
        </a>
        <a href="' . route('admin.assignments.trashed') . '" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash"></i> Trash
        </a>
    ';
@endphp

@section('content')
    @livewire('assignment-table')
@endsection