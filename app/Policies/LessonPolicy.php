<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    /**
     * Determine whether the user can view any lessons.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view lessons list
        return true;
    }

    /**
     * Determine whether the user can view the lesson.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        // Admin can view any lesson
        if ($user->isAdmin()) {
            return true;
        }

        // Instructor can view their own lessons
        if ($user->isInstructor() && $user->instructor->id === $lesson->instructor_id) {
            return true;
        }

        // Student can only view published lessons in their enrolled subjects
        if ($user->isStudent()) {
            if (!$lesson->is_published) {
                return false;
            }

            // Check if student is enrolled in a section that has this subject
            $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
            $month = now()->month;
            $currentSemester = ($month >= 6 && $month <= 10) ? '1st' : (($month >= 11 || $month <= 3) ? '2nd' : 'summer');
            
            // Get enrolled section IDs for current term
            $enrolledSectionIds = $user->student
                ->enrollments()
                ->where('academic_year', $currentAcademicYear)
                ->where('semester', $currentSemester)
                ->where('status', 'enrolled')
                ->pluck('section_id');
            
            // Check if any of these sections have this subject assigned for current term
            return \App\Models\InstructorSubjectSection::whereIn('section_id', $enrolledSectionIds)
                ->where('subject_id', $lesson->subject_id)
                ->where('academic_year', $currentAcademicYear)
                ->where('semester', $currentSemester)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create lessons.
     */
    public function create(User $user): bool
    {
        // Only instructors can create lessons
        return $user->isInstructor();
    }

    /**
     * Determine whether the user can update the lesson.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        // Only the lesson owner (instructor) can update
        if ($user->isInstructor() && $user->instructor->id === $lesson->instructor_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the lesson.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        // Admin can delete any lesson
        if ($user->isAdmin()) {
            return true;
        }

        // Instructor can delete their own lessons
        if ($user->isInstructor() && $user->instructor->id === $lesson->instructor_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the lesson.
     */
    public function restore(User $user, Lesson $lesson): bool
    {
        // Only admin can restore deleted lessons
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the lesson.
     */
    public function forceDelete(User $user, Lesson $lesson): bool
    {
        // Only admin can force delete lessons
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can publish/unpublish the lesson.
     */
    public function publish(User $user, Lesson $lesson): bool
    {
        // Only the lesson owner can publish/unpublish
        if ($user->isInstructor() && $user->instructor->id === $lesson->instructor_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can download the lesson file.
     */
    public function download(User $user, Lesson $lesson): bool
    {
        // Same as view permission
        return $this->view($user, $lesson);
    }
}