<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="আছিয়া ভ্যারাইটিস শপ ইনভেন্টরি সিস্টেমে লগইন করুন পণ্য ম্যানেজ, স্টক ট্র্যাক এবং দোকানকে সুশৃঙ্খল রাখতে।" />
    <meta name="author" content="আছিয়া ভ‍্যারাইটিস শপ" />
    <meta name="keywords" content="আছিয়া ভ‍্যারাইটিস শপ Login, আছিয়া ভ‍্যারাইটিস শপ Sing In, আছিয়া ভ‍্যারাইটিস শপ Sign Up, আছিয়া ভ‍্যারাইটিস শপ, Achiya Varieties Shop, Achiya Varieties Shop Login, Achiya Varieties Shop Sign In, Achiya Varieties Shop Sign Up" />

    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="canonical" href="https://demo-basic.adminkit.io/pages-sign-in.html" />

    <title>
        আছিয়া ভ‍্যারাইটিস শপ |@yield('title')
    </title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet" />

    <style>
        .auth-logo {
            font-weight: 800;
            font-size: 1.5rem;
            color: #5E63D0;
        }

        .btn-auth {
            background-color: #5E63D0;
            color: #ffffff;
            transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
        }

        .btn-auth:hover {
            background-color: rgb(75, 82, 215);
            color: #ffffff;
        }

        .btn-auth:focus {
            background-color: rgb(75, 82, 215);
            color: #ffffff;
        }

    </style>
    @stack('style')
</head>

<body>
    <main class="d-flex w-100">
        <div class="container d-flex flex-column">
            @yield('content')
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- <script src="{{ asset('assets/js/app.js') }}"></script> -->
    <script>
        function processNotify(msg, status, position) {
            if (!position) position = 'top-right';
            if (!status) status = 'success';

            toastr.options = {
                closeButton: true
                , debug: false
                , newestOnTop: true
                , progressBar: true
                , positionClass: `toast-${position}`
                , preventDuplicates: false
                , onclick: null
                , showDuration: '300'
                , hideDuration: '1000'
                , timeOut: '5000'
                , extendedTimeOut: '1000'
                , showEasing: 'swing'
                , hideEasing: 'linear'
                , showMethod: 'fadeIn'
                , hideMethod: 'fadeOut'
            };

            toastr[status](msg);
        }

    </script>
    @stack('script')
</body>

</html>
