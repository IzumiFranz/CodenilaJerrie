<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [1, 2, 3, 4];
        $sections = ['A', 'B', 'C'];
        
        foreach ($courses as $courseId) {
            for ($year = 1; $year <= 4; $year++) {
                foreach ($sections as $section) {
                    DB::table('sections')->insert([
                        'course_id' => $courseId,
                        'section_name' => $section,
                        'year_level' => $year,
                        'max_students' => 40,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // Year 1
            ['course_id' => 1, 'specialization_id' => null, 'subject_code' => 'CS101', 'subject_name' => 'Introduction to Computing', 'description' => 'Basic concepts of computing', 'year_level' => 1, 'units' => 3],
            ['course_id' => 1, 'specialization_id' => null, 'subject_code' => 'CS102', 'subject_name' => 'Computer Programming 1', 'description' => 'Fundamentals of programming', 'year_level' => 1, 'units' => 3],
            ['course_id' => 1, 'specialization_id' => null, 'subject_code' => 'MATH101', 'subject_name' => 'Calculus 1', 'description' => 'Differential calculus', 'year_level' => 1, 'units' => 3],
            
            // Year 2
            ['course_id' => 1, 'specialization_id' => null, 'subject_code' => 'CS201', 'subject_name' => 'Data Structures and Algorithms', 'description' => 'Study of data structures', 'year_level' => 2, 'units' => 3],
            ['course_id' => 1, 'specialization_id' => null, 'subject_code' => 'CS202', 'subject_name' => 'Object-Oriented Programming', 'description' => 'OOP concepts and implementation', 'year_level' => 2, 'units' => 3],
            ['course_id' => 1, 'specialization_id' => null, 'subject_code' => 'CS203', 'subject_name' => 'Database Management Systems', 'description' => 'Database design and SQL', 'year_level' => 2, 'units' => 3],
            
            // Year 3
            ['course_id' => 1, 'specialization_id' => 1, 'subject_code' => 'CS301', 'subject_name' => 'Web Development', 'description' => 'HTML, CSS, JavaScript, PHP', 'year_level' => 3, 'units' => 3],
            ['course_id' => 1, 'specialization_id' => 3, 'subject_code' => 'CS302', 'subject_name' => 'Machine Learning', 'description' => 'Introduction to ML algorithms', 'year_level' => 3, 'units' => 3],
            ['course_id' => 1, 'specialization_id' => null, 'subject_code' => 'CS303', 'subject_name' => 'Software Engineering', 'description' => 'SDLC and methodologies', 'year_level' => 3, 'units' => 3],
            
            // Year 4
            ['course_id' => 1, 'specialization_id' => null, 'subject_code' => 'CS401', 'subject_name' => 'Capstone Project 1', 'description' => 'Research and planning', 'year_level' => 4, 'units' => 3],
            ['course_id' => 1, 'specialization_id' => 4, 'subject_code' => 'CS402', 'subject_name' => 'Network Security', 'description' => 'Security protocols and practices', 'year_level' => 4, 'units' => 3],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert(array_merge($subject, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}