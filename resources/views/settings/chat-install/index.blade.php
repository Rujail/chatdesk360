@extends('layouts.app')

@section('title', 'Install Chat')


@section('content')
<div class="body-wrapper mb-0 pg-chatCustomization">
    <div class="container-fluid mw-100">

        <div class="widget-content searchable-container list h-100vh">

            <div class="card card-body install-options-card">
                <div class="alert alert-info domain-security-alert" role="alert">
                    <i data-feather="info" class="feather-sm fill-info  alert-icon"></i>
                    Add new websites to your <strong><a href="{{ route('settings.trusted-domains.index') }}" class="text-info alert-link">trusted domains</a></strong> list to ensure they can display the widget.
                </div>

                <div class="widget-header">
                    <h4 class="header-title">Install Chat</h4>
                    
                    <p class="status-indicator">
                        Install website widget 
                        
                        @if($isInstalled)
                            <!-- ★ Show Green "installed" if trusted domain exists -->
                            <span class="badge rounded-pill bg-success-subtle text-success status-badge">installed</span>
                        @else
                            <!-- ★ Show Red/Orange "not installed" if no trusted domain exists -->
                            <span class="badge rounded-pill bg-danger-subtle text-white status-badge">not installed</span>
                        @endif
                    </p>
                    
                    <p class="header-description">
                        To see Chat on your website, you'll need to add a bit of code or configure an integration. 
                        
                        @if(!$isInstalled)
                            <strong><a href="{{ route('settings.trusted-domains.index') }}" class="text-danger">Add a trusted domain first!</a></strong>
                        @else
                            <a href="#" class="link-secondary learn-more-link">Learn more</a>
                        @endif
                    </p>
                </div>

                <div class="row install-content-row">
                    <div class="col-md-7 install-instructions-col">
                        <section class="manual-install-module">
                            <header class="d-flex align-items-start module-header">
                                <div class="module-icon-container ">
                                    <iconify-icon icon="solar:chat-square-check-broken" class="module-icon"></iconify-icon>
                                </div>
                                <div class="module-text-content">
                                    <h5 class="module-title">Install chat code manually</h5>
                                    <p class="module-step-description">1. Copy and paste this code before the <code>&lt;/body&gt;</code> tag on every page of your website.</p>
                                </div>
                            </header>

                            <div class="code-box-wrapper position-relative code-snippet-container">
                                <!-- ★ DYNAMIC SNIPPET GENERATED FROM USER'S SITE ID -->
                                <textarea id="widget-code" class="form-control code-snippet-area" rows="8" readonly>&lt;script&gt;
    window.__cd=window.__cd||{};
    window.__cd.site_id="{{ $siteId }}";
    (function(d,w){
        var s=d.createElement('script');
        s.async=!0;
        s.type='text/javascript';
        s.src='{{ $baseUrl }}/livechat/loader.js';
        d.head.appendChild(s);
    }(document,window));
&lt;/script&gt;</textarea>
                                <button id="copy-widget-code" class="btn btn-sm btn-light-secondary copy-code-btn">Copy code</button>
                            </div>

                            <footer class="module-footer">
                                <h6 class="footer-heading">Need help?</h6>
                                <div class="d-flex help-action-group">
                                    <button class="btn btn-sm btn-outline-light d-flex align-items-center help-action-btn">
                                        <iconify-icon icon="solar:add-square-broken"></iconify-icon> Invite your developer
                                    </button>
                                    <button class="btn btn-sm btn-outline-light d-flex align-items-center help-action-btn">
                                        <iconify-icon icon="solar:code-circle-broken"></iconify-icon> Check install guide
                                    </button>
                                </div>
                            </footer>
                        </section>

                        <h6 class="text-muted text-uppercase integrations-header">Other ways to install</h6>

                        <div class="accordion integrations-accordion" id="gtmInstall">
                            <div class="accordion-item integration-item">
                                <h2 class="accordion-header integration-header" id="gtmHeading">
                                    <button class="accordion-button collapsed d-flex align-items-center integration-button" type="button" data-bs-toggle="collapse" data-bs-target="#gtmCollapse" aria-expanded="false" aria-controls="gtmCollapse">
                                        <i data-feather="link" class="feather-sm text-primary  integration-icon"></i>
                                        Connect with Google Tag Manager
                                    </button>
                                </h2>
                                <div id="gtmCollapse" class="accordion-collapse collapse integration-collapse" aria-labelledby="gtmHeading" data-bs-parent="#gtmInstall">
                                    <div class="accordion-body integration-body">
                                        <button class="btn btn-sm btn-primary install-action-btn">Install</button>
                                        <p class="integration-description">Connect your GTM account to quickly set up chat widget on your website.</p>
                                        <h6 class="footer-heading">Need help?</h6>
                                        <div class="d-flex help-action-group">
                                            <button class="btn btn-sm btn-outline-light d-flex align-items-center help-action-btn">
                                                <iconify-icon icon="solar:add-square-broken"></iconify-icon> Invite your developer
                                            </button>
                                            <button class="btn btn-sm btn-outline-light d-flex align-items-center help-action-btn">
                                                <iconify-icon icon="solar:code-circle-broken"></iconify-icon> Check install guide
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion integrations-accordion" id="wpInstall">
                            <div class="accordion-item integration-item">
                                <h2 class="accordion-header integration-header" id="wpHeading">
                                    <button class="accordion-button collapsed d-flex align-items-center integration-button" type="button" data-bs-toggle="collapse" data-bs-target="#wpCollapse" aria-expanded="false" aria-controls="wpCollapse">
                                        <i data-feather="link" class="feather-sm text-primary  integration-icon"></i>
                                        Connect with WordPress
                                    </button>
                                </h2>
                                <div id="wpCollapse" class="accordion-collapse collapse integration-collapse" aria-labelledby="wpHeading" data-bs-parent="#wpInstall">
                                    <div class="accordion-body integration-body">
                                        <button class="btn btn-sm btn-primary ms-auto  connect-action-btn">Connect</button>
                                        <p class="integration-description">Add chat widget to your WordPress site.</p>
                                        <h6 class="footer-heading">Need help?</h6>
                                        <div class="d-flex help-action-group">
                                            <button class="btn btn-sm btn-outline-light d-flex align-items-center help-action-btn">
                                                <iconify-icon icon="solar:add-square-broken"></iconify-icon> Invite your developer
                                            </button>
                                            <button class="btn btn-sm btn-outline-light d-flex align-items-center help-action-btn">
                                                <iconify-icon icon="solar:code-circle-broken"></iconify-icon> Check install guide
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5 preview-col">
                        <!-- Leave empty or add a preview mockup if you have one -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const copyBtn = document.getElementById('copy-widget-code');
            const codeBox = document.getElementById('widget-code');

            if (!copyBtn || !codeBox) return;

            copyBtn.addEventListener('click', () => {
                // Select the text area content
                codeBox.select();
                codeBox.setSelectionRange(0, 99999); // For mobile devices

                // Copy to clipboard
                navigator.clipboard.writeText(codeBox.value)
                    .then(() => {
                        copyBtn.textContent = 'Copied!';
                        copyBtn.classList.add('btn-success');
                        copyBtn.classList.remove('btn-light-secondary');
                        
                        setTimeout(() => {
                            copyBtn.textContent = 'Copy code';
                            copyBtn.classList.remove('btn-success');
                            copyBtn.classList.add('btn-light-secondary');
                        }, 2000);
                    })
                    .catch(err => {
                        // Fallback for older browsers
                        document.execCommand("copy");
                        copyBtn.textContent = 'Copied!';
                        setTimeout(() => {
                            copyBtn.textContent = 'Copy code';
                        }, 2000);
                    });
            });
        });
    </script>
@endpush