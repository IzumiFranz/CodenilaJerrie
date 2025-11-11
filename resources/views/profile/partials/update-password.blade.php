<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-lock mr-2"></i>Update Password
        </h6>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-4">
            Ensure your account is using a long, random password to stay secure.
        </p>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <!-- Current Password -->
            <div class="form-group">
                <label for="update_password_current_password">Current Password</label>
                <input type="password" 
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                       id="update_password_current_password" 
                       name="current_password" 
                       autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="row">
                <!-- New Password -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="update_password_password">New Password</label>
                        <input type="password" 
                               class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                               id="update_password_password" 
                               name="password" 
                               autocomplete="new-password">
                        <small class="form-text text-muted">
                            Min. 8 characters with uppercase, lowercase, and number
                        </small>
                        @error('password', 'updatePassword')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="update_password_password_confirmation">Confirm Password</label>
                        <input type="password" 
                               class="form-control" 
                               id="update_password_password_confirmation" 
                               name="password_confirmation" 
                               autocomplete="new-password">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>Update Password
                </button>
                
                @if (session('status') === 'password-updated')
                    <span class="text-success ml-3">
                        <i class="fas fa-check-circle mr-1"></i>Password updated successfully!
                    </span>
                @endif
            </div>
        </form>
    </div>
</div>