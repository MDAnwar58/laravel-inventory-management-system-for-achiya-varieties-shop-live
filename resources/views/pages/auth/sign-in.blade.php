@extends('layouts/auth-layout')

@section('title', ' Sign In')

@section('content')
<div class="row vh-100">
    <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
        <div class="d-table-cell align-middle">
            <div class="text-center mt-4">
                <x-auth.logo />
                {{-- <h1 class="h2">Welcome back!</h1> --}}
                <p class="lead">Sign in to your account to access your dashboard.</p>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="m-sm-3">
                        <form action="" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" />
                                <x-error fieldName="email" />
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Password</label>
                                <input class="form-control form-control-lg" type="password" name="password" placeholder="Enter your password" />
                                <x-error fieldName="password" />
                                @if(Session::has('error'))
                                    <strong class="badge text-danger fs-6">
                                        {{ Session::get('error') }}
                                    </strong>
                                @endif


                                <div class="text-end">
                                    <a href="{{ route('forget.password') }}" class="fs-5">Forgot password?</a>
                                </div>
                            </div>
                            <div>
                                <div class="form-check align-items-center">
                                    <input id="customControlInline" type="checkbox" class="form-check-input" value="false" name="remember" />
                                    <label class="form-check-label text-small" for="customControlInline">Remember me</label>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-lg btn-auth submit-btn">
                                    <span class="fw-semibold text-uppercase">Sign in</span>
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
                Don't have an account? <a href="{{ route('sign.up') }}">Sign up</a>
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
