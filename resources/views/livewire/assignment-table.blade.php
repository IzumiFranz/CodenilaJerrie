<div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search instructor...">
                </div>
                <div class="col-md-3">
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
            <h6 class="m-0 font-weight-bold text-primary">Assignments ({{ $assignments->total() }})</h6>
            <div wire:loading class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Instructor</th>
                            <th>Subject</th>
                            <th>Section</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->instructor->full_name }}</td>
                            <td>{{ $assignment->subject->subject_name }}</td>
                            <td>{{ $assignment->section->full_name }}</td>
                            <td>{{ $assignment->academic_year }}</td>
                            <td>{{ $assignment->semester }}</td>
                            <td>{{ $assignment->getEnrolledStudentsCount() }}</td>
                            <td>
                                <button wire:click="deleteAssignment({{ $assignment->id }})" 
                                        wire:confirm="Delete this assignment?"
                                        class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <p class="mb-0">No assignments found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</div>