@extends('layouts.instructor')
@section('title', isset($lesson) ? 'Edit Lesson' : 'Create Lesson')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endpush

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-book mr-2"></i>{{ isset($lesson) ? 'Edit Lesson' : 'Create Lesson' }}
    </h1>
    <a href="{{ route('instructor.lessons.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <form action="{{ isset($lesson) ? route('instructor.lessons.update', $lesson) : route('instructor.lessons.store') }}" 
              method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($lesson))
                @method('PUT')
            @endif

            <div class="form-group">
                <label>Subject <span class="text-danger">*</span></label>
                <select name="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" 
                            {{ (old('subject_id', $lesson->subject_id ?? $selectedSubject) == $subject->id) ? 'selected' : '' }}>
                        {{ $subject->subject_name }}
                    </option>
                    @endforeach
                </select>
                @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                       value="{{ old('title', $lesson->title ?? '') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Content <span class="text-danger">*</span></label>
                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" 
                          rows="10" required>{{ old('content', $lesson->content ?? '') }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Attach File (Optional)</label>
                <input type="file" name="file" class="form-control-file @error('file') is-invalid @enderror">
                <small class="form-text text-muted">Accepted: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP (Max: 10MB)</small>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                
                @if(isset($lesson) && $lesson->file_path)
                    <div class="mt-2 p-2 bg-light rounded">
                        <small>Current file: <strong>{{ $lesson->file_name }}</strong></small>
                        <label class="ml-3">
                            <input type="checkbox" name="remove_file" value="1"> Remove existing file
                        </label>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Order</label>
                        <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" 
                               value="{{ old('order', $lesson->order ?? 1) }}" min="1">
                        @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="d-block">Status</label>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input" id="is_published" 
                                   name="is_published" value="1" 
                                   {{ old('is_published', $lesson->is_published ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_published">
                                Publish lesson (visible to students)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-2"></i>{{ isset($lesson) ? 'Update' : 'Create' }} Lesson
                </button>
                <a href="{{ route('instructor.lessons.index') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {
    $('#content').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
});
</script>
@endpush