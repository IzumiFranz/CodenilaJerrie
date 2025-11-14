<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get all students
        $students = DB::table('students')->get();
        
        foreach ($students as $student) {
            // Get sections for the student's course and year level
            $sections = DB::table('sections')
                ->where('course_id', $student->course_id)
                ->where('year_level', $student->year_level)
                ->get();
            
            if ($sections->isNotEmpty()) {
                // Randomly assign to a section
                $section = $sections->random();
                
                // Only create if enrollment doesn't exist
                if (!DB::table('enrollments')
                    ->where('student_id', $student->id)
                    ->where('section_id', $section->id)
                    ->where('academic_year', '2024-2025')
                    ->where('semester', '1st')
                    ->exists()) {
                    DB::table('enrollments')->insert([
                        'student_id' => $student->id,
                        'section_id' => $section->id,
                        'academic_year' => '2024-2025',
                        'semester' => '1st',
                        'status' => 'enrolled',
                        'enrollment_date' => now()->subDays(rand(1, 30)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}

class InstructorSubjectSectionSeeder extends Seeder
{
    public function run(): void
    {
        // Assign instructors to subjects and sections
        $assignments = [
            ['instructor_id' => 1, 'subject_id' => 1, 'section_id' => 1], // CS101 Section A
            ['instructor_id' => 1, 'subject_id' => 1, 'section_id' => 2], // CS101 Section B
            ['instructor_id' => 2, 'subject_id' => 2, 'section_id' => 1], // CS102 Section A
            ['instructor_id' => 2, 'subject_id' => 2, 'section_id' => 2], // CS102 Section B
            ['instructor_id' => 3, 'subject_id' => 4, 'section_id' => 13], // CS201 Year 2
            ['instructor_id' => 3, 'subject_id' => 5, 'section_id' => 13], // CS202 Year 2
            ['instructor_id' => 4, 'subject_id' => 7, 'section_id' => 25], // CS301 Year 3
            ['instructor_id' => 5, 'subject_id' => 8, 'section_id' => 25], // CS302 Year 3
        ];

        foreach ($assignments as $assignment) {
            // Only create if assignment doesn't exist
            if (!DB::table('instructor_subject_section')
                ->where('instructor_id', $assignment['instructor_id'])
                ->where('subject_id', $assignment['subject_id'])
                ->where('section_id', $assignment['section_id'])
                ->where('academic_year', '2024-2025')
                ->where('semester', '1st')
                ->exists()) {
                DB::table('instructor_subject_section')->insert(array_merge($assignment, [
                    'academic_year' => '2024-2025',
                    'semester' => '1st',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}