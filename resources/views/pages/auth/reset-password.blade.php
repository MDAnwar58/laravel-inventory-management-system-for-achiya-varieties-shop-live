@extends('layouts/auth-layout')

@section('title', ' Reset Password')

@section('content')
<div class="row vh-100">
    <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
        <div class="d-table-cell align-middle">
            <div class="text-center mt-4">
                <h1 class="h2">Reset Your Password</h1>
                <p class="lead">Enter your new password below</p>
            </div>

            @if(session('alert'))
            <div class="alert alert-{{ session('alert.status') }} alert-dismissible fade show" role="alert">
                {{ session('alert.message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="m-sm-3">
                        <form action="{{ route('password.reset.request') }}" method="POST">
                            @csrf
                            <input type="hidden" id="email" name="email" value="{{ $email }}" />
                            <input type="hidden" name="token" value="{{ $token }}" />

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input class="form-control form-control-lg @error('password') is-invalid @enderror" type="password" id="password" name="password" placeholder="Enter new password" />
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input class="form-control form-control-lg" type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" />
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-lg btn-auth submit-btn">
                                    <span class="fw-semibold">Reset Password</span>
                                    <div id="spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3">
                Remember your password? <a href="{{ route('sign.in') }}">Sign In</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
@if(Session::has('status'))
<x-alert :msg="Session::get('msg')" :status="Session::get('status')" />
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email')
        const passwordInput = document.getElementById('password')
        const passwordConfirmationInput = document.getElementById('password_confirmation')
        const submitBtn = document.querySelector(".submit-btn")
        const spinnerBorder = document.getElementById("spinner-border")
        passwordInput.value = ''
        passwordConfirmationInput.value = ''

        const urlParams = new URLSearchParams(window.location.search)
        const email = urlParams.get('email')
        if (email) {
            emailInput.value = email
        } else {
            emailInput.value = ''
        }

        submitBtn.addEventListener("click", function() {
            submitBtn.classList.add("disabled")
            spinnerBorder.classList.remove("d-none")
        })
    })

</script>
@endpush
