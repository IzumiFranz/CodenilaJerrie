class BulkActions {
    constructor() {
        this.selectedIds = [];
        this.selectAllCheckbox = document.getElementById('selectAll');
        this.checkboxes = document.querySelectorAll('.item-checkbox');
        this.bulkActionsBar = document.getElementById('bulkActionsBar');
        this.selectedCountSpan = document.getElementById('selectedCount');
        
        this.init();
    }

    init() {
        // Select All functionality
        if (this.selectAllCheckbox) {
            this.selectAllCheckbox.addEventListener('change', (e) => {
                this.toggleSelectAll(e.target.checked);
            });
        }

        // Individual checkbox listeners
        this.checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateSelection();
            });
        });

        // Bulk action button listeners
        this.setupBulkActionButtons();
    }

    toggleSelectAll(checked) {
        this.checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        this.updateSelection();
    }

    updateSelection() {
        this.selectedIds = Array.from(this.checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        // Update UI
        if (this.selectedCountSpan) {
            this.selectedCountSpan.textContent = this.selectedIds.length;
        }

        // Show/hide bulk actions bar
        if (this.bulkActionsBar) {
            if (this.selectedIds.length > 0) {
                this.bulkActionsBar.classList.remove('d-none');
            } else {
                this.bulkActionsBar.classList.add('d-none');
            }
        }

        // Update select all checkbox state
        if (this.selectAllCheckbox) {
            const allChecked = this.selectedIds.length === this.checkboxes.length;
            const someChecked = this.selectedIds.length > 0 && !allChecked;
            
            this.selectAllCheckbox.checked = allChecked;
            this.selectAllCheckbox.indeterminate = someChecked;
        }
    }

    setupBulkActionButtons() {
        // Bulk Delete
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', () => {
                this.confirmBulkAction('delete', 'Are you sure you want to delete the selected items?');
            });
        }

        // Bulk Update Status
        const bulkStatusBtns = document.querySelectorAll('[data-bulk-action="update-status"]');
        bulkStatusBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const status = e.target.dataset.status;
                this.bulkUpdateStatus(status);
            });
        });

        // Bulk Send Notification
        const bulkNotifyBtn = document.getElementById('bulkNotifyBtn');
        if (bulkNotifyBtn) {
            bulkNotifyBtn.addEventListener('click', () => {
                this.showBulkNotificationModal();
            });
        }

        // Bulk Export
        const bulkExportBtn = document.getElementById('bulkExportBtn');
        if (bulkExportBtn) {
            bulkExportBtn.addEventListener('click', () => {
                this.bulkExport();
            });
        }

        // Bulk Restore
        const bulkRestoreBtn = document.getElementById('bulkRestoreBtn');
        if (bulkRestoreBtn) {
            bulkRestoreBtn.addEventListener('click', () => {
                this.confirmBulkAction('restore', 'Restore the selected items?');
            });
        }
    }

    confirmBulkAction(action, message) {
        if (this.selectedIds.length === 0) {
            this.showAlert('Please select at least one item.', 'warning');
            return;
        }

        if (confirm(message)) {
            this.executeBulkAction(action);
        }
    }

    executeBulkAction(action) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.getBulkActionUrl(action);

        // CSRF Token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfInput);

        // Method
        if (['delete', 'restore'].includes(action)) {
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
        }

        // Selected IDs
        this.selectedIds.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = action === 'delete' || action === 'restore' ? 'ids[]' : 'user_ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });

        document.body.appendChild(form);
        form.submit();
    }

    bulkUpdateStatus(status) {
        if (this.selectedIds.length === 0) {
            this.showAlert('Please select at least one item.', 'warning');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.getBulkActionUrl('update-status');

        // CSRF Token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfInput);

        // Status
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);

        // Selected IDs
        this.selectedIds.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'user_ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });

        document.body.appendChild(form);
        form.submit();
    }

    showBulkNotificationModal() {
        if (this.selectedIds.length === 0) {
            this.showAlert('Please select at least one item.', 'warning');
            return;
        }

        // Set selected IDs in modal form
        const modalForm = document.getElementById('bulkNotificationForm');
        if (modalForm) {
            // Clear existing ID inputs
            modalForm.querySelectorAll('input[name="user_ids[]"]').forEach(input => input.remove());
            
            // Add new ID inputs
            this.selectedIds.forEach(id => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'user_ids[]';
                idInput.value = id;
                modalForm.appendChild(idInput);
            });

            // Show modal
            $('#bulkNotificationModal').modal('show');
        }
    }

    bulkExport() {
        if (this.selectedIds.length === 0) {
            this.showAlert('Please select at least one item.', 'warning');
            return;
        }

        const format = prompt('Export format (csv/json):', 'csv');
        if (!format || !['csv', 'json'].includes(format.toLowerCase())) {
            return;
        }

        const url = new URL(this.getBulkActionUrl('export'), window.location.origin);
        url.searchParams.append('format', format);
        this.selectedIds.forEach(id => {
            url.searchParams.append('ids[]', id);
        });

        window.location.href = url.toString();
    }

    getBulkActionUrl(action) {
        const pathPrefix = window.location.pathname.split('/')[1]; // 'admin', 'instructor', etc.
        const resource = window.location.pathname.split('/')[2]; // 'users', 'courses', etc.
        
        const actionMap = {
            'delete': `/${pathPrefix}/bulk-actions/delete`,
            'update-status': `/${pathPrefix}/bulk-actions/update-status`,
            'restore': `/${pathPrefix}/bulk-actions/restore`,
            'export': `/${pathPrefix}/bulk-actions/export`,
            'notify': `/${pathPrefix}/bulk-actions/send-notifications`
        };

        return actionMap[action] || `/${pathPrefix}/bulk-actions/${action}`;
    }

    showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;

        const container = document.querySelector('.container-fluid');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    }

    clearSelection() {
        this.checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        this.updateSelection();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.item-checkbox')) {
        window.bulkActions = new BulkActions();
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BulkActions;
}