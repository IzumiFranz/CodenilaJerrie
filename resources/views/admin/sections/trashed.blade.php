@extends('layouts.admin')
@section('title', 'Trashed Sections')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-trash-restore mr-2"></i>Trashed Sections</h1>
    <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>
    <div class="card shadow mb-4">
        <div class="card-body">
            @if($sections->count() > 0)
                <table class="table table-bordered">
                    <thead><tr><th>Section</th><th>Course</th><th>Year</th><th>Deleted</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach($sections as $section)
                            <tr>
                                <td>{{ $section->section_name }}</td>
                                <td>{{ $section->course->course_code }}</td>
                                <td>Year {{ $section->year_level }}</td>
                                <td>{{ $section->deleted_at->format('M d, Y') }}</td>
                                <td>
                                    <button onclick="restore({{ $section->id }}, 'sections')" class="btn btn-sm btn-success"><i class="fas fa-undo"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $sections->links() }}
            @else
                <p class="text-center text-muted">No deleted sections</p>
            @endif
        </div>
    </div>
@endsection
@push('scripts')
<script>
function restore(id, type) {
    if (confirm('Restore?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/${type}/${id}/restore`;
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush