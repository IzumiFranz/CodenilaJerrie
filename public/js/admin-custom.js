(function($) {
    'use strict';

    // Initialize on document ready
    $(document).ready(function() {
        initializeComponents();
        initializeTooltips();
        initializePopovers();
        initializeConfirmations();
        initializeFileInputs();
        initializeAutoHideAlerts();
        initializeFormValidation();
        initializeDataTables();
        initializeSelectAll();
        initializeImagePreview();
        initializeDatePickers();
        initializeRichTextEditors();
        initializeDynamicForms();
    });

    // Initialize all components
    function initializeComponents() {
        console.log('Quiz LMS Admin Panel Initialized');
    }

    // Initialize Bootstrap tooltips
    function initializeTooltips() {
        $('[data-toggle="tooltip"]').tooltip();
    }

    // Initialize Bootstrap popovers
    function initializePopovers() {
        $('[data-toggle="popover"]').popover();
    }

    // Initialize delete confirmations
    function initializeConfirmations() {
        $('.btn-delete').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
                return false;
            }
        });

        // Form confirmation for dangerous actions
        $('form[data-confirm]').on('submit', function(e) {
            const message = $(this).data('confirm');
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Initialize custom file inputs
    function initializeFileInputs() {
        $('.custom-file-input').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Choose file...');
        });
    }

    // Auto-hide alerts after delay
    function initializeAutoHideAlerts() {
        $('.alert:not(.alert-permanent)').each(function() {
            const $alert = $(this);
            setTimeout(function() {
                $alert.fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        });
    }

    // Form validation enhancements
    function initializeFormValidation() {
        // Real-time validation
        $('form.needs-validation').on('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(this).addClass('was-validated');
        });

        // Email validation
        $('input[type="email"]').on('blur', function() {
            const email = $(this).val();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Phone validation
        $('input[type="tel"]').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            $(this).val(value);
        });
    }

    // Initialize DataTables (if used)
    function initializeDataTables() {
        if ($.fn.DataTable) {
            $('.datatable').DataTable({
                pageLength: 25,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records..."
                }
            });
        }
    }

    // Select all checkboxes functionality
    function initializeSelectAll() {
        $('.select-all').on('change', function() {
            const isChecked = $(this).prop('checked');
            $(this).closest('table').find('.select-item').prop('checked', isChecked);
        });

        $('.select-item').on('change', function() {
            const $table = $(this).closest('table');
            const totalItems = $table.find('.select-item').length;
            const checkedItems = $table.find('.select-item:checked').length;
            $table.find('.select-all').prop('checked', totalItems === checkedItems);
        });
    }

    // Initialize image preview
    function initializeImagePreview() {
        $('input[type="file"][accept*="image"]').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                const $preview = $(this).data('preview');
                
                reader.onload = function(e) {
                    if ($preview) {
                        $($preview).attr('src', e.target.result).show();
                    }
                };
                
                reader.readAsDataURL(file);
            }
        });
    }

    // Initialize date pickers
    function initializeDatePickers() {
        if ($.fn.datepicker) {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
        }
    }

    // Initialize rich text editors
    function initializeRichTextEditors() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '.rich-editor',
                height: 400,
                menubar: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | \
                         alignleft aligncenter alignright alignjustify | \
                         bullist numlist outdent indent | removeformat | help'
            });
        }
    }

    // Dynamic form behaviors
    function initializeDynamicForms() {
        // Role-based form fields visibility
        $('select[name="role"]').on('change', function() {
            const role = $(this).val();
            
            // Hide all role-specific sections
            $('.admin-fields, .instructor-fields, .student-fields').hide();
            
            // Show relevant section
            if (role === 'admin') {
                $('.admin-fields').show();
            } else if (role === 'instructor') {
                $('.instructor-fields').show();
            } else if (role === 'student') {
                $('.student-fields').show();
            }
        }).trigger('change');

        // Course-based subject filtering
        $('select[name="course_id"]').on('change', function() {
            const courseId = $(this).val();
            const $subjectSelect = $('select[name="subject_id"]');
            
            if (courseId) {
                QuizLMS.ajax(
                    '/admin/api/subjects?course_id=' + courseId,
                    'GET',
                    null,
                    function(response) {
                        $subjectSelect.empty().append('<option value="">Select Subject</option>');
                        response.subjects.forEach(function(subject) {
                            $subjectSelect.append(
                                `<option value="${subject.id}">${subject.name}</option>`
                            );
                        });
                    }
                );
            }
        });

        // Year level-based section filtering
        $('select[name="year_level"]').on('change', function() {
            const yearLevel = $(this).val();
            const courseId = $('select[name="course_id"]').val();
            const $sectionSelect = $('select[name="section_id"]');
            
            if (yearLevel && courseId) {
                QuizLMS.ajax(
                    `/admin/api/sections?course_id=${courseId}&year_level=${yearLevel}`,
                    'GET',
                    null,
                    function(response) {
                        $sectionSelect.empty().append('<option value="">Select Section</option>');
                        response.sections.forEach(function(section) {
                            $sectionSelect.append(
                                `<option value="${section.id}">${section.name}</option>`
                            );
                        });
                    }
                );
            }
        });

        // Duplicate checking for assignments
        $('#check-duplicate').on('click', function() {
            const subjectId = $('select[name="subject_id"]').val();
            const sectionId = $('select[name="section_id"]').val();
            const academicYear = $('select[name="academic_year"]').val();
            const semester = $('select[name="semester"]').val();
            
            if (subjectId && sectionId && academicYear && semester) {
                QuizLMS.ajax(
                    '/admin/api/check-assignment-duplicate',
                    'POST',
                    {
                        subject_id: subjectId,
                        section_id: sectionId,
                        academic_year: academicYear,
                        semester: semester
                    },
                    function(response) {
                        if (response.exists) {
                            QuizLMS.showToast('Assignment already exists for this combination!', 'warning');
                        } else {
                            QuizLMS.showToast('No duplicate found. You can proceed.', 'success');
                        }
                    }
                );
            } else {
                QuizLMS.showToast('Please fill all required fields first.', 'error');
            }
        });
    }

    // Utility Functions
    window.QuizLMS = {
        // Show loading overlay
        showLoading: function(message = 'Loading...') {
            const overlay = `
                <div class="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                     background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="text-white" style="font-size: 1.1rem;">${message}</p>
                    </div>
                </div>
            `;
            $('body').append(overlay);
        },

        // Hide loading overlay
        hideLoading: function() {
            $('.loading-overlay').fadeOut(function() {
                $(this).remove();
            });
        },

        // Show toast notification
        showToast: function(message, type = 'success') {
            const bgClass = {
                success: 'bg-success',
                error: 'bg-danger',
                warning: 'bg-warning',
                info: 'bg-info'
            }[type] || 'bg-success';

            const iconClass = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            }[type] || 'fa-check-circle';

            const toast = `
                <div class="custom-toast ${bgClass}" style="position: fixed; top: 20px; right: 20px; z-index: 10000; 
                     min-width: 300px; padding: 15px 20px; border-radius: 8px; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                     display: flex; align-items: center; animation: slideIn 0.3s ease-out;">
                    <i class="fas ${iconClass} mr-3" style="font-size: 1.5rem;"></i>
                    <span style="flex: 1;">${message}</span>
                    <button type="button" class="close ml-3" style="color: white; opacity: 0.8;" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

            const $toast = $(toast).appendTo('body');
            
            $toast.find('.close').on('click', function() {
                $toast.fadeOut(function() {
                    $(this).remove();
                });
            });

            setTimeout(function() {
                $toast.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        },

        // Confirm dialog with custom styling
        confirm: function(message, callback, title = 'Confirm Action') {
            const modal = `
                <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>${title}</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>${message}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmBtn">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            const $modal = $(modal).appendTo('body');
            $modal.modal('show');
            
            $modal.find('#confirmBtn').on('click', function() {
                $modal.modal('hide');
                callback();
            });
            
            $modal.on('hidden.bs.modal', function() {
                $(this).remove();
            });
        },

        // AJAX helper
        ajax: function(url, method, data, successCallback, errorCallback) {
            $.ajax({
                url: url,
                method: method,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (successCallback) successCallback(response);
                },
                error: function(xhr) {
                    if (errorCallback) {
                        errorCallback(xhr);
                    } else {
                        let errorMessage = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        QuizLMS.showToast(errorMessage, 'error');
                    }
                }
            });
        },

        // Format number with commas
        formatNumber: function(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        // Truncate text
        truncate: function(text, length = 100) {
            if (text.length <= length) return text;
            return text.substring(0, length) + '...';
        },

        // Copy to clipboard
        copyToClipboard: function(text) {
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            QuizLMS.showToast('Copied to clipboard!', 'success');
        },

        // Debounce function
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Bulk action handler
        handleBulkAction: function(action, selectedIds) {
            if (selectedIds.length === 0) {
                QuizLMS.showToast('Please select at least one item.', 'warning');
                return;
            }

            QuizLMS.confirm(
                `Are you sure you want to ${action} ${selectedIds.length} item(s)?`,
                function() {
                    QuizLMS.showLoading(`Processing ${action}...`);
                    
                    QuizLMS.ajax(
                        '/admin/bulk-action',
                        'POST',
                        {
                            action: action,
                            ids: selectedIds
                        },
                        function(response) {
                            QuizLMS.hideLoading();
                            QuizLMS.showToast(response.message, 'success');
                            location.reload();
                        },
                        function(xhr) {
                            QuizLMS.hideLoading();
                            QuizLMS.showToast('Bulk action failed.', 'error');
                        }
                    );
                },
                `Confirm ${action}`
            );
        },

        // File upload with progress
        uploadFile: function(file, url, progressCallback, successCallback, errorCallback) {
            const formData = new FormData();
            formData.append('file', file);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            if (progressCallback) progressCallback(percentComplete);
                        }
                    }, false);
                    return xhr;
                },
                success: successCallback,
                error: errorCallback
            });
        },

        // Format file size
        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },

        // Date formatting
        formatDate: function(date, format = 'Y-m-d') {
            const d = new Date(date);
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            
            return format
                .replace('Y', year)
                .replace('m', month)
                .replace('d', day);
        },

        // Time ago formatter
        timeAgo: function(date) {
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            const intervals = {
                year: 31536000,
                month: 2592000,
                week: 604800,
                day: 86400,
                hour: 3600,
                minute: 60,
                second: 1
            };

            for (const [name, value] of Object.entries(intervals)) {
                const interval = Math.floor(seconds / value);
                if (interval >= 1) {
                    return interval + ' ' + name + (interval > 1 ? 's' : '') + ' ago';
                }
            }
            return 'just now';
        }
    };

    // Auto-submit search forms with debounce
    const debouncedSearch = QuizLMS.debounce(function() {
        $(this).closest('form').submit();
    }, 500);

    $('input[name="search"]').on('keyup', debouncedSearch);

    // Prevent double form submission
    $('form').on('submit', function() {
        const $btn = $(this).find('button[type="submit"]');
        if ($btn.data('submitted') === true) {
            return false;
        }
        $btn.data('submitted', true);
        $btn.prop('disabled', true);
        
        setTimeout(function() {
            $btn.data('submitted', false);
            $btn.prop('disabled', false);
        }, 3000);
    });

    // Back to top button
    const $backToTop = $('<button class="btn btn-primary scroll-to-top" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: none; border-radius: 50%; width: 50px; height: 50px;">')
        .html('<i class="fas fa-arrow-up"></i>')
        .appendTo('body');

    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $backToTop.fadeIn();
        } else {
            $backToTop.fadeOut();
        }
    });

    $backToTop.on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 600);
    });

    // Export functionality helper
    window.exportTable = function(tableId, filename = 'export') {
        const $table = $(tableId);
        const csv = [];
        
        $table.find('tr').each(function() {
            const row = [];
            $(this).find('th, td').not('.actions').each(function() {
                const text = $(this).text().trim().replace(/\n/g, ' ').replace(/,/g, ';');
                row.push(text);
            });
            if (row.length > 0) {
                csv.push(row.join(','));
            }
        });

        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        QuizLMS.showToast('Table exported successfully!', 'success');
    };

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + K: Focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            $('input[name="search"]').focus();
        }
        
        // Escape: Close modals
        if (e.key === 'Escape') {
            $('.modal').modal('hide');
        }
    });

    // Initialize chart colors (if using charts)
    window.chartColors = {
        primary: '#4e73df',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b',
        secondary: '#858796',
        light: '#f8f9fc',
        dark: '#5a5c69'
    };

    // Handle AJAX errors globally
    $(document).ajaxError(function(event, xhr, settings, error) {
        console.error('AJAX Error:', error);
        if (xhr.status === 419) {
            QuizLMS.showToast('Session expired. Please refresh the page.', 'error');
            setTimeout(function() {
                location.reload();
            }, 2000);
        } else if (xhr.status === 403) {
            QuizLMS.showToast('You do not have permission to perform this action.', 'error');
        } else if (xhr.status === 500) {
            QuizLMS.showToast('Server error. Please try again later.', 'error');
        } else if (xhr.status === 404) {
            QuizLMS.showToast('Resource not found.', 'error');
        }
    });

    // Auto-save form draft (for long forms)
    $('form[data-autosave]').each(function() {
        const formId = $(this).attr('id');
        const $form = $(this);
        
        // Load saved draft
        const savedData = localStorage.getItem('form_draft_' + formId);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(function(key) {
                    $form.find(`[name="${key}"]`).val(data[key]);
                });
                QuizLMS.showToast('Draft restored', 'info');
            } catch (e) {
                console.error('Failed to load draft:', e);
            }
        }
        
        // Save draft on input
        const saveDraft = QuizLMS.debounce(function() {
            const formData = {};
            $form.serializeArray().forEach(function(field) {
                formData[field.name] = field.value;
            });
            localStorage.setItem('form_draft_' + formId, JSON.stringify(formData));
        }, 1000);
        
        $form.on('input change', saveDraft);
        
        // Clear draft on successful submit
        $form.on('submit', function() {
            localStorage.removeItem('form_draft_' + formId);
        });
    });

    // Sidebar toggle for mobile
    $('#sidebarToggle, #sidebarToggleTop').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('sidebar-toggled');
        $('.sidebar').toggleClass('toggled');
    });

    // Log console message
    console.log('%cQuiz LMS Admin Panel', 'color: #4e73df; font-size: 20px; font-weight: bold;');
    console.log('%cVersion 1.0.0', 'color: #858796; font-size: 12px;');
    console.log('%cDeveloped with ❤️', 'color: #1cc88a; font-size: 12px;');

})(jQuery);

// CSS Animation for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);