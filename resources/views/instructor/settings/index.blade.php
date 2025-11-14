@extends('layouts.instructor')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog"></i> Settings
        </h1>
        <form action="{{ route('instructor.settings.reset') }}" method="POST" 
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
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-bell"></i> Notification Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('instructor.settings.update-notifications') }}" method="POST">
                        @csrf
                        
                        <h6 class="font-weight-bold text-success mb-3">Email Notifications</h6>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_student_enrolled" 
                                   id="email_student_enrolled" {{ $settings->email_student_enrolled ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_student_enrolled">
                                <strong>Student Enrolled</strong>
                                <br><small class="text-muted">Get notified when a student enrolls in your section</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_quiz_submitted" 
                                   id="email_quiz_submitted" {{ $settings->email_quiz_submitted ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_quiz_submitted">
                                <strong>Quiz Submitted</strong>
                                <br><small class="text-muted">Get notified when a student submits a quiz</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_feedback_submitted" 
                                   id="email_feedback_submitted" {{ $settings->email_feedback_submitted ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_feedback_submitted">
                                <strong>Feedback Submitted</strong>
                                <br><small class="text-muted">Get notified when a student submits feedback</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_low_performance_alert" 
                                   id="email_low_performance_alert" {{ $settings->email_low_performance_alert ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_low_performance_alert">
                                <strong>Low Performance Alert</strong>
                                <br><small class="text-muted">Get notified when class average drops below threshold</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="email_announcement" 
                                   id="email_announcement" {{ $settings->email_announcement ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_announcement">
                                <strong>System Announcements</strong>
                                <br><small class="text-muted">Get notified about system announcements</small>
                            </label>
                        </div>
                        
                        <hr>
                        
                        <h6 class="font-weight-bold text-success mb-3 mt-4">In-App Notifications</h6>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_student_enrolled" 
                                   id="notification_student_enrolled" {{ $settings->notification_student_enrolled ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_student_enrolled">
                                <strong>Student Enrolled</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_quiz_submitted" 
                                   id="notification_quiz_submitted" {{ $settings->notification_quiz_submitted ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_quiz_submitted">
                                <strong>Quiz Submitted</strong>
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
                            <input class="form-check-input" type="checkbox" name="notification_low_performance_alert" 
                                   id="notification_low_performance_alert" {{ $settings->notification_low_performance_alert ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_low_performance_alert">
                                <strong>Low Performance Alert</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="notification_announcement" 
                                   id="notification_announcement" {{ $settings->notification_announcement ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_announcement">
                                <strong>System Announcements</strong>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save"></i> Save Notification Preferences
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Display & Quiz Defaults -->
        <div class="col-lg-6 mb-4">
            <!-- Display Preferences -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-palette"></i> Display Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('instructor.settings.update-display') }}" method="POST">
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
                        
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save"></i> Save Display Preferences
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quiz Defaults -->
            <div class="card shadow">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-clipboard-list"></i> Quiz Defaults
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('instructor.settings.update-quiz-defaults') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Default Time Limit</label>
                            <input type="number" name="default_quiz_time_limit" class="form-control" 
                                   value="{{ $settings->default_quiz_time_limit }}" min="5" max="300">
                            <small class="text-muted">Default time limit for new quizzes (5-300 minutes)</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Default Passing Score</label>
                            <input type="number" name="default_passing_score" class="form-control" 
                                   value="{{ $settings->default_passing_score }}" min="1" max="100">
                            <small class="text-muted">Default passing score percentage (1-100%)</small>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Default Max Attempts</label>
                            <input type="number" name="default_max_attempts" class="form-control" 
                                   value="{{ $settings->default_max_attempts }}" min="1" max="10">
                            <small class="text-muted">Default maximum attempts allowed (1-10)</small>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save"></i> Save Quiz Defaults
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection