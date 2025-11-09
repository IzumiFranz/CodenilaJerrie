@extends('layouts.student')

@section('title', 'My Feedback')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-comment-dots mr-2"></i>My Feedback
    </h1>
    <a href="{{ route('student.feedback.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i>Submit New Feedback
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject/Type</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Response</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedback as $item)
                    <tr>
                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($item->feedbackable)
                                @if($item->feedbackable_type === 'App\Models\Quiz')
                                    <span class="badge badge-success">Quiz</span><br>
                                    {{ Str::limit($item->feedbackable->title, 40) }}
                                @elseif($item->feedbackable_type === 'App\Models\Lesson')
                                    <span class="badge badge-primary">Lesson</span><br>
                                    {{ Str::limit($item->feedbackable->title, 40) }}
                                @endif
                            @else
                                <span class="badge badge-secondary">General</span>
                            @endif
                        </td>
                        <td>
                            @if($item->rating)
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $item->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $item->status === 'resolved' ? 'success' : ($item->status === 'reviewed' ? 'info' : 'warning') }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>
                            @if($item->admin_response)
                                <i class="fas fa-check-circle text-success"></i> Yes
                            @else
                                <i class="fas fa-clock text-muted"></i> Pending
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('student.feedback.show', $item) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No feedback submitted yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($feedback->hasPages())
        <div class="mt-3">
            {{ $feedback->links() }}
        </div>
        @endif
    </div>
</div>
@endsection