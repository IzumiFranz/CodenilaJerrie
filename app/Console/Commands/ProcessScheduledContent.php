<?php

namespace App\Console\Commands;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\AuditLog;
use Illuminate\Console\Command;

class ProcessScheduledContent extends Command
{
    protected $signature = 'content:process-scheduled';
    protected $description = 'Process scheduled publish/unpublish for lessons and quizzes';

    public function handle()
    {
        $this->info('Processing scheduled content...');
        
        $publishedLessons = $this->processLessons();
        $publishedQuizzes = $this->processQuizzes();
        
        $this->info("Processed {$publishedLessons} lessons and {$publishedQuizzes} quizzes");
        
        return 0;
    }
    
    private function processLessons(): int
    {
        $count = 0;
        
        // Auto-publish lessons
        $lessonsToPublish = Lesson::where('auto_publish', true)
            ->where('is_published', false)
            ->whereNotNull('scheduled_publish_at')
            ->where('scheduled_publish_at', '<=', now())
            ->get();
        
        foreach ($lessonsToPublish as $lesson) {
            $lesson->is_published = true;
            $lesson->published_at = now();
            $lesson->save();
            
            AuditLog::log('lesson_auto_published', $lesson);
            $this->line("Published lesson: {$lesson->title}");
            $count++;
            
            // TODO: Send notification to students
        }
        
        // Auto-unpublish lessons
        $lessonsToUnpublish = Lesson::where('is_published', true)
            ->whereNotNull('scheduled_unpublish_at')
            ->where('scheduled_unpublish_at', '<=', now())
            ->get();
        
        foreach ($lessonsToUnpublish as $lesson) {
            $lesson->is_published = false;
            $lesson->save();
            
            AuditLog::log('lesson_auto_unpublished', $lesson);
            $this->line("Unpublished lesson: {$lesson->title}");
            $count++;
        }
        
        return $count;
    }
    
    private function processQuizzes(): int
    {
        $count = 0;
        
        // Auto-publish quizzes
        $quizzesToPublish = Quiz::where('auto_publish', true)
            ->where('is_published', false)
            ->whereNotNull('scheduled_publish_at')
            ->where('scheduled_publish_at', '<=', now())
            ->get();
        
        foreach ($quizzesToPublish as $quiz) {
            // Don't publish if no questions
            if ($quiz->questions()->count() === 0) {
                $this->warn("Skipped quiz (no questions): {$quiz->title}");
                continue;
            }
            
            $quiz->is_published = true;
            $quiz->published_at = now();
            $quiz->save();
            
            AuditLog::log('quiz_auto_published', $quiz);
            $this->line("Published quiz: {$quiz->title}");
            $count++;
            
            // TODO: Send notification to students
        }
        
        // Auto-unpublish quizzes
        $quizzesToUnpublish = Quiz::where('is_published', true)
            ->whereNotNull('scheduled_unpublish_at')
            ->where('scheduled_unpublish_at', '<=', now())
            ->get();
        
        foreach ($quizzesToUnpublish as $quiz) {
            $quiz->is_published = false;
            $quiz->save();
            
            AuditLog::log('quiz_auto_unpublished', $quiz);
            $this->line("Unpublished quiz: {$quiz->title}");
            $count++;
        }
        
        return $count;
    }
}