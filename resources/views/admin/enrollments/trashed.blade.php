@extends('layouts.admin')
@section('title', 'Trashed Enrollments')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            @if($enrollments->count() > 0)
                <table class="table">
                    <thead><tr><th>Student</th><th>Section</th><th>Period</th><th>Deleted</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach($enrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->student->full_name }}</td>
                                <td>{{ $enrollment->section->full_name }}</td>
                                <td>{{ $enrollment->semester }} {{ $enrollment->academic_year }}</td>
                                <td>{{ $enrollment->deleted_at->format('M d, Y') }}</td>
                                <td><button onclick="restore({{ $enrollment->id }}, 'enrollments')" class="btn btn-sm btn-success"><i class="fas fa-undo"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $enrollments->links() }}
            @else
                <p class="text-center text-muted">No deleted enrollments</p>
            @endif
        </div>
    </div>
@endsection
