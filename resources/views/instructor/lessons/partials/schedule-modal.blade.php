<div class="modal fade" id="scheduleModal{{ $lesson->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('instructor.lessons.schedule', $lesson) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clock mr-2"></i>Schedule Lesson
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    {{-- Current Status --}}
                    @if($lesson->isScheduledForPublish())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Scheduled:</strong> Will publish on 
                            {{ $lesson->scheduled_publish_at->format('M d, Y h:i A') }}
                        </div>
                    @endif
                    
                    {{-- Auto-Publish Toggle --}}
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="autoPublish{{ $lesson->id }}"
                                   name="auto_publish"
                                   value="1"
                                   {{ $lesson->auto_publish ? 'checked' : '' }}>
                            <label class="custom-control-label" for="autoPublish{{ $lesson->id }}">
                                <strong>Enable Auto-Publish</strong>
                                <small class="d-block text-muted">
                                    Automatically publish this lesson at the scheduled time
                                </small>
                            </label>
                        </div>
                    </div>
                    
                    {{-- Scheduled Publish Date --}}
                    <div class="form-group">
                        <label for="scheduledPublish{{ $lesson->id }}">
                            Publish On
                            <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local" 
                               class="form-control" 
                               id="scheduledPublish{{ $lesson->id }}"
                               name="scheduled_publish_at"
                               value="{{ $lesson->scheduled_publish_at ? $lesson->scheduled_publish_at->format('Y-m-d\TH:i') : '' }}"
                               min="{{ now()->format('Y-m-d\TH:i') }}">
                        <small class="form-text text-muted">
                            The date and time when this lesson will be published
                        </small>
                    </div>
                    
                    {{-- Scheduled Unpublish Date --}}
                    <div class="form-group">
                        <label for="scheduledUnpublish{{ $lesson->id }}">
                            Unpublish On (Optional)
                        </label>
                        <input type="datetime-local" 
                               class="form-control" 
                               id="scheduledUnpublish{{ $lesson->id }}"
                               name="scheduled_unpublish_at"
                               value="{{ $lesson->scheduled_unpublish_at ? $lesson->scheduled_unpublish_at->format('Y-m-d\TH:i') : '' }}">
                        <small class="form-text text-muted">
                            Optionally hide this lesson after a specific date
                        </small>
                    </div>
                    
                    {{-- Warning --}}
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <small>
                            Make sure the lesson content is ready before scheduling.
                            Scheduled lessons will be published automatically.
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    @if($lesson->isScheduledForPublish())
                        <button type="button" 
                                class="btn btn-danger mr-auto" 
                                onclick="cancelSchedule({{ $lesson->id }}, 'lesson')">
                            <i class="fas fa-times"></i> Cancel Schedule
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>