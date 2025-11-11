@extends('layouts.' . auth()->user()->role)

@section('title', 'Edit Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-circle mr-2"></i>Edit Profile
        </h1>
        <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
        </a>
    </div>

    <!-- Success Message -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Picture Section -->
        <div class="col-lg-4 mb-4">
            @include('profile.partials.update-avatar')
        </div>

        <!-- Profile Information Section -->
        <div class="col-lg-8 mb-4">
            @include('profile.partials.update-profile-information')
        </div>

        <!-- Password Section -->
        <div class="col-lg-12 mb-4">
            @include('profile.partials.update-password')
        </div>

        <!-- Delete Account Section (Optional) -->
        @if(auth()->user()->role === 'student')
        <div class="col-lg-12 mb-4">
            @include('profile.partials.delete-account')
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview avatar before upload
    $('#avatar').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    // Auto-hide success messages
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);
});
</script>
@endpush