@extends('layouts.admin')

@section('title', 'Notifications Management')

@php
    $pageTitle = 'Notifications Management';
    $pageActions = '<a href="' . route('admin.notifications.create') . '" class="btn btn-primary">
        <i class="fas fa-plus"></i> Send Notification
    </a>';
@endphp

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter Notifications</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search title or message..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Info</option>
                    <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="danger" {{ request('type') == 'danger' ? 'selected' : '' }}>Danger</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">User</label>
                <input type="text" name="user_id" class="form-control" placeholder="User ID..." value="{{ request('user_id') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Notifications List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead>
                    <tr>
                        <th width="50">Type</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Recipient</th>
                        <th>Status</th>
                        <th>Sent</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr class="{{ !$notification->read_at ? 'table-active' : '' }}">
                            <td class="text-center">
                                @if($notification->type == 'info')
                                    <i class="fas fa-info-circle fa-2x text-info"></i>
                                @elseif($notification->type == 'success')
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                @elseif($notification->type == 'warning')
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                @else
                                    <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $notification->title }}</strong>
                            </td>
                            <td>
                                {{ Str::limit($notification->message, 80) }}
                                @if($notification->action_url)
                                    <br><small class="text-muted">
                                        <i class="fas fa-link"></i> Has action link
                                    </small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $notification->user->username }}</strong><br>
                                <small class="text-muted">{{ $notification->user->email }}</small>
                            </td>
                            <td>
                                @if($notification->read_at)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Read
                                    </span>
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($notification->read_at)->diffForHumans() }}</small>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-envelope"></i> Unread
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ $notification->created_at->format('M d, Y H:i') }}<br>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{ $notification->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- View Modal -->
                        <div class="modal fade" id="viewModal{{ $notification->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-{{ $notification->type }} text-white">
                                        <h5 class="modal-title">
                                            @if($notification->type == 'info')
                                                <i class="fas fa-info-circle"></i>
                                            @elseif($notification->type == 'success')
                                                <i class="fas fa-check-circle"></i>
                                            @elseif($notification->type == 'warning')
                                                <i class="fas fa-exclamation-triangle"></i>
                                            @else
                                                <i class="fas fa-exclamation-circle"></i>
                                            @endif
                                            {{ $notification->title }}
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>Recipient:</strong><br>
                                            {{ $notification->user->username }} ({{ $notification->user->email }})
                                        </div>
                                        <div class="mb-3">
                                            <strong>Message:</strong><br>
                                            <div class="border rounded p-3 bg-light">
                                                {{ $notification->message }}
                                            </div>
                                        </div>
                                        @if($notification->action_url)
                                            <div class="mb-3">
                                                <strong>Action URL:</strong><br>
                                                <a href="{{ $notification->action_url }}" target="_blank" class="text-break">
                                                    {{ $notification->action_url }}
                                                </a>
                                            </div>
                                        @endif
                                        <div class="mb-3">
                                            <strong>Status:</strong><br>
                                            @if($notification->read_at)
                                                <span class="badge badge-success">Read at {{ \Carbon\Carbon::parse($notification->read_at)->format('M d, Y H:i') }}</span>
                                            @else
                                                <span class="badge badge-warning">Unread</span>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>Sent:</strong><br>
                                            {{ $notification->created_at->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No notifications found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection