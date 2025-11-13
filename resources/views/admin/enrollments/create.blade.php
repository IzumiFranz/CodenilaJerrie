@extends('layouts.admin')

@section('title', 'Enroll Student')

@php
    $pageTitle = 'Enroll New Student';
    $pageActions = '<a href="' . route('admin.enrollments.index') . '" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to List</a>';
@endphp
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @livewire('enrollment-form')
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
                    'max_students' => $section->max_students,
                    'full_name' => $section->full_name,
                    'is_active' => $section->is_active
                ];
            });
        }));

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