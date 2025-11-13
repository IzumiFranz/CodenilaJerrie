<div class="modal fade" id="bulkUploadModal" tabindex="-1" role="dialog" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bulkUploadModalLabel">
                    <i class="fas fa-upload mr-2"></i>Bulk Upload Users
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('admin.users.bulk-upload') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                @csrf
                
                <div class="modal-body">
                    {{-- Instructions --}}
                    <div class="alert alert-info">
                        <h6 class="font-weight-bold mb-2">
                            <i class="fas fa-info-circle mr-1"></i>Instructions
                        </h6>
                        <ol class="mb-0 pl-3">
                            <li>Select the user role you want to create</li>
                            <li>Download the CSV template for that role</li>
                            <li>Fill in the required information</li>
                            <li>Upload the completed CSV file</li>
                            <li>Review and submit</li>
                        </ol>
                    </div>

                    {{-- Role Selection --}}
                    <div class="form-group">
                        <label for="bulk_role">User Role <span class="text-danger">*</span></label>
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
                            <i class="fas fa-lightbulb mr-1"></i>
                            Each role has different required fields
                        </small>
                    </div>

                    {{-- Template Download Section --}}
                    <div class="card bg-light mb-3" id="templateSection" style="display: none;">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary mb-2">
                                <i class="fas fa-download mr-1"></i>Step 1: Download Template
                            </h6>
                            <p class="mb-2">Download the CSV template for <strong id="selectedRole">selected role</strong>:</p>
                            <a href="#" id="downloadTemplateBtn" class="btn btn-success btn-sm">
                                <i class="fas fa-file-csv mr-1"></i>Download CSV Template
                            </a>
                            
                            {{-- Role-specific field information --}}
                            <div class="mt-3">
                                <p class="mb-1 font-weight-bold">Required fields for this role:</p>
                                <div id="requiredFieldsInfo"></div>
                            </div>
                        </div>
                    </div>

                    {{-- File Upload Section --}}
                    <div class="form-group">
                        <label for="csv_file">
                            <i class="fas fa-upload mr-1"></i>Step 2: Upload CSV File <span class="text-danger">*</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" 
                                   name="csv_file" 
                                   id="csv_file" 
                                   class="custom-file-input @error('csv_file') is-invalid @enderror" 
                                   accept=".csv" 
                                   required>
                            <label class="custom-file-label" for="csv_file">Choose file...</label>
                            @error('csv_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Maximum file size: 5MB. Accepted format: CSV only
                        </small>
                    </div>

                    {{-- File Preview --}}
                    <div id="filePreview" class="alert alert-success" style="display: none;">
                        <h6 class="font-weight-bold mb-2">
                            <i class="fas fa-check-circle mr-1"></i>File Selected
                        </h6>
                        <p class="mb-1"><strong>Filename:</strong> <span id="fileName"></span></p>
                        <p class="mb-0"><strong>Size:</strong> <span id="fileSize"></span></p>
                    </div>

                    {{-- Email Notification Option --}}
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-envelope mr-1"></i>Email Notifications
                            </h6>
                            
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="bulk_send_email" 
                                       name="send_email" 
                                       value="1" 
                                       checked>
                                <label class="custom-control-label" for="bulk_send_email">
                                    <strong>Send welcome emails to all users</strong>
                                </label>
                            </div>
                            
                            <small class="form-text text-muted ml-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                Each user will receive an email with their login credentials. 
                                A summary CSV with all credentials will also be sent to your email.
                            </small>
                        </div>
                    </div>

                    {{-- Important Notes --}}
                    <div class="alert alert-warning mt-3 mb-0">
                        <h6 class="font-weight-bold mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Important Notes
                        </h6>
                        <ul class="mb-0 pl-3">
                            <li>Usernames and passwords will be automatically generated</li>
                            <li>All users will be required to change their password on first login</li>
                            <li>Duplicate emails will be skipped</li>
                            <li>Invalid data rows will be reported after upload</li>
                            <li>Processing may take a few minutes for large files</li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBulkUpload" disabled>
                        <i class="fas fa-upload mr-1"></i>Upload Users
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Role field information
    const roleFields = {
        admin: {
            required: ['first_name', 'last_name', 'email'],
            optional: ['middle_name', 'phone', 'position', 'office']
        },
        instructor: {
            required: ['first_name', 'last_name', 'email', 'employee_id'],
            optional: ['middle_name', 'phone', 'specialization_id', 'department', 'hire_date']
        },
        student: {
            required: ['first_name', 'last_name', 'email', 'student_number'],
            optional: ['middle_name', 'phone', 'course_id', 'year_level', 'address', 'admission_date']
        }
    };

    // Handle role selection
    $('#bulk_role').on('change', function() {
        const role = $(this).val();
        
        if (role) {
            $('#templateSection').slideDown();
            $('#selectedRole').text(role);
            
            // Update download link
            $('#downloadTemplateBtn').attr('href', "{{ route('admin.users.download-template') }}?role=" + role);
            
            // Show required fields
            const fields = roleFields[role];
            let fieldsHtml = '<ul class="mb-0">';
            fieldsHtml += '<li><strong>Required:</strong> ' + fields.required.join(', ') + '</li>';
            fieldsHtml += '<li><strong>Optional:</strong> ' + fields.optional.join(', ') + '</li>';
            fieldsHtml += '</ul>';
            $('#requiredFieldsInfo').html(fieldsHtml);
        } else {
            $('#templateSection').slideUp();
            $('#filePreview').slideUp();
            $('#submitBulkUpload').prop('disabled', true);
        }
    });

    // Handle file selection
    $('#csv_file').on('change', function() {
        const file = this.files[0];
        const role = $('#bulk_role').val();
        
        if (file) {
            // Update custom file input label
            $(this).next('.custom-file-label').text(file.name);
            
            // Validate file type
            if (!file.name.endsWith('.csv')) {
                alert('Please upload a CSV file only');
                $(this).val('');
                $(this).next('.custom-file-label').text('Choose file...');
                $('#filePreview').slideUp();
                $('#submitBulkUpload').prop('disabled', true);
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must not exceed 5MB');
                $(this).val('');
                $(this).next('.custom-file-label').text('Choose file...');
                $('#filePreview').slideUp();
                $('#submitBulkUpload').prop('disabled', true);
                return;
            }
            
            // Show file preview
            $('#fileName').text(file.name);
            $('#fileSize').text((file.size / 1024).toFixed(2) + ' KB');
            $('#filePreview').slideDown();
            
            // Enable submit button if role is selected
            if (role) {
                $('#submitBulkUpload').prop('disabled', false);
            }
        } else {
            $(this).next('.custom-file-label').text('Choose file...');
            $('#filePreview').slideUp();
            $('#submitBulkUpload').prop('disabled', true);
        }
    });

    // Handle form submission
    $('#bulkUploadForm').on('submit', function(e) {
        const role = $('#bulk_role').val();
        const file = $('#csv_file')[0].files[0];
        
        if (!role) {
            e.preventDefault();
            alert('Please select a user role');
            $('#bulk_role').focus();
            return false;
        }
        
        if (!file) {
            e.preventDefault();
            alert('Please select a CSV file to upload');
            $('#csv_file').focus();
            return false;
        }
        
        // Show loading state
        $('#submitBulkUpload').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Uploading...');
        
        // Form will submit normally
        return true;
    });

    // Reset modal on close
    $('#bulkUploadModal').on('hidden.bs.modal', function() {
        $('#bulkUploadForm')[0].reset();
        $('#csv_file').next('.custom-file-label').text('Choose file...');
        $('#templateSection').hide();
        $('#filePreview').hide();
        $('#submitBulkUpload').prop('disabled', true).html('<i class="fas fa-upload mr-1"></i>Upload Users');
    });
});
</script>
@endpush