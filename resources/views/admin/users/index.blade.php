@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users mr-2"></i>Users Management</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Add User
        </a>
        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#bulkUploadModal">
            <i class="fas fa-upload mr-1"></i> Bulk Upload
        </button>
        <a href="{{ route('admin.export.users') }}" class="btn btn-success btn-sm">
            <i class="fas fa-download mr-1"></i> Export All
        </a>
        <a href="{{ route('admin.users.trashed') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-trash mr-1"></i> Trash
        </a>
    </div>
</div>

{{-- Bulk Actions Bar --}}
@include('admin.partials.bulk-actions-bar', [
    'model' => 'User',
    'actions' => ['delete', 'status', 'export', 'notify']
])

{{-- Include your Livewire user table --}}
@livewire('user-table')

{{-- Bulk Upload Modal --}}
@include('admin.users.partials.bulk-upload-modal')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bulk actions checkboxes
    if (document.querySelector('.item-checkbox')) {
        console.log('Bulk actions initialized');
    }

    // Auto-submit filters
    document.querySelectorAll('select[name="role"], select[name="status"]').forEach(function(el){
        el.addEventListener('change', function(){
            this.closest('form').submit();
        });
    });

    // jQuery code
    $(function() {
        // Update custom file input label
        $('#csv_file').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert-dismissible').fadeOut('slow');
        }, 5000);

        // Form validation
        $('form').on('submit', function(e) {
            let csvFile = $('#csv_file').val();
            let role = $('#bulk_role').val();

            if (!csvFile) {
                e.preventDefault();
                alert('Please select a CSV file to upload');
                return false;
            }

            if (!role) {
                e.preventDefault();
                alert('Please select a role');
                return false;
            }
        });
    });
});
</script>
@endpush
