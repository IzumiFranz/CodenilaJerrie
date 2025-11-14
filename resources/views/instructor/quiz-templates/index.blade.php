@extends('layouts.instructor')
@section('title', 'Quiz Templates')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-layer-group mr-2"></i>Quiz Templates
    </h1>
    <a href="{{ route('instructor.quiz-templates.create') }}" class="btn btn-success btn-sm">
        <i class="fas fa-plus mr-1"></i> Create Template
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="row">
            @forelse($templates as $template)
            <div class="col-md-4 mb-4">
                <div class="card h-100 {{ $template->is_shared ? 'border-primary' : '' }}">
                    <div class="card-body">
                        <h5 class="card-title">
                            {{ $template->name }}
                            @if($template->is_shared)
                                <span class="badge badge-primary badge-sm">Shared</span>
                            @endif
                        </h5>
                        <p class="card-text text-muted">
                            {{ Str::limit($template->description, 80) }}
                        </p>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-clock mr-1"></i>{{ $template->time_limit }} min
                            <span class="mx-2">•</span>
                            <i class="fas fa-percentage mr-1"></i>{{ $template->passing_score }}%
                            <span class="mx-2">•</span>
                            <i class="fas fa-redo mr-1"></i>{{ $template->max_attempts }}x
                        </small>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group btn-group-sm w-100">
                            <a href="{{ route('instructor.quizzes.create', ['template' => $template->id]) }}" 
                               class="btn btn-success">
                                <i class="fas fa-plus"></i> Use Template
                            </a>
                            @if($template->instructor_id == auth()->user()->instructor->id)
                            <form action="{{ route('instructor.quiz-templates.destroy', $template) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this template?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>No templates yet</h5>
                    <p>Create a template to reuse quiz settings</p>
                    <a href="{{ route('instructor.quiz-templates.create') }}" class="btn btn-success">
                        <i class="fas fa-plus mr-2"></i>Create First Template
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection