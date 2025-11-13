<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizAttemptSeeder extends Seeder
{
    public function run(): void
    {
        // Get some students to create attempts for
        $students = DB::table('students')->limit(20)->get();
        
        // Create quiz attempts for Quiz 1 (CS101)
        foreach ($students->take(10) as $index => $student) {
            $totalPoints = 3.00;
            $earnedPoints = rand(150, 280) / 100; // Random score between 1.5 and 2.8
            $percentage = ($earnedPoints / $totalPoints) * 100;
            $status = 'completed';
            
            $attemptId = DB::table('quiz_attempts')->insertGetId([
                'quiz_id' => 1,
                'student_id' => $student->id,
                'attempt_number' => 1,
                'score' => $earnedPoints,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
                'status' => $status,
                'started_at' => now()->subDays(4)->addHours($index),
                'completed_at' => now()->subDays(4)->addHours($index)->addMinutes(rand(30, 55)),
                'time_spent' => rand(1800, 3300), // 30-55 minutes in seconds
                'question_order' => json_encode([1, 2, 3]),
                'ip_address' => '192.168.1.' . rand(1, 254),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ]);
            
            // Create answers for this attempt
            $questions = [1, 2, 3];
            foreach ($questions as $questionId) {
                // Get correct choice for this question
                $correctChoice = DB::table('choices')
                    ->where('question_id', $questionId)
                    ->where('is_correct', true)
                    ->first();
                
                // Get all choices
                $allChoices = DB::table('choices')
                    ->where('question_id', $questionId)
                    ->pluck('id')
                    ->toArray();
                
                // Randomly decide if answer is correct (70% chance)
                $isCorrect = rand(1, 100) <= 70;
                $choiceId = $isCorrect ? $correctChoice->id : $allChoices[array_rand($allChoices)];
                $pointsEarned = $isCorrect ? 1.00 : 0.00;
                
                DB::table('quiz_answers')->insert([
                    'attempt_id' => $attemptId,
                    'question_id' => $questionId,
                    'choice_id' => $choiceId,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Create quiz attempts for Quiz 2 (CS102 - Programming)
        foreach ($students->skip(5)->take(8) as $index => $student) {
            $totalPoints = 3.00;
            $earnedPoints = rand(180, 300) / 100;
            $percentage = ($earnedPoints / $totalPoints) * 100;
            
            $attemptId = DB::table('quiz_attempts')->insertGetId([
                'quiz_id' => 2,
                'student_id' => $student->id,
                'attempt_number' => 1,
                'score' => $earnedPoints,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
                'status' => 'completed',
                'started_at' => now()->subDays(2)->addHours($index),
                'completed_at' => now()->subDays(2)->addHours($index)->addMinutes(rand(20, 28)),
                'time_spent' => rand(1200, 1680),
                'question_order' => json_encode([4, 5, 6]),
                'ip_address' => '192.168.1.' . rand(1, 254),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);
            
            // Create answers
            $questions = [4, 5, 6];
            foreach ($questions as $questionId) {
                $correctChoice = DB::table('choices')
                    ->where('question_id', $questionId)
                    ->where('is_correct', true)
                    ->first();
                
                $allChoices = DB::table('choices')
                    ->where('question_id', $questionId)
                    ->pluck('id')
                    ->toArray();
                
                $isCorrect = rand(1, 100) <= 75;
                $choiceId = $isCorrect ? $correctChoice->id : $allChoices[array_rand($allChoices)];
                $pointsEarned = $isCorrect ? 1.00 : 0.00;
                
                DB::table('quiz_answers')->insert([
                    'attempt_id' => $attemptId,
                    'question_id' => $questionId,
                    'choice_id' => $choiceId,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Create some in-progress attempts
        foreach ($students->take(3) as $student) {
            DB::table('quiz_attempts')->insert([
                'quiz_id' => 3,
                'student_id' => $student->id,
                'attempt_number' => 1,
                'score' => 0,
                'total_points' => 3.00,
                'percentage' => 0,
                'status' => 'in_progress',
                'started_at' => now()->subMinutes(rand(5, 30)),
                'completed_at' => null,
                'time_spent' => null,
                'question_order' => json_encode([7, 8]),
                'ip_address' => '192.168.1.' . rand(1, 254),
                'created_at' => now()->subMinutes(rand(5, 30)),
                'updated_at' => now()->subMinutes(rand(5, 30)),
            ]);
        }
    }
}