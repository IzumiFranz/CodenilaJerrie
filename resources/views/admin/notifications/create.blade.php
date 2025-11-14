@extends('layouts.admin')

@section('title', 'Send Notification')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-paper-plane mr-2"></i>Send Notification</h1>
    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-paper-plane"></i> Compose Notification
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.notifications.store') }}" method="POST">
                    @csrf

                    <!-- Recipient Type -->
                    <div class="form-group">
                        <label for="recipient_type">Send To <span class="text-danger">*</span></label>
                        <select name="recipient_type" id="recipient_type" 
                            class="form-control @error('recipient_type') is-invalid @enderror" 
                            required onchange="toggleRecipientFields()">
                            <option value="">Select recipient type...</option>
                            <option value="single" {{ old('recipient_type') == 'single' ? 'selected' : '' }}>Single User</option>
                            <option value="role" {{ old('recipient_type') == 'role' ? 'selected' : '' }}>All Users by Role</option>
                            <option value="all" {{ old('recipient_type') == 'all' ? 'selected' : '' }}>All Users</option>
                        </select>
                        @error('recipient_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Single User Field -->
                    <div class="form-group" id="single_user_field" style="display: none;">
                        <label for="user_id">Select User <span class="text-danger">*</span></label>
                        <input type="text" id="user_search" class="form-control mb-2" placeholder="Search by username or email...">
                        <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" size="5">
                            <option value="">Select a user...</option>
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Search and select a specific user</small>
                    </div>

                    <!-- Role Field -->
                    <div class="form-group" id="role_field" style="display: none;">
                        <label for="role">Select Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
                            <option value="">Select role...</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                            <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>Instructors</option>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Students</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notification Type -->
                    <div class="form-group">
                        <label for="type">Notification Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" 
                            class="form-control @error('type') is-invalid @enderror" 
                            required onchange="updatePreview()">
                            <option value="">Select type...</option>
                            <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Info (Blue)</option>
                            <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>Success (Green)</option>
                            <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Warning (Yellow)</option>
                            <option value="danger" {{ old('type') == 'danger' ? 'selected' : '' }}>Danger (Red)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" 
                            class="form-control @error('title') is-invalid @enderror" 
                            placeholder="Enter notification title..."
                            value="{{ old('title') }}"
                            maxlength="255"
                            required
                            onkeyup="updatePreview()">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label for="message">Message <span class="text-danger">*</span></label>
                        <textarea name="message" id="message" 
                            class="form-control @error('message') is-invalid @enderror" 
                            rows="5"
                            placeholder="Enter notification message..."
                            maxlength="1000"
                            required
                            onkeyup="updatePreview()">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <span id="char_count">0</span>/1000 characters
                        </small>
                    </div>

                    <!-- Action URL (Optional) -->
                    <div class="form-group">
                        <label for="action_url">Action URL (Optional)</label>
                        <input type="url" name="action_url" id="action_url" 
                            class="form-control @error('action_url') is-invalid @enderror" 
                            placeholder="https://example.com/action"
                            value="{{ old('action_url') }}"
                            maxlength="500">
                        @error('action_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Optional link for users to take action</small>
                    </div>

                    <hr>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Preview -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Preview</h6>
            </div>
            <div class="card-body">
                <div id="notification_preview" class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-info-circle"></i> <strong id="preview_title">Notification Title</strong></h6>
                    <p id="preview_message" class="mb-0">Notification message will appear here...</p>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-info-circle"></i> Information
                </h6>
            </div>
            <div class="card-body">
                <h6 class="text-primary">Notification Types:</h6>
                <ul class="small">
                    <li><strong>Info:</strong> General information</li>
                    <li><strong>Success:</strong> Positive updates</li>
                    <li><strong>Warning:</strong> Important notices</li>
                    <li><strong>Danger:</strong> Critical alerts</li>
                </ul>

                <hr>

                <h6 class="text-primary">Recipient Options:</h6>
                <ul class="small mb-0">
                    <li><strong>Single User:</strong> Send to one specific user</li>
                    <li><strong>By Role:</strong> Send to all users with specific role</li>
                    <li><strong>All Users:</strong> Broadcast to everyone</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleRecipientFields() {
    const recipientType = document.getElementById('recipient_type').value;
    
    document.getElementById('single_user_field').style.display = recipientType === 'single' ? 'block' : 'none';
    document.getElementById('role_field').style.display = recipientType === 'role' ? 'block' : 'none';
}

function updatePreview() {
    const type = document.getElementById('type').value || 'info';
    const title = document.getElementById('title').value || 'Notification Title';
    const message = document.getElementById('message').value || 'Notification message will appear here...';
    
    const preview = document.getElementById('notification_preview');
    preview.className = `alert alert-${type}`;
    
    let icon = 'fa-info-circle';
    if (type === 'success') icon = 'fa-check-circle';
    else if (type === 'warning') icon = 'fa-exclamation-triangle';
    else if (type === 'danger') icon = 'fa-exclamation-circle';
    
    document.getElementById('preview_title').innerHTML = `<i class="fas ${icon}"></i> ${title}`;
    document.getElementById('preview_message').textContent = message;
    
    // Update character count
    document.getElementById('char_count').textContent = message.length;
}

// Simple user search functionality
document.getElementById('user_search')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const options = document.querySelectorAll('#user_id option');
    
    options.forEach(option => {
        if (option.value === '') return;
        const text = option.textContent.toLowerCase();
        option.style.display = text.includes(searchTerm) ? 'block' : 'none';
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRecipientFields();
    updatePreview();
    
    // Load users via AJAX (you can implement this)
    // For now, you might want to pass users from controller
});
</script>
@endpush
@endsection