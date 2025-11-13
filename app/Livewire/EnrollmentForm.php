<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\Course;
use App\Models\Section;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class EnrollmentForm extends Component
{
    use WithFileUploads;

    public $students = [];
    public $courses = [];
    public $sections = [];
    public $academicYears = [];

    public $student_id = '';
    public $course_id = '';
    public $section_id = '';
    public $academic_year = '';
    public $semester = '1st';
    public $enrollment_date = '';

    public $csv_file;

    public $bulkResults = []; // To store success/failure per row

    protected $rules = [
        'student_id' => 'required|exists:students,id',
        'section_id' => 'required|exists:sections,id',
        'academic_year' => 'required|string',
        'semester' => 'required|in:1st,2nd,summer',
        'enrollment_date' => 'required|date',
    ];

    public function mount()
    {
        $this->students = Student::with('user', 'course')
            ->whereHas('user', fn($q) => $q->where('status', 'active'))
            ->get();
        
        $this->courses = Course::where('is_active', true)->get();
        $this->academicYears = $this->getAcademicYears();
        $this->enrollment_date = now()->format('Y-m-d');
    }

    public function updatedCourseId($value)
    {
        $this->sections = Section::where('course_id', $value)
            ->where('is_active', true)
            ->orderBy('year_level')
            ->orderBy('section_name')
            ->get();
        
        $this->section_id = '';
    }

    public function enroll()
    {
        $this->validate();

        try {
            $exists = Enrollment::where('student_id', $this->student_id)
                ->where('section_id', $this->section_id)
                ->where('academic_year', $this->academic_year)
                ->where('semester', $this->semester)
                ->exists();

            if ($exists) {
                session()->flash('error', 'Student is already enrolled in this section.');
                return;
            }

            $section = Section::findOrFail($this->section_id);
            if (!$section->hasAvailableSlots($this->academic_year, $this->semester)) {
                session()->flash('error', 'Section is full.');
                return;
            }

            Enrollment::create([
                'student_id' => $this->student_id,
                'section_id' => $this->section_id,
                'academic_year' => $this->academic_year,
                'semester' => $this->semester,
                'enrollment_date' => $this->enrollment_date,
                'status' => 'enrolled',
            ]);

            session()->flash('success', 'Student enrolled successfully!');
            $this->reset(['student_id', 'course_id', 'section_id']);
            $this->dispatch('enrollment-created');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to enroll student: ' . $e->getMessage());
        }
    }

    public function bulkEnroll()
    {
        $this->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $this->bulkResults = [];
        $path = $this->csv_file->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('trim', array_shift($rows));

        foreach ($rows as $index => $row) {
            $data = array_combine($header, $row);

            $rowNumber = $index + 2; // +2 because CSV header is line 1
            $result = ['row' => $rowNumber, 'status' => 'success', 'message' => ''];

            $validator = Validator::make($data, [
                'student_number' => 'required|exists:students,student_number',
                'section_id' => 'required|exists:sections,id',
            ]);

            if ($validator->fails()) {
                $result['status'] = 'failed';
                $result['message'] = implode(', ', $validator->errors()->all());
                $this->bulkResults[] = $result;
                continue;
            }

            $student = Student::where('student_number', $data['student_number'])->first();
            $section = Section::find($data['section_id']);

            // Skip if section is full
            if (!$section->hasAvailableSlots($this->academic_year, $this->semester)) {
                $result['status'] = 'failed';
                $result['message'] = 'Section full';
                $this->bulkResults[] = $result;
                continue;
            }

            // Skip if already enrolled
            $exists = Enrollment::where('student_id', $student->id)
                ->where('section_id', $section->id)
                ->where('academic_year', $this->academic_year)
                ->where('semester', $this->semester)
                ->exists();

            if ($exists) {
                $result['status'] = 'failed';
                $result['message'] = 'Already enrolled';
                $this->bulkResults[] = $result;
                continue;
            }

            Enrollment::create([
                'student_id' => $student->id,
                'section_id' => $section->id,
                'academic_year' => $this->academic_year,
                'semester' => $this->semester,
                'enrollment_date' => now()->format('Y-m-d'),
                'status' => 'enrolled',
            ]);

            $this->bulkResults[] = $result;
        }

        session()->flash('success', 'Bulk enrollment processed.');
    }

    private function getAcademicYears(): array
    {
        $years = [];
        $currentYear = now()->year;
        for ($i = -1; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[] = $year . '-' . ($year + 1);
        }
        return $years;
    }

    public function render()
    {
        return view('livewire.enrollment-form');
    }
}
