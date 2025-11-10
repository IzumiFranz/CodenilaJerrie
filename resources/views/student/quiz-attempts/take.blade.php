@extends('layouts.student')

@section('title', 'Taking Quiz')

@section('content')
<!-- Quiz Header -->
<div class="card shadow mb-4 border-left-primary" id="timer-card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-2">{{ $attempt->quiz->title }}</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-book mr-1"></i>{{ $attempt->quiz->subject->subject_name }} |
                    <i class="fas fa-user mr-1"></i>{{ $attempt->quiz->instructor->full_name }}
                </p>
            </div>
            <div class="col-md-4 text-right">
                @if($remainingTime !== null)
                <div class="alert alert-warning mb-0" id="timer-container">
                    <i class="fas fa-clock mr-2"></i>
                    <strong>Time Remaining:</strong>
                    <span id="timer" class="font-weight-bold" data-remaining="{{ $remainingTime }}">
                        {{ gmdate('H:i:s', $remainingTime * 60) }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quiz Instructions -->
@if($attempt->quiz->instructions)
<div class="alert alert-info">
    <h5><i class="fas fa-info-circle mr-2"></i>Instructions</h5>
    {!! nl2br(e($attempt->quiz->instructions)) !!}
</div>
@endif

<!-- Quiz Form -->
<form id="quiz-form" method="POST" action="{{ route('student.quiz-attempts.submit', $attempt) }}">
    @csrf

    <!-- Questions -->
    @foreach($questions as $index => $question)
    <div class="card shadow mb-4 question-card" id="question-{{ $question->id }}">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <span class="badge badge-primary">Question {{ $index + 1 }}</span>
                    <span class="badge badge-info">{{ $question->points }} {{ Str::plural('point', $question->points) }}</span>
                    @if($question->difficulty)
                    <span class="badge badge-secondary">{{ ucfirst($question->difficulty) }}</span>
                    @endif
                </h5>
                <span class="auto-save-indicator" id="save-indicator-{{ $question->id }}" style="display: none;">
                    <i class="fas fa-check-circle text-success"></i> Saved
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <p class="lead">{!! nl2br(e($question->question_text)) !!}</p>
            </div>

            @php
                $existingAnswer = $existingAnswers->get($question->id);
            @endphp

            @if($question->isMultipleChoice() || $question->isTrueFalse())
            <div class="choices-container">
                @foreach($question->choices as $choice)
                <div class="custom-control custom-radio mb-3">
                    <input type="radio"
                           class="custom-control-input answer-input"
                           id="choice-{{ $choice->id }}"
                           name="answers[{{ $question->id }}]"
                           value="{{ $choice->id }}"
                           data-question-id="{{ $question->id }}"
                           {{ $existingAnswer && $existingAnswer->choice_id == $choice->id ? 'checked' : '' }}>
                    <label class="custom-control-label" for="choice-{{ $choice->id }}">
                        {{ $choice->choice_text }}
                    </label>
                </div>
                @endforeach
            </div>

            @elseif($question->isIdentification())
            <div class="form-group">
                <input type="text"
                       class="form-control form-control-lg answer-input"
                       name="answers[{{ $question->id }}]"
                       data-question-id="{{ $question->id }}"
                       placeholder="Type your answer here..."
                       value="{{ $existingAnswer ? $existingAnswer->answer_text : '' }}">
            </div>

            @elseif($question->isEssay())
            <div class="form-group">
                <textarea class="form-control answer-input"
                          name="answers[{{ $question->id }}]"
                          data-question-id="{{ $question->id }}"
                          rows="8"
                          placeholder="Type your essay answer here...">{{ $existingAnswer ? $existingAnswer->answer_text : '' }}</textarea>
                <small class="form-text text-muted">
                    <span class="char-count" data-for="{{ $question->id }}">0</span> characters
                </small>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <!-- Navigation / Progress -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <p class="mb-0 text-muted">
                        <i class="fas fa-question-circle mr-2"></i>
                        <strong>Total Questions:</strong> {{ $questions->count() }}
                    </p>
                    <p class="mb-0 text-muted">
                        <i class="fas fa-star mr-2"></i>
                        <strong>Total Points:</strong> {{ $attempt->total_points }}
                    </p>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" id="review-btn">
                        <i class="fas fa-eye mr-1"></i>Review Answers
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="save-btn">
                        <i class="fas fa-save mr-1"></i>Save Now
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-paper-plane mr-1"></i>Submit Quiz
                    </button>
                </div>
            </div>

            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small id="progress-text">0 of {{ $questions->count() }} answered</small>
                    <small id="unanswered-status" class="text-muted" style="display: none;">Unanswered: <span id="unanswered-count">0</span></small>
                </div>
                <div class="progress" style="height: 12px;">
                    <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Submit Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                    Submit Quiz?
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit this quiz?</p>
                <div id="unanswered-warning" class="alert alert-warning" style="display: none;">
                    <strong>Warning:</strong> You have <span id="modal-unanswered-count">0</span> unanswered question(s).
                </div>
                <p class="text-muted mb-0">Once submitted, you cannot change your answers.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-submit-btn">
                    <i class="fas fa-check mr-1"></i>Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-list-check mr-2"></i>Review Your Answers
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="review-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11000;"></div>

<!-- Question Navigation Sidebar -->
<div class="question-nav-sidebar d-none d-lg-block">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-list"></i> Questions</h6>
        </div>
        <div class="card-body p-2">
            <div class="d-grid gap-2">
                @foreach($attempt->quiz->questions as $index => $question)
                    <button type="button"
                            class="btn btn-sm btn-outline-info question-nav-btn"
                            data-question="{{ $question->id }}">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .question-card { scroll-margin-top: 100px; }
    .auto-save-indicator { font-size: 0.875rem; animation: fadeIn 0.3s; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    @keyframes pulse { 0%,100%{ opacity:1 } 50%{ opacity:0.6 } }
    .timer-warning { color: #f6c23e !important; animation: pulse 2s infinite; }
    .timer-critical { color: #e74a3b !important; animation: pulse 0.5s infinite; }

    #save-indicator { transition: opacity 0.5s; }
    #progress-bar { transition: width 0.5s ease; }

    .question-nav-sidebar { position: fixed; right: 20px; top: 150px; width: 80px; max-height: calc(100vh - 200px); overflow-y: auto; z-index: 1000; }
    .question-card:target { box-shadow: 0 0 0 3px rgba(54,185,204,0.5); transition: box-shadow 0.3s; }
    .question-answered { border-left: 4px solid #1cc88a !important; }
    .question-unanswered { border-left: 4px solid #f6c23e !important; }

    @media (max-width: 991px) { .question-nav-sidebar { display: none !important; } }
    @media print { .timer-card, .save-indicator, .question-nav-sidebar, .btn, nav, footer { display: none !important; } }
</style>
@endpush

@push('scripts')
<script>
$(function() {
    const AUTOSAVE_DELAY = 2000;
    let autoSaveTimeouts = {};
    let hasUnsavedChanges = false;
    let isSubmitting = false;
    let timeInterval = null;
    let beepCooldown = false;

    // Toast helper
    function showToast(title, message, type='success') {
        const bgClass = type==='success'?'bg-success':'bg-danger';
        const toast = $(`
            <div class="toast align-items-center text-white ${bgClass} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><strong>${title}:</strong> ${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        $('#toast-container').append(toast);
        try { new bootstrap.Toast(toast[0]).show(); } catch(e){ toast.show(); }
        setTimeout(()=>toast.remove(),5000);
    }

    // Timer logic
    @if($remainingTime !== null)
    let remainingSeconds = {{ $remainingTime * 60 }};
    const $timer = $('#timer');
    const $timerCard = $('#timer-card');

    function playBeep(){
        if(beepCooldown) return;
        beepCooldown = true;
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZTA0OVqzn77FfHAU+ltj0y3okBSh+zPLaizsIG2i78OScTQwOUqbi8LtoHAY7k9b0ynskBSh+zPLaizsIG2i78OScTQwOUqbi8LtoHAY7k9b0ynskBSh+zPLaizsIG2i78OScTQwOUqbi8A==');
        audio.play().catch(()=>{});
        setTimeout(()=>beepCooldown=false, 1000);
    }

    function updateTimerDisplay(){
        if(remainingSeconds <=0){ autoSubmitQuiz(); return; }
        const h=Math.floor(remainingSeconds/3600), m=Math.floor((remainingSeconds%3600)/60), s=remainingSeconds%60;
        $timer.text(`${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`);
        $timer.removeClass('timer-warning timer-critical'); $timerCard.removeClass('border-warning border-danger');
        if(remainingSeconds<=60){ $timer.addClass('timer-critical'); $timerCard.addClass('border-danger'); playBeep(); }
        else if(remainingSeconds<=300){ $timer.addClass('timer-warning'); $timerCard.addClass('border-warning'); }
        remainingSeconds--;
    }

    updateTimerDisplay();
    timeInterval = setInterval(updateTimerDisplay, 1000);
    @endif

    function saveAnswer(qid, showNotification=false){
        if(isSubmitting) return;
        const $card = $(`#question-${qid}`), $inputs = $card.find('.answer-input'), $indicator = $(`#save-indicator-${qid}`);
        let data={_token:'{{ csrf_token() }}', question_id:qid};
        const $checked = $inputs.filter(':checked');
        if($checked.length) data.choice_id = $checked.val();
        else {
            const val = $inputs.filter('input[type="text"],textarea').val();
            data.answer_text = val!==undefined?val:'';
        }
        $indicator.html('<i class="fas fa-spinner fa-spin"></i> Saving...').show();
        return $.post('{{ route("student.quiz-attempts.save-answer",$attempt) }}',data)
            .done(()=>{ $indicator.html('<i class="fas fa-check-circle text-success"></i> Saved').show(); setTimeout(()=>$indicator.fadeOut(),1500); hasUnsavedChanges=false; if(showNotification) showToast('Success','Answers saved','success'); })
            .fail(()=>{ $indicator.html('<i class="fas fa-exclamation-circle text-danger"></i> Error').show(); showToast('Error','Could not save answer.','error'); });
    }

    $('.answer-input').on('input change',function(){
        const qid=$(this).data('question-id'); if(!qid) return;
        hasUnsavedChanges=true; clearTimeout(autoSaveTimeouts[qid]);
        autoSaveTimeouts[qid] = setTimeout(()=>saveAnswer(qid,false),AUTOSAVE_DELAY);
        updateProgress();
    });

    $('#save-btn').click(function(e){ e.preventDefault(); $('.question-card').each(function(){ const qid=$(this).attr('id').replace('question-',''); saveAnswer(qid,true); }); });

    setInterval(()=>{ if(hasUnsavedChanges&&!isSubmitting){ $('.question-card').each(function(){ const qid=$(this).attr('id').replace('question-',''); saveAnswer(qid,false); }); } },30000);

    $('textarea.answer-input').each(function(){
        const $t=$(this),qid=$t.data('question-id'),$counter=$(`.char-count[data-for="${qid}"]`);
        $t.on('input',()=>$counter.text($t.val().length)).trigger('input');
    });

    function updateProgress(){
        const total={{ $questions->count() }}, answered=$('.question-card').toArray().filter(c=>{
            const $c=$(c),checked=$c.find('.answer-input:checked').length>0;
            const text=$c.find('input[type="text"]').val(), ta=$c.find('textarea').val();
            const hasAns=checked||((text||'').trim()!== '')||((ta||'').trim()!== '');
            $c.toggleClass('question-answered',hasAns).toggleClass('question-unanswered',!hasAns);
            return hasAns;
        }).length;
        const percent=Math.round((answered/total)*100);
        $('#progress-bar').css('width',percent+'%').attr('aria-valuenow',percent);
        $('#progress-text').text(`${answered} of ${total} answered`);
        const unanswered=total-answered;
        if(unanswered>0){ $('#unanswered-status').show(); $('#unanswered-count').text(unanswered); }
        else $('#unanswered-status').hide();
    }

    updateProgress();

    $('#review-btn').click(function(e){
        e.preventDefault(); let html='<div class="list-group">';
        $('.question-card').each(function(i){
            const $c=$(this),qid=$c.attr('id'),checked=$c.find('.answer-input:checked').length>0;
            const text=($c.find('input[type="text"]').val()||'').trim();
            const ta=($c.find('textarea').val()||'').trim();
            const hasAnswer=checked||text!==''||ta!=='';
            html+=`<a href="#${qid}" class="list-group-item list-group-item-action"><div class="d-flex justify-content-between align-items-center"><span>Question ${i+1}</span>${hasAnswer?'<i class="fas fa-check-circle text-success"></i>':'<i class="fas fa-circle text-muted"></i>'}</div></a>`;
        });
        html+='</div>'; $('#review-content').html(html); $('#reviewModal').modal('show');
    });

    function countUnanswered(){
        const total={{ $questions->count() }}, answered=$('.question-card').toArray().filter(c=>{
            const $c=$(c),checked=$c.find('.answer-input:checked').length>0;
            const text=($c.find('input[type="text"]').val()||'').trim();
            const ta=($c.find('textarea').val()||'').trim();
            return checked||text!==''||ta!=='';
        }).length;
        return total-answered;
    }

    $('#submit-btn').click(function(e){
        e.preventDefault(); const unanswered=countUnanswered();
        if(unanswered>0){ $('#modal-unanswered-count').text(unanswered); $('#unanswered-warning').show(); }
        else $('#unanswered-warning').hide();
        $('#submitModal').modal('show');
    });

    $('#confirm-submit-btn').click(async function(){
        isSubmitting=true; window.removeEventListener('beforeunload',beforeUnloadHandler);
        // save all answers one last time
        const savePromises=$('.question-card').toArray().map(c=>saveAnswer($(c).attr('id').replace('question-',''),false));
        await Promise.all(savePromises);
        $('#quiz-form').append('<input type="hidden" name="final_submit" value="1">').submit();
    });

    function autoSubmitQuiz(){
        if(isSubmitting) return;
        isSubmitting=true; showToast('Time Expired','Quiz is being submitted automatically...','error');
        const savePromises=$('.question-card').toArray().map(c=>saveAnswer($(c).attr('id').replace('question-',''),false));
        Promise.all(savePromises).finally(()=>{
            $('#quiz-form').append('<input type="hidden" name="auto_submit" value="1">').submit();
        });
    }

    $('.question-nav-btn').click(function(){ const qid=$(this).data('question'); $('html,body').animate({scrollTop:$('#question-'+qid).offset().top-100},500); });

    function beforeUnloadHandler(e){ if(!isSubmitting&&hasUnsavedChanges){ e.preventDefault(); e.returnValue='You have unsaved changes. Are you sure?'; return e.returnValue; } }
    window.addEventListener('beforeunload',beforeUnloadHandler);
    $('#quiz-form').on('submit',()=>window.removeEventListener('beforeunload',beforeUnloadHandler));
});
</script>
@endpush
