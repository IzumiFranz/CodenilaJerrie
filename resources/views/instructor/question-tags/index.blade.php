@extends('layouts.instructor')

@section('title', 'Question Tags')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-tags mr-2"></i>Question Tags
    </h1>
    <a href="{{ route('instructor.question-tags.create') }}" class="btn btn-success btn-sm">
        <i class="fas fa-plus mr-1"></i> Create Tag
    </a>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-4">
                <select name="subject_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_code }} - {{ $subject->subject_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search tags..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Tags List --}}
<div class="card shadow">
    <div class="card-body">
        @if($tags->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tag</th>
                            <th>Subject</th>
                            <th>Questions</th>
                            <th>Description</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                            <tr>
                                <td>
                                    <span class="badge {{ $tag->getColorBadgeClass() }}" 
                                          style="background-color: {{ $tag->color }}; font-size: 14px;">
                                        <i class="fas fa-tag mr-1"></i>{{ $tag->name }}
                                    </span>
                                </td>
                                <td>
                                    @if($tag->subject)
                                        <small>{{ $tag->subject->subject_code }}</small>
                                    @else
                                        <small class="text-muted">All Subjects</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $tag->question_count }} {{ Str::plural('question', $tag->question_count) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ Str::limit($tag->description, 50) }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('instructor.question-tags.edit', $tag) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('instructor.question-tags.destroy', $tag) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Delete this tag? Questions will not be deleted.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $tags->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <p class="text-muted">No tags found. Create your first tag to organize questions.</p>
                <a href="{{ route('instructor.question-tags.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Create Tag
                </a>
            </div>
        @endif
    </div>
</div>
@endsection