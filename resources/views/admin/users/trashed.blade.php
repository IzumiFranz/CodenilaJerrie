@extends('layouts.admin')
@section('title', 'Trashed Users')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-trash-restore mr-2"></i>Trashed Users</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to Users
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">Deleted Users</h6>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            These users have been deleted. You can restore or permanently delete them.
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Deleted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <@if($user->role === 'admin')
                                            <span class="badge badge-primary">Admin</span>
                                        @elseif($user->role === 'instructor')
                                            <span class="badge badge-success">Instructor</span>
                                        @else
                                            <span class="badge badge-info">Student</span>
                                        @endif
                        </td>
                        <td>{{ $user->deleted_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="Restore">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#forceDeleteModal{{ $user->id }}"title="Permanently Delete">
                                            <i class="fas fa-trash-alt"></i>
                                </button>
                                {{-- Force Delete Modal --}}
                                    <div class="modal fade" id="forceDeleteModal{{ $user->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Confirm Permanent Delete</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">
                                                        <span>&times;</span>
                                                        </button>
                                                </div>
                                                <div class="modal-body">
                                                 <p>Are you sure you want to permanently delete <strong>{{ $user->full_name }}</strong>?</p>
                                                 <p class="text-danger"><strong>Warning:</strong> This action CANNOT be undone! All related data will be permanently deleted.</p>
                                                </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Permanently Delete</button>
                                                </form>
                                            </div>
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

        <div class="mt-3">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No deleted users found</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('scripts')
<script>
    function restoreUser(userId) {
        if (confirm('Are you sure you want to restore this user?')) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/users/${userId}/restore`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush