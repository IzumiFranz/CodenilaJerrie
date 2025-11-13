<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            SpecializationSeeder::class,
            CourseSeeder::class,
            UserSeeder::class,
            AdminSeeder::class,
            InstructorSeeder::class,
            StudentSeeder::class,
            SectionSeeder::class,
            SubjectSeeder::class,
            EnrollmentSeeder::class,
            InstructorSubjectSectionSeeder::class,
            LessonSeeder::class,
            QuestionBankSeeder::class,
            QuizSeeder::class,
            QuizAttemptSeeder::class,
            NotificationSeeder::class,
            FeedbackSeeder::class,
        ]);

        $this->command->info('All seeders completed successfully!');
    }
}