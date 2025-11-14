@extends('layouts.admin')

@section('title', 'Enroll Student')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-plus mr-2"></i>Enroll New Student</h1>
    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to List
    </a>
</div>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @livewire('enrollment-form')
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const sections = @json($sectionsData);

        // Show student info when selected
        $('#student_id').on('change', function() {
            const selected = $(this).find(':selected');
            if (selected.val()) {
                const courseId = selected.data('course');
                const yearLevel = selected.data('year');
                
                $('#studentCourse').text(selected.text().split('(')[1].split(')')[0].split('-')[0]);
                $('#studentYear').text('Year ' + yearLevel);
                $('#studentInfo').show();
                
                // Auto-filter sections
                $('#course_filter').val(courseId).trigger('change');
            } else {
                $('#studentInfo').hide();
            }
        });

        // Filter sections by course
        function loadSections() {
            const courseFilter = $('#course_filter').val();
            const $sectionSelect = $('#section_id');
            
            $sectionSelect.empty().append('<option value="">-- Select Section --</option>');
            
            let filteredSections = sections;
            if (courseFilter) {
                filteredSections = sections.filter(s => s.course_id == courseFilter);
            }
            
            filteredSections.forEach(function(section) {
                if (section.is_active) {
                    $sectionSelect.append(
                        `<option value="${section.id}" 
                                data-max="${section.max_students}">
                            ${section.full_name} (Year ${section.year_level}) - Max: ${section.max_students} students
                        </option>`
                    );
                }
            });
        }

        $('#course_filter').on('change', loadSections);

        // Show section capacity
        $('#section_id').on('change', function() {
            const selected = $(this).find(':selected');
            if (selected.val()) {
                const maxStudents = selected.data('max');
                $('#sectionCapacity').text(`Maximum capacity: ${maxStudents} students`);
            } else {
                $('#sectionCapacity').text('');
            }
        });

        // Validation before submit
        $('#enrollmentForm').on('submit', function(e) {
            const studentId = $('#student_id').val();
            const sectionId = $('#section_id').val();
            
            if (!studentId || !sectionId) {
                e.preventDefault();
                alert('Please select both student and section');
                return false;
            }
        });

        // Load sections initially
        loadSections();
    });
</script>
@endpush