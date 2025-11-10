<div class="modal fade" id="scheduleModal{{ $quiz->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('instructor.quizzes.schedule', $quiz) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clock mr-2"></i>Schedule Quiz
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    {{-- Validation Check --}}
                    @if($quiz->questions->count() === 0)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Cannot Schedule:</strong> This quiz has no questions.
                            Please add questions before scheduling.
                        </div>
                    @else
                        {{-- Current Status --}}
                        @if($quiz->isScheduledForPublish())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Scheduled:</strong> Will publish on 
                                {{ $quiz->scheduled_publish_at->format('M d, Y h:i A') }}
                            </div>
                        @endif
                        
                        {{-- Quiz Info --}}
                        <div class="mb-3 p-3 bg-light rounded">
                            <small class="d-block"><strong>Questions:</strong> {{ $quiz->questions->count() }}</small>
                            <small class="d-block"><strong>Total Points:</strong> {{ $quiz->getTotalPoints() }}</small>
                            <small class="d-block"><strong>Time Limit:</strong> {{ $quiz->time_limit }} minutes</small>
                        </div>
                        
                        {{-- Auto-Publish Toggle --}}
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="autoPublish{{ $quiz->id }}"
                                       name="auto_publish"
                                       value="1"
                                       {{ $quiz->auto_publish ? 'checked' : '' }}>
                                <label class="custom-control-label" for="autoPublish{{ $quiz->id }}">
                                    <strong>Enable Auto-Publish</strong>
                                    <small class="d-block text-muted">
                                        Automatically publish this quiz at the scheduled time
                                    </small>
                                </label>
                            </div>
                        </div>
                        
                        {{-- Scheduled Publish Date --}}
                        <div class="form-group">
                            <label for="scheduledPublish{{ $quiz->id }}">
                                Publish On
                                <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   id="scheduledPublish{{ $quiz->id }}"
                                   name="scheduled_publish_at"
                                   value="{{ $quiz->scheduled_publish_at ? $quiz->scheduled_publish_at->format('Y-m-d\TH:i') : '' }}"
                                   min="{{ now()->format('Y-m-d\TH:i') }}">
                            <small class="form-text text-muted">
                                The date and time when this quiz will be published
                            </small>
                        </div>
                        
                        {{-- Scheduled Unpublish Date --}}
                        <div class="form-group">
                            <label for="scheduledUnpublish{{ $quiz->id }}">
                                Unpublish On (Optional)
                            </label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   id="scheduledUnpublish{{ $quiz->id }}"
                                   name="scheduled_unpublish_at"
                                   value="{{ $quiz->scheduled_unpublish_at ? $quiz->scheduled_unpublish_at->format('Y-m-d\TH:i') : '' }}">
                            <small class="form-text text-muted">
                                Optionally close this quiz after a specific date
                            </small>
                        </div>
                        
                        {{-- Note about availability --}}
                        @if($quiz->available_from || $quiz->available_until)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <small>
                                    <strong>Note:</strong> This quiz also has availability dates set.
                                    Students can only take it during the availability window.
                                </small>
                            </div>
                        @endif
                    @endif
                </div>
                
                <div class="modal-footer">
                    @if($quiz->isScheduledForPublish())
                        <button type="button" 
                                class="btn btn-danger mr-auto" 
                                onclick="cancelSchedule({{ $quiz->id }}, 'quiz')">
                            <i class="fas fa-times"></i> Cancel Schedule
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    @if($quiz->questions->count() > 0)
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Schedule
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>