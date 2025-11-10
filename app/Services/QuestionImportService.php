<?php

namespace App\Services;

use App\Models\QuestionBank;
use App\Models\Choice;
use App\Models\Instructor;
use App\Models\Subject;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class QuestionImportService
{
    /**
     * Import questions from an Excel file for a given instructor and subject.
     *
     * @param string $filePath
     * @param Instructor $instructor
     * @param Subject $subject
     * @return array
     *   [
     *       'success' => int,
     *       'failed' => int,
     *       'errors' => array
     *   ]
     */
    public function importFromExcel(string $filePath, Instructor $instructor, Subject $subject): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we skipped header

                try {
                    $this->importQuestion($row, $instructor, $subject);
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $results['errors'][] = "File read error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Import a single question from a row.
     *
     * Expected row format:
     * 0: Question Text
     * 1: Type (multiple_choice, true_false, identification, essay)
     * 2: Points
     * 3: Difficulty (easy, medium, hard)
     * 4: Bloom's Level (optional)
     * 5: Explanation (optional)
     * 6-11: Choices (for multiple choice, format: "Choice Text|1" where 1 = correct)
     *
     * @param array $row
     * @param Instructor $instructor
     * @param Subject $subject
     * @return void
     * @throws \Exception
     */
    private function importQuestion(array $row, Instructor $instructor, Subject $subject): void
    {
        $questionText = trim($row[0] ?? '');
        $type = strtolower(trim($row[1] ?? ''));
        $points = (int)($row[2] ?? 1);
        $difficulty = strtolower(trim($row[3] ?? 'medium'));
        $bloomsLevel = trim($row[4] ?? '');
        $explanation = trim($row[5] ?? '');

        // Validate required fields
        if (empty($questionText)) {
            throw new \Exception("Question text is required");
        }

        if (!in_array($type, ['multiple_choice', 'true_false', 'identification', 'essay'])) {
            throw new \Exception("Invalid question type: {$type}");
        }

        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            throw new \Exception("Invalid difficulty: {$difficulty}");
        }

        if ($points < 1 || $points > 100) {
            throw new \Exception("Points must be between 1 and 100");
        }

        DB::beginTransaction();

        try {
            // Create question
            $question = QuestionBank::create([
                'instructor_id' => $instructor->id,
                'subject_id' => $subject->id,
                'question_text' => $questionText,
                'type' => $type,
                'points' => $points,
                'difficulty' => $difficulty,
                'bloom_level' => $bloomsLevel ?: null,
                'explanation' => $explanation ?: null,
            ]);

            // Create choices based on type
            if ($type === 'multiple_choice') {
                $this->createMultipleChoices($question, array_slice($row, 6));
            } elseif ($type === 'true_false') {
                $this->createTrueFalseChoices($question, array_slice($row, 6));
            } elseif ($type === 'identification') {
                $this->createIdentificationAnswer($question, array_slice($row, 6));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create multiple choice answers for a question.
     *
     * @param QuestionBank $question
     * @param array $choicesData
     * @return void
     * @throws \Exception
     */
    private function createMultipleChoices(QuestionBank $question, array $choicesData): void
    {
        $choices = [];
        $hasCorrect = false;

        foreach ($choicesData as $choiceData) {
            if (empty($choiceData)) continue;

            $parts = explode('|', $choiceData);
            $choiceText = trim($parts[0]);
            $isCorrect = isset($parts[1]) && trim($parts[1]) === '1';

            if (!empty($choiceText)) {
                $choices[] = [
                    'choice_text' => $choiceText,
                    'is_correct' => $isCorrect,
                    'order' => count($choices) + 1,
                ];

                if ($isCorrect) $hasCorrect = true;
            }
        }

        if (count($choices) < 2) {
            throw new \Exception("Multiple choice questions must have at least 2 choices");
        }

        if (!$hasCorrect) {
            throw new \Exception("Multiple choice questions must have at least one correct answer");
        }

        foreach ($choices as $choiceData) {
            Choice::create([
                'question_id' => $question->id,
                'choice_text' => $choiceData['choice_text'],
                'is_correct' => $choiceData['is_correct'],
                'order' => $choiceData['order'],
            ]);
        }
    }

    /**
     * Create True/False choices for a question.
     *
     * @param QuestionBank $question
     * @param array $choicesData
     * @return void
     */
    private function createTrueFalseChoices(QuestionBank $question, array $choicesData): void
    {
        $correctAnswer = 'true';

        if (!empty($choicesData[0])) {
            $parts = explode('|', $choicesData[0]);
            $answer = strtolower(trim($parts[0]));
            if (in_array($answer, ['true', 'false'])) {
                $correctAnswer = $answer;
            }
        }

        Choice::create([
            'question_id' => $question->id,
            'choice_text' => 'True',
            'is_correct' => $correctAnswer === 'true',
            'order' => 1,
        ]);

        Choice::create([
            'question_id' => $question->id,
            'choice_text' => 'False',
            'is_correct' => $correctAnswer === 'false',
            'order' => 2,
        ]);
    }

    /**
     * Create identification answer for a question.
     *
     * @param QuestionBank $question
     * @param array $choicesData
     * @return void
     * @throws \Exception
     */
    private function createIdentificationAnswer(QuestionBank $question, array $choicesData): void
    {
        $answer = trim($choicesData[0] ?? '');

        if (empty($answer)) {
            throw new \Exception("Identification questions must have a correct answer");
        }

        // Remove the "|1" marker if present
        $answer = explode('|', $answer)[0];

        Choice::create([
            'question_id' => $question->id,
            'choice_text' => trim($answer),
            'is_correct' => true,
            'order' => 1,
        ]);
    }

    /**
     * Generate a template for importing questions (for Excel or CSV).
     *
     * @return array
     */
    public function generateTemplate(): array
    {
        return [
            'headers' => [
                'Question Text',
                'Type (multiple_choice/true_false/identification/essay)',
                'Points',
                'Difficulty (easy/medium/hard)',
                "Bloom's Level (optional)",
                'Explanation (optional)',
                'Choice 1',
                'Choice 2',
                'Choice 3',
                'Choice 4',
                'Choice 5',
                'Choice 6',
            ],
            'examples' => [
                [
                    'What is the capital of France?',
                    'multiple_choice',
                    '1',
                    'easy',
                    'remember',
                    'Paris is the capital and largest city of France',
                    'Paris|1',
                    'London',
                    'Berlin',
                    'Rome',
                    '',
                    '',
                ],
                [
                    'The Earth is flat.',
                    'true_false',
                    '1',
                    'easy',
                    'remember',
                    'The Earth is approximately spherical',
                    'False|1',
                    '',
                    '',
                    '',
                    '',
                    '',
                ],
                [
                    'Who wrote Romeo and Juliet?',
                    'identification',
                    '2',
                    'medium',
                    'remember',
                    '',
                    'William Shakespeare|1',
                    '',
                    '',
                    '',
                    '',
                    '',
                ],
                [
                    'Explain the concept of object-oriented programming.',
                    'essay',
                    '10',
                    'hard',
                    'analyze',
                    'Should cover: classes, objects, inheritance, polymorphism',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ],
            ],
        ];
    }
}
