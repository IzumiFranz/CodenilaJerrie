<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Get some random student user IDs
        $students = DB::table('users')->where('role', 'student')->limit(20)->pluck('id');
        
        $notificationTypes = [
            [
                'type' => 'info',
                'title' => 'New Lesson Published',
                'message' => 'A new lesson "Introduction to Computer Systems" has been published in CS101.',
                'action_url' => '/lessons/1',
            ],
            [
                'type' => 'warning',
                'title' => 'Quiz Deadline Approaching',
                'message' => 'The quiz "Introduction to Computing - Midterm Exam" will close in 2 days.',
                'action_url' => '/quizzes/1',
            ],
            [
                'type' => 'success',
                'title' => 'Quiz Result Available',
                'message' => 'Your results for "Programming Fundamentals - Quiz 1" are now available.',
                'action_url' => '/quiz-attempts/1',
            ],
            [
                'type' => 'info',
                'title' => 'New Quiz Available',
                'message' => 'A new quiz "Data Structures - Arrays and Lists" is now available.',
                'action_url' => '/quizzes/3',
            ],
            [
                'type' => 'danger',
                'title' => 'Enrollment Reminder',
                'message' => 'Enrollment for next semester opens in 1 week. Please prepare your documents.',
                'action_url' => '/enrollment',
            ],
        ];
        
        foreach ($students as $userId) {
            // Create 2-5 notifications per student
            $notifCount = rand(2, 5);
            
            for ($i = 0; $i < $notifCount; $i++) {
                $notif = $notificationTypes[array_rand($notificationTypes)];
                $isRead = rand(1, 100) <= 40; // 40% chance notification is read
                
                DB::table('notifications')->insert([
                    'user_id' => $userId,
                    'type' => $notif['type'],
                    'title' => $notif['title'],
                    'message' => $notif['message'],
                    'action_url' => $notif['action_url'],
                    'read_at' => $isRead ? now()->subDays(rand(1, 5)) : null,
                    'created_at' => now()->subDays(rand(1, 10)),
                    'updated_at' => now()->subDays(rand(1, 10)),
                ]);
            }
        }
    }
}

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        // Get some students
        $students = DB::table('users')->where('role', 'student')->limit(15)->get();
        
        $feedbackData = [
            [
                'type' => 'lesson',
                'subject' => 'Great lesson content',
                'message' => 'The lesson on Computer Systems was very informative and well-structured. The examples helped me understand the concepts better.',
                'rating' => 5,
                'status' => 'responded',
                'response' => 'Thank you for your positive feedback! I\'m glad the lesson was helpful.',
            ],
            [
                'type' => 'quiz',
                'subject' => 'Quiz was too difficult',
                'message' => 'The midterm exam had some questions that weren\'t covered in the lessons. Could you please review the content alignment?',
                'rating' => 3,
                'status' => 'responded',
                'response' => 'Thank you for bringing this to my attention. I\'ll review the quiz questions and make sure they align with our lessons.',
            ],
            [
                'type' => 'instructor',
                'subject' => 'Request for additional examples',
                'message' => 'Could you provide more programming examples during class? It would help us understand better.',
                'rating' => 4,
                'status' => 'pending',
                'response' => null,
            ],
            [
                'type' => 'general',
                'subject' => 'System suggestion',
                'message' => 'It would be great if we could download the lessons in PDF format for offline studying.',
                'rating' => null,
                'status' => 'pending',
                'response' => null,
            ],
            [
                'type' => 'lesson',
                'subject' => 'Video content request',
                'message' => 'The written content is good, but having video lectures would make it easier to follow along.',
                'rating' => 4,
                'status' => 'responded',
                'response' => 'Great suggestion! I\'m working on creating video content for future lessons.',
            ],
        ];
        
        foreach ($students->take(10) as $index => $student) {
            $feedback = $feedbackData[$index % count($feedbackData)];
            $isAnonymous = rand(1, 100) <= 20; // 20% anonymous
            
            $feedbackId = DB::table('feedbacks')->insertGetId([
                'user_id' => $student->id,
                'type' => $feedback['type'],
                'subject' => $feedback['subject'],
                'message' => $feedback['message'],
                'rating' => $feedback['rating'],
                'status' => $feedback['status'],
                'is_anonymous' => $isAnonymous,
                'response' => $feedback['response'],
                'response_by_id' => $feedback['response'] ? rand(2, 6) : null, // Random instructor
                'responded_at' => $feedback['response'] ? now()->subDays(rand(1, 3)) : null,
                'created_at' => now()->subDays(rand(1, 15)),
                'updated_at' => now()->subDays(rand(1, 15)),
            ]);
        }
    }
}