<div>
    {{-- Search and Filters --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filters</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           class="form-control" 
                           id="search"
                           placeholder="Username, email, name...">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="roleFilter" class="form-label">Role</label>
                    <select wire:model.live="roleFilter" class="form-control" id="roleFilter">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="instructor">Instructor</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select wire:model.live="statusFilter" class="form-control" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <button wire:click="clearFilters" class="btn btn-secondary btn-block">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
            <div>
                <span class="text-muted">Total: {{ $users->total() }} users</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortByColumn('id')" style="cursor: pointer;">
                                ID
                                @if($sortBy === 'id')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortByColumn('username')" style="cursor: pointer;">
                                Username
                                @if($sortBy === 'username')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Full Name</th>
                            <th wire:click="sortByColumn('email')" style="cursor: pointer;">
                                Email
                                @if($sortBy === 'email')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortByColumn('role')" style="cursor: pointer;">
                                Role
                                @if($sortBy === 'role')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortByColumn('status')" style="cursor: pointer;">
                                Status
                                @if($sortBy === 'status')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortByColumn('created_at')" style="cursor: pointer;">
                                Created
                                @if($sortBy === 'created_at')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->full_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge badge-primary">Admin</span>
                                    @elseif($user->role === 'instructor')
                                        <span class="badge badge-success">Instructor</span>
                                    @else
                                        <span class="badge badge-info">Student</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($user->status === 'inactive')
                                        <span class="badge badge-secondary">Inactive</span>
                                    @else
                                        <span class="badge badge-danger">Suspended</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
         style="background: rgba(0,0,0,0.1); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>