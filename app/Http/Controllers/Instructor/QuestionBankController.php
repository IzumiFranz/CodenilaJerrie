<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Choice;
use App\Models\Subject;
use App\Models\InstructorSubjectSection;
use App\Models\AuditLog;
use App\Services\QuestionImportService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        $query = QuestionBank::where('instructor_id', $instructor->id)
            ->with(['subject', 'choices']);

        if ($request->filled('search')) {
            $query->where('question_text', 'like', "%{$request->search}%");
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get subjects taught by this instructor
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        if ($request->has('ai_generate')) {
        session()->flash('show_ai_modal', true);
    }

        return view('instructor.question-bank.index', compact('questions', 'subjects'));
    }

    public function create(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $selectedSubject = $request->get('subject');

        return view('instructor.question-bank.create', compact('subjects', 'selectedSubject'));
    }

    public function store(Request $request)
    {
        $instructor = auth()->user()->instructor;

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'question_text' => ['required', 'string'],
            'type' => ['required', 'in:multiple_choice,true_false,identification,essay'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'blooms_level' => ['nullable', 'in:remember,understand,apply,analyze,evaluate,create'],
            'explanation' => ['nullable', 'string'],
            
            // For multiple choice
            'choices' => ['required_if:type,multiple_choice', 'array', 'min:2', 'max:6'],
            'choices.*.text' => ['required_if:type,multiple_choice', 'string'],
            'choices.*.is_correct' => ['boolean'],
            
            // For true/false
            'correct_answer' => ['required_if:type,true_false', 'in:true,false'],
            
            // For identification
            'identification_answer' => ['required_if:type,identification', 'string'],
        ]);

        try {
            // Verify instructor teaches this subject
            $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$canTeach) {
                return back()->withInput()
                    ->with('error', 'You are not assigned to teach this subject.');
            }

            DB::beginTransaction();

            $question = QuestionBank::create([
                'instructor_id' => $instructor->id,
                'subject_id' => $validated['subject_id'],
                'question_text' => $validated['question_text'],
                'type' => $validated['type'],
                'points' => $validated['points'],
                'difficulty' => $validated['difficulty'],
                'blooms_level' => $validated['blooms_level'] ?? null,
                'explanation' => $validated['explanation'] ?? null,
            ]);

            // Handle choices based on question type
            if ($validated['type'] === 'multiple_choice') {
                $hasCorrect = false;
                foreach ($validated['choices'] as $index => $choiceData) {
                    $isCorrect = isset($choiceData['is_correct']) && $choiceData['is_correct'];
                    if ($isCorrect) $hasCorrect = true;
                    
                    Choice::create([
                        'question_id' => $question->id,
                        'choice_text' => $choiceData['text'],
                        'is_correct' => $isCorrect,
                        'order' => $index + 1,
                    ]);
                }

                if (!$hasCorrect) {
                    DB::rollBack();
                    return back()->withInput()
                        ->with('error', 'Please mark at least one correct answer.');
                }
            } elseif ($validated['type'] === 'true_false') {
                Choice::create([
                    'question_id' => $question->id,
                    'choice_text' => 'True',
                    'is_correct' => $validated['correct_answer'] === 'true',
                    'order' => 1,
                ]);
                Choice::create([
                    'question_id' => $question->id,
                    'choice_text' => 'False',
                    'is_correct' => $validated['correct_answer'] === 'false',
                    'order' => 2,
                ]);
            } elseif ($validated['type'] === 'identification') {
                Choice::create([
                    'question_id' => $question->id,
                    'choice_text' => $validated['identification_answer'],
                    'is_correct' => true,
                    'order' => 1,
                ]);
            }

            DB::commit();

            if ($request->filled('tags')) {
                $questionBank->syncTags($request->tags);
            }

            AuditLog::log('question_created', $question);
    
            return redirect()
                ->route('instructor.question-bank.index')
                ->with('success', 'Question created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create question: ' . $e->getMessage());
        }

    }

    public function show(QuestionBank $questionBank)
    {
        $this->authorize('view', $questionBank);
        $questionBank->load(['subject.course', 'choices', 'quizzes']);
        
        return view('instructor.question-bank.show', compact('questionBank'));
    }

    public function edit(QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);
        
        $instructor = auth()->user()->instructor;
        
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $questionBank->load('choices');

        return view('instructor.question-bank.edit', compact('questionBank', 'subjects'));
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'question_text' => ['required', 'string'],
            'type' => ['required', 'in:multiple_choice,true_false,identification,essay'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'blooms_level' => ['nullable', 'in:remember,understand,apply,analyze,evaluate,create'],
            'explanation' => ['nullable', 'string'],
            
            'choices' => ['required_if:type,multiple_choice', 'array', 'min:2', 'max:6'],
            'choices.*.text' => ['required_if:type,multiple_choice', 'string'],
            'choices.*.is_correct' => ['boolean'],
            
            'correct_answer' => ['required_if:type,true_false', 'in:true,false'],
            'identification_answer' => ['required_if:type,identification', 'string'],
        ]);

        try {
            $instructor = auth()->user()->instructor;

            $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$canTeach) {
                return back()->withInput()
                    ->with('error', 'You are not assigned to teach this subject.');
            }

            DB::beginTransaction();

            $oldValues = $questionBank->toArray();

            $questionBank->update([
                'subject_id' => $validated['subject_id'],
                'question_text' => $validated['question_text'],
                'type' => $validated['type'],
                'points' => $validated['points'],
                'difficulty' => $validated['difficulty'],
                'blooms_level' => $validated['blooms_level'] ?? null,
                'explanation' => $validated['explanation'] ?? null,
            ]);

            // Delete old choices and create new ones
            $questionBank->choices()->delete();

            if ($validated['type'] === 'multiple_choice') {
                $hasCorrect = false;
                foreach ($validated['choices'] as $index => $choiceData) {
                    $isCorrect = isset($choiceData['is_correct']) && $choiceData['is_correct'];
                    if ($isCorrect) $hasCorrect = true;
                    
                    Choice::create([
                        'question_id' => $questionBank->id,
                        'choice_text' => $choiceData['text'],
                        'is_correct' => $isCorrect,
                        'order' => $index + 1,
                    ]);
                }

                if (!$hasCorrect) {
                    DB::rollBack();
                    return back()->withInput()
                        ->with('error', 'Please mark at least one correct answer.');
                }
            } elseif ($validated['type'] === 'true_false') {
                Choice::create([
                    'question_id' => $questionBank->id,
                    'choice_text' => 'True',
                    'is_correct' => $validated['correct_answer'] === 'true',
                    'order' => 1,
                ]);
                Choice::create([
                    'question_id' => $questionBank->id,
                    'choice_text' => 'False',
                    'is_correct' => $validated['correct_answer'] === 'false',
                    'order' => 2,
                ]);
            } elseif ($validated['type'] === 'identification') {
                Choice::create([
                    'question_id' => $questionBank->id,
                    'choice_text' => $validated['identification_answer'],
                    'is_correct' => true,
                    'order' => 1,
                ]);
            }

            DB::commit();

            if ($request->has('tags')) {
                $questionBank->syncTags($request->tags ?? []);
            }

            AuditLog::log('question_updated', $questionBank, $oldValues, $questionBank->toArray());

            return redirect()
                ->route('instructor.question-bank.index')
                ->with('success', 'Question updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    public function destroy(QuestionBank $questionBank)
    {
        $this->authorize('delete', $questionBank);

        try {
            // Check if question is used in any active quiz
            if ($questionBank->quizzes()->count() > 0) {
                return back()->with('error', 'Cannot delete question that is used in quizzes.');
            }

            AuditLog::log('question_deleted', $questionBank);
            $questionBank->delete();

            return redirect()
                ->route('instructor.question-bank.index')
                ->with('success', 'Question deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete question: ' . $e->getMessage());
        }
    }

    public function duplicate(QuestionBank $questionBank)
    {
        $this->authorize('duplicate', $questionBank);

        try {
            DB::beginTransaction();

            $newQuestion = $questionBank->replicate();
            $newQuestion->question_text = $questionBank->question_text . ' (Copy)';
            $newQuestion->save();

            // Copy choices
            foreach ($questionBank->choices as $choice) {
                $newChoice = $choice->replicate();
                $newChoice->question_id = $newQuestion->id;
                $newChoice->save();
            }

            DB::commit();

            AuditLog::log('question_duplicated', $newQuestion);

            return redirect()
                ->route('instructor.question-bank.edit', $newQuestion)
                ->with('success', 'Question duplicated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to duplicate question: ' . $e->getMessage());
        }
    }

    public function validateQuestion(QuestionBank $questionBank)
    {
        $this->authorize('validate', $questionBank);

        // TODO: Implement AI validation
        // Use OpenAI API to validate question quality
        
        return back()->with('info', 'AI validation feature coming soon.');
    }

    public function generateWithAI(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'lesson_ids' => ['nullable', 'array'],
            'num_questions' => ['required', 'integer', 'min:1', 'max:50'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'question_types' => ['required', 'array'],
        ]);

        // TODO: Implement AI generation
        // Create AIJob and process in background
        
        return back()->with('info', 'AI generation feature coming soon.');
    }

    public function analytics(QuestionBank $questionBank)
    {
        $this->authorize('viewAnalytics', $questionBank);

        // TODO: Implement question analytics
        // - Times used
        // - Average score
        // - Difficulty index
        // - Discrimination index
        
        return view('instructor.question-bank.analytics', compact('questionBank'));
    }

    public function preview(QuestionBank $questionBank)
    {
        $this->authorize('view', $questionBank);
        
        $questionBank->load('choices', 'subject');
        
        return view('instructor.question-bank.preview', compact('questionBank'));
    }

    public function importForm()
    {
        $instructor = auth()->user()->instructor;
        
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        
        $subjects = Subject::whereIn('id', $subjectIds)->get();
        
        return view('instructor.question-bank.import', compact('subjects'));
    }
    
    /**
     * Process import
     */
    public function import(Request $request, QuestionImportService $importService)
    {
        $instructor = auth()->user()->instructor;
        
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'], // 5MB
        ]);
        
        try {
            // Verify instructor teaches this subject
            $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                ->where('subject_id', $validated['subject_id'])
                ->exists();
            
            if (!$canTeach) {
                return back()->with('error', 'You are not assigned to teach this subject.');
            }
            
            $subject = Subject::findOrFail($validated['subject_id']);
            $file = $request->file('file');
            $filePath = $file->getRealPath();
            
            // Import questions
            $results = $importService->importFromExcel($filePath, $instructor, $subject);
            
            AuditLog::log('questions_imported', null, [], $results);
            
            $message = "Import completed: {$results['success']} questions imported";
            if ($results['failed'] > 0) {
                $message .= ", {$results['failed']} failed";
            }
            
            return redirect()
                ->route('instructor.question-bank.index')
                ->with('success', $message)
                ->with('import_errors', $results['errors']);
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Download import template
     */
    public function downloadTemplate(QuestionImportService $importService)
    {
        try {
            $templateData = $importService->generateTemplate();
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers
            $sheet->fromArray($templateData['headers'], null, 'A1');
            
            // Style headers
            $headerStyle = [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
            ];
            $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
            
            // Add example rows
            $startRow = 2;
            foreach ($templateData['examples'] as $example) {
                $sheet->fromArray($example, null, 'A' . $startRow);
                $startRow++;
            }
            
            // Auto-size columns
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Add instructions sheet
            $instructionsSheet = $spreadsheet->createSheet();
            $instructionsSheet->setTitle('Instructions');
            $instructions = [
                ['Question Bank Import Template - Instructions'],
                [''],
                ['Column Descriptions:'],
                ['1. Question Text - The question text (required)'],
                ['2. Type - Question type: multiple_choice, true_false, identification, or essay (required)'],
                ['3. Points - Points for correct answer, 1-100 (required)'],
                ['4. Difficulty - easy, medium, or hard (required)'],
                ["5. Bloom's Level - remember, understand, apply, analyze, evaluate, or create (optional)"],
                ['6. Explanation - Explanation for the correct answer (optional)'],
                ['7-12. Choices - Answer choices (required for multiple_choice and true_false)'],
                [''],
                ['Format for Choices:'],
                ['- For multiple choice: "Choice Text|1" where |1 indicates the correct answer'],
                ['- For true/false: "True|1" or "False|1" in Choice 1 column'],
                ['- For identification: "Correct Answer|1" in Choice 1 column'],
                ['- For essay: Leave all choice columns empty'],
                [''],
                ['Examples are provided in the Template sheet.'],
            ];
            $instructionsSheet->fromArray($instructions, null, 'A1');
            $instructionsSheet->getColumnDimension('A')->setWidth(80);
            
            // Set active sheet back to template
            $spreadsheet->setActiveSheetIndex(0);
            
            // Generate file
            $writer = new Xlsx($spreadsheet);
            $filename = 'question_bank_import_template_' . now()->format('Y-m-d') . '.xlsx';
            
            // Output to browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate template: ' . $e->getMessage());
        }
    }
}