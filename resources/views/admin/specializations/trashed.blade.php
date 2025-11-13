@extends('layouts.admin')
@section('title', 'Trashed Specializations')
@section('content')
    <div class="card shadow">
        <div class="card-body">
            @if($specializations->count() > 0)
                <table class="table">
                    <thead><tr><th>Code</th><th>Name</th><th>Deleted</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach($specializations as $spec)
                            <tr>
                                <td>{{ $spec->code }}</td>
                                <td>{{ $spec->name }}</td>
                                <td>{{ $spec->deleted_at->format('M d, Y') }}</td>
                                <td><button onclick="restore({{ $spec->id }}, 'specializations')" class="btn btn-sm btn-success"><i class="fas fa-undo"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $specializations->links() }}
            @else
                <p class="text-center text-muted">No deleted specializations</p>
            @endif
        </div>
    </div>
@endsection