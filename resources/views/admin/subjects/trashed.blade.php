@extends('layouts.admin')

@section('title', 'Trashed Subjects')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-trash-restore mr-2"></i>Trashed Subjects</h1>
    <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to Subjects
    </a>
</div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Deleted Subjects</h6>
        </div>
        <div class="card-body">
            @if($subjects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Units</th>
                                <th>Deleted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjects as $subject)
                                <tr>
                                    <td><strong>{{ $subject->subject_code }}</strong></td>
                                    <td>{{ $subject->subject_name }}</td>
                                    <td>{{ $subject->course->course_code }}</td>
                                    <td>Year {{ $subject->year_level }}</td>
                                    <td>{{ $subject->units }}</td>
                                    <td>{{ $subject->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                onclick="restore({{ $subject->id }}, 'subjects')"
                                                title="Restore">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                data-toggle="modal" 
                                                data-target="#deleteModal{{ $subject->id }}"
                                                title="Permanently Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <div class="modal fade" id="deleteModal{{ $subject->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Permanent Delete</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Permanently delete <strong>{{ $subject->subject_name }}</strong>?</p>
                                                        <p class="text-danger"><strong>Warning:</strong> This cannot be undone!</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.subjects.force-delete', $subject->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete Permanently</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $subjects->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No deleted subjects</p>
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-primary">Back to Subjects</a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
function restore(id, type) {
    if (confirm('Restore this item?')) {
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