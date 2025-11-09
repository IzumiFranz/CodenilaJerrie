/* Admin Custom JavaScript */

(function($) {
    "use strict";

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert:not(.alert-permanent)').fadeOut('slow');
    }, 5000);

    // Delete confirmation
    let deleteForm = null;
    
    $(document).on('click', '[data-confirm]', function(e) {
        e.preventDefault();
        deleteForm = $(this).closest('form');
        const message = $(this).data('confirm') || 'Are you sure you want to delete this item?';
        $('#deleteModal .modal-body').text(message);
        $('#deleteModal').modal('show');
    });

    $('#confirmDelete').on('click', function() {
        if (deleteForm) {
            deleteForm.submit();
        }
    });

    // DataTables initialization
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            pageLength: 20,
            lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries to show",
                infoFiltered: "(filtered from _MAX_ total entries)",
                zeroRecords: "No matching records found",
                emptyTable: "No data available in table",
                paginate: {
                    first: "First",
                    previous: "Previous",
                    next: "Next",
                    last: "Last"
                }
            },
            order: [[0, 'desc']]
        });
    }

    // Form validation helper
    $('form[data-validate]').on('submit', function(e) {
        const form = $(this);
        let isValid = true;

        form.find('[required]').each(function() {
            const field = $(this);
            if (!field.val()) {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: form.find('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });

    // Remove validation on input
    $('.form-control, .form-select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    // Loading spinner
    function showLoading() {
        $('body').append('<div class="loading-spinner show"><div class="spinner-border spinner-border-lg text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
    }

    function hideLoading() {
        $('.loading-spinner').remove();
    }

    // Ajax form submission
    $(document).on('submit', 'form[data-ajax]', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        const method = form.attr('method') || 'POST';
        const formData = new FormData(this);

        showLoading();

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showToast('Success', response.message || 'Operation completed successfully', 'success');
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                }
            },
            error: function(xhr) {
                hideLoading();
                const message = xhr.responseJSON?.message || 'An error occurred';
                showToast('Error', message, 'danger');
            }
        });
    });

    // Toast notification
    function showToast(title, message, type = 'info') {
        const bgClass = {
            success: 'bg-success',
            danger: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info'
        }[type] || 'bg-info';

        const toast = $(`
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
                <div class="toast-header ${bgClass} text-white">
                    <strong class="mr-auto">${title}</strong>
                    <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `);

        let container = $('.toast-container');
        if (!container.length) {
            container = $('<div class="toast-container"></div>').appendTo('body');
        }

        container.append(toast);
        toast.toast('show');

        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    // Bulk selection
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.select-item').prop('checked', isChecked);
        toggleBulkActions();
    });

    $(document).on('change', '.select-item', function() {
        toggleBulkActions();
        updateSelectAll();
    });

    function toggleBulkActions() {
        const checkedCount = $('.select-item:checked').length;
        if (checkedCount > 0) {
            $('.bulk-actions').addClass('show');
            $('.bulk-actions .count').text(checkedCount);
        } else {
            $('.bulk-actions').removeClass('show');
        }
    }

    function updateSelectAll() {
        const totalItems = $('.select-item').length;
        const checkedItems = $('.select-item:checked').length;
        $('#selectAll').prop('checked', totalItems === checkedItems && totalItems > 0);
    }

    // Dynamic form fields (show/hide based on role selection)
    $('select[name="role"]').on('change', function() {
        const role = $(this).val();
        $('.role-specific').hide();
        $(`.${role}-fields`).show();
    }).trigger('change');

    // AJAX get sections by course
    $(document).on('change', 'select[name="course_id"]', function() {
        const courseId = $(this).val();
        const sectionSelect = $('select[name="section_id"]');
        
        if (!courseId) {
            sectionSelect.html('<option value="">Select Section</option>');
            return;
        }

        showLoading();

        $.ajax({
            url: `/api/sections/by-course/${courseId}`,
            type: 'GET',
            success: function(sections) {
                hideLoading();
                let options = '<option value="">Select Section</option>';
                sections.forEach(function(section) {
                    options += `<option value="${section.id}">${section.year_level} - ${section.section_name}</option>`;
                });
                sectionSelect.html(options);
            },
            error: function() {
                hideLoading();
                showToast('Error', 'Failed to load sections', 'danger');
            }
        });
    });

    // AJAX get subjects by course
    $(document).on('change', 'select[name="course_id"], select[name="year_level"]', function() {
        const courseId = $('select[name="course_id"]').val();
        const yearLevel = $('select[name="year_level"]').val();
        const subjectSelect = $('select[name="subject_id"]');
        
        if (!courseId) {
            subjectSelect.html('<option value="">Select Subject</option>');
            return;
        }

        showLoading();

        const url = yearLevel ? 
            `/api/subjects/by-course/${courseId}/${yearLevel}` : 
            `/api/subjects/by-course/${courseId}`;

        $.ajax({
            url: url,
            type: 'GET',
            success: function(subjects) {
                hideLoading();
                let options = '<option value="">Select Subject</option>';
                subjects.forEach(function(subject) {
                    options += `<option value="${subject.id}">${subject.subject_code} - ${subject.subject_name}</option>`;
                });
                subjectSelect.html(options);
            },
            error: function() {
                hideLoading();
                showToast('Error', 'Failed to load subjects', 'danger');
            }
        });
    });

    // File upload preview
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        const preview = $(this).siblings('.file-upload-preview');
        
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.html(`<img src="${e.target.result}" alt="Preview">`);
            };
            reader.readAsDataURL(file);
        } else if (file) {
            preview.html(`<p class="text-muted"><i class="fas fa-file"></i> ${file.name}</p>`);
        }
    });

    // CSV download template
    $('.download-template').on('click', function(e) {
        e.preventDefault();
        const role = $(this).data('role');
        window.location.href = `/admin/users/export/template?role=${role}`;
    });

    // Confirm before leaving page with unsaved changes
    let formChanged = false;
    
    $('form input, form select, form textarea').on('change', function() {
        formChanged = true;
    });

    $('form').on('submit', function() {
        formChanged = false;
    });

    $(window).on('beforeunload', function() {
        if (formChanged) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    // Initialize tooltips
    if ($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Initialize popovers
    if ($.fn.popover) {
        $('[data-toggle="popover"]').popover();
    }

    // Copy to clipboard
    $(document).on('click', '.copy-btn', function() {
        const text = $(this).data('copy');
        const tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(text).select();
        document.execCommand('copy');
        tempInput.remove();
        
        showToast('Success', 'Copied to clipboard!', 'success');
    });

    // Export table to CSV
    $('.export-csv').on('click', function() {
        const table = $(this).closest('.card').find('table');
        const csv = [];
        
        // Get headers
        const headers = [];
        table.find('thead th').each(function() {
            headers.push($(this).text().trim());
        });
        csv.push(headers.join(','));
        
        // Get data
        table.find('tbody tr').each(function() {
            const row = [];
            $(this).find('td').each(function() {
                row.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
            });
            csv.push(row.join(','));
        });
        
        // Download
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'export.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showToast('Success', 'Table exported successfully!', 'success');
    });

    // Print function
    $('.print-btn').on('click', function() {
        window.print();
    });

})(jQuery);