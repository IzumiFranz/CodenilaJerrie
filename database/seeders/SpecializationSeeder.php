<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            ['name' => 'Web Development', 'code' => 'WEB-DEV', 'description' => 'Specialization in web technologies and frameworks', 'is_active' => true],
            ['name' => 'Mobile Development', 'code' => 'MOB-DEV', 'description' => 'Specialization in mobile app development', 'is_active' => true],
            ['name' => 'Data Science', 'code' => 'DATA-SCI', 'description' => 'Specialization in data analysis and machine learning', 'is_active' => true],
            ['name' => 'Cybersecurity', 'code' => 'CYBER-SEC', 'description' => 'Specialization in network and system security', 'is_active' => true],
            ['name' => 'Game Development', 'code' => 'GAME-DEV', 'description' => 'Specialization in game design and development', 'is_active' => true],
        ];

        foreach ($specializations as $spec) {
            // Only create if specialization doesn't exist
            if (!DB::table('specializations')->where('code', $spec['code'])->exists()) {
                DB::table('specializations')->insert(array_merge($spec, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['course_code' => 'BSCS', 'course_name' => 'Bachelor of Science in Computer Science', 'description' => '4-year program in computer science', 'max_years' => 4, 'is_active' => true],
            ['course_code' => 'BSIT', 'course_name' => 'Bachelor of Science in Information Technology', 'description' => '4-year program in IT', 'max_years' => 4, 'is_active' => true],
            ['course_code' => 'BSCpE', 'course_name' => 'Bachelor of Science in Computer Engineering', 'description' => '5-year program in computer engineering', 'max_years' => 5, 'is_active' => true],
            ['course_code' => 'BSIS', 'course_name' => 'Bachelor of Science in Information Systems', 'description' => '4-year program in information systems', 'max_years' => 4, 'is_active' => true],
        ];

        foreach ($courses as $course) {
            // Only create if course doesn't exist
            if (!DB::table('courses')->where('course_code', $course['course_code'])->exists()) {
                DB::table('courses')->insert(array_merge($course, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}