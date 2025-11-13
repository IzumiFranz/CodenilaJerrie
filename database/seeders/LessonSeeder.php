<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $lessons = [
            // CS101 Lessons
            [
                'instructor_id' => 1,
                'subject_id' => 1,
                'title' => 'Introduction to Computer Systems',
                'content' => 'This lesson covers the basic components of computer systems including hardware, software, and their interactions. Students will learn about the fundamental architecture of computers and how different components work together.',
                'order' => 1,
                'is_published' => true,
                'published_at' => now()->subDays(20),
                'view_count' => rand(50, 150),
            ],
            [
                'instructor_id' => 1,
                'subject_id' => 1,
                'title' => 'Operating Systems Fundamentals',
                'content' => 'Understanding the role and functionality of operating systems. Topics include process management, memory management, file systems, and user interfaces.',
                'order' => 2,
                'is_published' => true,
                'published_at' => now()->subDays(15),
                'view_count' => rand(40, 120),
            ],
            [
                'instructor_id' => 1,
                'subject_id' => 1,
                'title' => 'Number Systems and Data Representation',
                'content' => 'Learn about binary, octal, decimal, and hexadecimal number systems. Understanding how computers represent and process data internally.',
                'order' => 3,
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'view_count' => rand(30, 100),
            ],
            
            // CS102 Lessons
            [
                'instructor_id' => 2,
                'subject_id' => 2,
                'title' => 'Introduction to Programming',
                'content' => 'Basic concepts of programming including variables, data types, operators, and expressions. Introduction to algorithm design and problem-solving.',
                'order' => 1,
                'is_published' => true,
                'published_at' => now()->subDays(18),
                'view_count' => rand(60, 180),
            ],
            [
                'instructor_id' => 2,
                'subject_id' => 2,
                'title' => 'Control Structures',
                'content' => 'Understanding conditional statements (if-else, switch) and loops (for, while, do-while). Learning how to control program flow and make decisions.',
                'order' => 2,
                'is_published' => true,
                'published_at' => now()->subDays(12),
                'view_count' => rand(50, 150),
            ],
            [
                'instructor_id' => 2,
                'subject_id' => 2,
                'title' => 'Functions and Modular Programming',
                'content' => 'Introduction to functions, parameters, return values, and scope. Benefits of modular programming and code reusability.',
                'order' => 3,
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'view_count' => rand(40, 130),
            ],
            
            // CS201 Lessons
            [
                'instructor_id' => 3,
                'subject_id' => 4,
                'title' => 'Arrays and Linked Lists',
                'content' => 'Understanding array data structure and its applications. Introduction to linked lists, types of linked lists, and their operations.',
                'order' => 1,
                'is_published' => true,
                'published_at' => now()->subDays(16),
                'view_count' => rand(45, 140),
            ],
            [
                'instructor_id' => 3,
                'subject_id' => 4,
                'title' => 'Stacks and Queues',
                'content' => 'Learning about stack and queue data structures. Implementation, applications, and real-world use cases of LIFO and FIFO structures.',
                'order' => 2,
                'is_published' => true,
                'published_at' => now()->subDays(9),
                'view_count' => rand(35, 110),
            ],
            
            // CS301 Web Development Lessons
            [
                'instructor_id' => 4,
                'subject_id' => 7,
                'title' => 'HTML Fundamentals',
                'content' => 'Introduction to HTML5, semantic elements, forms, and best practices for creating web pages. Understanding document structure and accessibility.',
                'order' => 1,
                'is_published' => true,
                'published_at' => now()->subDays(14),
                'view_count' => rand(55, 170),
            ],
            [
                'instructor_id' => 4,
                'subject_id' => 7,
                'title' => 'CSS Styling and Layout',
                'content' => 'Learning CSS syntax, selectors, box model, flexbox, and grid layout. Creating responsive and visually appealing web designs.',
                'order' => 2,
                'is_published' => true,
                'published_at' => now()->subDays(8),
                'view_count' => rand(48, 145),
            ],
        ];

        foreach ($lessons as $lesson) {
            DB::table('lessons')->insert(array_merge($lesson, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}