<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    {{-- <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5" />
    <meta name="author" content="AdminKit" />
    <meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web" /> --}}

    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- <link rel="canonical" href="https://demo-basic.adminkit.io/" /> --}}

    <title>
        আছিয়া ভ‍্যারাইটিস শপ @yield('title')
    </title>

    <style>
        .auth-logo {
            font-weight: 800;
            font-size: 1.5rem;
            color: #5E63D0;
        }

        .form-control.focus-ring-none:focus {
            box-shadow: none !important;
            outline: none !important;
            /* optional: stop border color change */
        }

    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/progress.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <link href="{{ asset('assets/css/sidebar-offcanvas.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    @stack('style')
</head>

<body>
    <x-admin.preloader />
    <div class="wrapper">
        <x-admin.sidebar />

        <div class="main" style="z-index: 10;">
            <x-admin.navbar />

            <main class="content">
                <div class="container-fluid p-0">
                    @yield('content')
                    <div id="voice-list" class="d-none">
                    </div>
                </div>
            </main>

            <x-admin.footer />
        </div>
    </div>
    <x-admin.low-stocks />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/react.js') }}"></script>
    <script src="{{ asset('assets/js/preloader.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/sidebar-offcanvas.js') }}"></script>
    {{-- <script src="{{ asset('service-worker.js') }}"></script> --}}
    <x-admin.low-stock-scripts :settings="$settings" />
    @stack('script')
</body>

</html>
