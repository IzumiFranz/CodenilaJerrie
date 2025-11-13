@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-user-plus mr-2"></i>Create New User
    </h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i>Back to Users
    </a>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <!-- Role Selection -->
                        <div class="form-group">
                            <label for="role">Role <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">-- Select Role --</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                                <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Username and password will be auto-generated and sent via email</small>
                        </div>

                    <hr>

                    <!-- Basic Information -->
                        <h6 class="font-weight-bold text-primary mb-3">Basic Information</h6>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_name" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" name="middle_name" id="middle_name" 
                                           class="form-control @error('middle_name') is-invalid @enderror" 
                                           value="{{ old('middle_name') }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="last_name" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" name="phone" id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    <hr>

                    <!-- Admin Specific Fields -->
                    <div id="admin-fields" style="display: none;">
                        <h6 class="font-weight-bold text-primary mb-3">Admin Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="position">Position</label>
                                        <input type="text" name="position" id="position" 
                                               class="form-control @error('position') is-invalid @enderror" 
                                               value="{{ old('position') }}">
                                        @error('position')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="office">Office</label>
                                        <input type="text" name="office" id="office" 
                                               class="form-control @error('office') is-invalid @enderror" 
                                               value="{{ old('office') }}">
                                        @error('office')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Instructor Specific Fields -->
                    <div id="instructor-fields" style="display: none;">
                       <h6 class="font-weight-bold text-success mb-3">Instructor Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_id">Employee ID <span class="text-danger">*</span></label>
                                        <input type="text" name="employee_id" id="employee_id" 
                                               class="form-control @error('employee_id') is-invalid @enderror" 
                                               value="{{ old('employee_id') }}">
                                        @error('employee_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="specialization_id">Specialization</label>
                                        <select name="specialization_id" id="specialization_id" 
                                                class="form-control @error('specialization_id') is-invalid @enderror">
                                            <option value="">-- Select Specialization --</option>
                                            @foreach($specializations as $spec)
                                                <option value="{{ $spec->id }}" {{ old('specialization_id') == $spec->id ? 'selected' : '' }}>
                                                    {{ $spec->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('specialization_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department">Department</label>
                                        <input type="text" name="department" id="department" 
                                               class="form-control @error('department') is-invalid @enderror" 
                                               value="{{ old('department') }}">
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hire_date">Hire Date</label>
                                        <input type="date" name="hire_date" id="hire_date" 
                                               class="form-control @error('hire_date') is-invalid @enderror" 
                                               value="{{ old('hire_date') }}">
                                        @error('hire_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input type="text" name="department" id="department" 
                                           class="form-control @error('department') is-invalid @enderror" 
                                           value="{{ old('department') }}">
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hire_date">Hire Date</label>
                                    <input type="date" name="hire_date" id="hire_date" 
                                           class="form-control @error('hire_date') is-invalid @enderror" 
                                           value="{{ old('hire_date') }}">
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>

                    <!-- Student Specific Fields -->
                    <h6 class="font-weight-bold text-info mb-3">Student Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="student_number">Student Number <span class="text-danger">*</span></label>
                                        <input type="text" name="student_number" id="student_number" 
                                               class="form-control @error('student_number') is-invalid @enderror" 
                                               value="{{ old('student_number') }}">
                                        @error('student_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="course_id">Course <span class="text-danger">*</span></label>
                                        <select name="course_id" id="course_id" 
                                                class="form-control @error('course_id') is-invalid @enderror">
                                            <option value="">-- Select Course --</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->course_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="year_level">Year Level <span class="text-danger">*</span></label>
                                        <select name="year_level" id="year_level" 
                                                class="form-control @error('year_level') is-invalid @enderror">
                                            <option value="">-- Select Year Level --</option>
                                            @for($i = 1; $i <= 6; $i++)
                                                <option value="{{ $i }}" {{ old('year_level') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('year_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="admission_date">Admission Date</label>
                                        <input type="date" name="admission_date" id="admission_date" 
                                               class="form-control @error('admission_date') is-invalid @enderror" 
                                               value="{{ old('admission_date') }}">
                                        @error('admission_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" rows="3" 
                                          class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        
                    </div>

                    <!-- Email Notification Option -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-envelope mr-2"></i>Email Notification
                            </h6>
                            
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="send_email" 
                                       name="send_email" 
                                       value="1" 
                                       {{ old('send_email', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="send_email">
                                    <strong>Send login credentials via email</strong>
                                </label>
                            </div>
                            
                            <small class="form-text text-muted ml-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                If checked, the user will receive an email with their username and temporary password. 
                                They will be required to change their password upon first login.
                            </small>
                            
                            <div class="alert alert-info mt-3 mb-0">
                                <strong>Note:</strong> If email is not sent, you must manually provide the credentials to the user.
                                The username and password will be displayed after user creation.
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide role-specific fields
    function toggleRoleFields() {
        let role = $('#role').val();
        
        // Hide all role-specific fields
        $('#admin-fields, #instructor-fields, #student-fields').hide();
        
        // Show selected role fields
        if (role === 'admin') {
            $('#admin-fields').show();
        } else if (role === 'instructor') {
            $('#instructor-fields').show();
        } else if (role === 'student') {
            $('#student-fields').show();
        }
    }
    
    // Trigger on page load
    toggleRoleFields();
    
    // Trigger on role change
    $('#role').on('change', function() {
        toggleRoleFields();
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        let role = $('#role').val();
        
        if (!role) {
            e.preventDefault();
            alert('Please select a user role');
            $('#role').focus();
            return false;
        }
        
        // Role-specific validation
        if (role === 'instructor') {
            let employeeId = $('#employee_id').val();
            if (!employeeId) {
                e.preventDefault();
                alert('Employee ID is required for instructors');
                $('#employee_id').focus();
                return false;
            }
        }
        
        if (role === 'student') {
            let studentNumber = $('#student_number').val();
            let courseId = $('#course_id').val();
            let yearLevel = $('#year_level').val();
            
            if (!studentNumber) {
                e.preventDefault();
                alert('Student Number is required');
                $('#student_number').focus();
                return false;
            }
            
            if (!courseId) {
                e.preventDefault();
                alert('Course is required for students');
                $('#course_id').focus();
                return false;
            }
            
            if (!yearLevel) {
                e.preventDefault();
                alert('Year Level is required for students');
                $('#year_level').focus();
                return false;
            }
        }
    });
});
</script>
@endpush