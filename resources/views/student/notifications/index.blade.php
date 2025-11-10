@extends('layouts.student')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-bell"></i> Notifications
        </h1>
        <div>
            <form action="{{ route('student.notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-info" {{ $unreadCount == 0 ? 'disabled' : '' }}>
                    <i class="fas fa-check-double"></i> Mark All as Read
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('student.notifications.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="danger" {{ request('type') == 'danger' ? 'selected' : '' }}>Important</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-info d-block w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card shadow">
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        @if($notification->type === 'info')
                                            <i class="fas fa-info-circle text-info fa-lg me-2"></i>
                                        @elseif($notification->type === 'success')
                                            <i class="fas fa-check-circle text-success fa-lg me-2"></i>
                                        @elseif($notification->type === 'warning')
                                            <i class="fas fa-exclamation-triangle text-warning fa-lg me-2"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle text-danger fa-lg me-2"></i>
                                        @endif
                                        
                                        <h5 class="mb-0">
                                            {{ $notification->title }}
                                            @if(!$notification->is_read)
                                                <span class="badge bg-info ms-2">New</span>
                                            @endif
                                        </h5>
                                    </div>
                                    
                                    <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                    
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                
                                <div class="ms-3">
                                    @if($notification->action_url)
                                        <form action="{{ route('student.notifications.mark-read', $notification) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-info mb-1">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if(!$notification->is_read)
                                        <form action="{{ route('student.notifications.mark-read', $notification) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-info mb-1">
                                                <i class="fas fa-check"></i> Mark Read
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('student.notifications.destroy', $notification) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger mb-1" onclick="return confirm('Delete this notification?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No notifications found.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
