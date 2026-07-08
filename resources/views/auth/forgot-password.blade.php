@extends('layouts.base')

@section('title', 'Sign In - InterSys ChatDesk')

@section('content')
    <div id="main-wrapper">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 w-100">
            <div class="position-relative z-index-5">
                <div class="row gx-0">
                    <div class="col-lg-6 col-xl-5 col-xxl-4">
                        <div class="min-vh-100 bg-body row justify-content-center align-items-center p-5">
                            <div class="col-12 auth-card">
                                <a href="{{ route('home') }}" class="text-nowrap logo-img d-block w-100">
                                    <img src="{{ asset('assets/images/favicon-white.png') }}" class="dark-logo" alt="Logo-Dark" />
                                </a>
                                <h2 class="mb-2 mt-4 fs-7 fw-bolder">{{ __('Forgot Your Password?') }}</h2>
                                <p class="mb-9">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>

                                <form method="POST" action="{{ route('password.email') }}">
                                @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ __('Email') }}</label>
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button class="btn btn-primary w-100 py-8 mb-3">
                                        {{ __('Email Password Reset Link') }}
                                    </button>
                                    <a href="{{ route('login') }}" class="btn bg-primary-subtle text-primary w-100 py-8"
                                        >Back to Login</a
                                    >
                                </form>
                                <!-- Session status (simple alert) -->
                                @if (session('status'))
                                    <div class="alert alert-success mt-9">
                                        {{ session('status') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div
                        class="col-lg-6 col-xl-7 col-xxl-8 position-relative overflow-hidden bg-dark-light d-none d-lg-block">
                        <div class="circle-top"></div>
                        <div>
                            <img src="{{ asset('assets/images/logo-icon.svg') }}" class="circle-bottom" alt="Logo-Dark" />
                        </div>
                        <div class="d-lg-flex align-items-center z-index-5 position-relative h-n80">
                            <div class="row justify-content-center w-100">
                                <div class="col-lg-6">
                                    <h2 class="text-white fs-10 mb-3 lh-sm">
                                        Welcome to
                                        <br />
                                        ChatDesk
                                    </h2>
                                    <span class="opacity-75 fs-4 text-white d-block mb-3"
                                        >ChatDesk helps developers to build organized and well
                                        <br />
                                        coded dashboards full of beautiful and rich modules.
                                    </span>
                                    <a href="/" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection