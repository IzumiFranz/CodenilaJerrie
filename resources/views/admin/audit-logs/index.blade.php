@extends('layouts.admin')
@section('title', 'Audit Logs')
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history mr-2"></i>Audit Logs</h1>
    <div class="btn-group">
        <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="fas fa-download mr-1"></i> Export CSV
        </a>
        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#clearLogsModal">
            <i class="fas fa-trash-alt mr-1"></i> Clear Old Logs
        </button>
    </div>
</div>

@livewire('audit-log-table')

{{-- Clear Logs Modal --}}
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear Old Logs</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.audit-logs.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        This will permanently delete logs older than the specified days.
                    </div>
                    <div class="form-group">
                        <label>Delete logs older than (days)</label>
                        <input type="number" name="days" class="form-control" value="30" min="30" required>
                        <small class="form-text text-muted">Minimum: 30 days</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt mr-1"></i> Clear Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection