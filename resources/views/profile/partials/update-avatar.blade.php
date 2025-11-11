<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-image mr-2"></i>Profile Picture
        </h6>
    </div>
    <div class="card-body text-center">
        <!-- Current Avatar -->
        <div class="mb-4">
            @if(auth()->user()->profile_picture)
                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
                     class="rounded-circle img-thumbnail" 
                     id="avatarPreview"
                     width="200" 
                     height="200" 
                     alt="Profile Picture"
                     style="object-fit: cover;">
            @else
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center img-thumbnail"
                     style="width: 200px; height: 200px; font-size: 5rem;">
                    <i class="fas fa-user" id="avatarPreview"></i>
                </div>
            @endif
        </div>

        <!-- Upload Form -->
        <form action="{{ route('profile.upload-avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="avatar" class="btn btn-primary btn-block">
                    <i class="fas fa-upload mr-2"></i>Choose New Picture
                </label>
                <input type="file" 
                       class="d-none" 
                       id="avatar" 
                       name="avatar" 
                       accept="image/*">
                @error('avatar')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-success btn-block">
                <i class="fas fa-save mr-2"></i>Upload Picture
            </button>
        </form>

        <!-- Delete Avatar -->
        @if(auth()->user()->profile_picture)
            <form action="{{ route('profile.delete-avatar') }}" method="POST" class="mt-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove your profile picture?')">
                    <i class="fas fa-trash mr-2"></i>Remove Picture
                </button>
            </form>
        @endif

        <hr class="my-3">

        <!-- User Info -->
        <div class="text-left">
            <p class="mb-2"><strong>Username:</strong> {{ auth()->user()->username }}</p>
            <p class="mb-2"><strong>Role:</strong> 
                <span class="badge badge-{{ auth()->user()->role === 'admin' ? 'primary' : (auth()->user()->role === 'instructor' ? 'success' : 'info') }}">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </p>
            <p class="mb-2"><strong>Status:</strong> 
                <span class="badge badge-{{ auth()->user()->status === 'active' ? 'success' : 'warning' }}">
                    {{ ucfirst(auth()->user()->status) }}
                </span>
            </p>
            <p class="mb-0"><strong>Member Since:</strong> {{ auth()->user()->created_at->format('M d, Y') }}</p>
        </div>
    </div>
</div>
