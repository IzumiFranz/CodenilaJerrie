<div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search users...">
                </div>
                <div class="col-md-3 mb-3">
                    <select wire:model.live="role" class="form-control">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="instructor">Instructor</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select wire:model.live="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <select wire:model.live="perPage" class="form-control">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
        </div>
        <div class="card-body">
            <div wire:loading class="alert alert-info">
                <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortByColumn('id')" style="cursor: pointer;">
                                ID @if($sortBy === 'id')<i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>@endif
                            </th>
                            <th>Name</th>
                            <th wire:click="sortByColumn('email')" style="cursor: pointer;">
                                Email @if($sortBy === 'email')<i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>@endif
                            </th>
                            <th>Role</th>
                            <th>Status</th>
                            <th wire:click="sortByColumn('created_at')" style="cursor: pointer;">
                                Created @if($sortBy === 'created_at')<i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>@endif
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->profile_picture)
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" class="avatar-sm rounded-circle mr-2" alt="Avatar">
                                    @else
                                        <img src="{{ asset('img/undraw_profile.svg') }}" class="avatar-sm rounded-circle mr-2" alt="Avatar">
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">{{ $user->full_name }}</div>
                                        <div class="text-xs text-muted">@{{ $user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ $user->role === 'admin' ? 'primary' : ($user->role === 'instructor' ? 'success' : 'info') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'secondary' : 'danger') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" data-confirm="Delete this user?" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p class="mb-0">No users found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>