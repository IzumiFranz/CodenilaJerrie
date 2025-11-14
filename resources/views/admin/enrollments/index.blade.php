@extends('layouts.admin')

@section('title', 'Enrollments Management')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-graduate mr-2"></i>Enrollments Management</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-user-plus mr-1"></i> Enroll Student
        </a>
        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#bulkEnrollModal">
            <i class="fas fa-upload mr-1"></i> Bulk Enroll
        </button>
        <a href="{{ route('admin.enrollments.trashed') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash mr-1"></i> Trash
        </a>
    </div>
</div>
    @livewire('enrollment-table')

    {{-- Bulk Enroll Modal --}}
    <div class="modal fade" id="bulkEnrollModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.enrollments.bulk-enroll') }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      data-confirm="Are you sure you want to bulk enroll students? This will create enrollments for all valid rows in the CSV file."
                      id="bulkEnrollForm">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-upload"></i> Bulk Enroll Students
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Instructions:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Download the CSV template below</li>
                                <li>Fill in student_number and section_id columns</li>
                                <li>Select academic year and semester</li>
                                <li>Upload the completed CSV file</li>
                                <li>System will validate and enroll students</li>
                            </ol>
                        </div>

                        {{-- Template Download --}}
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-success mb-2">
                                    <i class="fas fa-download mr-1"></i>Step 1: Download Template
                                </h6>
                                <p class="mb-2">Download the CSV template with the correct format:</p>
                                <a href="{{ route('admin.enrollments.bulk-enroll.template') }}" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-file-csv mr-1"></i>Download CSV Template
                                </a>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <strong>Required columns:</strong> student_number, section_id
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="academic_year">Academic Year <span class="text-danger">*</span></label>
                                    <select name="academic_year" id="academic_year" class="form-control" required>
                                        <option value="">-- Select Academic Year --</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="semester">Semester <span class="text-danger">*</span></label>
                                    <select name="semester" id="semester" class="form-control" required>
                                        <option value="">-- Select Semester --</option>
                                        <option value="1st">1st Semester</option>
                                        <option value="2nd">2nd Semester</option>
                                        <option value="summer">Summer</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="csv_file">CSV File <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" 
                                       name="csv_file" 
                                       id="csv_file" 
                                       class="custom-file-input" 
                                       accept=".csv,.txt" 
                                       required>
                                <label class="custom-file-label" for="csv_file">Choose file...</label>
                            </div>
                            <small class="form-text text-muted">
                                CSV format: student_number, section_id (Max 5MB)
                            </small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="bulkEnrollSubmitBtn">
                            <i class="fas fa-upload"></i> Upload & Enroll
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Update custom file input label
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Add confirmation and loading state
    $('#bulkEnrollForm').on('submit', function(e) {
        const fileInput = $('#csv_file')[0];
        const academicYear = $('#academic_year').val();
        const semester = $('#semester').val();
        
        if (!academicYear || !semester) {
            e.preventDefault();
            alert('Please select both Academic Year and Semester');
            return false;
        }
        
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please select a CSV file to upload');
            return false;
        }
        
        // Show loading state
        $('#bulkEnrollSubmitBtn')
            .html('<i class="fas fa-spinner fa-spin"></i> Processing...')
            .prop('disabled', true);
    });
</script>
@endpush