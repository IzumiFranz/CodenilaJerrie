@extends('layouts.instructor')

@section('title', 'Import Questions from Excel')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-excel mr-2"></i>Import Questions from Excel
            </h1>
            <a href="{{ route('instructor.question-bank.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Question Bank
            </a>
        </div>

        {{-- Instructions Card --}}
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-info-circle mr-2"></i>How to Import Questions
                </h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">
                        <strong>Download the Excel Template</strong>
                        <p class="text-muted mb-0">Click the button below to download the properly formatted template file.</p>
                    </li>
                    <li class="mb-2">
                        <strong>Fill in Your Questions</strong>
                        <p class="text-muted mb-0">Add your questions following the format shown in the examples.</p>
                    </li>
                    <li class="mb-2">
                        <strong>Upload the File</strong>
                        <p class="text-muted mb-0">Select your subject and upload the completed file below.</p>
                    </li>
                </ol>
                
                <div class="mt-3">
                    <a href="{{ route('instructor.question-bank.import.template') }}" 
                       class="btn btn-success">
                        <i class="fas fa-download"></i> Download Excel Template
                    </a>
                </div>
            </div>
        </div>

        {{-- Import Errors --}}
        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Import Errors ({{ count(session('import_errors')) }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        Some questions could not be imported. Please review the errors below:
                    </div>
                    <ul class="mb-0" style="max-height: 300px; overflow-y: auto;">
                        @foreach(session('import_errors') as $error)
                            <li class="text-danger">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Upload Form --}}
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-upload mr-2"></i>Upload Excel File
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" 
                      action="{{ route('instructor.question-bank.import.process') }}" 
                      enctype="multipart/form-data">
                    @csrf

                    {{-- Subject Selection --}}
                    <div class="form-group">
                        <label for="subject_id">
                            Subject <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('subject_id') is-invalid @enderror" 
                                id="subject_id" 
                                name="subject_id" 
                                required>
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_code }} - {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Select the subject for these questions
                        </small>
                    </div>

                    {{-- File Upload --}}
                    <div class="form-group">
                        <label for="file">
                            Excel File <span class="text-danger">*</span>
                        </label>
                        <div class="custom-file">
                            <input type="file" 
                                   class="custom-file-input @error('file') is-invalid @enderror" 
                                   id="file" 
                                   name="file" 
                                   accept=".xlsx,.xls"
                                   required>
                            <label class="custom-file-label" for="file">Choose file...</label>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            Accepted formats: .xlsx, .xls (Max size: 5MB)
                        </small>
                    </div>

                    {{-- Important Notes --}}
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Important Notes:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Make sure your Excel file follows the template format exactly</li>
                            <li>For multiple choice questions, mark correct answers with "|1" after the choice text</li>
                            <li>Minimum 2 choices required for multiple choice questions</li>
                            <li>At least one correct answer is required for multiple choice</li>
                            <li>Essay questions don't need any choices</li>
                        </ul>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('instructor.question-bank.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Import Questions
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Format Examples --}}
        <div class="card shadow mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-lightbulb mr-2"></i>Format Examples
                </h6>
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold">Multiple Choice Example:</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Question Text</th>
                                <th>Type</th>
                                <th>Points</th>
                                <th>Difficulty</th>
                                <th>Choice 1</th>
                                <th>Choice 2</th>
                                <th>Choice 3</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>What is 2+2?</td>
                                <td>multiple_choice</td>
                                <td>1</td>
                                <td>easy</td>
                                <td>4|1</td>
                                <td>3</td>
                                <td>5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="font-weight-bold">True/False Example:</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Question Text</th>
                                <th>Type</th>
                                <th>Points</th>
                                <th>Difficulty</th>
                                <th>Choice 1</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>The sky is blue.</td>
                                <td>true_false</td>
                                <td>1</td>
                                <td>easy</td>
                                <td>True|1</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="font-weight-bold">Identification Example:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Question Text</th>
                                <th>Type</th>
                                <th>Points</th>
                                <th>Difficulty</th>
                                <th>Choice 1</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Who discovered gravity?</td>
                                <td>identification</td>
                                <td>2</td>
                                <td>medium</td>
                                <td>Isaac Newton|1</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update file input label with selected filename
    $('.custom-file-input').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });
    
    // Form validation before submit
    $('form').on('submit', function(e) {
        const fileInput = $('#file')[0];
        const subjectInput = $('#subject_id');
        
        if (!subjectInput.val()) {
            e.preventDefault();
            alert('Please select a subject');
            subjectInput.focus();
            return false;
        }
        
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please select a file to upload');
            return false;
        }
        
        const file = fileInput.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (file.size > maxSize) {
            e.preventDefault();
            alert('File size must not exceed 5MB');
            return false;
        }
        
        const validExtensions = ['xlsx', 'xls'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (!validExtensions.includes(fileExtension)) {
            e.preventDefault();
            alert('Please upload a valid Excel file (.xlsx or .xls)');
            return false;
        }
        
        // Show loading indicator
        $(this).find('button[type="submit"]')
            .html('<i class="fas fa-spinner fa-spin"></i> Importing...')
            .prop('disabled', true);
    });
});
</script>
@endpush