@extends('layouts.instructor')

@section('title', 'Manage Attachments - ' . $lesson->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-paperclip mr-2"></i> Manage Attachments
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.lessons.index') }}">Lessons</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instructor.lessons.show', $lesson) }}">{{ Str::limit($lesson->title, 30) }}</a></li>
                    <li class="breadcrumb-item active">Attachments</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('instructor.lessons.show', $lesson) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Lesson
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-files fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Files</h6>
                            <h3 class="mb-0">{{ $statistics['total_count'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-eye fs-3 text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Visible</h6>
                            <h3 class="mb-0">{{ $statistics['visible_count'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-download fs-3 text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Downloads</h6>
                            <h3 class="mb-0">{{ $statistics['total_downloads'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-hdd fs-3 text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Size</h6>
                            <h3 class="mb-0">{{ $lesson->formatted_attachment_size }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Upload New Attachments</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.lessons.attachments.upload', $lesson) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>File Requirements:</strong> Max {{ App\Services\LessonAttachmentService::getMaxFileSizeMB() }}MB per file. 
                    Allowed types: {{ implode(', ', App\Services\LessonAttachmentService::getAllowedExtensions()) }}
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Select Files (Max 10)</label>
                    <input type="file" class="form-control @error('files.*') is-invalid @enderror" 
                           name="files[]" id="fileInput" multiple required accept=".{{ implode(',. ', App\Services\LessonAttachmentService::getAllowedExtensions()) }}">
                    @error('files.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div id="fileList" class="mb-3"></div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                        <i class="bi bi-upload me-2"></i>Upload Files
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('uploadForm').reset(); updateFileList();">
                        <i class="bi bi-x-circle me-2"></i>Clear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attachments List -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Current Attachments</h5>
        </div>
        <div class="card-body">
            @if($lesson->attachments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">No attachments yet. Upload your first file above!</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="attachmentsTable">
                        <thead class="table-light">
                            <tr>
                                <th width="40"><i class="bi bi-grip-vertical"></i></th>
                                <th>File</th>
                                <th>Size</th>
                                <th>Uploaded</th>
                                <th>Downloads</th>
                                <th>Visibility</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortableAttachments">
                            @foreach($lesson->attachments as $attachment)
                                <tr data-id="{{ $attachment->id }}">
                                    <td class="drag-handle" style="cursor: move;">
                                        <i class="bi bi-grip-vertical text-muted"></i>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi {{ $attachment->file_icon }} fs-3 me-3"></i>
                                            <div>
                                                <div class="fw-bold">{{ $attachment->original_filename }}</div>
                                                @if($attachment->description)
                                                    <small class="text-muted">{{ $attachment->description }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $attachment->formatted_file_size }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $attachment->created_at->format('M d, Y') }}<br>
                                            by {{ $attachment->uploader->name }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="bi bi-download me-1"></i>{{ $attachment->download_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm {{ $attachment->is_visible ? 'btn-success' : 'btn-secondary' }} toggle-visibility"
                                                data-id="{{ $attachment->id }}">
                                            <i class="bi {{ $attachment->is_visible ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                                            {{ $attachment->is_visible ? 'Visible' : 'Hidden' }}
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('instructor.lessons.attachments.download', [$lesson, $attachment]) }}" 
                                               class="btn btn-outline-primary" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <button class="btn btn-outline-info edit-description" 
                                                    data-id="{{ $attachment->id }}"
                                                    data-description="{{ $attachment->description }}"
                                                    title="Edit Description">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('instructor.lessons.attachments.delete', [$lesson, $attachment]) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this attachment? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Description Modal -->
<div class="modal fade" id="descriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Description</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="descriptionText" rows="3" maxlength="500"></textarea>
                <small class="text-muted">Optional description for this attachment</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDescription">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// File input preview
document.getElementById('fileInput').addEventListener('change', updateFileList);

function updateFileList() {
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    
    if (!fileInput.files.length) {
        fileList.innerHTML = '';
        return;
    }

    let html = '<div class="card"><div class="card-body"><h6 class="mb-3">Selected Files:</h6><div class="list-group">';
    
    Array.from(fileInput.files).forEach((file, index) => {
        const size = (file.size / 1024 / 1024).toFixed(2);
        const icon = getFileIcon(file.name);
        
        html += `
            <div class="list-group-item d-flex align-items-center">
                <i class="bi ${icon} fs-4 me-3"></i>
                <div class="flex-grow-1">
                    <strong>${file.name}</strong>
                    <small class="text-muted d-block">${size} MB</small>
                </div>
                <textarea class="form-control form-control-sm ms-2" name="descriptions[]" 
                          placeholder="Description (optional)" rows="1" style="max-width: 300px;"></textarea>
            </div>
        `;
    });
    
    html += '</div></div></div>';
    fileList.innerHTML = html;
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const icons = {
        'pdf': 'bi-file-pdf text-danger',
        'doc': 'bi-file-word text-primary',
        'docx': 'bi-file-word text-primary',
        'xls': 'bi-file-excel text-success',
        'xlsx': 'bi-file-excel text-success',
        'ppt': 'bi-file-ppt text-warning',
        'pptx': 'bi-file-ppt text-warning',
        'jpg': 'bi-file-image text-info',
        'jpeg': 'bi-file-image text-info',
        'png': 'bi-file-image text-info',
        'gif': 'bi-file-image text-info',
        'zip': 'bi-file-zip text-secondary',
        'rar': 'bi-file-zip text-secondary',
    };
    return icons[ext] || 'bi-file-earmark text-secondary';
}

// Sortable
@if(!$lesson->attachments->isEmpty())
const sortable = new Sortable(document.getElementById('sortableAttachments'), {
    handle: '.drag-handle',
    animation: 150,
    onEnd: function(evt) {
        const order = Array.from(evt.to.children).map(row => row.dataset.id);
        
        fetch('{{ route("instructor.lessons.attachments.reorder", $lesson) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Order saved successfully', 'success');
            }
        });
    }
});
@endif

// Toggle visibility
document.querySelectorAll('.toggle-visibility').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        
        fetch(`/instructor/lessons/{{ $lesson->id }}/attachments/${id}/toggle-visibility`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const icon = this.querySelector('i');
                if (data.visible) {
                    this.className = 'btn btn-sm btn-success toggle-visibility';
                    icon.className = 'bi bi-eye';
                    this.innerHTML = '<i class="bi bi-eye"></i> Visible';
                } else {
                    this.className = 'btn btn-sm btn-secondary toggle-visibility';
                    icon.className = 'bi bi-eye-slash';
                    this.innerHTML = '<i class="bi bi-eye-slash"></i> Hidden';
                }
                showToast('Visibility updated', 'success');
            }
        });
    });
});

// Edit description
let currentAttachmentId = null;
const descriptionModal = new bootstrap.Modal(document.getElementById('descriptionModal'));

document.querySelectorAll('.edit-description').forEach(btn => {
    btn.addEventListener('click', function() {
        currentAttachmentId = this.dataset.id;
        document.getElementById('descriptionText').value = this.dataset.description || '';
        descriptionModal.show();
    });
});

document.getElementById('saveDescription').addEventListener('click', function() {
    const description = document.getElementById('descriptionText').value;
    
    fetch(`/instructor/lessons/{{ $lesson->id }}/attachments/${currentAttachmentId}/description`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ description })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            descriptionModal.hide();
            showToast('Description updated', 'success');
            location.reload();
        }
    });
});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endpush