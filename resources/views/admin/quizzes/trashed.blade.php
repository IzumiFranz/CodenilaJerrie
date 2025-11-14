@extends('layouts.admin')

@section('title', 'Trashed Quizzes')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-trash-restore mr-2"></i>Trashed Quizzes</h1>
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to Quizzes
    </a>
</div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Deleted Quizzes</h6>
        </div>
        <div class="card-body">
            @if($quizzes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Instructor</th>
                                <th>Questions</th>
                                <th>Attempts</th>
                                <th>Deleted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td><strong>{{ $quiz->title }}</strong></td>
                                    <td>
                                        <a href="{{ route('admin.subjects.show', $quiz->subject) }}">
                                            {{ $quiz->subject->subject_code }}
                                        </a>
                                    </td>
                                    <td>{{ $quiz->instructor->full_name ?? 'N/A' }}</td>
                                    <td><span class="badge badge-info">{{ $quiz->questions_count }}</span></td>
                                    <td><span class="badge badge-warning">{{ $quiz->attempts_count }}</span></td>
                                    <td>{{ $quiz->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <form action="{{ route('admin.quizzes.restore', $quiz->id) }}" method="POST" class="d-inline" data-confirm="Are you sure you want to restore this quiz?">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" data-action="restore" title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.quizzes.force-delete', $quiz->id) }}" method="POST" class="d-inline" data-confirm="Are you sure you want to permanently delete this quiz? This action cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" data-action="force-delete" title="Permanently Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $quizzes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No trashed quizzes found</p>
                    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Quizzes
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

