{{-- resources/views/admin/partials/bulk-actions-bar.blade.php --}}
{{-- 
    Usage: @include('admin.partials.bulk-actions-bar', ['model' => 'User', 'actions' => ['delete', 'status', 'export']])
--}}


<div id="bulkActionsBar" class="card shadow mb-4 d-none no-print">
    <div class="card-body bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-check-square text-primary"></i>
                <strong><span id="selectedCount">0</span></strong> item(s) selected
            </div>
            <div class="btn-group">
                {{-- Delete Action --}}
                @if(in_array('delete', $actions ?? []))
                    <button type="button" id="bulkDeleteBtn" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                @endif

                {{-- Status Actions --}}
                @if(in_array('status', $actions ?? []))
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-toggle-on"></i> Update Status
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" data-bulk-action="update-status" data-status="active">
                                <i class="fas fa-check text-success"></i> Set Active
                            </button>
                            <button class="dropdown-item" data-bulk-action="update-status" data-status="inactive">
                                <i class="fas fa-times text-secondary"></i> Set Inactive
                            </button>
                            @if($model === 'User')
                                <button class="dropdown-item" data-bulk-action="update-status" data-status="suspended">
                                    <i class="fas fa-ban text-danger"></i> Suspend
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Export Action --}}
                @if(in_array('export', $actions ?? []))
                    <button type="button" id="bulkExportBtn" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Export Selected
                    </button>
                @endif

                {{-- Notification Action (Users only) --}}
                @if(in_array('notify', $actions ?? []) && $model === 'User')
                    <button type="button" id="bulkNotifyBtn" class="btn btn-info btn-sm" data-toggle="modal" data-target="#bulkNotificationModal">
                        <i class="fas fa-bell"></i> Send Notification
                    </button>
                @endif

                {{-- Restore Action (Trash pages) --}}
                @if(in_array('restore', $actions ?? []))
                    <button type="button" id="bulkRestoreBtn" class="btn btn-warning btn-sm">
                        <i class="fas fa-undo"></i> Restore Selected
                    </button>
                @endif

                {{-- Enroll Action (Students) --}}
                @if(in_array('enroll', $actions ?? []))
                    <button type="button" id="bulkEnrollBtn" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#bulkEnrollModal">
                        <i class="fas fa-user-plus"></i> Bulk Enroll
                    </button>
                @endif

                {{-- Clear Selection --}}
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="bulkActions.clearSelection()">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Notification Modal (for Users) --}}
@if(in_array('notify', $actions ?? []) && $model === 'User')
    <div class="modal fade" id="bulkNotificationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-bell"></i> Send Bulk Notification
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="bulkNotificationForm" action="{{ route('admin.bulk-actions.send-notifications') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Notification Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="info">Info</option>
                                <option value="success">Success</option>
                                <option value="warning">Warning</option>
                                <option value="danger">Danger</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label>Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" maxlength="1000" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Action URL (Optional)</label>
                            <input type="url" name="action_url" class="form-control" maxlength="500">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-paper-plane"></i> Send Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- Bulk Enroll Modal (for Students) --}}
@if(in_array('enroll', $actions ?? []))
    <div class="modal fade" id="bulkEnrollModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Bulk Enroll Students
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="bulkEnrollForm" action="{{ route('admin.enrollments.bulk-enroll') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <span id="enrollCount">0</span> student(s) will be enrolled.
                        </div>
                        
                        <div class="form-group">
                            <label>Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year" class="form-control" required>
                                @php
                                    $currentYear = now()->year;
                                    for ($i = -1; $i <= 2; $i++) {
                                        $year = $currentYear + $i;
                                        $ay = $year . '-' . ($year + 1);
                                        echo "<option value='$ay'>$ay</option>";
                                    }
                                @endphp
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-control" required>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="summer">Summer</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Section <span class="text-danger">*</span></label>
                            <select name="section_id" class="form-control" required>
                                <option value="">Select Section...</option>
                                {{-- Sections will be loaded via AJAX based on course --}}
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Enroll Students
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script src="{{ asset('js/bulk-actions.js') }}"></script>
<script>
    // Update enroll count when modal opens
    $('#bulkEnrollModal').on('show.bs.modal', function() {
        const count = window.bulkActions ? window.bulkActions.selectedIds.length : 0;
        $('#enrollCount').text(count);
    });
</script>
@endpush