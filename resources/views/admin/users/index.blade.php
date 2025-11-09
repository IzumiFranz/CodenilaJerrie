@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users mr-2"></i>Manage Users</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.trashed') }}" class="btn btn-warning btn-sm">
            <i class="fas fa-trash-restore mr-1"></i> Trash
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-plus mr-1"></i> Add User
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{ route('admin.users.create') }}">
                    <i class="fas fa-user-plus mr-2"></i> Single User
                </a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#bulkUploadModal">
                    <i class="fas fa-file-upload mr-2"></i> Bulk Upload
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Username or email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
@livewire('user-table')

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Upload Users</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Upload a CSV file with user data. Download the template below to see the required format.
                    </div>

                    <div class="form-group">
                        <label class="form-label">Select Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="instructor">Instructor</option>
                            <option value="student">Student</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <small class="form-text text-muted">Maximum file size: 5MB</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Download Template</label>
                        <div class="btn-group btn-block">
                            <a href="{{ route('admin.users.download-template', ['role' => 'admin']) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download mr-1"></i> Admin Template
                            </a>
                            <a href="{{ route('admin.users.download-template', ['role' => 'instructor']) }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download mr-1"></i> Instructor Template
                            </a>
                            <a href="{{ route('admin.users.download-template', ['role' => 'student']) }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-download mr-1"></i> Student Template
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload mr-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#usersTable').DataTable({
        pageLength: 20,
        order: [[1, 'desc']],
        columnDefs: [
            { orderable: false, targets: [0, 7] }
        ]
    });
});
</script>
@endpush