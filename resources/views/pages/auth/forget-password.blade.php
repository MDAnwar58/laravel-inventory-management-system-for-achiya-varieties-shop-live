@extends('layouts/auth-layout')

@section('title', ' Forget Password')

@section('content')
<div class="row vh-100">
    <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
        <div class="d-table-cell align-middle">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1 class="h2">Please enter your email</h1>
                        <p class="lead">We will send you a link to reset your password.</p>
                        @if (Session::get('status') === "success")
                        <p class="lead text-success">
                            {{ Session::get('msg') }} <b><a href="https://mail.google.com/mail/u/0/#inbox" class="text-success" target="_blank">{{ Session::get('user_inbox_email') }}</a></b>
                        </p>
                        @endif
                    </div>
                    <div class="m-sm-3">
                        <form action="{{ route('forget.password.send') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control form-control-lg" type="email" name="email" />
                                <x-error fieldName="email" />
                            </div>
                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-lg btn-auth submit-btn">
                                    <span class="fw-semibold text-uppercase">Send</span>
                                    <div id="spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="text-center mb-3">
                Back to? <a href="{{ route('sign.in') }}">Sign In</a>
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
    document.addEventListener("DOMContentLoaded", function() {
        const submitBtn = document.querySelector(".submit-btn")
        const spinnerBorder = document.getElementById("spinner-border")
        submitBtn.addEventListener("click", function() {
            submitBtn.classList.add("disabled")
            spinnerBorder.classList.remove("d-none")
        })
    })

</script>
@endpush
