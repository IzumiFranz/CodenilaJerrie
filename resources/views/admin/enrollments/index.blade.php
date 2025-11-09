@extends('layouts.admin')
@section('title', 'Manage Enrollments')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-graduate mr-2"></i>Manage Enrollments</h1>
    <div class="btn-group">
        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Enroll Student
        </a>
        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#bulkEnrollModal">
            <i class="fas fa-upload mr-1"></i> Bulk Enroll
        </button>
    </div>
</div>

@livewire('enrollment-table')

{{-- Bulk Enroll Modal --}}
<div class="modal fade" id="bulkEnrollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Enrollment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.enrollments.bulk-enroll') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Academic Year <span class="text-danger">*</span></label>
                        <input type="text" name="academic_year" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-control" required>
                            <option value="1st">1st Semester</option>
                            <option value="2nd">2nd Semester</option>
                            <option value="summer">Summer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control-file" accept=".csv" required>
                        <small class="form-text text-muted">
                            CSV should have: student_number, section_id
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload mr-1"></i> Upload & Enroll
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection