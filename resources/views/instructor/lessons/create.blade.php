@extends('layouts.instructor')

@section('title', 'Create Lesson')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
    .form-section {
        background: #fff;
        border-radius: 0.35rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.25rem;
        border-radius: 0.35rem 0.35rem 0 0;
        font-weight: 600;
    }
    
    .section-body {
        padding: 1.5rem 1.25rem;
    }
    
    .file-item {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        transition: all 0.3s;
    }
    
    .file-item:hover {
        background: #eaecf4;
        border-color: #d1d3e2;
    }
    
    .file-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.25rem;
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }
    
    .file-icon.pdf { background: #fee; color: #dc3545; }
    .file-icon.doc { background: #e3f2fd; color: #2196f3; }
    .file-icon.img { background: #e8f5e9; color: #4caf50; }
    .file-icon.zip { background: #fff3e0; color: #ff9800; }
    .file-icon.video { background: #fce4ec; color: #e91e63; }
    .file-icon.default { background: #f5f5f5; color: #757575; }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .status-badge:hover {
        transform: translateY(-2px);
    }
    
    .status-badge.active {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .note-editor.note-frame {
        border: 1px solid #d1d3e2;
    }
    
    .note-editor.note-frame.is-invalid {
        border-color: #e74a3b;
    }
    
    .char-counter {
        font-size: 0.875rem;
        color: #858796;
        float: right;
        margin-top: 0.25rem;
    }
    
    .drag-drop-area {
        border: 2px dashed #d1d3e2;
        border-radius: 0.35rem;
        padding: 2rem;
        text-align: center;
        background: #f8f9fc;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .drag-drop-area:hover,
    .drag-drop-area.dragover {
        border-color: #4e73df;
        background: #eef2ff;
    }
    
    .preview-content {
        background: #fff;
        padding: 1.5rem;
        border-radius: 0.35rem;
        border: 1px solid #e3e6f0;
        min-height: 200px;
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-plus-circle mr-2"></i>Create New Lesson
    </h1>
    <a href="{{ route('instructor.lessons.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back to Lessons
    </a>
</div>

<!-- Error Messages -->
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm">
    <h6 class="alert-heading mb-2">
        <i class="fas fa-exclamation-triangle mr-2"></i>Please fix the following errors:
    </h6>
    <ul class="mb-0 pl-4">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<form action="{{ route('instructor.lessons.store') }}" method="POST" enctype="multipart/form-data" id="lessonForm">
    @csrf

    <!-- Basic Information Section -->
    <div class="form-section">
        <div class="section-header">
            <i class="fas fa-info-circle mr-2"></i>Basic Information
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Lesson Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title"
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" 
                               placeholder="Enter a clear, descriptive title..."
                               required
                               maxlength="255">
                        <small class="form-text text-muted">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Make it clear and engaging for students
                        </small>
                        <span class="char-counter">
                            <span id="titleCount">0</span>/255 characters
                        </span>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Subject <span class="text-danger">*</span>
                        </label>
                        <select name="subject_id" 
                                id="subject_id"
                                class="form-control @error('subject_id') is-invalid @enderror" 
                                required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" 
                                    {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->subject_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">
                    Short Description <span class="text-muted">(Optional)</span>
                </label>
                <textarea name="description" 
                          id="description"
                          class="form-control @error('description') is-invalid @enderror" 
                          rows="2"
                          maxlength="500"
                          placeholder="Brief overview of the lesson (shown in lesson lists)...">{{ old('description') }}</textarea>
                <small class="form-text text-muted">
                    This helps students understand what they'll learn
                </small>
                <span class="char-counter">
                    <span id="descCount">0</span>/500 characters
                </span>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Display Order</label>
                        <input type="number" 
                               name="order" 
                               class="form-control @error('order') is-invalid @enderror" 
                               value="{{ old('order', 1) }}" 
                               min="1"
                               style="max-width: 150px;">
                        <small class="form-text text-muted">
                            Lower numbers appear first in the list
                        </small>
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold d-block">Publishing Status</label>
                        <div class="custom-control custom-radio custom-control-inline status-badge" id="statusDraft">
                            <input type="radio" 
                                   class="custom-control-input" 
                                   id="draft" 
                                   name="status" 
                                   value="draft"
                                   {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="draft">
                                <i class="fas fa-file-alt mr-1"></i>Draft
                            </label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline status-badge ml-3" id="statusPublished">
                            <input type="radio" 
                                   class="custom-control-input" 
                                   id="published" 
                                   name="status" 
                                   value="published"
                                   {{ old('status') === 'published' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="published">
                                <i class="fas fa-check-circle mr-1"></i>Published
                            </label>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            Draft lessons are not visible to students
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lesson Content Section -->
    <div class="form-section">
        <div class="section-header">
            <i class="fas fa-edit mr-2"></i>Lesson Content
        </div>
        <div class="section-body">
            <div class="form-group">
                <label class="font-weight-bold">
                    Content <span class="text-danger">*</span>
                </label>
                <textarea name="content" 
                          id="content" 
                          class="form-control @error('content') is-invalid @enderror" 
                          required>{{ old('content') }}</textarea>
                <small class="form-text text-muted mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Use the rich text editor to format your content. You can add images, links, lists, and more.
                </small>
                @error('content')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Word Count -->
            <div class="text-right">
                <small class="text-muted">
                    <i class="fas fa-file-word mr-1"></i>
                    <span id="wordCount">0</span> words · 
                    <i class="fas fa-clock mr-1"></i>
                    ~<span id="readTime">0</span> min read
                </small>
            </div>
        </div>
    </div>

    <!-- File Attachments Section -->
    <div class="form-section">
        <div class="section-header">
            <i class="fas fa-paperclip mr-2"></i>File Attachments 
            <small class="ml-2 opacity-75">(Optional)</small>
        </div>
        <div class="section-body">
            <div class="alert alert-info mb-3">
                <div class="d-flex">
                    <i class="fas fa-info-circle fa-lg mr-3 mt-1"></i>
                    <div>
                        <strong>Supported Files:</strong> PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, PNG, GIF, ZIP, RAR, MP4, MP3
                        <br>
                        <strong>Max Size:</strong> 10MB per file | <strong>Max Files:</strong> 5 files
                    </div>
                </div>
            </div>

            <!-- Drag & Drop Area -->
            <div class="drag-drop-area" id="dropArea">
                <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                <h5>Drag & Drop Files Here</h5>
                <p class="text-muted mb-3">or</p>
                <label for="file" class="btn btn-primary btn-sm mb-0">
                    <i class="fas fa-folder-open mr-2"></i>Browse Files
                </label>
                <input type="file" 
                       name="file" 
                       id="file"
                       class="d-none @error('file') is-invalid @enderror"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar,.mp4,.mp3"
                       onchange="handleFiles(this.files)">
            </div>
            
            @error('file')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            <!-- File Preview Area -->
            <div id="filePreview" class="mt-3"></div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-section">
        <div class="section-body">
            <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-info" onclick="previewLesson()">
                    <i class="fas fa-eye mr-2"></i>Preview Lesson
                </button>
                
                <div>
                    <a href="{{ route('instructor.lessons.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                        <i class="fas fa-save mr-2"></i>Create Lesson
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>Lesson Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <span class="badge badge-primary" id="previewSubject"></span>
                    <span class="badge badge-secondary ml-2" id="previewStatus"></span>
                </div>
                <h3 class="mb-3" id="previewTitle"></h3>
                <p class="text-muted mb-4" id="previewDescription"></p>
                <hr>
                <div class="preview-content" id="previewContent"></div>
                <div id="previewFile" class="mt-4"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Close Preview
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Summernote
    $('#content').summernote({
        height: 400,
        placeholder: 'Start writing your lesson content here...\n\nTips:\n• Use headings to organize your content\n• Add images and links to make it engaging\n• Break content into digestible sections\n• Include examples and explanations',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onChange: function(contents) {
                updateWordCount();
            }
        }
    });

    // Character counter for title
    $('#title').on('input', function() {
        const count = $(this).val().length;
        $('#titleCount').text(count);
        if (count > 255) {
            $('#titleCount').addClass('text-danger');
        } else {
            $('#titleCount').removeClass('text-danger');
        }
    });

    // Character counter for description
    $('#description').on('input', function() {
        const count = $(this).val().length;
        $('#descCount').text(count);
        if (count > 500) {
            $('#descCount').addClass('text-danger');
        } else {
            $('#descCount').removeClass('text-danger');
        }
    });

    // Status badge selection visual feedback
    $('input[name="status"]').on('change', function() {
        $('.status-badge').removeClass('active');
        $(this).closest('.status-badge').addClass('active');
    });

    // Trigger initial state
    $('input[name="status"]:checked').trigger('change');

    // Drag and drop functionality
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('file');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.classList.remove('dragover');
        }, false);
    });

    dropArea.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        handleFiles(files);
    }, false);

    // Form validation
    $('#lessonForm').on('submit', function(e) {
        const title = $('#title').val().trim();
        const subject = $('#subject_id').val();
        const content = $('#content').summernote('code').trim();

        if (!title || !subject || !content || content === '<p><br></p>') {
            e.preventDefault();
            alert('Please fill in all required fields (Title, Subject, and Content)');
            return false;
        }

        // Show loading state
        $('#submitBtn').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm mr-2"></span>Creating Lesson...');
    });

    // Warn before leaving with unsaved changes
    let formChanged = false;
    $('#lessonForm input, #lessonForm textarea, #lessonForm select').on('change', function() {
        formChanged = true;
    });

    $(window).on('beforeunload', function() {
        if (formChanged) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    $('#lessonForm').on('submit', function() {
        formChanged = false;
    });
});

// Word count and reading time
function updateWordCount() {
    const content = $('#content').summernote('code');
    const text = $('<div>').html(content).text();
    const words = text.trim() ? text.trim().split(/\s+/).length : 0;
    const readTime = Math.ceil(words / 200); // 200 words per minute

    $('#wordCount').text(words);
    $('#readTime').text(readTime);
}

// Handle file selection
function handleFiles(files) {
    const filePreview = document.getElementById('filePreview');
    filePreview.innerHTML = '';

    if (!files || files.length === 0) return;

    const file = files[0]; // Only one file
    const size = (file.size / 1024 / 1024).toFixed(2);
    const ext = file.name.split('.').pop().toLowerCase();

    // Check file size
    if (file.size > 10485760) { // 10MB
        alert('File size exceeds 10MB limit. Please choose a smaller file.');
        document.getElementById('file').value = '';
        return;
    }

    // Determine file icon and class
    let iconClass = 'default';
    let icon = 'fa-file';
    
    if (['pdf'].includes(ext)) {
        iconClass = 'pdf';
        icon = 'fa-file-pdf';
    } else if (['doc', 'docx'].includes(ext)) {
        iconClass = 'doc';
        icon = 'fa-file-word';
    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
        iconClass = 'img';
        icon = 'fa-file-image';
    } else if (['zip', 'rar'].includes(ext)) {
        iconClass = 'zip';
        icon = 'fa-file-archive';
    } else if (['mp4', 'mp3'].includes(ext)) {
        iconClass = 'video';
        icon = 'fa-file-video';
    }

    const html = `
        <div class="file-item">
            <div class="file-icon ${iconClass}">
                <i class="fas ${icon}"></i>
            </div>
            <div class="flex-grow-1">
                <div class="font-weight-bold">${file.name}</div>
                <small class="text-muted">${size} MB</small>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeFile()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    filePreview.innerHTML = html;
}

function removeFile() {
    document.getElementById('file').value = '';
    document.getElementById('filePreview').innerHTML = '';
}

// Preview lesson function
function previewLesson() {
    const title = $('#title').val() || 'Untitled Lesson';
    const subject = $('#subject_id option:selected').text() || 'No Subject';
    const description = $('#description').val() || 'No description provided';
    const content = $('#content').summernote('code') || '<p class="text-muted">No content</p>';
    const status = $('input[name="status"]:checked').val() === 'published' ? 'Published' : 'Draft';
    const file = document.getElementById('file').files[0];

    $('#previewTitle').text(title);
    $('#previewSubject').text(subject);
    $('#previewDescription').text(description);
    $('#previewContent').html(content);
    $('#previewStatus').text(status)
        .removeClass('badge-secondary badge-success')
        .addClass(status === 'Published' ? 'badge-success' : 'badge-secondary');

    // Preview file
    let fileHTML = '';
    if (file) {
        fileHTML = `
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="fas fa-paperclip mr-2"></i>Attachment
                    </h6>
                    <div class="file-item">
                        <div class="file-icon default">
                            <i class="fas fa-file"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">${file.name}</div>
                            <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    $('#previewFile').html(fileHTML);

    $('#previewModal').modal('show');
}
</script>
@endpush