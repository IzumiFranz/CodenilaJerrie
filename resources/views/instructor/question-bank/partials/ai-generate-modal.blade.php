<div class="modal fade" id="aiGenerateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="aiGenerateForm" action="{{ route('instructor.ai.generate-questions') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-robot"></i> AI Question Generation
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>AI will analyze your lessons</strong> and generate high-quality questions based on the content.
                    </div>

                    <!-- Subject Selection -->
                    <div class="form-group">
                        <label for="subject_id">Select Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" id="subject_id" class="form-control" required>
                            <option value="">Choose subject...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->subject_name }} ({{ $subject->subject_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lessons Selection -->
                    <div class="form-group">
                        <label for="lessons">Select Lessons <span class="text-danger">*</span></label>
                        <select name="lesson_ids[]" id="lessons" class="form-control" multiple size="5" required>
                            <option value="">Select a subject first...</option>
                        </select>
                        <small class="form-text text-muted">
                            Hold Ctrl (Cmd on Mac) to select multiple lessons
                        </small>
                    </div>

                    <!-- Number of Questions -->
                    <div class="form-group">
                        <label for="count">Number of Questions <span class="text-danger">*</span></label>
                        <input type="number" name="count" id="count" class="form-control" 
                               value="10" min="1" max="50" required>
                        <small class="form-text text-muted">
                            Generate between 1 and 50 questions
                        </small>
                    </div>

                    <!-- Difficulty Level -->
                    <div class="form-group">
                        <label for="difficulty">Difficulty Level <span class="text-danger">*</span></label>
                        <select name="difficulty" id="difficulty" class="form-control" required>
                            <option value="easy">Easy</option>
                            <option value="medium" selected>Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>

                    <!-- Question Types -->
                    <div class="form-group">
                        <label>Question Types <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="type_mc" name="types[]" value="multiple_choice" checked>
                                    <label class="custom-control-label" for="type_mc">
                                        <i class="fas fa-list"></i> Multiple Choice
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="type_tf" name="types[]" value="true_false">
                                    <label class="custom-control-label" for="type_tf">
                                        <i class="fas fa-check-circle"></i> True/False
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="type_id" name="types[]" value="identification">
                                    <label class="custom-control-label" for="type_id">
                                        <i class="fas fa-edit"></i> Identification
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="type_essay" name="types[]" value="essay">
                                    <label class="custom-control-label" for="type_essay">
                                        <i class="fas fa-align-left"></i> Essay
                                    </label>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Select at least one question type
                        </small>
                    </div>

                    <!-- Estimated Time -->
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i>
                        <strong>Estimated Time:</strong> 
                        <span id="estimatedTime">30-60 seconds</span>
                        <br>
                        <small>You'll receive a notification when complete</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="generateBtn">
                        <i class="fas fa-magic"></i> Generate Questions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load lessons when subject is selected
    $('#subject_id').on('change', function() {
        const subjectId = $(this).val();
        const $lessons = $('#lessons');
        
        if (!subjectId) {
            $lessons.html('<option value="">Select a subject first...</option>');
            return;
        }
        
        $lessons.html('<option value="">Loading...</option>');
        
        $.ajax({
            url: `/instructor/subjects/${subjectId}/lessons`,
            method: 'GET',
            success: function(lessons) {
                if (lessons.length === 0) {
                    $lessons.html('<option value="">No published lessons found</option>');
                } else {
                    let options = '';
                    lessons.forEach(lesson => {
                        options += `<option value="${lesson.id}">${lesson.title}</option>`;
                    });
                    $lessons.html(options);
                }
            },
            error: function() {
                $lessons.html('<option value="">Error loading lessons</option>');
            }
        });
    });

    // Update estimated time based on question count
    $('#count').on('input', function() {
        const count = parseInt($(this).val()) || 10;
        const seconds = Math.ceil(count * 5); // ~5 seconds per question
        $('#estimatedTime').text(`${seconds}-${seconds + 30} seconds`);
    });

    // Form submission
    $('#aiGenerateForm').on('submit', function(e) {
        e.preventDefault();
        
        const $btn = $('#generateBtn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#aiGenerateModal').modal('hide');
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Generation Started!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
                    // Redirect to AI dashboard after 2 seconds
                    setTimeout(() => {
                        window.location.href = '/instructor/ai';
                    }, 2000);
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-magic"></i> Generate Questions');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Generation Failed',
                    text: xhr.responseJSON?.message || 'An error occurred'
                });
            }
        });
    });
});
</script>
@endpush