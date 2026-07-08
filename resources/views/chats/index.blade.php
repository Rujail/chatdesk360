@php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
@endphp

@extends('layouts.app')

@section('title', 'ChatDesk')

@push('styles')
<style>
/* ── Chat list sidebar ─────────────────────────────────── */
.chat-user-item {
    cursor: pointer;
    transition: background .12s;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.chat-user-item:hover  { background: rgba(0,0,0,0.04); }
.chat-user-item.active { background: rgba(43,96,208,0.08); }

.unread-badge {
    background: #ef4444; color: #fff;
    border-radius: 999px; font-size: 10px; font-weight: 700;
    padding: 1px 6px; min-width: 18px; text-align: center;
}

/* Online dot */
.online-dot  { background: #22c55e; }
.offline-dot { background: #9ca3af; }
.status-dot  {
    width: 9px; height: 9px; border-radius: 50%;
    border: 2px solid #fff;
    position: absolute; bottom: 0; right: 0;
}

/* ── Messages area ─────────────────────────────────────── */
.msg-visitor {
    background: #f3f4f6; color: #1f2937;
    border-radius: 12px 12px 12px 2px;
}
.msg-agent {
    background: #2b60d0; color: #fff;
    border-radius: 12px 12px 2px 12px;
}
.msg-system {
    background: #fef9c3; color: #92400e;
    border-radius: 8px; font-size: 12px;
    text-align: center; padding: 6px 12px;
}
.msg-wrap { display: flex; flex-direction: column; }
.msg-wrap.agent  { align-self: flex-end;   align-items: flex-end; }
.msg-wrap.visitor{ align-self: flex-start; align-items: flex-start; }
.msg-wrap.system { align-self: center; }

.msg-time { font-size: 10px; color: #9ca3af; margin-top: 3px; }

/* Typing indicator */
.typing-dots span {
    display: inline-block; width: 5px; height: 5px;
    background: #9ca3af; border-radius: 50%; margin: 0 1px;
    animation: bounce 1.2s infinite;
}
.typing-dots span:nth-child(2){ animation-delay: .2s }
.typing-dots span:nth-child(3){ animation-delay: .4s }
@keyframes bounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-4px)} }

/* Visitor live typing preview */
#visitor-typing-preview {
    background: #f0f9ff; border: 1px solid #bae6fd;
    border-radius: 8px; padding: 8px 14px; margin: 0 20px 10px;
    font-size: 12px; color: #0369a1; font-style: italic;
    display: none;
}

/* Chat header visitor info */
.visitor-status-badge {
    font-size: 11px; padding: 2px 8px; border-radius: 999px;
}
.badge-browsing  { background: #dcfce7; color: #166534; }
.badge-chatting  { background: #dbeafe; color: #1e40af; }
.badge-left      { background: #f3f4f6; color: #6b7280; }

/* No chat selected */
.no-chat-selected {
    display: flex; align-items: center; justify-content: center;
    height: 100%; color: #9ca3af; flex-direction: column; gap: 10px;
}

/* Supervise mode banner */
#supervise-mode-banner {
    background: #fef3c7; border-bottom: 1px solid #fbbf24;
    padding: 10px 20px; text-align: center; color: #92400e;
    font-size: 13px; font-weight: 600; display: none;
}

/* Shortcut Autocomplete */
#shortcut-dropdown {
    position: absolute; bottom: 70px; left: 50px; width: 350px;
    background: #1e1e1e; border: 1px solid #444; border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.6); z-index: 1055; padding: 6px 0;
    display: none; color: #ccc;
}
#shortcut-dropdown::before {
    content: ''; position: absolute; bottom: -6px; left: 20px;
    width: 12px; height: 12px; background: #1e1e1e;
    border-right: 1px solid #444; border-bottom: 1px solid #444;
    transform: rotate(45deg);
}
.shortcut-dropdown-header { padding: 8px 12px 4px; font-size: 11px; color: #888; text-transform: uppercase; border-bottom: 1px solid #333; margin-bottom: 4px; }
.shortcut-item { padding: 10px 12px; cursor: pointer; display: flex; align-items: flex-start; gap: 12px; transition: background 0.1s; }
.shortcut-item:hover, .shortcut-item.active { background: #094771; color: #fff; }
.shortcut-item .s-icon { color: #5c6bc0; font-size: 20px; margin-top: 2px; flex-shrink: 0; }
.shortcut-item.active .s-icon { color: #fff; }
.shortcut-item strong { display: block; font-size: 14px; font-weight: 600; margin-bottom: 2px; }
.shortcut-item small { color: #888; font-size: 12px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.shortcut-item.active small { color: #aac8e8; }
.shortcut-empty { padding: 20px; text-align: center; color: #666; font-size: 13px; }
.post-form-wrap {
  width: 100%;
}
.post-form {
  text-align: left;
  background: #f5f5f5;
  border-color: #000;
  padding: 10px 18px;
  color: #000;
  max-width: 50%;
}
.post-form > strong {
  font-size: 14px;
  font-weight: 7000;
  margin-bottom: 10px;
  display: block;
}
.post-form-field:not(:last-child) {
  margin-bottom: 10px;
}
.post-form-field .question {
  margin-bottom: 0;
}
.post-form-field .question strong {
  font-weight: 400;
}
.post-form-field  .answer {
  color: #000;
  text-transform: capitalize;
  font-weight: 600;
}

/* Transfer Modal Modern Light Theme */
.transfer-agent-item {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef !important;
    border-radius: 12px !important;
}
.transfer-agent-item:hover {
    background-color: #ffffff;
    border-color: #6366f1 !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
}
.transfer-agent-item:has(input:checked) {
    background-color: #eef2ff;
    border-color: #6366f1 !important;
}
.bg-primary-soft {
    background-color: #eef2ff;
    color: #4f46e5;
}
</style>
@endpush

@section('content')
<div class="body-wrapper">
    <div class="container-fluid mw-100">
        <div class="card overflow-hidden chat-application mb-0">

            {{-- Mobile toggle --}}
            <div class="d-flex align-items-center justify-content-between gap-6 m-3 d-lg-none">
                <button class="btn btn-primary d-flex" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#chat-sidebar">
                    <i class="ti ti-menu-2 fs-5"></i>
                </button>
                <div class="position-relative w-100">
                    <input type="text" class="form-control search-chat py-2 ps-5"
                        id="search-chat-mobile" placeholder="Search Contact" />
                    <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                </div>
            </div>

            <div class="d-flex" style="height: calc(100vh - 120px);">

                {{-- ── LEFT SIDEBAR ─────────────────────────────────────── --}}
                <div class="w-25 d-none d-lg-flex flex-column border-end user-chat-box">
                    <div class="px-4 pt-9 pb-4 border-bottom">
                        {{-- Agent info --}}
                        <div class="d-flex align-items-center mb-4">
                            <div class="position-relative">
                                <img src="{{ asset('assets/images/user-1.jpg') }}"
                                    width="44" height="44" class="rounded-circle" />
                                <span class="status-dot online-dot position-absolute"></span>
                            </div>
                            <div class="ms-3">
                                <h6 class="fw-semibold mb-0">{{ Auth::user()->name }}</h6>
                                <p class="mb-0 fs-2 text-muted">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        {{-- Search --}}
                        <div class="position-relative">
                            <input type="text" class="form-control search-chat py-2 ps-5"
                                id="search-chat-desktop" placeholder="Search visitors..." />
                            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </div>
                    </div>

                    {{-- Chat list --}}
                    <div class="flex-grow-1 overflow-auto" data-simplebar>
                        <div class="px-3 py-2">
                            <span class="text-muted fw-semibold fs-2">
                                Active Chats (<span id="chat-list-count">0</span>)
                            </span>
                        </div>
                        <ul class="list-unstyled mb-0" id="chat-user-list">
                            <li class="text-center py-4 text-muted fs-3" id="chat-list-loading">
                                Connecting...
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- ── MAIN CHAT AREA ───────────────────────────────────── --}}
                <div class="w-75 w-xs-100 d-flex flex-column chat-container">

                    {{-- No chat selected --}}
                    <div class="no-chat-selected flex-grow-1" id="no-chat-selected">
                        <div class="text-center p-5" style="max-width: 400px;">
                            <div class="mb-4 d-flex justify-content-center">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-soft" style="width: 80px; height: 80px;">
                                    <i class="ti ti-message-dots fs-1 text-primary"></i>
                                </div>
                            </div>
                            <h4 class="fw-semibold mb-2">Invite website visitors to chat</h4>
                            <p class="text-muted mb-4">See who's browsing your website and approach visitors who might need support.</p>
                            <a href="{{ url('/admin/traffic') }}" class="btn btn-primary px-4 py-2">
                                <i class="ti ti-eye me-1"></i> See visitors online
                            </a>
                        </div>
                    </div>

                    {{-- Active chat --}}
                    <div id="active-chat-area" style="display:none; flex-direction:column; height:100%;" class="d-flex flex-column">

                        {{-- Supervise mode banner --}}
                        <div id="supervise-mode-banner">
                            <i class="ti ti-eye me-2"></i>You are supervising this chat (read-only mode)
                        </div>

                        {{-- Chat header --}}
                        <div class="p-9 border-bottom d-flex align-items-center justify-content-between chat-meta-user">
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <iconify-icon icon="solar:user-circle-linear" width="36" height="36"></iconify-icon>
                                    <span class="status-dot online-dot position-absolute" id="header-status-dot"></span>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold" id="header-visitor-name">-</h6>
                                    <span class="visitor-status-badge badge-browsing" id="header-status-badge">Browsing</span>
                                    <small class="text-muted ms-2" id="header-visitor-page"></small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                {{-- Request name/email button --}}
                                <button class="btn btn-sm btn-outline-secondary" id="btn-request-info"
                                    title="Ask visitor for name & email">
                                    <i class="ti ti-user-question"></i> Request Info
                                </button>
                                {{-- Dropdown --}}
                                <div class="dropdown">
                                    <a class="text-dark fs-6 nav-icon-hover" href="javascript:void(0)"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-dots"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                href="javascript:void(0)" id="btn-transfer-chat">
                                                <i class="ti ti-arrows-exchange" style="color:#f97316;"></i> Transfer Chat
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                href="javascript:void(0)" id="btn-end-chat">
                                                <i class="ti ti-circle-x text-warning"></i> End Chat
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                href="javascript:void(0)" id="btn-ban-chat">
                                                <i class="ti ti-x"></i> Ban this chat
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                href="javascript:void(0)" id="btn-view-traffic">
                                                <i class="ti ti-eye"></i> View in Traffic
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                {{-- Visitor info panel toggle --}}
                                <a class="chat-menu text-dark px-2 fs-7 nav-icon-hover" href="javascript:void(0)">
                                    <i class="ti ti-user-circle"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Messages --}}
                        <div class="flex-grow-1 overflow-auto p-9" id="messages-area" data-simplebar>
                            {{-- Messages injected here --}}
                        </div>

                        {{-- Visitor live typing preview --}}
                        <div id="visitor-typing-preview">
                            <strong>Visitor is typing:</strong> <span id="visitor-typing-text"></span>
                        </div>

                        {{-- Typing indicator --}}
                        <div id="typing-indicator" class="px-9 py-1 text-muted fs-2" style="min-height:20px"></div>

                        {{-- File preview --}}
                        <div class="file-preview-container px-9 pt-3 border-top d-none"></div>

                        {{-- Input area --}}
                        <div class="position-relative">
                            {{-- Emoji picker --}}
                            <div class="emoji-picker-container">
                                <div>
                                    <span class="emoji-item" data-emoji="😊">😊</span>
                                    <span class="emoji-item" data-emoji="👍">👍</span>
                                    <span class="emoji-item" data-emoji="❤️">❤️</span>
                                    <span class="emoji-item" data-emoji="🔥">🔥</span>
                                    <span class="emoji-item" data-emoji="🎉">🎉</span>
                                    <span class="emoji-item" data-emoji="😂">😂</span>
                                    <span class="emoji-item" data-emoji="😍">😍</span>
                                    <span class="emoji-item" data-emoji="🤔">🤔</span>
                                    <span class="emoji-item" data-emoji="👏">👏</span>
                                    <span class="emoji-item" data-emoji="🙏">🙏</span>
                                    <span class="emoji-item" data-emoji="😢">😢</span>
                                    <span class="emoji-item" data-emoji="😎">😎</span>
                                </div>
                            </div>
                            <input type="file" id="fileAttachmentInput" class="d-none">
                            <div class="px-9 py-6 border-top chat-send-message-footer">
                                <div class="d-flex align-items-center gap-2 w-100">
                                    <button class="btn btn-sm btn-primary create-payment-link-btn"
                                        data-bs-toggle="modal" data-bs-target="#paymentLinkModal"
                                        title="Create Payment Link">
                                        <i class="ti ti-wallet"></i>
                                    </button>
                                    <input type="text" class="form-control message-type-box border-0 rounded-0 p-0"
                                        id="agent-message-input" placeholder="Type a Message..." />
                                    <a class="text-dark fs-7 nav-icon-hover attachment-btn" href="javascript:void(0)">
                                        <i class="ti ti-paperclip"></i>
                                    </a>
                                    <a class="text-dark fs-7 nav-icon-hover emoji-btn" href="javascript:void(0)">
                                        <i class="ti ti-mood-smile"></i>
                                    </a>
                                    <button class="btn btn-sm btn-primary" id="btn-send-msg">
                                        <i class="ti ti-send"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── RIGHT: VISITOR INFO PANEL ────────────────────────── --}}
                <div class="app-chat-offcanvas border-start d-none" id="visitor-info-panel">
                    <div class="custom-app-scroll" data-simplebar>
                        <div class="offcanvas-body p-8">
                            
                            {{-- Skeleton Loader --}}
                            <div id="vi-skeleton" class="d-none">
                                <div class="d-flex align-items-center mb-4 placeholder-glow">
                                    <span class="placeholder rounded-circle" style="width: 45px; height: 45px;"></span>
                                    <span class="placeholder col-7 ms-3"></span>
                                </div>
                                <div class="placeholder-glow mb-4">
                                    <span class="placeholder col-12"></span>
                                    <span class="placeholder col-12"></span>
                                    <span class="placeholder col-10"></span>
                                </div>
                                <div class="placeholder-glow mb-4">
                                    <span class="placeholder col-12"></span>
                                    <span class="placeholder col-9"></span>
                                </div>
                                <div class="placeholder-glow">
                                    <span class="placeholder col-12"></span>
                                    <span class="placeholder col-8"></span>
                                </div>
                            </div>

                            {{-- Actual Content --}}
                            <div id="vi-content" class="d-none">
                                <div class="accordion accordion-flush" id="visitorInfoAccordion">

                                    {{-- Basic info --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#vi-collapse1">
                                                <div class="cus-infocard">
                                                    <div class="cus-img"><i class="ti ti-user-circle"></i></div>
                                                    <h5 id="vi-name">Unnamed</h5>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="vi-collapse1" class="accordion-collapse collapse"
                                            data-bs-parent="#visitorInfoAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li><p id="vi-location">-</p></li>
                                                    <li><p id="vi-email">-</p></li>
                                                    <li><p id="vi-ip">-</p></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Additional --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#vi-collapse2">
                                                <div class="cus-infocard"><h5>Additional info</h5></div>
                                            </button>
                                        </h2>
                                        <div id="vi-collapse2" class="accordion-collapse collapse"
                                            data-bs-parent="#visitorInfoAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li><p id="vi-visits">-</p></li>
                                                    <li><p id="vi-device">-</p></li>
                                                    <li><p id="vi-browser">-</p></li>
                                                    <li><p id="vi-os">-</p></li>
                                                    <li><p id="vi-lastseen">-</p></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Visited pages --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#vi-collapse3">
                                                <div class="cus-infocard"><h5>Visited pages</h5></div>
                                            </button>
                                        </h2>
                                        <div id="vi-collapse3" class="accordion-collapse collapse"
                                            data-bs-parent="#visitorInfoAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-come" id="vi-pages">
                                                    <li class="text-muted">Select a visitor</li>
                                                </ul>
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
    </div>
</div>

{{-- Payment Link Modal --}}
<div class="modal fade" id="paymentLinkModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Payment Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" class="form-control" id="pay-amount" placeholder="e.g. 199.99">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Description (optional)</label>
                    <input type="text" class="form-control" id="pay-desc" placeholder="e.g. Book cover deposit">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Currency</label>
                    <select class="form-select" id="pay-currency">
                        <option value="USD">USD</option>
                        <option value="PKR">PKR</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-generate-payment">Generate & Send</button>
            </div>
        </div>
    </div>
</div>

{{-- Mobile sidebar offcanvas --}}
<div class="offcanvas offcanvas-start user-chat-box chat-offcanvas" id="chat-sidebar">
    <div class="offcanvas-header">
        <h5>Chats</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="overflow-auto" data-simplebar>
        <ul class="list-unstyled mb-0" id="chat-user-list-mobile">
            <li class="text-center py-4 text-muted fs-3">Connecting...</li>
        </ul>
    </div>
</div>


{{-- Ban Customer Modal (from Chat) --}}
<div class="modal fade" id="banChatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-ban me-2 text-danger"></i>Ban this Visitor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning py-2 fs-3 mb-3">
                    <i class="ti ti-alert-triangle me-1"></i>
                    Banned visitors will not see your chat widget, appear on traffic, or receive campaigns.
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Visitor IP</label>
                    <input type="text" class="form-control" id="ban-chat-ip" readonly>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Visitor ID</label>
                    <input type="text" class="form-control" id="ban-chat-visitor-id" readonly>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Reason (optional)</label>
                    <input type="text" class="form-control" id="ban-chat-reason"
                        placeholder="e.g. Spam, abuse...">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Duration</label>
                    <select class="form-select" id="ban-chat-duration">
                        <option value="1">1 day</option>
                        <option value="7" selected>7 days</option>
                        <option value="30">30 days</option>
                        <option value="permanent">Permanent</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div class="form-group mb-3 d-none" id="ban-chat-custom-end-wrap">
                    <label class="form-label">End date</label>
                    <input type="date" class="form-control" id="ban-chat-custom-end">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" id="btn-confirm-ban-chat">
                    <i class="ti ti-ban me-1"></i>Ban Visitor
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Transfer Chat Modal --}}
<div class="modal fade" id="transferChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center text-dark">
                    <i class="ti ti-arrows-exchange me-2 text-primary fs-5"></i> Transfer chat to...
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body px-4 py-3">
                {{-- Agent List --}}
                <div id="transfer-agent-list" class="mb-4">
                    <div class="text-center text-muted py-5">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        Loading agents...
                    </div>
                </div>

                {{-- Summarize & Note --}}
                <div class="mt-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold mb-1">Summarize chat</label>
                        <textarea class="form-control bg-light border-0" id="transfer-summary" rows="2" placeholder="Brief summary of the conversation..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold mb-1">Add internal note</label>
                        <textarea class="form-control bg-light border-0" id="transfer-note" rows="2" placeholder="Note for the next agent..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0 justify-content-end px-4 pb-4 pt-2">
                <button type="button" class="btn btn-light border me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary d-flex align-items-center shadow-sm" id="btn-confirm-transfer">
                    <i class="ti ti-arrows-exchange me-1"></i> Transfer Chat
                </button>
            </div>
        </div>
    </div>
</div>

{{-- End Chat Confirmation Modal --}}
<div class="modal fade" id="endChatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-circle-x me-2 text-warning"></i>End Chat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to end this chat?</p>
                <p class="text-muted fs-3 mb-0">The visitor will be notified and may see a post-chat survey if enabled.</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-warning" id="btn-confirm-end-chat">
                    <i class="ti ti-circle-x me-1"></i>End Chat
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Audio for chat messages --}}
<audio id="audio-new-message" src="/sounds/message.wav" preload="auto"></audio>
@endsection

@push('scripts')
<script>
(function () {
'use strict';

// ── Config ────────────────────────────────────────────────────────────────────
const AGENT_ID    = '{{ Auth::id() }}';
const AGENT_NAME  = '{{ Auth::user()->name }}';
const USER_ROLE   = '{{ Auth::user()->role }}';
const SITE_ID     = '{{ Auth::user()->site_id }}';
const TRAFFIC_URL = '{{ url("/admin/traffic") }}';
const LIVE_URL    = '{{ route("traffic.live") }}';

const FB_CONFIG = {
    apiKey:      '{{ config("services.firebase.api_key") }}',
    databaseURL: '{{ config("services.firebase.db_url") }}',
    projectId:   '{{ config("services.firebase.project_id") }}',
    appId:       '{{ config("services.firebase.app_id") }}',
    authDomain:  '{{ config("services.firebase.project_id") }}.firebaseapp.com',
};

// ── State ─────────────────────────────────────────────────────────────────────
let db, fbRef, fbPush, fbSet, fbRemove, fbOnChildAdded, fbOnChildChanged, fbOnValue, fbQuery, fbOrderByChild, fbLimitToLast, fbOnDisconnect, fbGet;
let activeDomainId  = null;
let activeVisitorId = null;
let activeRoomPath  = null;
let chatRooms       = {};
let typingTimer     = null;
let attachedFile    = null;
let renderedMsgIds  = new Set();
let unsubMessages   = null;
let isSuperviseMode = false;
let activeVisitorInfo = null;
let chatSoundsEnabled = false;

let unsubTyping = null;
let unsubVisitorTyping = null;

// ── Sound Helper ──────────────────────────────────────────────────────────────
function playSound(audioId) {
    const audio = document.getElementById(audioId);
    if (audio) {
        audio.currentTime = 0;
        audio.play().catch(e => {}); // Ignore autoplay errors
    }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function fmtTime(ts) {
    if (!ts) return '';
    return new Date(ts).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
function esc(s) {
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
}

function timeAgo(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    const now = new Date();
    const diffMs = now - date;
    const diffMinutes = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    if (diffMinutes < 1) return 'just now';
    if (diffMinutes < 60) return `${diffMinutes}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays === 1) return 'yesterday';
    if (diffDays < 7) return `${diffDays}d ago`;
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
}

function shortUrl(url) {
    if (!url) return '-';
    try { return new URL(url).pathname || '/'; } catch { return url; }
}

// ── Firebase init ─────────────────────────────────────────────────────────────
const FB_BASE = 'https://www.gstatic.com/firebasejs/10.12.0';

Promise.all([
    import(`${FB_BASE}/firebase-app.js`),
    import(`${FB_BASE}/firebase-database.js`),
]).then(([appMod, dbMod]) => {

    const { initializeApp } = appMod;
    const {
        getDatabase,
        ref,
        push,
        set,
        remove,
        onChildAdded,
        onChildChanged,
        onValue,
        query,
        orderByChild,
        limitToLast,
        onDisconnect,
        get
    } = dbMod;

    fbRef = ref;
    fbPush = push;
    fbSet = set;
    fbRemove = remove;
    fbOnChildAdded = onChildAdded;
    fbOnChildChanged = onChildChanged;
    fbOnValue = onValue;
    fbQuery = query;
    fbOrderByChild = orderByChild;
    fbLimitToLast = limitToLast;
    fbOnDisconnect = onDisconnect;
    fbGet = get;

    const app = initializeApp(FB_CONFIG);
    db = getDatabase(app);

    // ── Agent presence ────────────────────────────────────────────────────────
    const agentPresRef = fbRef(db, `agent_presence/${SITE_ID}/${AGENT_ID}`);
    fbSet(agentPresRef, { name: AGENT_NAME, online: true, ts: Date.now() });
    fbOnDisconnect(agentPresRef).remove();

    listenToChatRooms();
    pollVisitorList();
    setInterval(pollVisitorList, 15000);

    // ── Check URL params ──────────────────────────────────────────────────────
    const urlParams = new URLSearchParams(window.location.search);
    const targetVisitor = urlParams.get('visitor');
    const mode = urlParams.get('mode');
    
    if (mode === 'supervise') {
        isSuperviseMode = true;
    }

    if (targetVisitor) {
        setTimeout(() => openChatByVisitorId(targetVisitor), 2000);
    }

}).catch(err => {
    console.error('[Chat] Firebase init failed:', err);
    pollVisitorList();
    setInterval(pollVisitorList, 15000);
});

/**
 * Find the visitor's actual DOMAIN_ID from Firebase before opening a chat.
 */
async function resolveVisitorDomain(visitorId) {
    if (!db) return null;

    try {
        const snap = await fbGet(fbRef(db, `active_chats/${SITE_ID}`));
        const data = snap.val();
        if (data) {
            for (const [domainId, visitors] of Object.entries(data)) {
                if (visitors && visitors[visitorId]) {
                    if (domainId !== 'unknown' && domainId !== 'unknown_domain') {
                        return domainId;
                    }
                }
            }
        }
    } catch (e) { console.warn('[resolveDomain] active_chats lookup failed', e); }

    try {
        const snap = await fbGet(fbRef(db, `presence/${SITE_ID}/${visitorId}`));
        const data = snap.val();
        if (data && data.domain_id && data.domain_id !== 'unknown' && data.domain_id !== 'unknown_domain') {
            return data.domain_id;
        }
    } catch (e) { console.warn('[resolveDomain] presence lookup failed', e); }

    return null;
}

// ── Listen to chat rooms ──────────────────────────────────────────────────────
function listenToChatRooms() {
    fbOnValue(fbRef(db, `active_chats/${SITE_ID}`), snap => {
        pollVisitorList();
        const domains = snap.val();
        if (!domains || typeof domains !== 'object') return;
        
        const incoming = {};
        Object.entries(domains).forEach(([domainId, visitors]) => {
            if (!visitors || typeof visitors !== 'object') return;
            Object.entries(visitors).forEach(([visitorId, data]) => {
                const roomKey = `${domainId}__${visitorId}`;
                incoming[visitorId] = incoming[visitorId] || [];
                incoming[visitorId].push({
                    room_key:     roomKey,
                    domain_id:    domainId,
                    visitor_id:   visitorId,
                    visitor_name: data.visitor_name ?? 'Unnamed',
                    last_message: data.last_message ?? '',
                    last_ts:      data.last_timestamp ?? 0,
                    unread:       chatRooms[roomKey]?.unread ?? 0,
                    is_active:    data.is_active !== false,
                    assigned_agent: data.assigned_agent ?? chatRooms[roomKey]?.assigned_agent ?? null,
                    agent_name:     data.agent_name ?? chatRooms[roomKey]?.agent_name ?? null,
                });
            });
        });
        
        Object.entries(incoming).forEach(([visitorId, rooms]) => {
            const realRoom = rooms.find(r => r.domain_id !== 'unknown' && r.domain_id !== 'unknown_domain');
            const unknownRoom = rooms.find(r => r.domain_id === 'unknown' || r.domain_id === 'unknown_domain');
            
            if (realRoom && unknownRoom) {
                realRoom.unread = (realRoom.unread || 0) + (unknownRoom.unread || 0);
                realRoom.assigned_agent = realRoom.assigned_agent || unknownRoom.assigned_agent;
                chatRooms[realRoom.room_key] = realRoom;
                delete chatRooms[unknownRoom.room_key];
                
                fbRemove(fbRef(db, `active_chats/${SITE_ID}/${unknownRoom.domain_id}/${visitorId}`));
                
                if (realRoom.last_message) {
                    fbRemove(fbRef(db, `chats/${unknownRoom.domain_id}/general/${visitorId}/messages`));
                    fbRemove(fbRef(db, `chats/${unknownRoom.domain_id}/general/${visitorId}/meta`));
                    fbRemove(fbRef(db, `chats/${unknownRoom.domain_id}/general/${visitorId}/status`));
                }
            } else if (realRoom) {
                chatRooms[realRoom.room_key] = realRoom;
            } else if (unknownRoom) {
                chatRooms[unknownRoom.room_key] = unknownRoom;
            }
        });
        
        renderChatList();
        
        // ★ CHECK IF CURRENTLY OPEN CHAT WAS ENDED/ARCHIVED
        if (activeVisitorId) {
            const currentRoom = Object.values(chatRooms).find(r => r.visitor_id === activeVisitorId);
            if (!currentRoom || currentRoom.is_active === false) {
                closeActiveChatView();
            }
        }
    });
}

// ── Poll visitor list ─────────────────────────────────────────────────────────
let visitorDetails = {};

function pollVisitorList() {
    fetch(LIVE_URL)
        .then(r => r.json())
        .then(data => {
            (data.visitors || []).forEach(v => {
                visitorDetails[v.visitor_id] = v;
            });
            
            (data.visitors || []).filter(v => v.status === 'chatting').forEach(v => {
                let domainId = v.domain_id || null;
                if ((!domainId || domainId === 'unknown' || domainId === 'unknown_domain') && v.last_page_url) {
                    try {
                        domainId = new URL(v.last_page_url).hostname.replace(/\./g, '_');
                    } catch {}
                }
                
                if (!domainId || domainId === 'unknown' || domainId === 'unknown_domain') {
                    return;
                }
                
                const roomKey = `${domainId}__${v.visitor_id}`;
                if (!chatRooms[roomKey]) {
                    chatRooms[roomKey] = {
                        room_key:     roomKey,
                        domain_id:    domainId,
                        visitor_id:   v.visitor_id,
                        visitor_name: v.name,
                        last_message: '',
                        last_ts:      0,
                        unread:       0,
                        assigned_agent: v.assign_userID ?? null,
                    };
                } else {
                    chatRooms[roomKey].visitor_name = v.name || chatRooms[roomKey].visitor_name;
                    if (v.assign_userID !== undefined && v.assign_userID !== null) {
                        chatRooms[roomKey].assigned_agent = v.assign_userID;
                    }
                    if (v.assign_userName) {
                        chatRooms[roomKey].agent_name = v.assign_userName;
                    }
                }
                
                const unknownKey = `unknown__${v.visitor_id}`;
                const unknownDomainKey = `unknown_domain__${v.visitor_id}`;
                [unknownKey, unknownDomainKey].forEach(uKey => {
                    if (chatRooms[uKey]) {
                        chatRooms[roomKey].unread = (chatRooms[roomKey].unread || 0) + (chatRooms[uKey].unread || 0);
                        delete chatRooms[uKey];
                        const badDomain = uKey.split('__')[0];
                        fbRemove(fbRef(db, `active_chats/${SITE_ID}/${badDomain}/${v.visitor_id}`));
                    }
                });
            });
            renderChatList();
            
            if (activeVisitorId && visitorDetails[activeVisitorId]) {
                updateHeader(visitorDetails[activeVisitorId]);
            }
        })
        .catch(console.error);
}

// ── Render chat list ──────────────────────────────────────────────────────────
function renderChatList() {
    const activeRooms   = Object.values(chatRooms).filter(r => {
        const isActive = r.is_active !== false;
        const hasAccess = USER_ROLE.toLowerCase() === 'admin' 
                          || !r.assigned_agent 
                          || String(r.assigned_agent) === String(AGENT_ID);
        return isActive && hasAccess;
    }).sort((a,b) => b.last_ts - a.last_ts);
    
    const archivedRooms = Object.values(chatRooms).filter(r => {
        const isArchived = r.is_active === false;
        const hasAccess = USER_ROLE.toLowerCase() === 'admin' 
                          || !r.assigned_agent 
                          || String(r.assigned_agent) === String(AGENT_ID);
        return isArchived && hasAccess;
    }).sort((a,b) => b.last_ts - a.last_ts);

    const allRooms = [...activeRooms];

    document.getElementById('chat-list-count').textContent = activeRooms.length;

    const listHTML = allRooms.length ? allRooms.map(r => {
        const vd = visitorDetails[r.visitor_id] ?? {};
        const isActiveChat = r.room_key === `${activeDomainId}__${activeVisitorId}`;
        const isArchived = r.is_active === false;
        const statusClass = (!isArchived && vd.status === 'chatting') ? 'online-dot' : 'offline-dot';
        const unreadHtml = r.unread > 0 ? `<span class="unread-badge">${r.unread}</span>` : '';
        const lastMsg = r.last_message
            ? (r.last_message.length > 30 ? r.last_message.slice(0, 30) + '…' : r.last_message)
            : 'Chat started';
        const archivedBadge = isArchived ? `<span class="badge bg-secondary" style="font-size:9px">archived</span>` : '';

        return `
        <li>
            <a href="javascript:void(0)"
               class="px-4 py-3 d-flex align-items-start justify-content-between chat-user-item ${isActiveChat ? 'active' : ''} ${isArchived ? 'opacity-75' : ''}"
               data-domain="${r.domain_id}" data-visitor="${r.visitor_id}" data-room="${r.room_key}">
                <div class="d-flex align-items-center">
                    <span class="position-relative">
                        <iconify-icon icon="solar:user-circle-linear" width="30" height="30"></iconify-icon>
                        <span class="status-dot ${statusClass} position-absolute"></span>
                    </span>
                    <div class="ms-3" style="max-width: 160px;">
                        <h6 class="mb-0 fw-semibold text-truncate">${esc(r.visitor_name)} ${archivedBadge}</h6>
                        <span class="fs-3 text-truncate text-muted d-block">${esc(lastMsg)}</span>
                    </div>
                </div>
                <div class="text-end">
                    ${unreadHtml}
                    ${r.last_ts ? `<p class="fs-2 mb-0 text-muted mt-1">${fmtTime(r.last_ts)}</p>` : ''}
                </div>
            </a>
        </li>`;
    }).join('') : `
        <li class="px-4 py-5 text-center">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-3" style="width: 60px; height: 60px;">
                <i class="ti ti-users-plus fs-3 text-primary"></i>
            </div>
            <h6 class="fw-semibold mb-1">No active chats yet</h6>
            <p class="text-muted fs-3 mb-3 px-2">Approach visitors who might need support.</p>
            <a href="{{ url('/admin/traffic') }}" class="btn btn-sm btn-outline-primary">
                <i class="ti ti-eye me-1"></i> See visitors online
            </a>
        </li>
    `;

    const loadingEl = document.getElementById('chat-list-loading');
    if (loadingEl) loadingEl.style.display = 'none';
    const listEl = document.getElementById('chat-user-list');
    if (!listEl) return;
    listEl.innerHTML = listHTML;

    document.querySelectorAll('.chat-user-item').forEach(el => {
        el.addEventListener('click', function() {
            openChat(this.dataset.domain, this.dataset.visitor);
        });
    });

    // ★ AUTO-SELECT FIRST CHAT IF NONE IS SELECTED AND CHATS EXIST
    const urlParams = new URLSearchParams(window.location.search);
    const targetVisitor = urlParams.get('visitor');
    
    if (allRooms.length > 0 && !activeVisitorId && !isSuperviseMode && !targetVisitor) {
        openChat(allRooms[0].domain_id, allRooms[0].visitor_id);
    }
}

// ── Close active chat view ────────────────────────────────────────────────────
function closeActiveChatView() {
    if (typeof unsubMessages === 'function') unsubMessages();
    if (typeof unsubTyping === 'function') unsubTyping();
    if (typeof unsubVisitorTyping === 'function') unsubVisitorTyping();
    renderedMsgIds.clear();

    activeDomainId  = null;
    activeVisitorId = null;
    activeRoomPath  = null;
    activeVisitorInfo = null;

    document.getElementById('active-chat-area').style.display = 'none';
    document.getElementById('no-chat-selected').style.display = 'flex';
    document.getElementById('messages-area').innerHTML = '';

    const viPanel = document.getElementById('visitor-info-panel');
    if (viPanel) viPanel.classList.add('d-none');
}

// ── Open chat ─────────────────────────────────────────────────────────────────
function openChat(domainId, visitorId) {
    if (typeof unsubMessages === 'function') unsubMessages();
    if (typeof unsubTyping === 'function') unsubTyping();
    if (typeof unsubVisitorTyping === 'function') unsubVisitorTyping();
    renderedMsgIds.clear();

    activeDomainId  = domainId;
    activeVisitorId = visitorId;
    activeRoomPath  = `chats/${domainId}/general/${visitorId}`;

    activeVisitorInfo = null; 

    chatSoundsEnabled = false;
    setTimeout(() => { chatSoundsEnabled = true; }, 1500);

    document.getElementById('no-chat-selected').style.display = 'none';
    document.getElementById('active-chat-area').style.display = 'flex';
    document.getElementById('messages-area').innerHTML = '';

    // ★ SHOW PANEL & TRIGGER SKELETON
    const viPanel = document.getElementById('visitor-info-panel');
    viPanel.classList.remove('d-none');
    document.getElementById('vi-skeleton').classList.remove('d-none');
    document.getElementById('vi-content').classList.add('d-none');

    if (isSuperviseMode) {
        document.getElementById('supervise-mode-banner').style.display = 'block';
        document.getElementById('agent-message-input').disabled = true;
        document.getElementById('btn-send-msg').disabled = true;
        document.getElementById('btn-request-info').disabled = true;
    } else {
        document.getElementById('supervise-mode-banner').style.display = 'none';
        document.getElementById('agent-message-input').disabled = false;
        document.getElementById('btn-send-msg').disabled = false;
        document.getElementById('btn-request-info').disabled = false;
    }

    const roomKey = `${domainId}__${visitorId}`;
    if (chatRooms[roomKey]) chatRooms[roomKey].unread = 0;

    document.querySelectorAll('.chat-user-item').forEach(el => {
        el.classList.toggle('active', el.dataset.room === roomKey);
    });

    loadVisitorInfo(visitorId);

    const vd = visitorDetails[visitorId];
    if (vd) updateHeader(vd);
    else {
        const room = chatRooms[roomKey];
        document.getElementById('header-visitor-name').textContent = room?.visitor_name ?? 'Visitor';
    }

    listenToMessages(domainId, visitorId);
    markMessagesRead(domainId, visitorId);
    listenToTyping(domainId, visitorId);
    listenToVisitorTyping(domainId, visitorId);

    if (db && !isSuperviseMode) {
        fbSet(fbRef(db, `${activeRoomPath}/agent_present`), {
            agent_id:   AGENT_ID,
            agent_name: AGENT_NAME,
            joined_at:  Date.now(),
        });

        fbSet(fbRef(db, `active_chats/${SITE_ID}/${domainId}/${visitorId}/assigned_agent`), AGENT_ID);
        fbSet(fbRef(db, `active_chats/${SITE_ID}/${domainId}/${visitorId}/agent_name`), AGENT_NAME);
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'start' && !isSuperviseMode) {
        setTimeout(() => {
            checkAndSendWelcome(domainId, visitorId);
        }, 500);
        window.history.replaceState({}, '', `/admin/chats?visitor=${visitorId}`);
    }
}

async function checkAndSendWelcome(domainId, visitorId) {
    if (domainId === 'unknown' || domainId === 'unknown_domain') {
        const vd = visitorDetails[visitorId];
        let resolvedDomain = null;
        
        if (vd?.domain_id && vd.domain_id !== 'unknown' && vd.domain_id !== 'unknown_domain') {
            resolvedDomain = vd.domain_id;
        } else if (vd?.last_page_url) {
            try {
                resolvedDomain = new URL(vd.last_page_url).hostname.replace(/\./g, '_');
            } catch {}
        }
        
        if (!resolvedDomain) {
            try {
                const r = await fetch(`${TRAFFIC_URL}/${visitorId}`);
                const d = await r.json();
                const v = d.visitor;
                if (v?.domain_id && v.domain_id !== 'unknown' && v.domain_id !== 'unknown_domain') {
                    resolvedDomain = v.domain_id;
                } else if (v?.last_page_url) {
                    try {
                        resolvedDomain = new URL(v.last_page_url).hostname.replace(/\./g, '_');
                    } catch {}
                }
            } catch (e) {}
        }
        
        if (!resolvedDomain) {
            resolvedDomain = await resolveVisitorDomain(visitorId);
        }
        
        if (resolvedDomain && resolvedDomain !== 'unknown' && resolvedDomain !== 'unknown_domain') {
            if (activeDomainId === 'unknown' || activeDomainId === 'unknown_domain') {
                fbRemove(fbRef(db, `active_chats/${SITE_ID}/${activeDomainId}/${visitorId}`));
                openChat(resolvedDomain, visitorId);
                checkAndSendWelcome(resolvedDomain, visitorId);
            }
        }
        return;
    }
    
    const msgsRef = fbRef(db, `chats/${domainId}/general/${visitorId}/messages`);
    const snap = await fbGet(msgsRef);
    
    if (!snap.exists()) {
        fbPush(msgsRef, {
            uid:        AGENT_ID,
            agent_name: AGENT_NAME,
            message:    'Hello! How can I help you today?',
            timestamp:  Date.now(),
            status:     'sent',
            sender:     'agent',
        });

        const vd = visitorDetails[visitorId];
        const visitorName = vd?.name || 'Visitor';

        fbSet(fbRef(db, `active_chats/${SITE_ID}/${domainId}/${visitorId}`), {
            visitor_id:     visitorId,
            visitor_name:   visitorName,
            last_message:   'Hello! How can I help you today?',
            last_timestamp: Date.now(),
            site_id:        SITE_ID,
            domain_id:      domainId,
            is_active:      true,
            assigned_agent: AGENT_ID,
            agent_name:     AGENT_NAME,
        });

        fbSet(fbRef(db, `chats/${domainId}/general/${visitorId}/meta`), {
            last_message:   'Hello! How can I help you today?',
            last_timestamp: Date.now(),
            domain_id:      domainId,
            visitor_id:     visitorId,
            visitor_name:   visitorName,
            site_id:        SITE_ID,
            is_active:      true,
        });
    }
}

async function openChatByVisitorId(visitorId) {
    const room = Object.values(chatRooms).find(r =>
        r.visitor_id === visitorId &&
        r.domain_id &&
        r.domain_id !== 'unknown' &&
        r.domain_id !== 'unknown_domain'
    );
    if (room) {
        openChat(room.domain_id, visitorId);
        return;
    }

    const vd = visitorDetails[visitorId];
    if (vd?.domain_id && vd.domain_id !== 'unknown' && vd.domain_id !== 'unknown_domain') {
        openChat(vd.domain_id, visitorId);
        return;
    }

    if (vd?.last_page_url) {
        try {
            const domainId = new URL(vd.last_page_url).hostname.replace(/\./g, '_');
            if (domainId && domainId !== 'unknown') {
                openChat(domainId, visitorId);
                return;
            }
        } catch {}
    }

    try {
        const r = await fetch(`${TRAFFIC_URL}/${visitorId}`);
        const d = await r.json();
        const v = d.visitor;

        if (v?.domain_id && v.domain_id !== 'unknown' && v.domain_id !== 'unknown_domain') {
            visitorDetails[visitorId] = { ...visitorDetails[visitorId], ...v };
            openChat(v.domain_id, visitorId);
            return;
        }
        if (v?.last_page_url) {
            try {
                const domainId = new URL(v.last_page_url).hostname.replace(/\./g, '_');
                if (domainId) {
                    openChat(domainId, visitorId);
                    return;
                }
            } catch {}
        }
    } catch (e) {
        console.warn('[openChatByVisitorId] Server fetch failed:', e);
    }

    const firebaseDomain = await resolveVisitorDomain(visitorId);
    if (firebaseDomain && firebaseDomain !== 'unknown' && firebaseDomain !== 'unknown_domain') {
        openChat(firebaseDomain, visitorId);
        return;
    }

    const unknownRoom = Object.values(chatRooms).find(r =>
        r.visitor_id === visitorId &&
        (r.domain_id === 'unknown' || r.domain_id === 'unknown_domain')
    );
    if (unknownRoom) {
        openChat(unknownRoom.domain_id, visitorId);
        return;
    }

    openChat('unknown', visitorId);
}

// ── Listen to messages ────────────────────────────────────────────────────────
function listenToMessages(domainId, visitorId) {
    if (!db) return;
    const msgsPath = `chats/${domainId}/general/${visitorId}/messages`;
    const msgQ = fbQuery(
        fbRef(db, msgsPath),
        fbOrderByChild('timestamp'),
        fbLimitToLast(100)
    );

    unsubMessages = fbOnChildAdded(msgQ, snap => {
        const d = snap.val();
        const id = snap.key;
        if (renderedMsgIds.has(id)) return;
        renderedMsgIds.add(id);
        renderMessage(id, d);

        if (chatSoundsEnabled) {
            playSound('audio-new-message');
        }

        if (d.sender === 'visitor' && d.status === 'sent') {
            fbSet(fbRef(db, `${msgsPath}/${id}/status`), 'delivered');
        }

        if (d.sender === 'visitor' && d.status !== 'read' && 
            activeDomainId === domainId && activeVisitorId === visitorId) {
            fbSet(fbRef(db, `${msgsPath}/${id}/status`), 'read');
        }

        const roomKey = `${domainId}__${visitorId}`;
        if (chatRooms[roomKey]) {
            chatRooms[roomKey].last_message = d.message ?? '';
            chatRooms[roomKey].last_ts      = d.timestamp;
            if (activeDomainId !== domainId || activeVisitorId !== visitorId) {
                if (d.sender === 'visitor') {
                    chatRooms[roomKey].unread = (chatRooms[roomKey].unread ?? 0) + 1;
                }
            }
        }
        renderChatList();
    });

    fbOnChildChanged(fbRef(db, msgsPath), snap => {
        const d = snap.val();
        if (d.type === 'info_filled' && d.visitor_name) {
            updateVisitorName(domainId, visitorId, d.visitor_name, d.visitor_email);
        }
    });
}

// ── Render message ────────────────────────────────────────────────────────────
function renderMessage(id, d) {
    const area    = document.getElementById('messages-area');
    const isAgent = d.sender === 'agent';
    const isSystem = d.type === 'system' || d.type === 'info_request' || d.type === 'info_filled' || d.type === 'post_chat_response';

    if (isSystem) {
        const w = document.createElement('div');
        w.id = 'msg_' + id;
        w.className = 'msg-wrap system w-100 my-2';

        if (d.type === 'post_chat_response') {
            w.className = 'msg-wrap system w-100 my-2 post-form-wrap';
            const data = d.response_data || {};
            let html = '<strong>Post-chat Feedback</strong>';
            for (const [question, answer] of Object.entries(data)) {
                const displayAnswer = Array.isArray(answer) ? answer.join(', ') : answer;
                html += `<div class="post-form-field"><p class="question"><strong>${esc(question)}:</strong></p> <div class="answer">${esc(displayAnswer || '-')}</div></div>`;
            }
            w.innerHTML = `<div class="msg-system post-form">${html}</div>`;
            area.appendChild(w);
            area.scrollTop = area.scrollHeight;
            return;
        }

        let sysText = '';
        if (d.type === 'info_request')   sysText = '🔔 Agent requested visitor info';
        else if (d.type === 'info_filled') sysText = `✅ Visitor provided: ${esc(d.visitor_name ?? '')}${d.visitor_email ? ' · ' + esc(d.visitor_email) : ''}`;
        else sysText = esc(d.message ?? '');
        w.innerHTML = `<div class="msg-system">${sysText}</div>`;
        area.appendChild(w);
        area.scrollTop = area.scrollHeight;
        return;
    }

    const wrapClass   = isAgent ? 'agent' : 'visitor';
    const bubbleClass = isAgent ? 'msg-agent' : 'msg-visitor';
    const username    = isAgent ? (d.agent_name ?? 'Agent') : (d.username ?? 'Visitor');
    const time        = fmtTime(d.timestamp);

    let msgContent = '';

    if (d.fileUrl && d.fileType === 'image') {
        let fileUrl = d.fileUrl;
        if (fileUrl.includes('/storage/chat-files/')) {
            fileUrl = fileUrl.replace('/storage/chat-files/', '/api/chat-files/');
        }
        const fullUrl = fileUrl.startsWith('http') ? fileUrl : window.location.origin + fileUrl;
        msgContent = `<img src="${fullUrl}" style="max-width:240px;max-height:200px;border-radius:8px;cursor:pointer;display:block;" class="chat-img-preview" alt="${esc(d.fileName || 'image')}">`;
    } else if (d.fileUrl && d.fileType === 'file') {
        let fileUrl = d.fileUrl;
        if (fileUrl.includes('/storage/chat-files/')) {
            fileUrl = fileUrl.replace('/storage/chat-files/', '/api/chat-files/');
        }
        const fullUrl = fileUrl.startsWith('http') ? fileUrl : window.location.origin + fileUrl;
        msgContent = `<a href="${fullUrl}" target="_blank" style="color:inherit;text-decoration:none;display:flex;align-items:center;gap:8px;padding:4px 0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span style="text-decoration:underline;">${esc(d.fileName || 'Download file')}</span>
        </a>`;
    } else if (d.imageData) {
        msgContent = `<img src="${d.imageData}" style="max-width:240px;max-height:200px;border-radius:8px;cursor:pointer;" class="chat-img-preview">`;
    } else {
        msgContent = `<span>${esc(d.message)}</span>`;
    }

    const w = document.createElement('div');
    w.id = 'msg_' + id;
    w.className = 'msg-wrap ' + wrapClass + ' mb-3';
    w.innerHTML = `
        <div class="p-2 px-3 ${bubbleClass}" style="border-radius:${isAgent ? '12px 12px 2px 12px' : '12px 12px 12px 2px'};max-width:100%">
            ${msgContent}
        </div>
        <div class="msg-time">${username} · ${time}</div>
    `;

    if (d.type === 'payment') {
        w.querySelector('div').innerHTML = `
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-1">
                    <i class="ti ti-wallet me-2"></i>
                    <span class="fw-semibold">Payment Link Sent</span>
                </div>
                <p class="mb-1 fs-3">Amount: ${esc(d.currency ?? 'USD')} ${esc(String(d.amount))} (${esc(d.description ?? '')})</p>
                <a href="#" class="text-white text-decoration-underline fs-2">View Link</a>
            </div>
        `;
    }

    area.appendChild(w);
    area.scrollTop = area.scrollHeight;

    w.querySelectorAll('.chat-img-preview').forEach(img => {
        img.addEventListener('click', () => window.open(img.src, '_blank'));
    });
}

// ── Send message ──────────────────────────────────────────────────────────────
async function sendMessage() {
    if (!activeDomainId || !activeVisitorId || !db || isSuperviseMode) return;
    const input = document.getElementById('agent-message-input');
    const msg   = input.value.trim();
    if (!msg && !attachedFile) return;

    const msgsPath = `${activeRoomPath}/messages`;

    if (attachedFile) {
        const fileToSend = attachedFile;
        clearFilePreview();

        try {
            const formData = new FormData();
            formData.append('file', fileToSend);
            formData.append('site_id', SITE_ID);
            formData.append('visitor_id', activeVisitorId);

            const resp = await fetch('/api/widget/upload-file', {
                method: 'POST',
                body: formData
            });
            const result = await resp.json();

            if (result.success && result.file_url) {
                await fbPush(fbRef(db, msgsPath), {
                    uid: AGENT_ID,
                    agent_name: AGENT_NAME,
                    message: result.file_type === 'image' ? '[Image]' : `[File: ${result.file_name}]`,
                    fileUrl: result.file_url,
                    fileType: result.file_type,
                    fileName: result.file_name,
                    timestamp: Date.now(),
                    status: 'sent',
                    sender: 'agent',
                });
            } else {
                if (fileToSend.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = async e => {
                        await fbPush(fbRef(db, msgsPath), {
                            uid: AGENT_ID, agent_name: AGENT_NAME,
                            message: '[Image]', imageData: e.target.result,
                            timestamp: Date.now(), status: 'sent', sender: 'agent',
                        });
                    };
                    reader.readAsDataURL(fileToSend);
                } else {
                    alert('File upload failed. Please try again.');
                }
            }
        } catch (err) {
            console.error('[Upload] Error:', err);
            if (fileToSend.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = async e => {
                    await fbPush(fbRef(db, msgsPath), {
                        uid: AGENT_ID, agent_name: AGENT_NAME,
                        message: '[Image]', imageData: e.target.result,
                        timestamp: Date.now(), status: 'sent', sender: 'agent',
                    });
                };
                reader.readAsDataURL(fileToSend);
            }
        }
    }

    if (!msg) return;
    input.value = '';

    await fbPush(fbRef(db, msgsPath), {
        uid: AGENT_ID, agent_name: AGENT_NAME,
        message: msg, timestamp: Date.now(), status: 'sent', sender: 'agent',
    });

    const metaData = {
        last_message: msg, last_timestamp: Date.now(),
        domain_id: activeDomainId, visitor_id: activeVisitorId,
        site_id: SITE_ID,
        visitor_name: chatRooms[`${activeDomainId}__${activeVisitorId}`]?.visitor_name ?? '',
        assigned_agent: AGENT_ID,
        agent_name:     AGENT_NAME,
        is_active: true,
    };
    fbSet(fbRef(db, `${activeRoomPath}/meta`), metaData);
    fbSet(fbRef(db, `active_chats/${SITE_ID}/${activeDomainId}/${activeVisitorId}`), metaData);
    fbRemove(fbRef(db, `${activeRoomPath}/typing/${AGENT_ID}`));
}

function sendPaymentLink(amount, description, currency) {
    if (!activeDomainId || !db || isSuperviseMode) return;
    fbPush(fbRef(db, `${activeRoomPath}/messages`), {
        uid:         AGENT_ID,
        agent_name:  AGENT_NAME,
        message:     `Payment Link: ${currency} ${amount} — ${description}`,
        type:        'payment',
        amount,
        description,
        currency,
        timestamp:   Date.now(),
        status:      'sent',
        sender:      'agent',
    });
}

function requestVisitorInfo() {
    if (!activeDomainId || !db || isSuperviseMode) return;

    fbSet(fbRef(db, `${activeRoomPath}/info_request`), {
        requested_by: AGENT_NAME,
        requested_at: Date.now(),
        fulfilled:    false,
    });

    fbPush(fbRef(db, `${activeRoomPath}/messages`), {
        uid:       AGENT_ID,
        message:   'Agent requested your name & email',
        type:      'info_request',
        timestamp: Date.now(),
        sender:    'system',
    });
}

function updateVisitorName(domainId, visitorId, name, email) {
    const roomKey = `${domainId}__${visitorId}`;
    if (chatRooms[roomKey]) chatRooms[roomKey].visitor_name = name;
    if (domainId === activeDomainId && visitorId === activeVisitorId) {
        document.getElementById('header-visitor-name').textContent = name;
        if (visitorDetails[activeVisitorId]) {
            visitorDetails[activeVisitorId].name  = name;
            visitorDetails[activeVisitorId].email = email;
        }
        loadVisitorInfo(activeVisitorId);
    }
    renderChatList();
}

function markMessagesRead(domainId, visitorId) {
    if (!db) return;
    const msgsPath = `chats/${domainId}/general/${visitorId}/messages`;
    fbOnValue(fbRef(db, msgsPath), snap => {
        Object.entries(snap.val() || {}).forEach(([id, d]) => {
            if (d.sender === 'visitor' && d.status !== 'read') {
                fbSet(fbRef(db, `${msgsPath}/${id}/status`), 'read');
            }
        });
    }, { onlyOnce: true });
}

function listenToTyping(domainId, visitorId) {
    if (!db) return;
    unsubTyping = fbOnValue(fbRef(db, `chats/${domainId}/general/${visitorId}/typing`), snap => {
        const others = Object.entries(snap.val() || {})
            .filter(([k, v]) => k !== AGENT_ID && v.typing)
            .map(([, v]) => v.name || 'Visitor');
        const indicator = document.getElementById('typing-indicator');
        indicator.innerHTML = others.length
            ? `<span class="text-muted fs-2">${esc(others.join(', '))} is typing
               <span class="typing-dots"><span></span><span></span><span></span></span></span>`
            : '';
    });
}

function listenToVisitorTyping(domainId, visitorId) {
    if (!db) return;
    unsubVisitorTyping = fbOnValue(fbRef(db, `chats/${domainId}/general/${visitorId}/visitor_typing`), snap => {
        const data = snap.val();
        const preview = document.getElementById('visitor-typing-preview');
        const textEl = document.getElementById('visitor-typing-text');
        
        if (data && data.text && data.text.trim()) {
            preview.style.display = 'block';
            textEl.textContent = data.text;
        } else {
            preview.style.display = 'none';
            textEl.textContent = '';
        }
    });
}

function setAgentTyping() {
    if (!activeDomainId || !db || isSuperviseMode) return;
    fbSet(fbRef(db, `${activeRoomPath}/typing/${AGENT_ID}`), {
        name: AGENT_NAME, typing: true
    });
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        fbRemove(fbRef(db, `${activeRoomPath}/typing/${AGENT_ID}`));
    }, 1500);
}

// ── Load visitor info ─────────────────────────────────────────────────────────
function loadVisitorInfo(visitorId) {
    fetch(`${TRAFFIC_URL}/${visitorId}`)
        .then(r => r.json())
        .then(data => {
            // ★ HIDE SKELETON & SHOW CONTENT
            document.getElementById('vi-skeleton').classList.add('d-none');
            document.getElementById('vi-content').classList.remove('d-none');

            const v   = data.visitor;
            const loc = [v.city, v.state, v.country].filter(Boolean).join(', ') || '-';

            if (visitorId === activeVisitorId) {
                activeVisitorInfo = v;
            }

            document.getElementById('vi-name').textContent     = v.name ?? 'Unnamed';
            document.getElementById('vi-location').textContent = loc;
            document.getElementById('vi-email').textContent    = `Email: ${v.email ?? 'Not provided'}`;
            document.getElementById('vi-ip').textContent       = `IP: ${v.ip_address ?? '-'}`;
            document.getElementById('vi-visits').textContent   = `${v.visit_count} visits · ${v.chat_count} chats`;
            document.getElementById('vi-device').textContent   = `Device: ${v.device_type ?? '-'}`;
            document.getElementById('vi-browser').textContent  = `Browser: ${v.browser ?? '-'}`;
            document.getElementById('vi-os').textContent       = `OS: ${v.os ?? '-'}`;
            document.getElementById('vi-lastseen').textContent = `Last seen: ${timeAgo(v.last_seen_at)}`;

            const pages = document.getElementById('vi-pages');
            if (!data.pages.length) {
                pages.innerHTML = '<li class="text-muted">No pages recorded</li>';
            } else {
                pages.innerHTML = data.pages.map(p => `
                    <li title="${esc(p.url)}">
                        <i class="ti ti-point fs-4 me-2 text-primary"></i>
                        <div class="comefrom">
                            <h6>${esc(p.title || shortUrl(p.url))}</h6>
                            <div class="cometime">${p.time_spent}s · ${p.visited_at}</div>
                        </div>
                    </li>
                `).join('');
            }
        })
        .catch(() => {
            // ★ Ensure skeleton hides even if fetch fails
            document.getElementById('vi-skeleton').classList.add('d-none');
            document.getElementById('vi-content').classList.remove('d-none');
        });
}

function updateHeader(v) {
    document.getElementById('header-visitor-name').textContent = v.name ?? 'Unnamed Visitor';
    document.getElementById('header-visitor-page').textContent = shortUrl(v.last_page_url);

    const badge = document.getElementById('header-status-badge');
    const dot   = document.getElementById('header-status-dot');
    if (v.status === 'chatting') {
        badge.textContent = 'Chatting';
        badge.className   = 'visitor-status-badge badge-chatting';
        dot.className     = 'status-dot online-dot position-absolute';
    } else if (v.status === 'left_website') {
        badge.textContent = 'Left website';
        badge.className   = 'visitor-status-badge badge-left';
        dot.className     = 'status-dot offline-dot position-absolute';
    } else {
        badge.textContent = 'Browsing';
        badge.className   = 'visitor-status-badge badge-browsing';
        dot.className     = 'status-dot online-dot position-absolute';
    }
}

function clearFilePreview() {
    attachedFile = null;
    document.getElementById('fileAttachmentInput').value = '';
    const container = document.querySelector('.file-preview-container');
    if (container) { container.innerHTML = ''; container.classList.add('d-none'); }
}

// ── SHORTCUT AUTOCOMPLETE SYSTEM ─────────────────────────────────────
let cannedShortcuts = [];
let shortcutDropdown = null;
let activeShortcutIndex = -1;
let currentHashStartIndex = -1;

function loadChatShortcuts() {
    fetch('{{ route("shortcuts.json") }}')
        .then(res => res.json())
        .then(data => { cannedShortcuts = data; })
        .catch(err => console.error('Error loading shortcuts:', err));
}
loadChatShortcuts();

function createShortcutDropdown() {
    if (document.getElementById('shortcut-dropdown')) return;
    shortcutDropdown = document.createElement('div');
    shortcutDropdown.id = 'shortcut-dropdown';
    const footer = document.querySelector('.chat-send-message-footer');
    if (footer) {
        footer.style.position = 'relative';
        footer.appendChild(shortcutDropdown);
    }
}
createShortcutDropdown();

const agentInput = document.getElementById('agent-message-input');

agentInput.addEventListener('input', function(e) {
    const val = this.value;
    const cursorPos = this.selectionStart;
    const lastHash = val.lastIndexOf('#', cursorPos - 1);

    if (lastHash === -1 || (cursorPos > lastHash + 1 && val[lastHash + 1] === ' ')) {
        hideShortcutDropdown(); return;
    }

    const query = val.substring(lastHash + 1, cursorPos).toLowerCase();
    if (query.length >= 0) {
        currentHashStartIndex = lastHash;
        const filtered = cannedShortcuts.filter(s => s.shortcut.toLowerCase().startsWith('#' + query));
        showShortcutDropdown(filtered);
    } else { hideShortcutDropdown(); }
});

function showShortcutDropdown(items) {
    if (!shortcutDropdown) return;
    if (items.length === 0) {
        shortcutDropdown.innerHTML = `<div class="shortcut-empty">No shortcuts found</div>`;
        shortcutDropdown.style.display = 'block'; activeShortcutIndex = -1; return;
    }
    activeShortcutIndex = 0;
    shortcutDropdown.innerHTML = `
        <div class="shortcut-dropdown-header">Shortcuts</div>
        ${items.map((item, index) => `
            <div class="shortcut-item ${index === 0 ? 'active' : ''}" data-index="${index}" data-response="${item.response_text.replace(/"/g, '&quot;')}">
                <div class="s-icon"><i class="ti ti-command"></i></div>
                <div><strong>${item.shortcut}</strong><small>${item.response_text.substring(0, 60)}${item.response_text.length > 60 ? '...' : ''}</small></div>
            </div>
        `).join('')}
    `;
    shortcutDropdown.style.display = 'block';
    shortcutDropdown.querySelectorAll('.shortcut-item').forEach(el => {
        el.addEventListener('mousedown', function(e) {
            e.preventDefault(); selectShortcut(this.dataset.response);
        });
    });
}

function hideShortcutDropdown() {
    if (shortcutDropdown) shortcutDropdown.style.display = 'none';
    activeShortcutIndex = -1; currentHashStartIndex = -1;
}

agentInput.addEventListener('keydown', function(e) {
    if (!shortcutDropdown || shortcutDropdown.style.display === 'none') return;
    const items = shortcutDropdown.querySelectorAll('.shortcut-item');
    if (items.length === 0) return;

    if (e.key === 'ArrowDown') { e.preventDefault(); activeShortcutIndex = (activeShortcutIndex + 1) % items.length; updateActiveItem(items); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); activeShortcutIndex = (activeShortcutIndex - 1 + items.length) % items.length; updateActiveItem(items); }
    else if (e.key === 'Enter' && activeShortcutIndex >= 0 && currentHashStartIndex !== -1) {
        e.preventDefault(); e.stopImmediatePropagation(); // Prevent chat send
        selectShortcut(items[activeShortcutIndex].dataset.response);
    }
    else if (e.key === 'Escape') { e.preventDefault(); hideShortcutDropdown(); }
});

function updateActiveItem(items) {
    items.forEach((el, i) => el.classList.toggle('active', i === activeShortcutIndex));
    if (items[activeShortcutIndex]) items[activeShortcutIndex].scrollIntoView({ block: 'nearest' });
}

function selectShortcut(responseText) {
    const input = document.getElementById('agent-message-input');
    const val = input.value;
    const cursorPos = input.selectionStart;
    if (currentHashStartIndex === -1) return;

    const before = val.substring(0, currentHashStartIndex);
    const after = val.substring(cursorPos);
    input.value = before + responseText + after;
    const newPos = before.length + responseText.length;
    input.setSelectionRange(newPos, newPos);
    input.focus();
    hideShortcutDropdown();
}

document.addEventListener('click', function(e) {
    if (shortcutDropdown && !shortcutDropdown.contains(e.target) && e.target !== agentInput) {
        hideShortcutDropdown();
    }
});


// ── Bind UI events ────────────────────────────────────────────────────────────
function bindUI() {
    document.getElementById('btn-send-msg').addEventListener('click', sendMessage);

    document.getElementById('agent-message-input').addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    document.getElementById('agent-message-input').addEventListener('input', setAgentTyping);
    document.getElementById('btn-request-info').addEventListener('click', requestVisitorInfo);
   
    document.getElementById('btn-view-traffic').addEventListener('click', () => {
        if (activeVisitorId) window.open(`/admin/traffic?visitor=${activeVisitorId}`, '_blank');
    });

    document.querySelectorAll('.emoji-item').forEach(em => {
        em.addEventListener('click', () => {
            const input = document.getElementById('agent-message-input');
            input.value += em.dataset.emoji;
            input.focus();
            document.querySelector('.emoji-picker-container').classList.remove('active');
        });
    });

    document.querySelector('.emoji-btn').addEventListener('click', () => {
        document.querySelector('.emoji-picker-container').classList.toggle('active');
    });

    document.querySelector('.attachment-btn').addEventListener('click', () => {
        document.getElementById('fileAttachmentInput').click();
    });

    document.getElementById('fileAttachmentInput').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        attachedFile = file;
        const container = document.querySelector('.file-preview-container');
        container.classList.remove('d-none');

        let imageURL = null;
        let previewHTML = `
            <div class="d-flex align-items-center gap-2 p-2">
                <i class="ti ti-paperclip"></i>
                <span class="fs-3">${esc(file.name)}</span>
                <button class="btn-close ms-auto" id="remove-file-btn"></button>
            </div>
        `;

        if (file.type.startsWith('image/')) {
            imageURL = URL.createObjectURL(file);
            previewHTML += `
                <div class="mt-2">
                    <img src="${imageURL}" alt="Preview" style="max-width: 150px; border-radius: 6px;">
                </div>
            `;
        }

        container.innerHTML = previewHTML;

        document.getElementById('remove-file-btn').addEventListener('click', function() {
            clearFilePreview();
            if (imageURL) URL.revokeObjectURL(imageURL);
        });
    });

    document.getElementById('btn-generate-payment').addEventListener('click', () => {
        const amount   = document.getElementById('pay-amount').value;
        const desc     = document.getElementById('pay-desc').value || 'Payment';
        const currency = document.getElementById('pay-currency').value;
        if (!amount) { alert('Please enter an amount.'); return; }
        bootstrap.Modal.getInstance(document.getElementById('paymentLinkModal'))?.hide();
        sendPaymentLink(parseFloat(amount), desc, currency);
    });

    document.getElementById('search-chat-desktop')?.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('.chat-user-item').forEach(el => {
            const name = el.querySelector('h6')?.textContent.toLowerCase() ?? '';
            el.parentElement.style.display = name.includes(val) ? '' : 'none';
        });
    });

    document.querySelector('.chat-menu')?.addEventListener('click', function() {
        document.querySelector('.app-chat-offcanvas').classList.toggle('d-none');
        document.querySelector('.parent-chat-box')?.classList.toggle('app-chat-right');
    });
}

bindUI();

// ── End Chat ────────────────────────────────────────────────────────────────
document.getElementById('btn-end-chat').addEventListener('click', function () {
    if (!activeDomainId || !activeVisitorId) return;
    new bootstrap.Modal(document.getElementById('endChatModal')).show();
});

document.getElementById('btn-confirm-end-chat').addEventListener('click', function () {
    if (!activeDomainId || !activeVisitorId || !db) return;

    this.disabled  = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Ending...';

    fbSet(fbRef(db, `chats/${activeDomainId}/general/${activeVisitorId}/status`), 'ended');
    fbSet(fbRef(db, `active_chats/${SITE_ID}/${activeDomainId}/${activeVisitorId}/is_active`), false);

    if (activeRoomPath) {
        fbSet(fbRef(db, `${activeRoomPath}/meta/is_active`), false);
    }

    fbPush(fbRef(db, `chats/${activeDomainId}/general/${activeVisitorId}/messages`), {
        uid: AGENT_ID,
        message: 'Chat ended by agent',
        type: 'system',
        timestamp: Date.now(),
        sender: 'system',
    });

    bootstrap.Modal.getInstance(document.getElementById('endChatModal'))?.hide();

    const toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 m-3 p-3 bg-warning text-dark rounded shadow';
    toast.style.zIndex = '9999';
    toast.innerHTML = '<i class="ti ti-check me-1"></i>Chat ended successfully';
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);

    const roomKey = `${activeDomainId}__${activeVisitorId}`;
    if (chatRooms[roomKey]) chatRooms[roomKey].is_active = false;
    
    // ★ CLOSE VIEW IMMEDIATELY
    closeActiveChatView();

    this.disabled  = false;
    this.innerHTML = '<i class="ti ti-circle-x me-1"></i>End Chat';
});

// ── Ban Customer from Chat ────────────────────────────────────────────────────

document.getElementById('ban-chat-duration').addEventListener('change', function () {
    const wrap = document.getElementById('ban-chat-custom-end-wrap');
    wrap.classList.toggle('d-none', this.value !== 'custom');
});

document.getElementById('btn-ban-chat').addEventListener('click', async function () {
    if (!activeVisitorId) return;

    let ip = activeVisitorInfo?.ip_address
          || visitorDetails[activeVisitorId]?.ip_address
          || null;

    if (!ip) {
        try {
            const r = await fetch(`${TRAFFIC_URL}/${activeVisitorId}`);
            const d = await r.json();
            ip = d.visitor?.ip_address || null;
            if (d.visitor) activeVisitorInfo = d.visitor;
        } catch (_) {}
    }

    document.getElementById('ban-chat-ip').value        = ip || '-';
    document.getElementById('ban-chat-visitor-id').value = activeVisitorId;
    document.getElementById('ban-chat-reason').value     = '';
    document.getElementById('ban-chat-duration').value   = '7';
    document.getElementById('ban-chat-custom-end-wrap').classList.add('d-none');

    new bootstrap.Modal(document.getElementById('banChatModal')).show();
});

document.getElementById('btn-confirm-ban-chat').addEventListener('click', async function () {
    const ipRaw      = document.getElementById('ban-chat-ip').value;
    const visitorId  = document.getElementById('ban-chat-visitor-id').value;
    const reason     = document.getElementById('ban-chat-reason').value.trim();
    const duration   = document.getElementById('ban-chat-duration').value;
    const customEnd  = document.getElementById('ban-chat-custom-end').value || null;

    this.disabled  = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Banning...';

    try {
        const res = await fetch('{{ route("banned-customers.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                ip_address:  ipRaw === '-' ? null : ipRaw,
                visitor_id:  visitorId,
                chat_id:     visitorId,
                reason:      reason || null,
                duration:    duration,
                custom_end:  duration === 'custom' ? customEnd : null,
            }),
        });

        const data = await res.json();

        if (!res.ok) {
            alert(data.message || 'Failed to ban customer.');
            return;
        }

        if (db && activeDomainId) {
            fbSet(fbRef(db, `active_chats/${SITE_ID}/${activeDomainId}/${activeVisitorId}/is_active`), false);
            fbSet(fbRef(db, `banned_visitors/${SITE_ID}/${activeVisitorId}`), true);
            fbSet(fbRef(db, `${activeRoomPath}/meta/is_active`), false);
        }

        bootstrap.Modal.getInstance(document.getElementById('banChatModal'))?.hide();

        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 m-3 p-3 bg-success text-white rounded shadow';
        toast.style.zIndex = '9999';
        toast.innerHTML = '<i class="ti ti-check me-1"></i>Visitor banned successfully';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);

        const roomKey = `${activeDomainId}__${activeVisitorId}`;
        if (chatRooms[roomKey]) chatRooms[roomKey].is_active = false;
        
        // ★ CLOSE VIEW IMMEDIATELY
        closeActiveChatView();

    } catch (err) {
        console.error('[Ban] Error:', err);
        alert('Something went wrong. Please try again.');
    } finally {
        this.disabled  = false;
        this.innerHTML = '<i class="ti ti-ban me-1"></i>Ban Visitor';
    }
});

// ── Transfer Chat ────────────────────────────────────────────────────────────
let dbAgents = [];

async function fetchAgentsFromDB() {
    try {
        const response = await fetch('{{ route("agents.list") }}');
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        dbAgents = data.agents || [];
    } catch (error) {
        console.error('[Transfer] Error fetching agents:', error);
        dbAgents = [];
    }
}

async function renderTransferAgentList() {
    const listEl = document.getElementById('transfer-agent-list');
    if (!listEl) return;

    listEl.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading agents...</div>';

    await fetchAgentsFromDB();

    const agents = dbAgents;

    if (!agents.length) {
        listEl.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="ti ti-users-minus fs-1 d-block mb-2 opacity-50"></i>
                <h6 class="fw-semibold text-dark">No other agents found</h6>
                <div class="fs-3 mt-1 opacity-75">You are the only agent assigned to this site.</div>
            </div>`;
        return;
    }

    listEl.innerHTML = agents.map(agent => {
        const statusColor = agent.is_online ? '#22c55e' : '#adb5bd';
        const statusText = agent.is_online ? 'Online' : `Last seen ${agent.last_seen}`;
        const avatarClass = agent.is_online ? 'bg-primary-soft' : 'bg-light text-secondary';
        
        return `
        <label class="d-flex align-items-center gap-3 p-3 mb-2 cursor-pointer transfer-agent-item" style="transition: all 0.2s;">
            <input type="radio" name="transfer-target" value="agent_${agent.id}" class="form-check-input mt-0" style="width:18px; height:18px; accent-color: #6366f1;">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div class="position-relative">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold ${avatarClass}" style="width:40px;height:40px;font-size:15px;">
                        ${esc(agent.name).charAt(0).toUpperCase()}
                    </div>
                    <span style="position:absolute;bottom:0;right:0;width:11px;height:11px;background:${statusColor};border-radius:50%;border:2px solid #fff;"></span>
                </div>
                <div>
                    <div class="fw-semibold text-dark" style="font-size:15px;">${esc(agent.name)}</div>
                    <small class="text-muted d-block" style="font-size: 12px;">${statusText}</small>
                </div>
            </div>
        </label>
    `}).join('');
}

document.getElementById('btn-transfer-chat')?.addEventListener('click', () => {
    if (!activeDomainId || !activeVisitorId) return;
    document.getElementById('transfer-summary').value = '';
    document.getElementById('transfer-note').value = '';
    new bootstrap.Modal(document.getElementById('transferChatModal')).show();
    renderTransferAgentList();
});

document.getElementById('btn-confirm-transfer')?.addEventListener('click', async function() {
    const selected = document.querySelector('input[name="transfer-target"]:checked');
    if (!selected) {
        alert('Please select an agent to transfer to.');
        return;
    }

    const target = selected.value;
    const summary = document.getElementById('transfer-summary').value.trim();
    const note = document.getElementById('transfer-note').value.trim();

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Transferring...';

    try {
        const newAgentId = target.startsWith('agent_') ? target.replace('agent_', '') : null;
        const selectedAgent = dbAgents.find(a => String(a.id) === String(newAgentId));
        const newAgentName = selectedAgent ? selectedAgent.name : 'Agent';

        const assignResponse = await fetch('/api/visitor/assign', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
            },
            body: JSON.stringify({ 
                visitor_id: activeVisitorId, 
                agent_id: newAgentId 
            })
        });

        if (!assignResponse.ok) {
            throw new Error('Failed to assign agent in database');
        }

        if (db && activeRoomPath) {
            await fbPush(fbRef(db, `${activeRoomPath}/messages`), {
                uid: AGENT_ID,
                message: `Chat transferred by ${AGENT_NAME}`,
                type: 'system',
                timestamp: Date.now(),
                sender: 'system',
            });

            const metaData = {
                last_message: `Chat transferred to ${newAgentName}`,
                last_timestamp: Date.now(),
                domain_id: activeDomainId,
                visitor_id: activeVisitorId,
                site_id: SITE_ID,
                visitor_name: chatRooms[`${activeDomainId}__${activeVisitorId}`]?.visitor_name ?? '',
                assigned_agent: newAgentId,
                agent_name: newAgentName,
                is_active: true,
                transfer_summary: summary,
                transfer_note: note,
            };

            fbSet(fbRef(db, `${activeRoomPath}/meta`), metaData);
            fbSet(fbRef(db, `active_chats/${SITE_ID}/${activeDomainId}/${activeVisitorId}`), metaData);
        }

        bootstrap.Modal.getInstance(document.getElementById('transferChatModal'))?.hide();

        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 m-3 p-3 rounded shadow';
        toast.style.cssText = 'z-index:9999;background:#22c55e;color:#fff;';
        toast.innerHTML = '<i class="ti ti-check me-1"></i>Chat transferred successfully';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);

    } catch (err) {
        console.error('[Transfer] Error:', err);
        alert('Transfer failed. Please try again.');
    } finally {
        this.disabled = false;
        this.innerHTML = '<i class="ti ti-arrows-exchange me-1"></i> Transfer Chat';
    }
});

})();
</script>
@endpush