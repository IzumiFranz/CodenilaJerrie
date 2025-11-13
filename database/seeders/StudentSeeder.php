<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [1, 2, 3, 4]; // BSCS, BSIT, BSCpE, BSIS
        $yearLevels = [1, 2, 3, 4];
        
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Luis', 'Carmen', 'Miguel', 'Isabel', 'Carlos', 'Sofia', 'Rafael', 'Elena', 'Diego', 'Laura', 'Gabriel', 'Patricia'];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Flores', 'Rivera', 'Gomez', 'Morales', 'Ramos', 'Castillo', 'Jimenez', 'Herrera', 'Medina'];
        $middleNames = ['Cruz', 'Lopez', 'Ramos', 'Mendoza', 'Torres', 'Santos', 'Garcia', 'Reyes', 'Flores', 'Rivera'];

        for ($i = 1; $i <= 50; $i++) {
            $userId = $i + 6; // Starting after admin + 5 instructors
            $courseId = $courses[array_rand($courses)];
            $yearLevel = $yearLevels[array_rand($yearLevels)];
            
            DB::table('students')->insert([
                'user_id' => $userId,
                'course_id' => $courseId,
                'student_number' => '2024-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'first_name' => $firstNames[array_rand($firstNames)],
                'last_name' => $lastNames[array_rand($lastNames)],
                'middle_name' => $middleNames[array_rand($middleNames)],
                'year_level' => $yearLevel,
                'phone' => '+6391712345' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'address' => 'Malolos, Bulacan, Philippines',
                'admission_date' => now()->subYears($yearLevel - 1)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}