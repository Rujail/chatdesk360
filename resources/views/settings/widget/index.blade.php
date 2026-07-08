@extends('layouts.app')

@section('title', 'Widget Customization')

@push('styles')
<style>
    
</style>
@endpush
@section('content')
<div class="body-wrapper mb-0">
    <div class="container-fluid mw-100 pb-0">

        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row equal-height">
                    <div class="col-md-7 ">
                        <div class="d-flex gap-2 mb-4 align-items-center">
                                <h4 class="flex-fill m-0">Widget Customization</h4>
                                <button id="save-widget-settings" class="btn btn-primary ">
                                    <i class="ti ti-device-floppy me-1"></i> Save Settings
                                </button>
                                <button id="reset-widget-settings" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh me-1"></i> Reset
                                </button>
                            </div>
                        <div class="left-panel">
                            <div class="accordion" id="chatCustomize">
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#chatApperance" aria-expanded="true" aria-controls="chatApperance">
                                            Appearance
                                        </button>
                                    </h2>
                                    <div id="chatApperance" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#chatCustomize">
                                        <div class="accordion-body">
                                            <h5>Minimized window</h5>
                                            <div class="widgetOption">
                                                <div class="cuscheckboxlist">
                                                    <div class="cuscheckbox">
                                                        <input type="radio" class="cusradio" name="bar_bubble" id="bar">
                                                        <label class="" for="bar">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="32" height="32">
                                                                <path fill="currentcolor" d="M20.022 11.02a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M20.022 13.026q.243 0 .478-.033V15h.5a1 1 0 1 1 0 2H3a1 1 0 1 1 0-2h.5v-3.724a2 2 0 0 1 2-2h11.025q-.008.12-.008.245a3.505 3.505 0 0 0 3.505 3.505"></path>
                                                            </svg>
                                                        </label>
                                                        Bar
                                                    </div>
                                                    <div class="cuscheckbox">
                                                        <input type="radio" class="cusradio" name="bar_bubble" id="bubble" checked>
                                                        <label class="" for="bubble">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="32" height="32">
                                                                <path fill="currentcolor" d="M19.373 8.89Q18.953 9 18.5 9a3.5 3.5 0 0 1-3.39-4.373 8 8 0 1 0 4.263 4.263M20 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"></path>
                                                            </svg>
                                                        </label>
                                                        Bubble
                                                    </div>
                                                </div>
                                            </div>
                                            <h5>Theme and colors</h5>
                                            <div class="widgetOption">
                                                <div class="cuscheckboxlist">
                                                    <div class="cuscheckbox">
                                                        <input type="radio" class="cusradio" name="widgetTheme" id="light" value="light" checked>
                                                        <label class="" for="light">
                                                            <i class="ti ti-brightness-up"></i>
                                                        </label>
                                                        Light
                                                    </div>
                                                    <div class="cuscheckbox">
                                                        <input type="radio" class="cusradio" name="widgetTheme" id="dark" value="dark">
                                                        <label class="" for="dark">
                                                            <i class="ti ti-moon"></i>
                                                        </label>
                                                        Dark
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widgetOption">
                                                <div class="themeColorsec">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="themeColorcheck" id="themeColorcheck-toggle" value="themeColor" checked>
                                                        <label class="form-label" for="themeColorcheck-toggle">Theme colors</label>
                                                    </div>
                                                    <div class="d-flex align-items-center picker-row">
                                                        <!-- preset swatches -->
                                                        <div class="color-swatch" data-color="#111827" style="background:#111827"></div>
                                                        <div class="color-swatch" data-color="#7c3aed" style="background:#7c3aed"></div>
                                                        <div class="color-swatch selected" data-color="#2366ff" style="background:#2366ff"></div>
                                                        <div class="color-swatch" data-color="#06b6d4" style="background:#06b6d4"></div>
                                                        <div class="color-swatch" data-color="#10b981" style="background:#10b981"></div>
                                                        <div class="color-swatch" data-color="#f97316" style="background:#f97316"></div>
                                                        <div class="color-swatch" data-color="#ef4444" style="background:#ef4444"></div>
                                                        <div class="color-swatch" data-color="#a21caf" style="background:#a21caf"></div>
                                                        <div class="color-swatch" data-color="" title="Custom">
                                                            <input id="theme-color" type="color" style="width:30px;height:30px;border:0;padding:0;background:transparent;cursor:pointer;border-radius:50%;">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-2 more-color">
                                                    <div class="form-check">
                                                        <input id="more-colors-toggle" type="radio" name="themeColorcheck" value="moreColor" class="form-check-input">
                                                        <label class="form-label" for="more-colors-toggle">More color settings</label>
                                                        <div class="small-muted mt-2">Make the chat widget more unique by choosing a custom color scheme.</div>
                                                    </div>
                                                    <div id="more-colors" class="mt-2 d-none">
                                                        <div class="mb-2">
                                                            <label class="form-label small">Widget background</label>
                                                            <input id="widget-bg-color" type="color" class="form-control form-control-color">
                                                        </div>
                                                        <div class="mb-1">
                                                            <label class="form-label small">Text color</label>
                                                            <input id="widget-text-color" type="color" class="form-control form-control-color">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#chatPosotion" aria-expanded="false" aria-controls="chatPosotion">
                                            Position
                                        </button>
                                    </h2>
                                    <div id="chatPosotion" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#chatCustomize">
                                        <div class="accordion-body">
                                            <h5>Minimized window</h5>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label">Align to:</label>
                                                    <div class="filters form-group">
                                                        <select class="custom-selectoption" name="widgetPosition" placeholder="Select Option">
                                                            <option value="right">Right</option>
                                                            <option value="left">Left</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Side spacing (px)</label>
                                                    <input id="side-spacing" type="number" class="form-control " value="24">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Bottom spacing (px)</label>
                                                    <input id="bottom-spacing" type="number" class="form-control" value="24">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="tweaksHeading">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#chatTweaks" aria-expanded="false" aria-controls="chatTweaks">
                                            Additional tweaks
                                        </button>
                                    </h2>
                                    <div id="chatTweaks" class="accordion-collapse collapse" aria-labelledby="tweaksHeading" data-bs-parent="#chatCustomize">
                                        <div class="accordion-body">
                                            <div class="widgetOption">
                                                <ul class="twakslist">
                                                    <li>
                                                        <span class="twakLabel">Show Logo</span>
                                                        <label class="file-upload">Upload your logo
                                                            <input type="file" hidden class="form-control-file" id="logo-file-input" accept="image/*" />
                                                        </label>

                                                        <div class="form-check form-switch custom-switch-lg ">
                                                            <input class="form-check-input success" type="checkbox" id="showLogo" name="showLogo">
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <span class="twakLabel">Show agent’s photo</span>
                                                        <div class="form-check form-switch custom-switch-lg ">
                                                            <input class="form-check-input success" type="checkbox" id="showAgent" name="showAgent">
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <span class="twakLabel">Enable sound notifications for customers</span>
                                                        <div class="form-check form-switch custom-switch-lg ">
                                                            <input class="form-check-input success" type="checkbox" id="shownotification" name="shownotification" checked>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <span class="twakLabel">Let customers rate agents</span>
                                                        <div class="form-check form-switch custom-switch-lg ">
                                                            <input class="form-check-input success" type="checkbox" id="showcusrate" name="showcusrate" checked>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <span class="twakLabel">Let customers get chat transcripts</span>
                                                        <div class="form-check form-switch custom-switch-lg ">
                                                            <input class="form-check-input success" type="checkbox" id="showTranscripts" name="showTranscripts" checked>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <span class="twakLabel">White label widget</span>
                                                        <div class="form-check form-switch custom-switch-lg ">
                                                            <input class="form-check-input success" type="checkbox" id="showWhitelabel" name="showWhitelabel" checked>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="eyecatchHeading">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#eyecatcher"
                                            aria-expanded="false" aria-controls="eyecatcher">
                                            Eye Catcher
                                        </button>
                                    </h2>
                                    <div id="eyecatcher" class="accordion-collapse collapse"
                                        aria-labelledby="eyecatchHeading" data-bs-parent="#chatCustomize">
                                        <div class="accordion-body">
                                            <div class="eye-catcher-section">
                                                <div class="eye-catcher-list" id="eye-catcher-list">
                                                    <div class="eye-option eye-option-none" data-eye="" title="None">
                                                        <i class="ti ti-ban" style="font-size:22px;color:#9ca3af;"></i>
                                                        <small style="font-size:10px;color:#9ca3af;">None</small>
                                                    </div>
                                                    <div class="eye-option eye-option-custom" id="eye-custom-upload" title="Upload custom image">
                                                        <i class="ti ti-upload" style="font-size:22px;color:#9ca3af;"></i>
                                                        <small style="font-size:10px;color:#9ca3af;">Upload</small>
                                                        <input type="file" id="eye-catcher-file-input" accept="image/*" style="display:none;">
                                                    </div>
                                                    <div id="eye-custom-preview" class="mt-3" style="display:none;">
                                                        <label class="form-label small">Custom image preview:</label>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <img id="eye-custom-preview-img" class="eye-option selected"
                                                                data-eye="" style="max-width:64px;max-height:64px;border-radius:8px;cursor:pointer;">
                                                            <button id="eye-custom-remove" class="btn btn-sm btn-outline-danger">
                                                                <i class="ti ti-trash me-1"></i>Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <img src="/assets/images/eye1.png" class="eye-option" data-eye="/assets/images/eye1.png">
                                                    <img src="/assets/images/eye2.png" class="eye-option" data-eye="/assets/images/eye2.png">
                                                    <img src="/assets/images/eye3.png" class="eye-option" data-eye="/assets/images/eye3.png">
                                                    <img src="/assets/images/eye4.png" class="eye-option" data-eye="/assets/images/eye4.png">
                                                    
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 preview-col">
                        <div class="preview-card">
                            <h4 class="mb-3">Preview</h4>
                            <div class="preview-simulation position-relative" style="flex:1;">
                                <div id="chat-widget-container">
                                    <div id="chat-bubble">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>

                                    <div id="chat-popup" class="hidden theme-root">

                                        <div id="chat-home-screen">
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

                                        <div id="chat-conversation" class="hidden">
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
                                                <div class="message-container bot">
                                                    <div class="message-bubble bot">
                                                        Chat Start
                                                    </div>
                                                </div>
                                                <div class="message-container user">
                                                    <div class="message-bubble user">Hello. How may I help you?</div>
                                                    <div class="read-status">Read</div>
                                                </div>
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
        window.__widgetSiteId   = "{{ auth()->user()->site_id }}";
        window.__widgetSettings = @json($savedSettings);
        window.__widgetSaveUrl  = '{{ route("settings.widget.save") }}';
    </script>
    <script src="{{ asset('assets/js/chatwidget.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/customizer.js') }}?v={{ time() }}"></script>
@endpush