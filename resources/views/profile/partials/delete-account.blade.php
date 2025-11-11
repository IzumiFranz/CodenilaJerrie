<div class="card shadow border-danger">
    <div class="card-header py-3 bg-danger">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-exclamation-triangle mr-2"></i>Delete Account
        </h6>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
        </p>

        <button type="button" 
                class="btn btn-danger" 
                data-toggle="modal" 
                data-target="#deleteAccountModal">
            <i class="fas fa-trash mr-2"></i>Delete Account
        </button>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Account Deletion
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                
                <div class="modal-body">
                    <p class="text-danger font-weight-bold">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        This action cannot be undone!
                    </p>
                    <p>
                        Once your account is deleted, all of its resources and data will be permanently deleted. 
                        Please enter your password to confirm you would like to permanently delete your account.
                    </p>

                    <div class="form-group">
                        <label for="delete_password">Password</label>
                        <input type="password" 
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                               id="delete_password" 
                               name="password" 
                               placeholder="Enter your password" 
                               required>
                        @error('password', 'userDeletion')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i>Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>