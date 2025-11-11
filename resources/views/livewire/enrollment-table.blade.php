<div>
    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <input type="text" 
                        wire:model.live.debounce.300ms="search" 
                        class="form-control" 
                        placeholder="Search student...">
                </div>
                <div class="col-md-2 mb-3">
                    <select wire:model.live="courseId" class="form-control">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <input type="text" 
                        wire:model.live="academicYear" 
                        class="form-control" 
                        placeholder="Academic Year">
                </div>
                <div class="col-md-2 mb-3">
                    <select wire:model.live="semester" class="form-control">
                        <option value="">All Semesters</option>
                        <option value="1st">1st Semester</option>
                        <option value="2nd">2nd Semester</option>
                        <option value="summer">Summer</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <select wire:model.live="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="enrolled">Enrolled</option>
                        <option value="dropped">Dropped</option>
                        <option value="completed">Completed</option>
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

    <!-- Enrollments Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Enrollments List ({{ $enrollments->total() }})
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
                            <th>Student Number</th>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Section</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Enrolled Date</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td>
                                    <span class="badge badge-primary">{{ $enrollment->student->student_number }}</span>
                                </td>
                                <td>
                                    <strong>{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</strong>
                                    <br><small class="text-muted">{{ $enrollment->student->user->email }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $enrollment->student->course->course_code }}</span>
                                    <br><small>{{ $enrollment->student->course->course_name }}</small>
                                </td>
                                <td>
                                    <strong>{{ $enrollment->section->section_name }}</strong>
                                    <br><small class="text-muted">Year {{ $enrollment->section->year_level }}</small>
                                </td>
                                <td>{{ $enrollment->academic_year }}</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $enrollment->semester }}</span>
                                </td>
                                <td>
                                    @if($enrollment->status === 'enrolled')
                                        <span class="badge badge-success">Enrolled</span>
                                    @elseif($enrollment->status === 'dropped')
                                        <span class="badge badge-danger">Dropped</span>
                                    @else
                                        <span class="badge badge-info">Completed</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($enrollment->enrollment_date)->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.enrollments.show', $enrollment) }}" 
                                            class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($enrollment->status === 'enrolled')
                                            <button wire:click="dropEnrollment({{ $enrollment->id }})" 
                                                wire:confirm="Drop this student from the section?"
                                                class="btn btn-warning" title="Drop">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No enrollments found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $enrollments->links() }}
            </div>
        </div>
    </div>
</div>