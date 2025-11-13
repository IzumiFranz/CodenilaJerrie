<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        // CS101 Questions
        $questions = [
            [
                'instructor_id' => 1,
                'subject_id' => 1,
                'question_text' => 'What is the primary function of the CPU in a computer system?',
                'type' => 'multiple_choice',
                'points' => 1.00,
                'difficulty' => 'easy',
                'bloom_level' => 'Remember',
                'explanation' => 'The CPU (Central Processing Unit) is responsible for executing instructions and processing data.',
                'is_validated' => true,
                'choices' => [
                    ['text' => 'To process and execute instructions', 'is_correct' => true],
                    ['text' => 'To store data permanently', 'is_correct' => false],
                    ['text' => 'To display output to users', 'is_correct' => false],
                    ['text' => 'To connect to the internet', 'is_correct' => false],
                ]
            ],
            [
                'instructor_id' => 1,
                'subject_id' => 1,
                'question_text' => 'RAM stands for Random Access Memory.',
                'type' => 'true_false',
                'points' => 1.00,
                'difficulty' => 'easy',
                'bloom_level' => 'Remember',
                'explanation' => 'RAM is indeed an acronym for Random Access Memory, a type of volatile computer memory.',
                'is_validated' => true,
                'choices' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            [
                'instructor_id' => 1,
                'subject_id' => 1,
                'question_text' => 'Which of the following is NOT a type of operating system?',
                'type' => 'multiple_choice',
                'points' => 1.00,
                'difficulty' => 'medium',
                'bloom_level' => 'Understand',
                'explanation' => 'Microsoft Word is application software, not an operating system.',
                'is_validated' => true,
                'choices' => [
                    ['text' => 'Windows', 'is_correct' => false],
                    ['text' => 'Linux', 'is_correct' => false],
                    ['text' => 'Microsoft Word', 'is_correct' => true],
                    ['text' => 'macOS', 'is_correct' => false],
                ]
            ],
            
            // CS102 Programming Questions
            [
                'instructor_id' => 2,
                'subject_id' => 2,
                'question_text' => 'What is a variable in programming?',
                'type' => 'multiple_choice',
                'points' => 1.00,
                'difficulty' => 'easy',
                'bloom_level' => 'Remember',
                'explanation' => 'A variable is a named storage location that can hold different values during program execution.',
                'is_validated' => true,
                'choices' => [
                    ['text' => 'A fixed value that never changes', 'is_correct' => false],
                    ['text' => 'A named storage location for data', 'is_correct' => true],
                    ['text' => 'A type of loop structure', 'is_correct' => false],
                    ['text' => 'A function parameter', 'is_correct' => false],
                ]
            ],
            [
                'instructor_id' => 2,
                'subject_id' => 2,
                'question_text' => 'Which loop structure checks the condition before executing the loop body?',
                'type' => 'multiple_choice',
                'points' => 1.00,
                'difficulty' => 'medium',
                'bloom_level' => 'Understand',
                'explanation' => 'A while loop evaluates the condition before executing the statements inside the loop.',
                'is_validated' => true,
                'choices' => [
                    ['text' => 'do-while loop', 'is_correct' => false],
                    ['text' => 'while loop', 'is_correct' => true],
                    ['text' => 'for loop (also correct)', 'is_correct' => false],
                    ['text' => 'switch statement', 'is_correct' => false],
                ]
            ],
            [
                'instructor_id' => 2,
                'subject_id' => 2,
                'question_text' => 'The modulo operator (%) returns the remainder of a division operation.',
                'type' => 'true_false',
                'points' => 1.00,
                'difficulty' => 'easy',
                'bloom_level' => 'Remember',
                'explanation' => 'The modulo operator divides two numbers and returns the remainder.',
                'is_validated' => true,
                'choices' => [
                    ['text' => 'True', 'is_correct' => true],
                    ['text' => 'False', 'is_correct' => false],
                ]
            ],
            
            // CS201 Data Structures Questions
            [
                'instructor_id' => 3,
                'subject_id' => 4,
                'question_text' => 'What is the time complexity of accessing an element in an array by index?',
                'type' => 'multiple_choice',
                'points' => 2.00,
                'difficulty' => 'medium',
                'bloom_level' => 'Apply',
                'explanation' => 'Array access by index is O(1) because it uses direct memory addressing.',
                'is_validated' => true,
                'choices' => [
                    ['text' => 'O(1)', 'is_correct' => true],
                    ['text' => 'O(n)', 'is_correct' => false],
                    ['text' => 'O(log n)', 'is_correct' => false],
                    ['text' => 'O(n²)', 'is_correct' => false],
                ]
            ],
            [
                'instructor_id' => 3,
                'subject_id' => 4,
                'question_text' => 'Which data structure follows the LIFO (Last In First Out) principle?',
                'type' => 'multiple_choice',
                'points' => 1.00,
                'difficulty' => 'easy',
                'bloom_level' => 'Remember',
                'explanation' => 'A stack operates on the LIFO principle where the last element added is the first to be removed.',
                'is_validated' => true,
                'choices' => [
                    ['text' => 'Queue', 'is_correct' => false],
                    ['text' => 'Stack', 'is_correct' => true],
                    ['text' => 'Array', 'is_correct' => false],
                    ['text' => 'Tree', 'is_correct' => false],
                ]
            ],
        ];

        foreach ($questions as $questionData) {
            $choices = $questionData['choices'];
            unset($questionData['choices']);
            
            $questionId = DB::table('question_bank')->insertGetId(array_merge($questionData, [
                'usage_count' => 0,
                'quality_score' => rand(70, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            
            // Insert choices for this question
            foreach ($choices as $index => $choice) {
                DB::table('choices')->insert([
                    'question_id' => $questionId,
                    'choice_text' => $choice['text'],
                    'is_correct' => $choice['is_correct'],
                    'order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}