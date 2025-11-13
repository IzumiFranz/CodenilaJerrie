@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-edit mr-2"></i>Edit User</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to List
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" data-validate>
                    @csrf
                    @method('PUT')
                    
                    {{-- Role Display (Cannot be changed) --}}
                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                            <small class="form-text text-muted">Role cannot be changed after creation</small>
                        </div>
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                <h6 class="font-weight-bold text-primary mb-3">Basic Information</h6>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $user->profile->first_name ?? '') }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->profile->last_name ?? '') }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $user->profile->middle_name ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required>
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
                                           value="{{ old('phone', $user->profile->phone ?? '') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    <hr>

                    @if($user->isAdmin())
                    <h6 class="font-weight-bold text-primary mb-3">Admin Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="position">Position</label>
                                <input type="text" name="position" id="position" 
                                        class="form-control @error('position') is-invalid @enderror" 
                                        value="{{ old('position', $user->admin->position ?? '') }}">
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
                                        value="{{ old('office', $user->admin->office ?? '') }}">
                                    @error('office')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($user->isInstructor())
                    <hr><h6 class="font-weight-bold text-success mb-3">Instructor Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id">Employee ID</label>
                                <input type="text" name="employee_id" id="employee_id" 
                                        class="form-control @error('employee_id') is-invalid @enderror" 
                                        value="{{ old('employee_id', $user->instructor->employee_id ?? '') }}">
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
                                        <option value="{{ $spec->id }}" 
                                            {{ old('specialization_id', $user->instructor->specialization_id ?? '') == $spec->id ? 'selected' : '' }}>
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
                                        value="{{ old('department', $user->instructor->department ?? '') }}">
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
                                            value="{{ old('hire_date', $user->instructor->hire_date ? $user->instructor->hire_date->format('Y-m-d') : '') }}">
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif
<hr>
                    @if($user->isStudent())
                    <h6 class="font-weight-bold text-info mb-3">Student Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="student_number">Student Number</label>
                                        <input type="text" name="student_number" id="student_number" 
                                               class="form-control @error('student_number') is-invalid @enderror" 
                                               value="{{ old('student_number', $user->student->student_number ?? '') }}">
                                        @error('student_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="course_id">Course</label>
                                        <select name="course_id" id="course_id" 
                                                class="form-control @error('course_id') is-invalid @enderror">
                                            <option value="">-- Select Course --</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" 
                                                    {{ old('course_id', $user->student->course_id ?? '') == $course->id ? 'selected' : '' }}>
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
                                        <label for="year_level">Year Level</label>
                                        <select name="year_level" id="year_level" 
                                                class="form-control @error('year_level') is-invalid @enderror">
                                            <option value="">-- Select Year Level --</option>
                                            @for($i = 1; $i <= 6; $i++)
                                                <option value="{{ $i }}" 
                                                    {{ old('year_level', $user->student->year_level ?? '') == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('year_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Admission Date</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $user->student->admission_date ? $user->student->admission_date->format('M d, Y') : 'N/A' }}" 
                                               disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" rows="3" 
                                          class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->student->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                    <hr>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">User Details</h6>
            </div>
            <div class="card-body text-center">
                @if($user->profile_picture)
                    <img src="{{ asset('storage/' . $user->profile_picture) }}" class="img-fluid rounded-circle mb-3" style="max-width: 150px;" alt="Profile">
                @else
                    <img src="{{ asset('img/undraw_profile.svg') }}" class="img-fluid rounded-circle mb-3" style="max-width: 150px;" alt="Profile">
                @endif
                
                <h5>{{ $user->full_name }}</h5>
                <p class="text-muted">@{{ $user->username }}</p>
                
                <div class="mb-3">
                    <span class="badge badge-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'instructor' ? 'success' : 'info') }} mr-2">
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>

                <hr>
                
                <div class="text-left">
                    <p class="mb-2"><strong>Created:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                    <p class="mb-2"><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y') }}</p>
                    @if($user->last_login_at)
                    <p class="mb-2"><strong>Last Login:</strong> {{ $user->last_login_at->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Danger Zone</h6>
            </div>
            <div class="card-body">
                @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block" data-confirm="Are you sure you want to delete this user? This action cannot be undone.">
                        <i class="fas fa-trash mr-1"></i> Delete User
                    </button>
                </form>
                @else
                <p class="text-muted mb-0">You cannot delete your own account.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
