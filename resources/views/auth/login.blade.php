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
                                    <img src="{{ asset('front-assets/images/logo-icon.svg') }}" class="dark-logo" alt="Logo-Dark" />
                                </a>

                                {{-- MULTI-TENANT WORKSPACE INDICATOR --}}
                                @if(isset($tenant) && $tenant)
                                    <div class="alert alert-info mt-3 text-center py-2 px-3" style="font-size: 0.85rem;">
                                        Logging into workspace: <strong>{{ $tenant->domain_name }}</strong>
                                    </div>
                                @endif

                                {{-- MAIN DOMAIN MESSAGE (NO LOGIN FORM) --}}
                                @if(!isset($tenant) || !$tenant)
                                    <h2 class="mb-2 mt-4 fs-7 fw-bolder">Welcome to ChatDesk</h2>
                                    <p class="text-muted mb-4">Please log in through your specific workspace URL (e.g., yourworkspace.abc.com).</p>
                                    
                                    <a href="{{ route('workspace.request') }}" class="btn btn-outline-primary w-100 py-8 mb-4 rounded-2">
                                        Forgot your workspace?
                                    </a>
                                @else
                                    {{-- NORMAL LOGIN FORM FOR SUBDOMAINS --}}
                                    <h2 class="mb-2 mt-4 fs-7 fw-bolder">{{ __('Sign In') }}</h2>
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="email" class="form-label">{{ __('Email') }}</label>
                                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                                            @error('email')
                                                <div class="invalid-feedback d-block">{!! $message !!}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="password" class="form-label">{{ __('Password') }}</label>
                                            <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input primary" type="checkbox" name="remember" id="remember_me"/>
                                                <label for="remember_me" class="form-check-label">{{ __('Remember me') }}</label>
                                            </div>
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="text-primary fw-medium">
                                                    {{ __('Forgot Password ?') }}
                                                </a>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">
                                            {{ __('Sign In') }}
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-xl-7 col-xxl-8 position-relative overflow-hidden bg-dark-light d-none d-lg-block">
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
                                    <span class="opacity-75 fs-4 text-white d-block mb-3">
                                        ChatDesk helps developers to build organized and well
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