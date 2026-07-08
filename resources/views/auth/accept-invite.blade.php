@extends('layouts.base')

@section('title', 'Accept Invite - InterSys ChatDesk')

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
                                <h2 class="mb-2 mt-4 fs-7 fw-bolder">{{ __('Set Your Password') }}</h2>
                                <form method="POST" action="/invite/complete">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $invite->token }}">
                                    <div class="mb-4">
                                        <label for="password" class="form-label">{{ __('Password') }}</label>
                                        <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">
                                        {{ __('Create Account') }}
                                    </button>
                                    {{-- <div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-4 mb-0 fw-medium">New to ChatDesk?</p>
                                        <a class="text-primary fw-medium ms-2" href="../main/authentication-register.html" >Create an account</a>
                                    </div> --}}
                                </form>
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