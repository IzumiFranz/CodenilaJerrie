@extends('layouts.instructor')

@section('title', 'Edit Question Tag')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tag mr-2"></i>Edit Question Tag
            </h1>
            <a href="{{ route('question-tags.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('question-tags.update', $tag->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Tag Name --}}
                    <div class="form-group">
                        <label for="name">
                            Tag Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $tag->name) }}"
                               placeholder="e.g., Algebra, Programming Basics"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Subject --}}
                    <div class="form-group">
                        <label for="subject_id">Subject (Optional)</label>
                        <select class="form-control @error('subject_id') is-invalid @enderror" 
                                id="subject_id" 
                                name="subject_id">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $tag->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_code }} - {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Leave blank to use this tag across all subjects
                        </small>
                    </div>

                    {{-- Color --}}
                    <div class="form-group">
                        <label for="color">
                            Tag Color <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="color" 
                                   class="form-control @error('color') is-invalid @enderror" 
                                   id="color" 
                                   name="color" 
                                   value="{{ old('color', $tag->color ?? '#007bff') }}"
                                   style="height: 45px;"
                                   required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="colorPreview" style="background-color: {{ old('color', $tag->color ?? '#007bff') }}; width: 50px;"></span>
                            </div>
                        </div>
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Choose a color to identify this tag visually
                        </small>
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Brief description of this tag's purpose">{{ old('description', $tag->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    {{-- Preview --}}
                    <div class="form-group">
                        <label>Preview:</label>
                        <div>
                            <span id="tagPreview" class="badge" style="background-color: {{ old('color', $tag->color ?? '#007bff') }}; color: white; font-size: 14px;">
                                <i class="fas fa-tag mr-1"></i><span id="previewText">{{ old('name', $tag->name) }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('question-tags.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update Tag
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update preview when name changes
    $('#name').on('input', function() {
        const text = $(this).val() || 'Your Tag Name';
        $('#previewText').text(text);
    });
    
    // Update preview when color changes
    $('#color').on('input', function() {
        const color = $(this).val();
        $('#colorPreview').css('background-color', color);
        $('#tagPreview').css('background-color', color);
        
        // Adjust text color based on background brightness
        const brightness = getColorBrightness(color);
        const textColor = brightness > 128 ? '#000000' : '#FFFFFF';
        $('#tagPreview').css('color', textColor);
    });
    
    function getColorBrightness(hex) {
        hex = hex.replace('#', '');
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        return (r * 299 + g * 587 + b * 114) / 1000;
    }
});
</script>
@endpush
