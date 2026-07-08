@extends('layouts.app')

@section('title', 'Post Chat Form')


@section('content')
<div class="body-wrapper mb-0 pg-postchat">
    <div class="container-fluid mw-100">

        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row equal-height">
                    <div class="col-md-7 ">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="mb-0">Post-chat form</h4>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input success" type="checkbox"
                                        id="postchat-enabled-toggle"
                                        {{ $postChatForm && $postChatForm->enabled ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2" for="postchat-enabled-toggle">
                                        <span id="postchat-enabled-label"
                                            class="badge {{ $postChatForm && $postChatForm->enabled ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $postChatForm && $postChatForm->enabled ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="lef-panel left-panel">
                            <!-- START: Post-chat Form Builder (insert this inside <div class="lef-panel">) -->
                            <div id="postchat-builder-root" class="postchat-builder-root">
                                <div class="builder-header">
                                    <div class="builder-controls">
                                        <button class="add-field-btn btn btn-outline-primary" data-type="thankyou">Thank you message</button>
                                        <button class="add-field-btn btn btn-outline-primary" data-type="question">Question</button>
                                        <button class="add-field-btn btn btn-outline-primary" data-type="message">Message</button>
                                        <button class="add-field-btn btn btn-outline-primary" data-type="choice">Choice list</button>
                                        <button class="add-field-btn btn btn-outline-primary" data-type="dropdown">Dropdown</button>
                                        <button class="add-field-btn btn btn-outline-primary" data-type="multiple">Multiple choice</button>
                                        <button class="add-field-btn btn btn-outline-primary" data-type="rating">Chat rating</button>
                                    </div>
                                </div>

                                <div id="form-builder" class="form-builder" aria-label="Form builder area">
                                    <!-- Dynamic field items will be appended here -->
                                </div>

                                {{-- <div class="builder-footer">
                                    <!-- <button id="load-example" class="small-btn">Load Example</button>
                                    <button id="export-json" class="small-btn">Export JSON</button> -->
                                    <span class="note">Drag to reorder fields · Edit labels/options on the left · Preview updates live</span>
                                </div> --}}
                                {{-- Replace the existing builder-footer with this --}}
                                <div class="builder-footer d-flex justify-content-between align-items-center">
                                    <span class="note">Drag to reorder fields · Edit labels/options on the left · Preview updates live</span>
                                    <button id="save-postchat-form" class="btn btn-primary btn-sm">
                                        <i class="ti ti-device-floppy me-1"></i> Save Form
                                    </button>
                                </div>
                            </div>
                            <!-- END: Post-chat Form Builder -->

                        </div>
                    </div>
                    <div class="col-md-5 preview-col">
                        <div class="preview-card">
                            <h4 class="mb-3">Preview</h4>
                            <div class="preview-simulation position-relative" style="flex:1;">
                                <!-- PREVIEW TARGET for Post-chat form (add inside .preview-simulation) -->

                                <div id="chat-widget-container">
                                    <div id="chat-bubble">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>

                                    <div id="chat-popup" class="theme-root">

                                        <div id="chat-home-screen" class="hidden">
                                            <div class="home-header">Welcome!</div>
                                            <div class="home-title">Text us</div>
                                            <div class="admin-card">
                                                <div class="admin-badge">
                                                    <div class="avatar-circle">A <span class="status-dot"></span></div>
                                                    <span class="admin-text">Admin</span>
                                                </div>
                                                <div class="admin-info">Hello. How may I help you?</div>
                                                <button id="start-chat-btn" class="start-chat-btn">
                                                    Back to chat
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="12" y1="19" x2="12" y2="5"></line>
                                                        <polyline points="5 12 12 5 19 12"></polyline>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div id="chat-conversation" class="">
                                            <div id="chat-header">
                                                <div class="header-left">
                                                    <button class="icon-btn" id="back-to-home-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <line x1="19" y1="12" x2="5" y2="12"></line>
                                                            <polyline points="12 19 5 12 12 5"></polyline>
                                                        </svg>
                                                    </button>
                                                    <button class="icon-btn" id="options-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="1"></circle>
                                                            <circle cx="19" cy="12" r="1"></circle>
                                                            <circle cx="5" cy="12" r="1"></circle>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="admin-badge">
                                                    <div class="avatar-circle">A <span class="status-dot"></span></div>
                                                    <span class="admin-text">Admin</span>
                                                </div>

                                                <div class="header-right">
                                                    <button class="icon-btn" id="minimize-chat">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                                        </svg>
                                                    </button>
                                                    <button class="icon-btn" id="close-popup">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div id="options-menu" class="hidden">
                                                <div class="options-item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                        <polyline points="22,6 12,13 2,6"></polyline>
                                                    </svg>
                                                    <span>Send transcript</span>
                                                </div>
                                                <div class="options-item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                                                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                                                    </svg>
                                                    <span>Sounds</span>
                                                    <label class="switch">
                                                        <input type="checkbox" checked>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div id="chat-messages">
                                                <div id="postchat-preview" style="position:relative; margin-top:8px;"></div>
                                            </div>

                                            <div id="file-upload-preview" class="hidden">
                                                <div class="preview-header">
                                                    <span class="preview-count">0 of 2 uploaded</span>
                                                    <svg class="chevron-down" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="6 9 12 15 18 9"></polyline>
                                                    </svg>
                                                </div>
                                                <div class="preview-list">
                                                </div>
                                            </div>

                                            <div id="attachment-menu" class="hidden">
                                                <button class="menu-item" id="send-file-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                        <polyline points="13 2 13 9 20 9"></polyline>
                                                        <line x1="12" y1="11" x2="12" y2="17"></line>
                                                        <line x1="9" y1="14" x2="15" y2="14"></line>
                                                    </svg>
                                                    <span>Send a file</span>
                                                </button>
                                                <input type="file" id="hidden-file-input" style="display: none;" multiple>
                                                <button class="menu-item" id="add-screenshot-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                        <polyline points="7 10 12 15 17 10"></polyline>
                                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                                    </svg>
                                                    <span>Add screenshot</span>
                                                </button>
                                            </div>

                                            <div id="emoji-menu" class="hidden">
                                                <div class="emoji-grid">
                                                    <span>🙂</span><span>😀</span><span>😂</span><span>😉</span><span>😍</span>
                                                    <span>😐</span><span>😕</span><span>😓</span><span>😢</span><span>😭</span>
                                                    <span>🎉</span><span>❤️</span><span>👌</span><span>👍</span><span>🙏</span>
                                                </div>
                                            </div>

                                            <div id="chat-input-container">
                                                <div class="input-pill">
                                                    <button id="attach-btn" class="input-action-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                                        </svg>
                                                    </button>

                                                    <input type="text" id="chat-input" placeholder="Write a message...">

                                                    <button id="emoji-btn" class="input-action-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                                                            <line x1="9" y1="9" x2="9.01" y2="9"></line>
                                                            <line x1="15" y1="9" x2="15.01" y2="9"></line>
                                                        </svg>
                                                    </button>

                                                    <button id="chat-submit" class="send-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <line x1="12" y1="19" x2="12" y2="5"></line>
                                                            <polyline points="5 12 12 5 19 12"></polyline>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="chat-footer">
                                            <button id="home-tab-btn" class="tab-btn active-tab">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                                </svg>
                                                <span>Home</span>
                                            </button>
                                            <button id="chat-tab-btn" class="tab-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                </svg>
                                                <span>Chat</span>
                                            </button>
                                        </div>

                                    </div>
                                    <div id="image-preview-modal" class="hidden">
                                        <div id="modal-content">
                                            <button id="close-modal-btn">&times;</button>
                                            <img id="modal-image" src="" alt="Full size preview" />
                                        </div>
                                    </div>
                                    <div class="eyecatcher">
                                        <img src="" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
    <script>
        window.__widgetSiteId = "{{ auth()->user()->site_id }}";
        window.__postChatFormConfig = @json($postChatForm ? $postChatForm->form_config : []);
        window.__postChatFormEnabled = {{ $postChatForm && $postChatForm->enabled ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('assets/js/chatwidget.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/customizer.js') }}?v={{ time() }}"></script>
    <script>
        (function() {
            const toggle = document.getElementById('postchat-enabled-toggle');
            const label  = document.getElementById('postchat-enabled-label');
            if (!toggle || !label) return;

            toggle.addEventListener('change', function() {
                if (this.checked) {
                    label.textContent   = 'Enabled';
                    label.className     = 'badge bg-success';
                } else {
                    label.textContent   = 'Disabled';
                    label.className     = 'badge bg-secondary';
                }
            });
        })();

        document.getElementById('save-postchat-form')?.addEventListener('click', function() {
            const enabled = document.getElementById('postchat-enabled-toggle')?.checked ? true : false;

            // ★ FIX: Use .field-wrapper (NOT .field-item) and correct input selectors
            const fields = [];
            document.querySelectorAll('#form-builder .field-wrapper').forEach(wrapper => {
                const type = wrapper.dataset.type;
                if (!type) return;

                const field = { type: type };
                const body = wrapper.querySelector('.form-field');

                if (type === 'thankyou') {
                    // ★ FIX: textarea, not input
                    const textEl = body?.querySelector('textarea[data-field-prop="text"]');
                    if (textEl) field.text = textEl.value.trim();
                } else {
                    // ★ FIX: use data-field-prop="label" (NOT .field-label-input)
                    const labelEl = body?.querySelector('input[data-field-prop="label"]');
                    if (labelEl) field.label = labelEl.value.trim();
                }

                // ★ Collect options for choice/dropdown/multiple
                if (type === 'choice' || type === 'dropdown' || type === 'multiple') {
                    const optInputs = body?.querySelectorAll('.option-input') || [];
                    const opts = Array.from(optInputs).map(o => o.value.trim()).filter(Boolean);
                    if (opts.length) field.options = opts;
                }

                fields.push(field);
            });

            const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const btn  = document.getElementById('save-postchat-form');
            const orig = btn?.innerHTML;

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="ti ti-loader-2 me-1 spin"></i> Saving…';
            }

            fetch('{{ route("settings.post-chat-form.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    enabled: enabled,
                    form_config: fields,
                }),
            })
            .then(r => {
                if (!r.ok) {
                    return r.json().then(errData => {
                        let msg = 'Save failed.';
                        if (errData.errors) {
                            const firstErr = Object.values(errData.errors)[0];
                            if (firstErr) msg = firstErr.join(', ');
                        } else if (errData.message) {
                            msg = errData.message;
                        }
                        throw new Error(msg);
                    });
                }
                return r.json();
            })
            .then(data => {
                // Show toast
                let el = document.getElementById('widget-toast');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'widget-toast';
                    Object.assign(el.style, {
                        position:'fixed',top:'20px',right:'20px',
                        padding:'12px 24px',borderRadius:'8px',
                        color:'#fff',fontSize:'14px',fontWeight:'500',
                        zIndex:99999,transition:'all .3s ease',
                        transform:'translateY(-16px)',opacity:0,
                    });
                    document.body.appendChild(el);
                }
                el.textContent = data.message || 'Saved!';
                el.style.background = data.success ? '#10b981' : '#ef4444';
                el.style.transform  = 'translateY(0)';
                el.style.opacity     = '1';
                clearTimeout(el._t);
                el._t = setTimeout(() => {
                    el.style.transform = 'translateY(-16px)';
                    el.style.opacity   = '0';
                }, 3200);
            })
            .catch(err => {
                alert('Save failed: ' + err.message);
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                }
            });
        });
    </script>
@endpush