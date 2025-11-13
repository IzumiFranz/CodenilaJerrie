<div>
    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Search by action, model type, or username...">
                </div>
                <div class="col-md-3 mb-3">
                    <select wire:model.live="action" class="form-control">
                        <option value="">All Actions</option>
                        @foreach($actions as $actionItem)
                            <option value="{{ $actionItem }}">{{ ucfirst(str_replace('_', ' ', $actionItem)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <input type="date" 
                        wire:model.live="dateFrom" 
                        class="form-control" 
                        placeholder="From Date">
                </div>
                <div class="col-md-2 mb-3">
                    <input type="date" 
                        wire:model.live="dateTo" 
                        class="form-control" 
                        placeholder="To Date">
                </div>
                <div class="col-md-1 mb-3">
                    <button wire:click="$refresh" class="btn btn-secondary btn-block">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Changes</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <small>{{ $log->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($log->user->profile_picture)
                                            <img src="{{ asset('storage/' . $log->user->profile_picture) }}" 
                                                class="rounded-circle mr-2" 
                                                style="width: 30px; height: 30px; object-fit: cover;"
                                                alt="Avatar">
                                        @else
                                            <div class="rounded-circle bg-primary text-white mr-2 d-flex align-items-center justify-content-center" 
                                                style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                {{ strtoupper(substr($log->user->username, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $log->user->username }}</strong><br>
                                            <small class="text-muted">{{ $log->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $log->action == 'deleted' ? 'danger' : ($log->action == 'created' ? 'success' : 'info') }}">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->model_type)
                                        <span class="badge badge-secondary">
                                            {{ class_basename($log->model_type) }}
                                        </span>
                                        @if($log->model_id)
                                            <br><small class="text-muted">ID: {{ $log->model_id }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->old_values || $log->new_values)
                                        <button class="btn btn-sm btn-info" 
                                            type="button" 
                                            data-toggle="modal" 
                                            data-target="#changesModal{{ $log->id }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        
                                        <!-- Changes Modal -->
                                        <div class="modal fade" id="changesModal{{ $log->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Changes Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            @if($log->old_values)
                                                            <div class="col-md-6">
                                                                <h6>Old Values</h6>
                                                                <pre class="bg-light p-3 rounded">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                            </div>
                                                            @endif
                                                            @if($log->new_values)
                                                            <div class="col-md-6">
                                                                <h6>New Values</h6>
                                                                <pre class="bg-light p-3 rounded">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No changes</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $log->ip_address ?? 'N/A' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Audit Logs Found</h5>
                                    <p class="text-muted mb-0">There are no audit logs matching your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $logs->links() }}
        </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading class="text-center mt-3">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="text-muted mt-2">Loading audit logs...</p>
    </div>
</div>
