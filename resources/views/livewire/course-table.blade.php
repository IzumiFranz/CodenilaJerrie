<div>
    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Search courses by code or name...">
                </div>
                <div class="col-md-3 mb-3">
                    <select wire:model.live="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select wire:model.live="perPage" class="form-control">
                        <option value="10">10 per page</option>
                        <option value="20">20 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Courses List ({{ $courses->total() }})
            </h6>
            <div wire:loading class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th wire:click="sortByColumn('course_code')" style="cursor: pointer;">
                                Code 
                                @if($sortBy === 'course_code')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortByColumn('course_name')" style="cursor: pointer;">
                                Course Name
                                @if($sortBy === 'course_name')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Max Years</th>
                            <th>Subjects</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td><span class="badge badge-primary">{{ $course->course_code }}</span></td>
                                <td>
                                    <strong>{{ $course->course_name }}</strong>
                                    @if($course->description)
                                        <br><small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $course->max_years }} years</td>
                                <td>
                                    <span class="badge badge-info">{{ $course->subjects_count }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-success">{{ $course->students_count }}</span>
                                </td>
                                <td>
                                    @if($course->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.courses.show', $course) }}" 
                                            class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course) }}" 
                                            class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="toggleStatus({{ $course->id }})" 
                                            class="btn btn-{{ $course->is_active ? 'secondary' : 'success' }}" 
                                            title="Toggle Status">
                                            <i class="fas fa-toggle-{{ $course->is_active ? 'on' : 'off' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No courses found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</div>