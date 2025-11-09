@extends('layouts.admin')
@section('title', 'Create Notification')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-bell mr-2"></i>Create Notification</h1>
    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.notifications.store') }}" method="POST" data-validate>
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Recipient Type <span class="text-danger">*</span></label>
                        <select name="recipient_type" id="recipient_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="single" {{ old('recipient_type') == 'single' ? 'selected' : '' }}>Single User</option>
                            <option value="role" {{ old('recipient_type') == 'role' ? 'selected' : '' }}>By Role</option>
                            <option value="all" {{ old('recipient_type') == 'all' ? 'selected' : '' }}>All Users</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Notification Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="info" {{ old('type', 'info') == 'info' ? 'selected' : '' }}>Info</option>
                            <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="danger" {{ old('type') == 'danger' ? 'selected' : '' }}>Danger</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="single_user_field" style="display: none;">
                <div class="form-group">
                    <label class="form-label">Select User</label>
                    <input type="text" class="form-control" placeholder="Search user by username or email...">
                    <input type="hidden" name="user_id">
                </div>
            </div>

            <div id="role_field" style="display: none;">
                <div class="form-group">
                    <label class="form-label">Select Role</label>
                    <select name="role" class="form-control">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="instructor">Instructor</option>
                        <option value="student">Student</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Message <span class="text-danger">*</span></label>
                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5" required>{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Action URL (Optional)</label>
                <input type="url" name="action_url" class="form-control" value="{{ old('action_url') }}" placeholder="https://...">
                <small class="form-text text-muted">Users will be redirected to this URL when they click the notification</small>
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane mr-1"></i> Send Notification
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#recipient_type').on('change', function() {
        const type = $(this).val();
        $('#single_user_field, #role_field').hide();
        
        if (type === 'single') {
            $('#single_user_field').show();
        } else if (type === 'role') {
            $('#role_field').show();
        }
    }).trigger('change');
});
</script>
@endpush
