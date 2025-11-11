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
                        placeholder="Search instructor...">
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
                    <input type="text" 
                        wire:model.live="academicYear" 
                        class="form-control" 
                        placeholder="Academic Year">
                </div>
                <div class="col-md-2 mb-3">
                    <select wire:model.live="semester" class="form-control">
                        <option value="">All Semesters</option>
                        <option value="1st">1st</option>
                        <option value="2nd">2nd</option>
                        <option value="summer">Summer</option>
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

    <!-- Assignments Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Teaching Assignments ({{ $assignments->total() }})
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
                            <th>Instructor</th>
                            <th>Subject</th>
                            <th>Section</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Students</th>
                            <th>Specialization</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($assignment->instructor->user->profile_picture)
                                            <img src="{{ asset('storage/' . $assignment->instructor->user->profile_picture) }}" 
                                                class="rounded-circle mr-2" 
                                                style="width: 32px; height: 32px; object-fit: cover;"
                                                alt="Avatar">
                                        @else
                                            <div class="rounded-circle bg-primary text-white mr-2 d-flex align-items-center justify-content-center" 
                                                style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ strtoupper(substr($assignment->instructor->first_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $assignment->instructor->first_name }} {{ $assignment->instructor->last_name }}</strong>
                                            <br><small class="text-muted">{{ $assignment->instructor->employee_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $assignment->subject->subject_code }}</span>
                                    <br><strong>{{ $assignment->subject->subject_name }}</strong>
                                    <br><small class="text-muted">{{ $assignment->subject->units }} units</small>
                                </td>
                                <td>
                                    <strong>{{ $assignment->section->section_name }}</strong>
                                    <br><small class="text-muted">
                                        {{ $assignment->section->course->course_code }} - Year {{ $assignment->section->year_level }}
                                    </small>
                                </td>
                                <td>{{ $assignment->academic_year }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $assignment->semester }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success">
                                        {{ $assignment->section->getEnrolledStudentsCount($assignment->academic_year, $assignment->semester) }}
                                    </span>
                                </td>
                                <td>
                                    @if($assignment->instructor->specialization)
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-graduation-cap"></i> 
                                            {{ $assignment->instructor->specialization->name }}
                                        </span>
                                        @if($assignment->subject->specialization_id && 
                                            $assignment->instructor->specialization_id === $assignment->subject->specialization_id)
                                            <br><small class="text-success">
                                                <i class="fas fa-check-circle"></i> Matched
                                            </small>
                                        @elseif($assignment->subject->specialization_id)
                                            <br><small class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Mismatch
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">No specialization</span>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="deleteAssignment({{ $assignment->id }})" 
                                        wire:confirm="Delete this teaching assignment?"
                                        class="btn btn-danger btn-sm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No teaching assignments found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</div>