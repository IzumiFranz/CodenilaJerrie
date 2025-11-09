<div>
    <form wire:submit.prevent="enroll">
        <div class="form-group">
            <label class="form-label">Student <span class="text-danger">*</span></label>
            <select wire:model="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                <option value="">Select Student</option>
                @foreach($students as $student)
                <option value="{{ $student->id }}">
                    {{ $student->student_number }} - {{ $student->first_name }} {{ $student->last_name }}
                </option>
                @endforeach
            </select>
            @error('student_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Course <span class="text-danger">*</span></label>
            <select wire:model.live="course_id" class="form-control" required>
                <option value="">Select Course</option>
                @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Section <span class="text-danger">*</span></label>
            <select wire:model="section_id" class="form-control @error('section_id') is-invalid @enderror" required {{ !$course_id ? 'disabled' : '' }}>
                <option value="">Select Section</option>
                @foreach($sections as $section)
                <option value="{{ $section->id }}">{{ $section->year_level }} - {{ $section->section_name }}</option>
                @endforeach
            </select>
            @error('section_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                    <select wire:model="academic_year" class="form-control" required>
                        @foreach($academicYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Semester <span class="text-danger">*</span></label>
                    <select wire:model="semester" class="form-control" required>
                        <option value="1st">1st Semester</option>
                        <option value="2nd">2nd Semester</option>
                        <option value="summer">Summer</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Enrollment Date <span class="text-danger">*</span></label>
            <input type="date" wire:model="enrollment_date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block" wire:loading.attr="disabled">
            <span wire:loading.remove>
                <i class="fas fa-save mr-1"></i> Enroll Student
            </span>
            <span wire:loading>
                <i class="fas fa-spinner fa-spin mr-1"></i> Enrolling...
            </span>
        </button>
    </form>
</div>