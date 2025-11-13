<div>
    {{-- Single Enrollment --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Enroll Single Student</h6>
        </div>
        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form wire:submit.prevent="enroll">
                <div class="form-group">
                    <label>Student <span class="text-danger">*</span></label>
                    <select wire:model="student_id" class="form-control @error('student_id') is-invalid @enderror">
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
                    <label>Course <span class="text-danger">*</span></label>
                    <select wire:model="course_id" class="form-control">
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Section <span class="text-danger">*</span></label>
                    <select wire:model="section_id" class="form-control" {{ !$course_id ? 'disabled' : '' }}>
                        <option value="">Select Section</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->year_level }} - {{ $section->section_name }}</option>
                        @endforeach
                    </select>
                    @error('section_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>Academic Year <span class="text-danger">*</span></label>
                        <select wire:model="academic_year" class="form-control">
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Semester <span class="text-danger">*</span></label>
                        <select wire:model="semester" class="form-control">
                            <option value="1st">1st Semester</option>
                            <option value="2nd">2nd Semester</option>
                            <option value="summer">Summer</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label>Enrollment Date <span class="text-danger">*</span></label>
                    <input type="date" wire:model="enrollment_date" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary mt-3" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fas fa-save mr-1"></i> Enroll Student</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin mr-1"></i> Enrolling...</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Bulk Enrollment --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">Bulk Enrollment</h6>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="bulkEnroll" enctype="multipart/form-data">
                <div class="form-group">
                    <label>CSV File <span class="text-danger">*</span></label>
                    <input type="file" wire:model="csv_file" class="form-control">
                </div>
                <div class="form-group">
                    <label>Academic Year</label>
                    <select wire:model="academic_year" class="form-control">
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Semester</label>
                    <select wire:model="semester" class="form-control">
                        <option value="1st">1st Semester</option>
                        <option value="2nd">2nd Semester</option>
                        <option value="summer">Summer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success mt-2" wire:loading.attr="disabled">
                    <span wire:loading.remove>Upload & Enroll</span>
                    <span wire:loading>Processing...</span>
                </button>
            </form>

            @if($bulkResults)
                <div class="mt-3">
                    <h6>Bulk Enrollment Results:</h6>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Row</th>
                                <th>Status</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bulkResults as $result)
                                <tr>
                                    <td>{{ $result['row'] }}</td>
                                    <td>{{ ucfirst($result['status']) }}</td>
                                    <td>{{ $result['message'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
