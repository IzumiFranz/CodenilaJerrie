@extends('layouts.admin')

@section('title', 'Users Management')

@php
    $pageTitle = 'Users Management';
    $pageActions = '
        <a href="' . route('admin.users.create') . '" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add User
        </a>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#bulkUploadModal">
            <i class="fas fa-upload"></i> Bulk Upload
        </button>
        <a href="' . route('admin.export.users') . '" class="btn btn-success">
            <i class="fas fa-download"></i> Export All
        </a>
        <a href="{{ route('admin.users.trashed') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash mr-1"></i>Trash
        </a>';
@endphp
@section('content')

{{-- Bulk Actions Bar --}}
@include('admin.partials.bulk-actions-bar', [
    'model' => 'User',
    'actions' => ['delete', 'status', 'export', 'notify']
])

<!-- Search and Filter Card -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Search by username or email..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="role">Filter by Role</label>
                        <select name="role" id="role" class="form-control">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Filter by Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i>Search
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
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Users List ({{ $users->total() }} total)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable">
                <thead>
                    <tr>
                    <th width="30"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><input type="checkbox" class="form-check-input item-checkbox" value="{{ $user->id }}"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->profile_picture)
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" class="rounded-circle mr-2" style="width: 40px; height: 40px; object-fit: cover;" alt="Avatar">
                                    @else
                                        <div class="rounded-circle bg-primary text-white mr-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($user->username, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $user->username }}</strong>
                                        @if($user->profile)
                                            <br><small class="text-muted">{{ $user->profile->first_name }} {{ $user->profile->last_name }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'instructor' ? 'success' : 'info') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'secondary' : 'danger') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-{{ $user->status == 'active' ? 'secondary' : 'success' }}" title="Toggle Status">
                                                <i class="fas fa-toggle-{{ $user->status == 'active' ? 'on' : 'off' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this user?')" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="mt-3">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.users.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-upload mr-2"></i>Bulk Upload Users
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Instructions -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Instructions:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Download the CSV template for your desired role</li>
                            <li>Fill in the user information</li>
                            <li>Upload the completed CSV file</li>
                            <li>Users will receive their credentials via email (if enabled)</li>
                        </ol>
                    </div>

                    <!-- Select Role -->
                    <div class="form-group">
                        <label for="bulk_role">Select Role <span class="text-danger">*</span></label>
                        <select name="role" id="bulk_role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">-- Select Role --</option>
                            <option value="admin">Admin</option>
                            <option value="instructor">Instructor</option>
                            <option value="student">Student</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Select the role for all users in the CSV file
                        </small>
                    </div>

                    <!-- Download Template -->
                    <div class="form-group">
                        <label>Download CSV Template</label>
                        <div class="btn-group btn-group-sm d-block" role="group">
                            <a href="{{ route('admin.users.download-template', ['role' => 'admin']) }}" 
                               class="btn btn-outline-danger">
                                <i class="fas fa-download mr-1"></i>Admin Template
                            </a>
                            <a href="{{ route('admin.users.download-template', ['role' => 'instructor']) }}" 
                               class="btn btn-outline-success">
                                <i class="fas fa-download mr-1"></i>Instructor Template
                            </a>
                            <a href="{{ route('admin.users.download-template', ['role' => 'student']) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-download mr-1"></i>Student Template
                            </a>
                        </div>
                        <small class="form-text text-muted">
                            Download the appropriate template based on the role you're creating
                        </small>
                    </div>

                    <!-- Upload CSV File -->
                    <div class="form-group">
                        <label for="csv_file">Upload CSV File <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" 
                                   name="csv_file" 
                                   id="csv_file" 
                                   class="custom-file-input @error('csv_file') is-invalid @enderror" 
                                   accept=".csv,.txt" 
                                   required>
                            <label class="custom-file-label" for="csv_file">Choose file...</label>
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                            Maximum file size: 5MB. Accepted formats: CSV, TXT
                        </small>
                    </div>

                    <!-- Email Option -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="send_email_bulk" 
                                   name="send_email" 
                                   value="1" 
                                   checked>
                            <label class="custom-control-label" for="send_email_bulk">
                                <strong>Send credentials summary via email</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted ml-4">
                            <i class="fas fa-envelope mr-1"></i>
                            You'll receive a CSV file with all user credentials attached to an email
                        </small>
                    </div>

                    <!-- Warning -->
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Important:</strong> Make sure all email addresses in the CSV are valid. 
                        Invalid entries will be skipped and reported.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload mr-1"></i>Upload & Create Users
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bulk actions checkboxes
    if (document.querySelector('.item-checkbox')) {
        console.log('Bulk actions initialized');
    }

    // Auto-submit filters
    document.querySelectorAll('select[name="role"], select[name="status"]').forEach(function(el){
        el.addEventListener('change', function(){
            this.closest('form').submit();
        });
    });

    // jQuery code
    $(function() {
        // Update custom file input label
        $('#csv_file').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert-dismissible').fadeOut('slow');
        }, 5000);

        // Form validation
        $('form').on('submit', function(e) {
            let csvFile = $('#csv_file').val();
            let role = $('#bulk_role').val();

            if (!csvFile) {
                e.preventDefault();
                alert('Please select a CSV file to upload');
                return false;
            }

            if (!role) {
                e.preventDefault();
                alert('Please select a role');
                return false;
            }
        });
    });
});
</script>
@endpush
