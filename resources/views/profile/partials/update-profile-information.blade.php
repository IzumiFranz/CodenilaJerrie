<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-user-edit mr-2"></i>Profile Information
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="row">
                <!-- First Name -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('first_name') is-invalid @enderror" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', auth()->user()->profile->first_name) }}" 
                               required>
                        @error('first_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Middle Name -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" 
                               class="form-control @error('middle_name') is-invalid @enderror" 
                               id="middle_name" 
                               name="middle_name" 
                               value="{{ old('middle_name', auth()->user()->profile->middle_name) }}">
                        @error('middle_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Last Name -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('last_name') is-invalid @enderror" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', auth()->user()->profile->last_name) }}" 
                               required>
                        @error('last_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email Address <span class="text-danger">*</span></label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', auth()->user()->email) }}" 
                       required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                @if(!auth()->user()->hasVerifiedEmail())
                    <small class="text-warning d-block mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Your email is not verified. 
                        <a href="{{ route('verification.notice') }}">Click here to verify</a>
                    </small>
                @endif
            </div>

            <!-- Phone -->
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone', auth()->user()->profile->phone) }}" 
                       placeholder="+1 (555) 123-4567">
                @error('phone')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Role-specific fields --}}
            @if(auth()->user()->isAdmin())
                <!-- Position -->
                <div class="form-group">
                    <label for="position">Position</label>
                    <input type="text" 
                           class="form-control @error('position') is-invalid @enderror" 
                           id="position" 
                           name="position" 
                           value="{{ old('position', auth()->user()->profile->position) }}">
                    @error('position')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Office -->
                <div class="form-group">
                    <label for="office">Office</label>
                    <input type="text" 
                           class="form-control @error('office') is-invalid @enderror" 
                           id="office" 
                           name="office" 
                           value="{{ old('office', auth()->user()->profile->office) }}">
                    @error('office')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

            @elseif(auth()->user()->isInstructor())
                <!-- Employee ID (Read-only) -->
                <div class="form-group">
                    <label for="employee_id">Employee ID</label>
                    <input type="text" 
                           class="form-control" 
                           value="{{ auth()->user()->profile->employee_id }}" 
                           readonly>
                </div>

                <!-- Department -->
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" 
                           class="form-control @error('department') is-invalid @enderror" 
                           id="department" 
                           name="department" 
                           value="{{ old('department', auth()->user()->profile->department) }}">
                    @error('department')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Specialization (Read-only) -->
                @if(auth()->user()->profile->specialization)
                <div class="form-group">
                    <label>Specialization</label>
                    <input type="text" 
                           class="form-control" 
                           value="{{ auth()->user()->profile->specialization->name }}" 
                           readonly>
                </div>
                @endif

            @elseif(auth()->user()->isStudent())
                <!-- Student Number (Read-only) -->
                <div class="form-group">
                    <label for="student_number">Student Number</label>
                    <input type="text" 
                           class="form-control" 
                           value="{{ auth()->user()->profile->student_number }}" 
                           readonly>
                </div>

                <!-- Course (Read-only) -->
                @if(auth()->user()->profile->course)
                <div class="form-group">
                    <label>Course</label>
                    <input type="text" 
                           class="form-control" 
                           value="{{ auth()->user()->profile->course->course_name }}" 
                           readonly>
                </div>
                @endif

                <!-- Year Level (Read-only) -->
                <div class="form-group">
                    <label>Year Level</label>
                    <input type="text" 
                           class="form-control" 
                           value="Year {{ auth()->user()->profile->year_level }}" 
                           readonly>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              id="address" 
                              name="address" 
                              rows="3">{{ old('address', auth()->user()->profile->address) }}</textarea>
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            @endif

            <!-- Submit Button -->
            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
