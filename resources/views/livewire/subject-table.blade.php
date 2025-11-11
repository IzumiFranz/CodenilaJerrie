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
                        placeholder="Search subjects...">
                </div>
                <div class="col-md-3 mb-3">
                    <select wire:model.live="courseId" class="form-control">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <select wire:model.live="yearLevel" class="form-control">
                        <option value="">All Years</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                        <option value="5">5th Year</option>
                        <option value="6">6th Year</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <select wire:model.live="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-1 mb-3">
                    <button wire:click="$refresh" class="btn btn-secondary btn-block">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Subjects List ({{ $subjects->total() }})
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
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Units</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td><span class="badge badge-primary">{{ $subject->subject_code }}</span></td>
                                <td>
                                    <strong>{{ $subject->subject_name }}</strong>
                                    @if($subject->specialization)
                                        <br><small class="text-muted">
                                            <i class="fas fa-graduation-cap"></i> {{ $subject->specialization->name }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $subject->course->course_code }}</span>
                                    <br><small>{{ $subject->course->course_name }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $subject->year_level }}{{ ordinal_suffix($subject->year_level) }} Year</span>
                                </td>
                                <td>{{ $subject->units }} units</td>
                                <td>
                                    @if($subject->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.subjects.show', $subject) }}" 
                                            class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                            class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="toggleStatus({{ $subject->id }})" 
                                            class="btn btn-{{ $subject->is_active ? 'secondary' : 'success' }}" 
                                            title="Toggle Status">
                                            <i class="fas fa-toggle-{{ $subject->is_active ? 'on' : 'off' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No subjects found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $subjects->links() }}
            </div>
        </div>
    </div>
</div>

@php
// Helper function for ordinal suffix
if (!function_exists('ordinal_suffix')) {
    function ordinal_suffix($number) {
        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return 'th';
        }
        return $ends[$number % 10];
    }
}
@endphp