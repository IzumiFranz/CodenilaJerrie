@extends('layouts.admin')

@section('title', 'Trashed Courses')

@php
    $pageTitle = 'Trashed Courses';
    $pageActions = '<a href="' . route('admin.courses.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to Courses</a>';
@endphp

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Deleted Courses</h6>
        </div>
        <div class="card-body">
            @if($courses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Course Name</th>
                                <th>Max Years</th>
                                <th>Subjects</th>
                                <th>Students</th>
                                <th>Deleted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td><strong>{{ $course->course_code }}</strong></td>
                                    <td>{{ $course->course_name }}</td>
                                    <td>{{ $course->max_years }} Years</td>
                                    <td><span class="badge badge-info">{{ $course->subjects_count }}</span></td>
                                    <td><span class="badge badge-warning">{{ $course->students_count }}</span></td>
                                    <td>{{ $course->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                onclick="restoreCourse({{ $course->id }})"
                                                title="Restore">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                data-toggle="modal" 
                                                data-target="#forceDeleteModal{{ $course->id }}"
                                                title="Permanently Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        {{-- Force Delete Modal --}}
                                        <div class="modal fade" id="forceDeleteModal{{ $course->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Permanent Delete</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Permanently delete <strong>{{ $course->course_name }}</strong>?</p>
                                                        <p class="text-danger"><strong>Warning:</strong> This cannot be undone!</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.courses.force-delete', $course->id) }}" method="POST">
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
                <div class="mt-3">{{ $courses->links() }}</div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No deleted courses</p>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-primary">Back to Courses</a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
function restoreCourse(id) {
    if (confirm('Restore this course?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/courses/${id}/restore`;
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush