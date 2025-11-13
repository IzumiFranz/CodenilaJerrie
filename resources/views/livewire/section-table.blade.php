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
                        placeholder="Search sections...">
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
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">{{ $i }}{{ ordinal_suffix($i) }} Year</option>
                        @endfor
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

    <!-- Sections Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Sections List ({{ $sections->total() }})
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
                            <th>Course</th>
                            <th>Year Level</th>
                            <th>Section Name</th>
                            <th>Capacity</th>
                            <th>Enrolled</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                            <tr>
                                <td>
                                    <span class="badge badge-info">{{ $section->course->course_code }}</span>
                                    <br><small>{{ $section->course->course_name }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ $section->year_level }}{{ ordinal_suffix($section->year_level) }} Year
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $section->section_name }}</strong>
                                </td>
                                <td>
                                    <i class="fas fa-users text-primary"></i> {{ $section->max_students }}
                                </td>
                                <td>
                                    @php
                                        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
                                        $currentSemester = getCurrentSemester();
                                        $enrolledCount = $section->getEnrolledStudentsCount($currentAcademicYear, $currentSemester);
                                        $percentage = $section->max_students > 0 ? ($enrolledCount / $section->max_students) * 100 : 0;
                                    @endphp
                                    <span class="badge badge-{{ $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success') }}">
                                        {{ $enrolledCount }}/{{ $section->max_students }}
                                    </span>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-{{ $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success') }}" 
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                                <td>
                                    @if($section->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.sections.show', $section) }}" 
                                            class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.sections.edit', $section) }}" 
                                            class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="toggleStatus({{ $section->id }})" 
                                            class="btn btn-{{ $section->is_active ? 'secondary' : 'success' }}" 
                                            title="Toggle Status">
                                            <i class="fas fa-toggle-{{ $section->is_active ? 'on' : 'off' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No sections found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $sections->links() }}
            </div>
        </div>
    </div>
</div>
