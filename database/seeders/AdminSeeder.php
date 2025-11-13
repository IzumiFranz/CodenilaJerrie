<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->insert([
            'user_id' => 1,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'middle_name' => 'Santos',
            'position' => 'System Administrator',
            'office' => 'IT Department',
            'phone' => '+639171234567',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

class InstructorSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = [
            ['user_id' => 2, 'employee_id' => 'EMP-2024-001', 'first_name' => 'Jose', 'last_name' => 'Santos', 'middle_name' => 'Cruz', 'specialization_id' => 1, 'department' => 'Computer Science', 'phone' => '+639171234568', 'hire_date' => '2020-06-01'],
            ['user_id' => 3, 'employee_id' => 'EMP-2024-002', 'first_name' => 'Maria', 'last_name' => 'Reyes', 'middle_name' => 'Lopez', 'specialization_id' => 2, 'department' => 'Information Technology', 'phone' => '+639171234569', 'hire_date' => '2019-08-15'],
            ['user_id' => 4, 'employee_id' => 'EMP-2024-003', 'first_name' => 'Antonio', 'last_name' => 'Garcia', 'middle_name' => 'Ramos', 'specialization_id' => 3, 'department' => 'Computer Science', 'phone' => '+639171234570', 'hire_date' => '2021-01-10'],
            ['user_id' => 5, 'employee_id' => 'EMP-2024-004', 'first_name' => 'Rosa', 'last_name' => 'Cruz', 'middle_name' => 'Mendoza', 'specialization_id' => 4, 'department' => 'Cybersecurity', 'phone' => '+639171234571', 'hire_date' => '2018-03-20'],
            ['user_id' => 6, 'employee_id' => 'EMP-2024-005', 'first_name' => 'Luis', 'last_name' => 'Bautista', 'middle_name' => 'Torres', 'specialization_id' => 5, 'department' => 'Computer Science', 'phone' => '+639171234572', 'hire_date' => '2022-09-01'],
        ];

        foreach ($instructors as $instructor) {
            DB::table('instructors')->insert(array_merge($instructor, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}