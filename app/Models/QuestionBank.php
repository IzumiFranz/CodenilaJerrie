<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionBank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'question_bank';

    protected $fillable = [
        'instructor_id',
        'subject_id',
        'question_text',
        'type',
        'points',
        'difficulty',
        'bloom_level',
        'explanation',
        'tags',
        'usage_count',
        'difficulty_index',
        'discrimination_index',
        'is_validated',
        'quality_score',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'decimal:2',
            'difficulty_index' => 'decimal:2',
            'discrimination_index' => 'decimal:2',
            'is_validated' => 'boolean',
            'quality_score' => 'integer',
            'usage_count' => 'integer',
            'tags' => 'array',
        ];
    }

    // Relationships
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function choices()
    {
        return $this->hasMany(Choice::class, 'question_id');
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_question')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function quizAnswers()
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }

    public function tags()
    {
        return $this->belongsToMany(QuestionTag::class, 'question_tag_pivot')
            ->withTimestamps();
    }

    // Helper Methods
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function hasChoices(): bool
    {
        return in_array($this->type, ['multiple_choice', 'true_false']);
    }

    public function getCorrectChoice()
    {
        return $this->choices()->where('is_correct', true)->first();
    }

    public function isMultipleChoice(): bool
    {
        return $this->type === 'multiple_choice';
    }

    public function isTrueFalse(): bool
    {
        return $this->type === 'true_false';
    }

    public function isIdentification(): bool
    {
        return $this->type === 'identification';
    }

    public function isEssay(): bool
    {
        return $this->type === 'essay';
    }

    public function calculateDifficultyIndex(int $correctCount, int $totalAttempts): void
    {
        if ($totalAttempts > 0) {
            $this->difficulty_index = round($correctCount / $totalAttempts, 2);
            $this->save();
        }
    }

    public function calculateDiscriminationIndex(int $topCorrect, int $bottomCorrect, int $groupSize): void
    {
        if ($groupSize > 0) {
            $this->discrimination_index = round(($topCorrect - $bottomCorrect) / $groupSize, 2);
            $this->save();
        }
    }

    public function syncTags(array $tagIds): void
    {
        // Use the relationship method to get old tag IDs
        $oldTagIds = $this->tags()->pluck('id')->toArray();
        
        $this->tags()->sync($tagIds);
        
        // Update question counts for all affected tags
        $allTagIds = array_unique(array_merge($oldTagIds, $tagIds));
        foreach ($allTagIds as $tagId) {
            $tag = QuestionTag::find($tagId);
            if ($tag) {
                $tag->updateQuestionCount();
            }
        }
    }

    public function hasTag(string $slug): bool
    {
        return $this->tags()->where('slug', $slug)->exists();
    }

    /**
     * Get tags as a comma-separated string (accessor)
     * Note: This conflicts with the relationship, so use $question->tags()->get() for collection
     * or access the relationship directly when eager loaded
     */
    public function getTagsStringAttribute(): string
    {
        // Use the relationship method to avoid undefined property error
        return $this->tags()->pluck('name')->join(', ');
    }

}
