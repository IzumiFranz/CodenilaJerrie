<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // Create quizzes
        $quizzes = [
            [
                'instructor_id' => 1,
                'subject_id' => 1,
                'title' => 'Introduction to Computing - Midterm Exam',
                'description' => 'Covers topics from Week 1-8 including computer systems, operating systems, and number systems.',
                'instructions' => 'Read each question carefully. Select the best answer. You have 60 minutes to complete this quiz.',
                'time_limit' => 60,
                'passing_score' => 60.00,
                'max_attempts' => 1,
                'randomize_questions' => true,
                'randomize_choices' => true,
                'show_results' => true,
                'show_answers' => false,
                'is_published' => true,
                'available_from' => now()->subDays(5),
                'available_until' => now()->addDays(25),
                'published_at' => now()->subDays(5),
                'question_ids' => [1, 2, 3], // Will be used to link questions
            ],
            [
                'instructor_id' => 2,
                'subject_id' => 2,
                'title' => 'Programming Fundamentals - Quiz 1',
                'description' => 'Basic programming concepts including variables, operators, and control structures.',
                'instructions' => 'Answer all questions. This is an open-book quiz. Time limit: 30 minutes.',
                'time_limit' => 30,
                'passing_score' => 70.00,
                'max_attempts' => 2,
                'randomize_questions' => false,
                'randomize_choices' => true,
                'show_results' => true,
                'show_answers' => true,
                'is_published' => true,
                'available_from' => now()->subDays(3),
                'available_until' => now()->addDays(27),
                'published_at' => now()->subDays(3),
                'question_ids' => [4, 5, 6],
            ],
            [
                'instructor_id' => 3,
                'subject_id' => 4,
                'title' => 'Data Structures - Arrays and Lists',
                'description' => 'Assessment on arrays, linked lists, and their operations.',
                'instructions' => 'Complete all questions. Show your work for partial credit on essay questions.',
                'time_limit' => 45,
                'passing_score' => 65.00,
                'max_attempts' => 1,
                'randomize_questions' => true,
                'randomize_choices' => false,
                'show_results' => false,
                'show_answers' => false,
                'is_published' => true,
                'available_from' => now()->subDays(2),
                'available_until' => now()->addDays(28),
                'published_at' => now()->subDays(2),
                'question_ids' => [7, 8],
            ],
            [
                'instructor_id' => 2,
                'subject_id' => 2,
                'title' => 'Programming Practice Quiz',
                'description' => 'Practice quiz for final exam preparation.',
                'instructions' => 'This is a practice quiz. You can take it multiple times.',
                'time_limit' => 20,
                'passing_score' => 60.00,
                'max_attempts' => 99,
                'randomize_questions' => true,
                'randomize_choices' => true,
                'show_results' => true,
                'show_answers' => true,
                'is_published' => true,
                'available_from' => now()->subDays(10),
                'available_until' => now()->addDays(50),
                'published_at' => now()->subDays(10),
                'question_ids' => [4, 5, 6],
            ],
        ];

        foreach ($quizzes as $quizData) {
            $questionIds = $quizData['question_ids'];
            unset($quizData['question_ids']);
            
            // Only create if quiz with same title and instructor doesn't exist
            $existingQuiz = DB::table('quizzes')
                ->where('instructor_id', $quizData['instructor_id'])
                ->where('subject_id', $quizData['subject_id'])
                ->where('title', $quizData['title'])
                ->first();
            
            if (!$existingQuiz) {
                $quizId = DB::table('quizzes')->insertGetId(array_merge($quizData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                
                // Link questions to quiz
                foreach ($questionIds as $index => $questionId) {
                    // Only link if not already linked
                    if (!DB::table('quiz_question')
                        ->where('quiz_id', $quizId)
                        ->where('question_bank_id', $questionId)
                        ->exists()) {
                        DB::table('quiz_question')->insert([
                            'quiz_id' => $quizId,
                            'question_bank_id' => $questionId,
                            'order' => $index + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}