@extends('layouts.student')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog"></i> Settings
        </h1>
        <form action="{{ route('student.settings.reset') }}" method="POST" 
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
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-bell"></i> Notification Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.settings.update-notifications') }}" method="POST">
                        @csrf
                        
                        <h6 class="font-weight-bold text-info mb-3">Email Notifications</h6>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_lesson_published" 
                                   id="email_lesson_published" {{ $settings->email_lesson_published ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_lesson_published">
                                <strong>Lesson Published</strong>
                                <br><small class="text-muted">Get notified when a new lesson is published</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_quiz_published" 
                                   id="email_quiz_published" {{ $settings->email_quiz_published ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_quiz_published">
                                <strong>Quiz Published</strong>
                                <br><small class="text-muted">Get notified when a new quiz is available</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_quiz_result" 
                                   id="email_quiz_result" {{ $settings->email_quiz_result ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_quiz_result">
                                <strong>Quiz Results</strong>
                                <br><small class="text-muted">Get notified when your quiz is graded</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_feedback_response" 
                                   id="email_feedback_response" {{ $settings->email_feedback_response ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_feedback_response">
                                <strong>Feedback Responses</strong>
                                <br><small class="text-muted">Get notified when admin responds to your feedback</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="email_enrollment" 
                                   id="email_enrollment" {{ $settings->email_enrollment ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_enrollment">
                                <strong>Enrollment Confirmation</strong>
                                <br><small class="text-muted">Get notified when you're enrolled in a section</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="email_announcement" 
                                   id="email_announcement" {{ $settings->email_announcement ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_announcement">
                                <strong>Announcements</strong>
                                <br><small class="text-muted">Get notified about system announcements</small>
                            </label>
                        </div>
                        
                        <hr>
                        
                        <h6 class="font-weight-bold text-info mb-3 mt-4">In-App Notifications</h6>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_lesson_published" 
                                   id="notification_lesson_published" {{ $settings->notification_lesson_published ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_lesson_published">
                                <strong>Lesson Published</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_quiz_published" 
                                   id="notification_quiz_published" {{ $settings->notification_quiz_published ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_quiz_published">
                                <strong>Quiz Published</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_quiz_result" 
                                   id="notification_quiz_result" {{ $settings->notification_quiz_result ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_quiz_result">
                                <strong>Quiz Results</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_feedback_response" 
                                   id="notification_feedback_response" {{ $settings->notification_feedback_response ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_feedback_response">
                                <strong>Feedback Responses</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_enrollment" 
                                   id="notification_enrollment" {{ $settings->notification_enrollment ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_enrollment">
                                <strong>Enrollment Confirmation</strong>
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="notification_announcement" 
                                   id="notification_announcement" {{ $settings->notification_announcement ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_announcement">
                                <strong>Announcements</strong>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-save"></i> Save Notification Preferences
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Display Settings -->
        <div class="col-lg-6 mb-4">
            <!-- Display Preferences -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-palette"></i> Display Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.settings.update-display') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Theme</label>
                            <select name="theme" class="form-select">
                                <option value="light" {{ $settings->theme === 'light' ? 'selected' : '' }}>Light</option>
                                <option value="dark" {{ $settings->theme === 'dark' ? 'selected' : '' }}>Dark</option>
                            </select>
                            <small class="text-muted">Choose your preferred theme</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Language</label>
                            <select name="language" class="form-select">
                                <option value="en" {{ $settings->language === 'en' ? 'selected' : '' }}>English</option>
                                <option value="tl" {{ $settings->language === 'tl' ? 'selected' : '' }}>Tagalog</option>
                                <option value="es" {{ $settings->language === 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ $settings->language === 'fr' ? 'selected' : '' }}>French</option>
                            </select>
                            <small class="text-muted">Select your preferred language</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Timezone</label>
                            <select name="timezone" class="form-select">
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
                        
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-save"></i> Save Display Preferences
                        </button>
                    </form>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-shield-alt"></i> Privacy Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.settings.update-privacy') }}" method="POST">
                        @csrf
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="show_profile_to_others" 
                                   id="show_profile_to_others" {{ $settings->show_profile_to_others ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_profile_to_others">
                                <strong>Show Profile to Other Students</strong>
                                <br><small class="text-muted">Allow other students to view your profile</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="show_progress_to_instructors" 
                                   id="show_progress_to_instructors" {{ $settings->show_progress_to_instructors ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_progress_to_instructors">
                                <strong>Share Progress with Instructors</strong>
                                <br><small class="text-muted">Allow instructors to view your detailed progress</small>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-save"></i> Save Privacy Preferences
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quiz Preferences -->
            <div class="card shadow">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-clipboard-list"></i> Quiz Preferences
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.settings.update-quiz') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Auto-Save Interval</label>
                            <input type="number" name="auto_save_interval" class="form-control" 
                                   value="{{ $settings->auto_save_interval }}" min="1" max="10">
                            <small class="text-muted">How often to auto-save your answers (1-10 seconds)</small>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="show_timer_warning" 
                                   id="show_timer_warning" {{ $settings->show_timer_warning ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_timer_warning">
                                <strong>Show Timer Warning</strong>
                                <br><small class="text-muted">Display visual warning when time is running out</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="play_timer_sound" 
                                   id="play_timer_sound" {{ $settings->play_timer_sound ? 'checked' : '' }}>
                            <label class="form-check-label" for="play_timer_sound">
                                <strong>Play Timer Sound</strong>
                                <br><small class="text-muted">Play audio alert when time is running out</small>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-save"></i> Save Quiz Preferences
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection