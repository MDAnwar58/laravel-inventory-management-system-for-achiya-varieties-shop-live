@extends('layouts/auth-layout')

@section('title', ' Sign Up')

@section('content')
<div class="row vh-100">
    <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
        <div class="d-table-cell align-middle">
            <div class="text-center mt-4 pb-3">
                <x-auth.logo />
                {{-- <p class="lead">Start creating the best possible user experience for you customers.</p> --}}
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="m-sm-3">
                        <form action="" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Full name</label>
                                <input class="form-control form-control-lg" type="text" name="name" placeholder="Enter your name" />
                                <x-error fieldName="name" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" />
                                <x-error fieldName="email" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text h-100">+880</span>
                                    </div>
                                    <input class="form-control form-control-lg" type="text" id="phone_number" name="phone_number" placeholder="Enter your phone number" />
                                </div>
                                <x-error fieldName="phone_number" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input class="form-control form-control-lg" type="password" name="password" placeholder="Enter password" />
                                <x-error fieldName="password" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input class="form-control form-control-lg" type="password" name="password_confirmation" placeholder="Enter confirm password" />
                                <x-error fieldName="password_confirmation" />
                            </div>
                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-lg btn-auth submit-btn">
                                    <span class="fw-semibold text-uppercase">Sign up</span>
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
                Already have account? <a href="{{ route('sign.in') }}">Sing In</a>
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
        const phoneNumberInput = document.getElementById('phone_number')
        const submitBtn = document.querySelector(".submit-btn")
        const spinnerBorder = document.getElementById("spinner-border")

        phoneNumberInput.addEventListener('input', function(event) {
            // Replace everything that's not a digit
            event.currentTarget.value = event.currentTarget.value.replace(/\D/g, '')
        })
        submitBtn.addEventListener("click", function() {
            submitBtn.classList.add("disabled")
            spinnerBorder.classList.remove("d-none")
        })
    })

</script>
@endpush
