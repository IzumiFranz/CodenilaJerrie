@extends('layouts.admin')

@section('title', 'Create Assignment')

@php
    $pageTitle = 'Create Teaching Assignment';
    $pageActions = '<a href="' . route('admin.assignments.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to List</a>';
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assignment Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.assignments.store') }}" method="POST" id="assignmentForm">
                        @csrf

                        {{-- Academic Period --}}
                        <h6 class="font-weight-bold text-primary mb-3">Academic Period</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="academic_year">Academic Year <span class="text-danger">*</span></label>
                                    <select name="academic_year" 
                                            id="academic_year" 
                                            class="form-control @error('academic_year') is-invalid @enderror" 
                                            required>
                                        <option value="">-- Select Academic Year --</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year }}" {{ old('academic_year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="semester">Semester <span class="text-danger">*</span></label>
                                    <select name="semester" 
                                            id="semester" 
                                            class="form-control @error('semester') is-invalid @enderror" 
                                            required>
                                        <option value="">-- Select Semester --</option>
                                        <option value="1st" {{ old('semester') === '1st' ? 'selected' : '' }}>1st Semester</option>
                                        <option value="2nd" {{ old('semester') === '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                        <option value="summer" {{ old('semester') === 'summer' ? 'selected' : '' }}>Summer</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Instructor Selection --}}
                        <h6 class="font-weight-bold text-success mb-3">Instructor Selection</h6>

                        <div class="form-group">
                            <label for="instructor_id">Instructor <span class="text-danger">*</span></label>
                            <select name="instructor_id" 
                                    id="instructor_id" 
                                    class="form-control @error('instructor_id') is-invalid @enderror" 
                                    required>
                                <option value="">-- Select Instructor --</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" 
                                        data-specialization="{{ $instructor->specialization_id }}"
                                        data-specialization-name="{{ $instructor->specialization->name ?? 'None' }}"
                                        {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->employee_id }} - {{ $instructor->full_name }}
                                        @if($instructor->specialization)
                                            ({{ $instructor->specialization->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('instructor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="instructorInfo"></small>
                        </div>

                        <hr>

                        {{-- Course and Section Selection --}}
                        <h6 class="font-weight-bold text-info mb-3">Section Selection</h6>

                        <div class="form-group">
                            <label for="course_filter">Filter by Course</label>
                            <select id="course_filter" class="form-control">
                                <option value="">-- All Courses --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('section') && request('section') ? 'selected' : '' }}>
                                        {{ $course->course_code }} - {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="section_id">Section <span class="text-danger">*</span></label>
                            <select name="section_id" 
                                    id="section_id" 
                                    class="form-control @error('section_id') is-invalid @enderror" 
                                    required>
                                <option value="">-- Select Section --</option>
                            </select>
                            @error('section_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="sectionInfo"></small>
                        </div>

                        <hr>

                        {{-- Subject Selection --}}
                        <h6 class="font-weight-bold text-warning mb-3">Subject Selection</h6>

                        <div class="form-group">
                            <label for="subject_id">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" 
                                    id="subject_id" 
                                    class="form-control @error('subject_id') is-invalid @enderror" 
                                    required>
                                <option value="">-- Select Subject --</option>
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="subjectInfo"></small>
                        </div>

                        <div id="specializationWarning" class="alert alert-warning" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> <span id="warningMessage"></span>
                        </div>

                        <div id="duplicateWarning" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            <strong>Error:</strong> This assignment already exists! An instructor is already assigned to teach this subject in this section.
                        </div>

                        <hr>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Assignment Summary:</strong>
                            <div class="mt-2" id="assignmentSummary">
                                <p class="mb-1"><strong>Instructor:</strong> <span id="summaryInstructor">Not selected</span></p>
                                <p class="mb-1"><strong>Section:</strong> <span id="summarySection">Not selected</span></p>
                                <p class="mb-1"><strong>Subject:</strong> <span id="summarySubject">Not selected</span></p>
                                <p class="mb-0"><strong>Period:</strong> <span id="summaryPeriod">Not selected</span></p>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Create Assignment
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
        const sections = @json($courses->flatMap(function($course) {
            return $course->sections->map(function($section) use ($course) {
                return [
                    'id' => $section->id,
                    'course_id' => $course->id,
                    'course_code' => $course->course_code,
                    'year_level' => $section->year_level,
                    'section_name' => $section->section_name,
                    'full_name' => $section->full_name,
                    'is_active' => $section->is_active
                ];
            });
        }));

        const subjects = @json($courses->flatMap(function($course) {
            return $course->subjects->map(function($subject) use ($course) {
                return [
                    'id' => $subject->id,
                    'course_id' => $course->id,
                    'year_level' => $subject->year_level,
                    'subject_code' => $subject->subject_code,
                    'subject_name' => $subject->subject_name,
                    'units' => $subject->units,
                    'specialization_id' => $subject->specialization_id,
                    'specialization_name' => $subject->specialization ? $subject->specialization->name : null
                ];
            });
        }));

        // Load sections based on course filter
        function loadSections() {
            const courseFilter = $('#course_filter').val();
            const $sectionSelect = $('#section_id');
            
            $sectionSelect.empty().append('<option value="">-- Select Section --</option>');
            $('#subject_id').empty().append('<option value="">-- Select Section First --</option>');
            
            let filteredSections = sections;
            if (courseFilter) {
                filteredSections = sections.filter(s => s.course_id == courseFilter);
            }
            
            filteredSections.forEach(function(section) {
                if (section.is_active) {
                    $sectionSelect.append(
                        `<option value="${section.id}" 
                                data-course="${section.course_id}"
                                data-year="${section.year_level}">
                            ${section.full_name}
                        </option>`
                    );
                }
            });

            updateSummary();
        }

        // Load subjects based on section
        function loadSubjects() {
            const sectionId = $('#section_id').val();
            const section = sections.find(s => s.id == sectionId);
            const $subjectSelect = $('#subject_id');
            
            $subjectSelect.empty().append('<option value="">-- Select Subject --</option>');
            
            if (!section) {
                $('#sectionInfo').text('');
                return;
            }

            $('#sectionInfo').html(`<strong>Course:</strong> ${section.course_code} | <strong>Year:</strong> ${section.year_level}`);
            
            // Filter subjects by course and year level
            const filteredSubjects = subjects.filter(s => 
                s.course_id == section.course_id && 
                s.year_level == section.year_level
            );
            
            filteredSubjects.forEach(function(subject) {
                $subjectSelect.append(
                    `<option value="${subject.id}" 
                            data-specialization="${subject.specialization_id || ''}"
                            data-units="${subject.units}">
                        ${subject.subject_code} - ${subject.subject_name} (${subject.units} units)
                        ${subject.specialization_name ? ' - Req: ' + subject.specialization_name : ''}
                    </option>`
                );
            });

            updateSummary();
        }

        // Check specialization match
        function checkSpecialization() {
            const instructorId = $('#instructor_id').val();
            const subjectId = $('#subject_id').val();
            
            if (!instructorId || !subjectId) {
                $('#specializationWarning').hide();
                return;
            }

            const instructor = $('#instructor_id').find(':selected');
            const subject = subjects.find(s => s.id == subjectId);
            
            const instructorSpec = instructor.data('specialization');
            const subjectSpec = subject.specialization_id;

            $('#subjectInfo').html(`<strong>Units:</strong> ${subject.units} | <strong>Code:</strong> ${subject.subject_code}`);

            if (subjectSpec && instructorSpec != subjectSpec) {
                $('#warningMessage').text(
                    `This subject requires "${subject.specialization_name}" specialization, ` +
                    `but the selected instructor has "${instructor.data('specialization-name')}" specialization.`
                );
                $('#specializationWarning').show();
            } else {
                $('#specializationWarning').hide();
            }

            updateSummary();
        }

        // Update summary
        function updateSummary() {
            const instructor = $('#instructor_id').find(':selected').text();
            const section = $('#section_id').find(':selected').text();
            const subject = $('#subject_id').find(':selected').text();
            const academicYear = $('#academic_year').val();
            const semester = $('#semester').find(':selected').text();

            $('#summaryInstructor').text(instructor || 'Not selected');
            $('#summarySection').text(section || 'Not selected');
            $('#summarySubject').text(subject || 'Not selected');
            $('#summaryPeriod').text(academicYear && semester ? `${semester} ${academicYear}` : 'Not selected');
        }

        // Event listeners
        $('#course_filter').on('change', loadSections);
        $('#section_id').on('change', loadSubjects);
        $('#subject_id').on('change', checkSpecialization);
        $('#instructor_id').on('change', function() {
            const selected = $(this).find(':selected');
            if (selected.val()) {
                $('#instructorInfo').html(
                    `<strong>Specialization:</strong> ${selected.data('specialization-name')}`
                );
            } else {
                $('#instructorInfo').text('');
            }
            checkSpecialization();
        });
        $('#academic_year, #semester').on('change', updateSummary);

        // Form validation
        $('#assignmentForm').on('submit', function(e) {
            const required = ['instructor_id', 'section_id', 'subject_id', 'academic_year', 'semester'];
            let isValid = true;

            required.forEach(function(field) {
                if (!$(`#${field}`).val()) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return false;
            }
        });

        // Initial load
        @if(request('section'))
            const preSelectedSection = {{ request('section') }};
            const section = sections.find(s => s.id == preSelectedSection);
            if (section) {
                $('#course_filter').val(section.course_id).trigger('change');
                setTimeout(() => {
                    $('#section_id').val(preSelectedSection).trigger('change');
                }, 100);
            }
        @else
            loadSections();
        @endif
    });
</script>
@endpush