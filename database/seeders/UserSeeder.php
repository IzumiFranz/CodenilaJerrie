<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        DB::table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@lms.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'must_change_password' => false,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Instructor users
        $instructors = [
            ['username' => 'prof.santos', 'email' => 'jsantos@lms.edu', 'role' => 'instructor'],
            ['username' => 'prof.reyes', 'email' => 'mreyes@lms.edu', 'role' => 'instructor'],
            ['username' => 'prof.garcia', 'email' => 'agarcia@lms.edu', 'role' => 'instructor'],
            ['username' => 'prof.cruz', 'email' => 'rcruz@lms.edu', 'role' => 'instructor'],
            ['username' => 'prof.bautista', 'email' => 'lbautista@lms.edu', 'role' => 'instructor'],
        ];

        foreach ($instructors as $instructor) {
            DB::table('users')->insert(array_merge($instructor, [
                'password' => Hash::make('password'),
                'status' => 'active',
                'must_change_password' => false,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Student users
        for ($i = 1; $i <= 50; $i++) {
            DB::table('users')->insert([
                'username' => 'student' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => "student{$i}@lms.edu",
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
                'must_change_password' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}