<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand gradient-text d-flex gap-1" href="#">
            {{-- <i class="fas fa-cube me-2"></i> --}}
            <img src="{{ asset('favicon_io2/android-chrome-512x512.png') }}" style="width: 50px;" alt="logo">
            <div class="app-logo">
                আছিয়া <div style="font-size: 12px; margin-top: -0.595rem;">ভ‍্যারাইটিস শপ</div>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto text-md-start text-center">
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>

                {{-- <li class="nav-item ms-2"><a class="btn btn-outline-primary rounded-pill px-3" href="#demo">Get Demo</a></li> --}}
            </ul>
            <div class="text-md-start text-center py-md-0 pt-2 pb-3">
                @if(!auth()->check())
                <a href="{{ route('sign.in') }}" class="btn btn-gradient-border rounded-5 text-uppercase ms-lg-3 ms-0">
                    <i class="fas fa-user me-2"></i>Sign In
                </a>
                @else
                <form action="{{ route('sign.out') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-gradient-border rounded-circle text-uppercase ms-lg-3 ms-0">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</nav>
