@extends('layouts.admin')
@section('title', 'Trashed Assignments')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            @if($assignments->count() > 0)
                <table class="table">
                    <thead><tr><th>Instructor</th><th>Subject</th><th>Section</th><th>Deleted</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                            <tr>
                                <td>{{ $assignment->instructor->full_name }}</td>
                                <td>{{ $assignment->subject->subject_code }}</td>
                                <td>{{ $assignment->section->full_name }}</td>
                                <td>{{ $assignment->deleted_at->format('M d, Y') }}</td>
                                <td><button onclick="restore({{ $assignment->id }}, 'assignments')" class="btn btn-sm btn-success"><i class="fas fa-undo"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $assignments->links() }}
            @else
                <p class="text-center text-muted">No deleted assignments</p>
            @endif
        </div>
    </div>
@endsection