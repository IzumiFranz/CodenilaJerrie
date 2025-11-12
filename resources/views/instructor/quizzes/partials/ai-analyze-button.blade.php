<button type="button" class="btn btn-info btn-sm analyze-quiz-btn" 
        data-quiz-id="{{ $quiz->id }}"
        data-quiz-title="{{ $quiz->title }}"
        title="Analyze with AI">
    <i class="fas fa-chart-line"></i> AI Analysis
</button>

@push('scripts')
<script>
$(document).on('click', '.analyze-quiz-btn', function() {
    const quizId = $(this).data('quiz-id');
    const quizTitle = $(this).data('quiz-title');
    const attemptsCount = $(this).data('attempts-count') || 0;
    
    if (attemptsCount < 5) {
        Swal.fire({
            icon: 'warning',
            title: 'Not Enough Data',
            text: 'This quiz needs at least 5 completed attempts for meaningful analysis.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    Swal.fire({
        title: 'Analyze Quiz?',
        html: `<p><strong>${quizTitle}</strong></p>
               <p>AI will provide insights on:</p>
               <ul class="text-left">
                   <li>Overall difficulty assessment</li>
                   <li>Question performance analysis</li>
                   <li>Student performance patterns</li>
                   <li>Recommendations for improvement</li>
               </ul>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-chart-line"></i> Analyze',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/instructor/ai/analyze-quiz/${quizId}`,
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
                title: 'Analysis Started!',
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