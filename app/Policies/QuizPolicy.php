<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuizPolicy
{
    /**
     * Determine whether the user can view any quizzes.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view quiz lists
        return true;
    }

    /**
     * Determine whether the user can view the quiz.
     */
    public function view(User $user, Quiz $quiz): bool
    {
        // Admin can view any quiz
        if ($user->isAdmin()) {
            return true;
        }

        // Instructor can view their own quizzes
        if ($user->isInstructor() && $user->instructor->id === $quiz->instructor_id) {
            return true;
        }

        // Student can only view published quizzes in their enrolled subjects
        if ($user->isStudent()) {
            if (!$quiz->is_published) {
                return false;
            }

            // Check if quiz is currently available
            if (!$quiz->isAvailable()) {
                return false;
            }

            // Check if student is enrolled in a section that has this subject
            $enrolledSubjectIds = $user->student
                ->enrollments()
                ->where('status', 'enrolled')
                ->with('section.subjects')
                ->get()
                ->pluck('section.subjects')
                ->flatten()
                ->pluck('id')
                ->unique();

            return $enrolledSubjectIds->contains($quiz->subject_id);
        }

        return false;
    }

    /**
     * Determine whether the user can create quizzes.
     */
    public function create(User $user): bool
    {
        // Only instructors can create quizzes
        return $user->isInstructor();
    }

    /**
     * Determine whether the user can update the quiz.
     */
    public function update(User $user, Quiz $quiz): bool
    {
        // Only the quiz owner (instructor) can update
        if ($user->isInstructor() && $user->instructor->id === $quiz->instructor_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the quiz.
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        // Admin can delete any quiz
        if ($user->isAdmin()) {
            return true;
        }

        // Instructor can delete their own quizzes only if no attempts exist
        if ($user->isInstructor() && $user->instructor->id === $quiz->instructor_id) {
            // Check if quiz has any completed attempts
            $hasAttempts = $quiz->attempts()->where('status', 'completed')->exists();
            
            if ($hasAttempts) {
                return false; // Cannot delete quiz with completed attempts
            }
            
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the quiz.
     */
    public function restore(User $user, Quiz $quiz): bool
    {
        // Only admin can restore deleted quizzes
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the quiz.
     */
    public function forceDelete(User $user, Quiz $quiz): bool
    {
        // Only admin can force delete quizzes
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can publish/unpublish the quiz.
     */
    public function publish(User $user, Quiz $quiz): bool
    {
        // Only the quiz owner can publish/unpublish
        if ($user->isInstructor() && $user->instructor->id === $quiz->instructor_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can take the quiz.
     */
    public function take(User $user, Quiz $quiz): bool
    {
        // Only students can take quizzes
        if (!$user->isStudent()) {
            return false;
        }

        // Quiz must be published and available
        if (!$quiz->isAvailable()) {
            return false;
        }

        // Check if student is enrolled in the subject
        $enrolledSubjectIds = $user->student
            ->enrollments()
            ->where('status', 'enrolled')
            ->with('section.subjects')
            ->get()
            ->pluck('section.subjects')
            ->flatten()
            ->pluck('id')
            ->unique();

        if (!$enrolledSubjectIds->contains($quiz->subject_id)) {
            return false;
        }

        // Check if student has attempts remaining
        return $quiz->studentCanTakeQuiz($user->student);
    }

    /**
     * Determine whether the user can view quiz results.
     */
    public function viewResults(User $user, Quiz $quiz): bool
    {
        // Admin can view all results
        if ($user->isAdmin()) {
            return true;
        }

        // Instructor can view their quiz results
        if ($user->isInstructor() && $user->instructor->id === $quiz->instructor_id) {
            return true;
        }

        // Students can view their own results if quiz allows it
        if ($user->isStudent() && $quiz->show_results) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage quiz questions.
     */
    public function manageQuestions(User $user, Quiz $quiz): bool
    {
        // Only the quiz owner can manage questions
        if ($user->isInstructor() && $user->instructor->id === $quiz->instructor_id) {
            return true;
        }

        return false;
    }
}