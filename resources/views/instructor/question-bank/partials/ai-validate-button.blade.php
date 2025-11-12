<button type="button" class="btn btn-sm btn-success validate-question-btn" 
        data-question-id="{{ $question->id }}"
        data-question-text="{{ $question->question_text }}"
        title="Validate with AI">
    <i class="fas fa-check-double"></i> Validate
</button>

@push('scripts')
<script>
$(document).on('click', '.validate-question-btn', function() {
    const questionId = $(this).data('question-id');
    const questionText = $(this).data('question-text');
    
    Swal.fire({
        title: 'Validate Question?',
        html: `<p>AI will analyze this question for:</p>
               <ul class="text-left">
                   <li>Grammar and clarity</li>
                   <li>Question quality</li>
                   <li>Difficulty appropriateness</li>
                   <li>Improvement suggestions</li>
               </ul>
               <p class="text-muted">${questionText.substring(0, 100)}...</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Validate',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/instructor/ai/validate-question/${questionId}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                }
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Validation Started!',
                text: 'You will be notified when the analysis is complete.',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = '/instructor/ai';
            });
        }
    });
});
</script>
@endpush