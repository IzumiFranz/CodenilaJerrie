@extends('layouts.admin')

@section('title', 'Enrollments Management')

@php
    $pageTitle = 'Enrollments Management';
    $pageActions = '
        <a href="' . route('admin.enrollments.create') . '" class="btn btn-primary btn-sm mr-2">
            <i class="fas fa-user-plus"></i> Enroll Student
        </a>
        <button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#bulkEnrollModal">
            <i class="fas fa-upload"></i> Bulk Enroll
        </button>
        <a href="' . route('admin.enrollments.trashed') . '" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash"></i> Trash
        </a>
    ';
@endphp

@section('content')
    @livewire('enrollment-table')

    {{-- Bulk Enroll Modal --}}
    <div class="modal fade" id="bulkEnrollModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.enrollments.bulk-enroll') }}" method="POST" enctype="multipart/form-data">
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
                                <li>Prepare CSV with columns: student_number, section_id</li>
                                <li>Select academic year and semester</li>
                                <li>Upload the CSV file</li>
                                <li>System will validate and enroll students</li>
                            </ol>
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

                        <div class="form-group">
                            <label>Sample CSV Format:</label>
                            <pre class="bg-light p-3 rounded"><code>student_number,section_id
2024-001,1
2024-002,1
2024-003,2</code></pre>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
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
</script>
@endpush