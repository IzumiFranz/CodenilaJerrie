<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Quiz LMS {{ date('Y') }} | 
                @if(auth()->user()->isAdmin())
                    Admin Panel
                @elseif(auth()->user()->isInstructor())
                    Instructor Portal
                @else
                    Student Portal
                @endif
            </span>
        </div>
    </div>
</footer>