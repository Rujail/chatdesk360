@extends('layouts.base')

@section('title', 'Sign Up - InterSys ChatDesk')

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
                                <h2 class="mb-2 mt-4 fs-7 fw-bolder">{{ __('Sign Up') }}</h2>
                                
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <!-- 🔹 Hidden Fields for Onboarding Flow -->
                                    <input type="hidden" name="package" value="{{ request('package') }}">
                                    <input type="hidden" name="source_id" value="{{ request('source_id') }}">
                                    <input type="hidden" name="source_type" value="{{ request('source_type') }}">
                                    <input type="hidden" name="redirect_uri" value="{{ request('redirect_uri') }}">
                                    {{-- Name --}}
                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ __('Full Name') }}</label>
                                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ __('Email') }}</label>
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="username">
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Subdomain Input --}}
                                    <div class="mb-3">
                                        <label for="subdomain" class="form-label">{{ __('Workspace Subdomain') }}</label>
                                        <div class="input-group">
                                            <input id="subdomain" type="text" class="form-control" name="subdomain" value="{{ old('subdomain') }}" placeholder="your-company" required>
                                            <span class="input-group-text">.{{ config('app.domain') }}</span>
                                        </div>
                                        <small class="text-muted">Only lowercase letters, numbers, and hyphens allowed.</small>
                                        @error('subdomain')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Password --}}
                                    <div class="mb-3">
                                        <label for="password" class="form-label">{{ __('Password') }}</label>
                                        <input id="password" type="password" class="form-control" name="password" required autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">
                                        {{ __('Create Workspace') }}
                                    </button>
                                    
                                    <div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-4 mb-0 fw-medium">Already have an account?</p>
                                        <a class="text-primary fw-medium ms-2" href="{{ route('login') }}">Sign In</a>
                                    </div>
                                </form>
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
                                        Create Your
                                        <br />
                                        Workspace
                                    </h2>
                                    <span class="opacity-75 fs-4 text-white d-block mb-3">
                                        Set up your unique subdomain to get started with ChatDesk.
                                    </span>
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

@push('scripts')
<script>
    $(document).ready(function() {
        let subdomainManuallyEdited = false;

        // 1. AUTO-SUGGESTION: Generate subdomain from the Name field
        $('#name').on('input', function() {
            if (!subdomainManuallyEdited) {
                let nameVal = $(this).val();
                
                // Slugify the name: lowercase, replace spaces with hyphens, remove special chars
                let slug = nameVal.toLowerCase()
                                 .replace(/[^a-z0-9\s\-]/g, '') // remove special characters except spaces/hyphens
                                 .replace(/[\s\-]+/g, '-')      // replace spaces and multiple hyphens with single hyphen
                                 .replace(/^-+|-+$/g, '');      // trim hyphens from start/end
                
                $('#subdomain').val(slug);
            }
        });

        // 2. REAL-TIME FORMATTING: Clean up the subdomain field as user types
        $('#subdomain').on('input', function() {
            // Mark as manually edited so the name field stops overwriting it
            subdomainManuallyEdited = true;

            let val = $(this).val();
            
            // Strip capitals, spaces, and special characters like @, #, !
            let formattedVal = val.toLowerCase()
                                 .replace(/[^a-z0-9\-]/g, '') // remove anything that isn't lowercase letter, number, or hyphen
                                 .replace(/-+/g, '-')         // prevent multiple consecutive hyphens
                                 .replace(/^-+/, '');         // prevent starting with a hyphen (backend validation requires starting with alphanumeric)

            // Update the input value
            $(this).val(formattedVal);
        });

        // Reset manual edit flag if the subdomain field is cleared completely (allows name suggestion again)
        $('#subdomain').on('blur', function() {
            if ($(this).val() === '') {
                subdomainManuallyEdited = false;
            }
        });
    });
</script>
@endpush