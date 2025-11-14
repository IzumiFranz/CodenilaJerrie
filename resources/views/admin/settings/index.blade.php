@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog"></i> Settings
        </h1>
        <form action="{{ route('admin.settings.reset') }}" method="POST" 
              data-confirm="Are you sure you want to reset all settings to default? All your current settings will be lost.">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger" data-action="reset">
                <i class="fas fa-redo"></i> Reset to Default
            </button>
        </form>
    </div>

    <div class="row">
        <!-- Notifications Settings -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-bell"></i> Notification Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update-notifications') }}" method="POST">
                        @csrf
                        
                        <h6 class="font-weight-bold text-primary mb-3">Email Notifications</h6>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_new_user" 
                                   id="email_new_user" {{ $settings->email_new_user ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_new_user">
                                <strong>New User Registered</strong>
                                <br><small class="text-muted">Get notified when a new user account is created</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_feedback_submitted" 
                                   id="email_feedback_submitted" {{ $settings->email_feedback_submitted ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_feedback_submitted">
                                <strong>Feedback Submitted</strong>
                                <br><small class="text-muted">Get notified when any feedback is submitted</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_system_error" 
                                   id="email_system_error" {{ $settings->email_system_error ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_system_error">
                                <strong>System Errors</strong>
                                <br><small class="text-muted">Get notified when critical errors occur</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_weekly_report" 
                                   id="email_weekly_report" {{ $settings->email_weekly_report ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_weekly_report">
                                <strong>Weekly Report</strong>
                                <br><small class="text-muted">Receive weekly summary of system activity</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="email_course_milestone" 
                                   id="email_course_milestone" {{ $settings->email_course_milestone ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_course_milestone">
                                <strong>Course Milestones</strong>
                                <br><small class="text-muted">Get notified when enrollment/completion milestones are reached</small>
                            </label>
                        </div>
                        
                        <hr>
                        
                        <h6 class="font-weight-bold text-primary mb-3 mt-4">In-App Notifications</h6>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_new_user" 
                                   id="notification_new_user" {{ $settings->notification_new_user ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_new_user">
                                <strong>New User Registered</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_feedback_submitted" 
                                   id="notification_feedback_submitted" {{ $settings->notification_feedback_submitted ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_feedback_submitted">
                                <strong>Feedback Submitted</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_system_error" 
                                   id="notification_system_error" {{ $settings->notification_system_error ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_system_error">
                                <strong>System Errors</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_weekly_report" 
                                   id="notification_weekly_report" {{ $settings->notification_weekly_report ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_weekly_report">
                                <strong>Weekly Report</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="notification_course_milestone" 
                                   id="notification_course_milestone" {{ $settings->notification_course_milestone ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_course_milestone">
                                <strong>Course Milestones</strong>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Save Notification Preferences
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Display & System Defaults -->
        <div class="col-lg-6 mb-4">
            <!-- Display Preferences -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-palette"></i> Display Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update-display') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Theme</label>
                            <select name="theme" class="form-control">
                                <option value="light" {{ $settings->theme === 'light' ? 'selected' : '' }}>Light</option>
                                <option value="dark" {{ $settings->theme === 'dark' ? 'selected' : '' }}>Dark</option>
                            </select>
                            <small class="text-muted">Choose your preferred theme</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Language</label>
                            <select name="language" class="form-control">
                                <option value="en" {{ $settings->language === 'en' ? 'selected' : '' }}>English</option>
                                <option value="tl" {{ $settings->language === 'tl' ? 'selected' : '' }}>Tagalog</option>
                                <option value="es" {{ $settings->language === 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ $settings->language === 'fr' ? 'selected' : '' }}>French</option>
                            </select>
                            <small class="text-muted">Select your preferred language</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Timezone</label>
                            <select name="timezone" class="form-control">
                                <option value="Asia/Manila" {{ $settings->timezone === 'Asia/Manila' ? 'selected' : '' }}>Asia/Manila (PHT)</option>
                                <option value="UTC" {{ $settings->timezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ $settings->timezone === 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                                <option value="America/Los_Angeles" {{ $settings->timezone === 'America/Los_Angeles' ? 'selected' : '' }}>America/Los Angeles (PST)</option>
                            </select>
                            <small class="text-muted">Choose your timezone for accurate dates and times</small>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Items Per Page</label>
                            <input type="number" name="items_per_page" class="form-control" 
                                   value="{{ $settings->items_per_page }}" min="10" max="100">
                            <small class="text-muted">Number of items to display per page (10-100)</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Save Display Preferences
                        </button>
                    </form>
                </div>
            </div>

            <!-- System Defaults -->
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-server"></i> System Defaults
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update-system-defaults') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Default Academic Year</label>
                            <input type="text" name="default_academic_year" class="form-control" 
                                   value="{{ $settings->default_academic_year }}" placeholder="e.g., 2024-2025">
                            <small class="text-muted">Default academic year for new records (optional)</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Default Semester</label>
                            <select name="default_semester" class="form-control">
                                <option value="1st" {{ $settings->default_semester === '1st' ? 'selected' : '' }}>1st Semester</option>
                                <option value="2nd" {{ $settings->default_semester === '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                <option value="summer" {{ $settings->default_semester === 'summer' ? 'selected' : '' }}>Summer</option>
                            </select>
                            <small class="text-muted">Default semester for new records</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Default Section Max Students</label>
                            <input type="number" name="default_section_max_students" class="form-control" 
                                   value="{{ $settings->default_section_max_students }}" min="10" max="100">
                            <small class="text-muted">Default maximum students per section (10-100)</small>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="auto_approve_enrollments" 
                                   id="auto_approve_enrollments" {{ $settings->auto_approve_enrollments ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_approve_enrollments">
                                <strong>Auto-Approve Enrollments</strong>
                                <br><small class="text-muted">Automatically approve enrollment requests without admin review</small>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Save System Defaults
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection