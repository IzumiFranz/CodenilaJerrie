<div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search student...">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="courseId" class="form-control">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" wire:model.live="academicYear" class="form-control" placeholder="Academic Year">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="semester" class="form-control">
                        <option value="">All Semesters</option>
                        <option value="1st">1st</option>
                        <option value="2nd">2nd</option>
                        <option value="summer">Summer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="enrolled">Enrolled</option>
                        <option value="dropped">Dropped</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button wire:click="$refresh" class="btn btn-secondary btn-block">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Enrollments ({{ $enrollments->total() }})</h6>
            <div wire:loading class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student Number</th>
                            <th>Student Name</th>
                            <th>Section</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->student->student_number }}</td>
                            <td>{{ $enrollment->student->full_name }}</td>
                            <td>{{ $enrollment->section->full_name }}</td>
                            <td>{{ $enrollment->academic_year }}</td>
                            <td>{{ $enrollment->semester }}</td>
                            <td>
                                <span class="badge badge-{{ $enrollment->status === 'enrolled' ? 'success' : ($enrollment->status === 'dropped' ? 'danger' : 'info') }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($enrollment->status === 'enrolled')
                                    <button wire:click="dropEnrollment({{ $enrollment->id }})" 
                                            wire:confirm="Drop this student from section?"
                                            class="btn btn-warning">
                                        <i class="fas fa-user-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p class="mb-0">No enrollments found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $enrollments->links() }}
            </div>
        </div>
    </div>
</div>