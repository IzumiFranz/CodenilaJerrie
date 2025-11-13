
@extends('layouts.admin')

@section('title', 'Sections Management')

@php
    $pageTitle = 'Sections Management';
    $pageActions = '
        <a href="' . route('admin.sections.create') . '" class="btn btn-primary btn-sm mr-2">
            <i class="fas fa-plus"></i> Add Section
        </a>
        <a href="' . route('admin.sections.trashed') . '" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash"></i> Trash
        </a>
    ';
@endphp

@section('content')
    @livewire('section-table')
@endsection