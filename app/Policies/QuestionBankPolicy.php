<?php

namespace App\Policies;

use App\Models\QuestionBank;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuestionBankPolicy
{
    /**
     * Determine whether the user can view any questions.
     */
    public function viewAny(User $user): bool
    {
        // Only instructors and admins can view question banks
        return $user->isInstructor() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the question.
     */
    public function view(User $user, QuestionBank $question): bool
    {
        // Admin can view any question
        if ($user->isAdmin()) {
            return true;
        }

        // Instructor can view their own questions
        if ($user->isInstructor() && $user->instructor->id === $question->instructor_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create questions.
     */
    public function create(User $user): bool
    {
        // Only instructors can create questions
        return $user->isInstructor();
    }

    /**
     * Determine whether the user can update the question.
     */
    public function update(User $user, QuestionBank $question): bool
    {
        // Only the question owner (instructor) can update
        if ($user->isInstructor() && $user->instructor->id === $question->instructor_id) {
            // Check if question is being used in any active quizzes
            $usedInActiveQuizzes = $question->quizzes()
                ->where('is_published', true)
                ->exists();

            // Warn but allow update (could add stricter check if needed)
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the question.
     */
    public function delete(User $user, QuestionBank $question): bool
    {
        // Admin can delete any question
        if ($user->isAdmin()) {
            return true;
        }

        // Instructor can delete their own questions
        if ($user->isInstructor() && $user->instructor->id === $question->instructor_id) {
            // Check if question is being used in any quizzes
            $usedInQuizzes = $question->quizzes()->exists();
            
            if ($usedInQuizzes) {
                return false; // Cannot delete question used in quizzes
            }
            
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the question.
     */
    public function restore(User $user, QuestionBank $question): bool
    {
        // Only admin can restore deleted questions
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the question.
     */
    public function forceDelete(User $user, QuestionBank $question): bool
    {
        // Only admin can force delete questions
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can duplicate the question.
     */
    public function duplicate(User $user, QuestionBank $question): bool
    {
        // Only instructors can duplicate questions
        if (!$user->isInstructor()) {
            return false;
        }

        // Instructor can duplicate their own questions
        if ($user->instructor->id === $question->instructor_id) {
            return true;
        }

        // Instructor can duplicate questions from the same subject
        // if they teach that subject
        $instructorSubjects = $user->instructor
            ->subjects()
            ->pluck('subjects.id');

        return $instructorSubjects->contains($question->subject_id);
    }

    /**
     * Determine whether the user can validate the question using AI.
     */
    public function validate(User $user, QuestionBank $question): bool
    {
        // Only the question owner can validate
        if ($user->isInstructor() && $user->instructor->id === $question->instructor_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view question analytics.
     */
    public function viewAnalytics(User $user, QuestionBank $question): bool
    {
        // Admin can view all analytics
        if ($user->isAdmin()) {
            return true;
        }

        // Instructor can view analytics for their questions
        if ($user->isInstructor() && $user->instructor->id === $question->instructor_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can generate questions using AI.
     */
    public function generateWithAI(User $user): bool
    {
        // Only instructors can generate questions with AI
        return $user->isInstructor();
    }
}