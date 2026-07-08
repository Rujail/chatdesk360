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
</style>
@endpush

@section('content')
<div class="body-wrapper">
    <div class="container-fluid mw-100">
        <div class="card overflow-hidden chat-application mb-0" data-chat-mode="archive">

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
                <div class="w-30 d-none d-lg-flex flex-column border-end user-chat-box">
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
                    <div class="flex-grow-1 overflow-auto">
                        <div class="px-3 py-2">
                            <span class="text-muted fw-semibold fs-2">
                                Archive Chats (<span id="chat-list-count">0</span>)
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
                <div class="w-70 w-xs-100 d-flex flex-column chat-container">

                    {{-- No chat selected --}}
                    <div class="no-chat-selected flex-grow-1" id="no-chat-selected">
                        <i class="ti ti-message-dots" style="font-size:48px; opacity:.3"></i>
                        <span>Select a visitor to start chatting</span>
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
                                                href="javascript:void(0)" id="btn-stop-chat">
                                                <i class="ti ti-x"></i> Stop this chat
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
                        <div class="flex-grow-1 overflow-auto p-9" id="messages-area">
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
                <div class="app-chat-offcanvas border-start" id="visitor-info-panel">
                    <div class="offcanvas-body p-8">
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

@endsection
@push('scripts')
<script>
(function () {
'use strict';

// ── Config ────────────────────────────────────────────────────────────────────

// const SITE_ID     = '{{ Auth::user()->site_id }}';
const rawSiteId = '{{ Auth::user()->site_id }}';
const SITE_ID = rawSiteId.startsWith('site_') ? rawSiteId : 'site_' + rawSiteId;

const AGENT_ID    = '{{ Auth::id() }}';
const AGENT_NAME  = '{{ Auth::user()->name }}';
const USER_ROLE   = '{{ Auth::user()->role }}';

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

// ✅ FIXED: Changed to window.chatRooms so you can check in F12 Console
window.chatRooms = {}; 

let typingTimer     = null;
let attachedFile    = null;
let renderedMsgIds  = new Set();
let unsubMessages   = null;
let isSuperviseMode = false;

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
        getDatabase, ref, push, set, remove,
        onChildAdded, onChildChanged, onValue,
        query, orderByChild, limitToLast, onDisconnect, get
    } = dbMod;

    fbRef = ref; fbPush = push; fbSet = set; fbRemove = remove;
    fbOnChildAdded = onChildAdded; fbOnChildChanged = onChildChanged;
    fbOnValue = onValue; fbQuery = query; fbOrderByChild = orderByChild;
    fbLimitToLast = limitToLast; fbOnDisconnect = onDisconnect; fbGet = get;

    const app = initializeApp(FB_CONFIG);
    db = getDatabase(app);

    const agentPresRef = fbRef(db, `agent_presence/${SITE_ID}/${AGENT_ID}`);
    fbSet(agentPresRef, { name: AGENT_NAME, online: true, ts: Date.now() });
    fbOnDisconnect(agentPresRef).remove();

    listenToChatRooms();
    pollVisitorList();
    setInterval(pollVisitorList, 15000);

    const urlParams = new URLSearchParams(window.location.search);
    const targetVisitor = urlParams.get('visitor');
    const mode = urlParams.get('mode');
    if (mode === 'supervise') isSuperviseMode = true;
    if (targetVisitor) setTimeout(() => openChatByVisitorId(targetVisitor), 2000);
    

}).catch(err => {
    console.error('[Chat] Firebase init failed:', err);
    pollVisitorList();
    setInterval(pollVisitorList, 15000);
});




// ── Listen to chat rooms ──────────────────────────────────────────────────────
function listenToChatRooms() {
    // console.log("Attempting to listen to path: active_chats/" + SITE_ID);
    
    fbOnValue(fbRef(db, `active_chats/${SITE_ID}`), snap => {
        // console.log("Firebase Response Received!");
        const domains = snap.val();
        
        if (!domains) {
            console.warn("Firebase returned NULL. This means the SITE_ID is wrong or the folder is empty.");
            return;
        }
        
        // console.log("Data found! Processing domains...");
        window.chatRooms = {}; 

        Object.entries(domains).forEach(([domainId, visitors]) => {
            // console.log("Processing domain:", domainId);
            if (!visitors || typeof visitors !== 'object') return;
            
            Object.entries(visitors).forEach(([visitorId, data]) => {

                const roomKey = `${domainId}__${visitorId}`;
                // console.log("Adding visitor to list:", visitorId);
                window.chatRooms[roomKey] = {
                    room_key:     roomKey,
                    domain_id:    domainId,
                    visitor_id:   visitorId,
                    visitor_name: data.visitor_name ?? 'Unnamed',
                    last_message: data.last_message ?? '',
                    last_ts:      data.last_timestamp ?? 0,
                    unread:       0,
                    is_active:    data.is_active !== false,
                    // assigned_agent: data.assigned_agent ?? null,
                    assigned_agent: data.assigned_agent ?? window.chatRooms[roomKey]?.assigned_agent ?? null,
                };
            });
        });
        
        // console.log("Final chatRooms object:", window.chatRooms);
        renderChatList();
    }, (error) => {
        console.error("Firebase Permission Error:", error);
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
                let domainId = 'unknown';
                if (v.last_page_url) {
                    try { domainId = new URL(v.last_page_url).hostname.replace(/\./g, '_'); } catch {}
                }
                const roomKey = `${domainId}__${v.visitor_id}`;
                if (!window.chatRooms[roomKey]) {
                    window.chatRooms[roomKey] = {
                        room_key:     roomKey,
                        domain_id:    domainId,
                        visitor_id:   v.visitor_id,
                        visitor_name: v.name,
                        last_message: '',
                        last_ts:      0,
                        unread:       0,
                        is_active: true,
                        assigned_agent: v.assign_userID ?? null,
                    };
                } else {
                    // ✅ ADD THIS BLOCK: Always update from DB if it exists
                    window.chatRooms[roomKey].visitor_name = v.name || window.chatRooms[roomKey].visitor_name;
                    if (v.assign_userID !== undefined && v.assign_userID !== null) {
                        window.chatRooms[roomKey].assigned_agent = v.assign_userID;
                    }
                }
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
    const isArchivePage = document.querySelector('[data-chat-mode="archive"]') !== null;
    
    // ✅ Case-insensitive check + allow unassigned chats to be seen by agents
    const hasAccess = (r) => USER_ROLE.toLowerCase() === 'admin' 
                             || !r.assigned_agent 
                             || String(r.assigned_agent) === String(AGENT_ID);

    let filteredRooms = [];
    if (isArchivePage) {
        filteredRooms = Object.values(window.chatRooms).filter(r => {
            const isArchived = r.is_active === false;
            return isArchived && hasAccess(r);
        });
    } else {
        filteredRooms = Object.values(window.chatRooms).filter(r => {
            const isActive = r.is_active !== false;
            return isActive && hasAccess(r);
        });
    }

    filteredRooms.sort((a, b) => b.last_ts - a.last_ts);
    document.getElementById('chat-list-count').textContent = filteredRooms.length;

    const listEl = document.getElementById('chat-user-list');
    if (!listEl) return;

    filteredRooms.forEach((r, index) => {
        // console.log(r);
        const roomKey = r.room_key;
        let itemEl = document.getElementById(`chat-item-${roomKey}`);
        
        const vd = visitorDetails[r.visitor_id] ?? {};
        const isActiveChat = r.room_key === `${activeDomainId}__${activeVisitorId}`;
        const isArchived = r.is_active === false;
        const statusClass = (!isArchived && vd.status === 'chatting') ? 'online-dot' : 'offline-dot';
        const lastMsg = r.last_message
            ? (r.last_message.length > 30 ? r.last_message.slice(0, 30) + '…' : r.last_message)
            : (isArchived ? 'Chat ended' : 'Chat started');

        if (!itemEl) {
            // FIRST TIME: Create the structure once
            const li = document.createElement('li');
            li.id = `chat-item-${roomKey}`;
            li.innerHTML = `
                <a href="javascript:void(0)" class="px-4 py-3 d-flex align-items-start justify-content-between chat-user-item">
                    <div class="d-flex align-items-center">
                        <span class="position-relative">
                            <iconify-icon icon="solar:user-circle-linear" width="30" height="30"></iconify-icon>
                            <span class="status-dot position-absolute"></span>
                        </span>
                        <div class="ms-3" style="max-width: 160px;">
                            <h6 class="mb-0 fw-semibold text-truncate visitor-name"></h6>
                            <span class="fs-3 text-truncate text-muted d-block last-msg"></span>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="unread-badge-container"></span>
                        <p class="fs-2 mb-0 text-muted mt-1 msg-time"></p>
                    </div>
                </a>`;
            
            // Bind click event once
            li.querySelector('a').onclick = function() {
                openChat(r.domain_id, r.visitor_id);
            };
            
            listEl.appendChild(li);
            itemEl = li;
        }

        // --- TARGETED UPDATES (No innerHTML here!) ---
        const link = itemEl.querySelector('a');
        const nameEl = itemEl.querySelector('.visitor-name');
        const msgEl = itemEl.querySelector('.last-msg');
        const timeEl = itemEl.querySelector('.msg-time');
        const dotEl = itemEl.querySelector('.status-dot');
        const badgeCont = itemEl.querySelector('.unread-badge-container');

        // 1. Update Active Class
        link.classList.toggle('active', isActiveChat);
        link.classList.toggle('opacity-75', isArchived);

        // 2. Update Name & Badge (Only if changed)
        const nameText = `${esc(r.visitor_name)} ${isArchived ? '<span class="badge bg-secondary" style="font-size:9px">archived</span>' : ''}`;
        if (nameEl.innerHTML !== nameText) nameEl.innerHTML = nameText;

        // 3. Update Message Text (Only if changed)
        if (msgEl.textContent !== lastMsg) msgEl.textContent = lastMsg;

        // 4. Update Time (Only if changed)
        const timeText = fmtTime(r.last_ts);
        if (timeEl.textContent !== timeText) timeEl.textContent = timeText;

        // 5. Update Status Dot
        if (dotEl.className !== `status-dot ${statusClass} position-absolute`) {
            dotEl.className = `status-dot ${statusClass} position-absolute`;
        }

        // 6. Update Unread Badge (Smoothly add/remove)
        if (r.unread > 0) {
            if (badgeCont.innerHTML !== `<span class="unread-badge">${r.unread}</span>`) {
                badgeCont.innerHTML = `<span class="unread-badge">${r.unread}</span>`;
            }
        } else {
            badgeCont.innerHTML = '';
        }
    });

    // Remove gone rooms
    const currentIds = filteredRooms.map(r => `chat-item-${r.room_key}`);
    Array.from(listEl.children).forEach(child => {
        if (!currentIds.includes(child.id)) child.remove();
    });

    const loadingEl = document.getElementById('chat-list-loading');
    if (loadingEl) loadingEl.style.display = 'none';
}

// ── Open chat ─────────────────────────────────────────────────────────────────
function openChat(domainId, visitorId) {
    if (typeof unsubMessages === 'function') unsubMessages();
    renderedMsgIds.clear();

    activeDomainId  = domainId;
    activeVisitorId = visitorId;
    activeRoomPath  = `chats/${domainId}/general/${visitorId}`;

    document.getElementById('no-chat-selected').style.display = 'none';
    document.getElementById('active-chat-area').style.display = 'flex';
    document.getElementById('messages-area').innerHTML = '';

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
    if (window.chatRooms[roomKey]) window.chatRooms[roomKey].unread = 0;

    document.querySelectorAll('.chat-user-item').forEach(el => {
        el.classList.toggle('active', el.dataset.room === roomKey);
    });

    loadVisitorInfo(visitorId);

    const vd = visitorDetails[visitorId];
    if (vd) updateHeader(vd);
    else {
        const room = window.chatRooms[roomKey];
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
    }
}

async function checkAndSendWelcome(domainId, visitorId) {
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
    }
}

function openChatByVisitorId(visitorId) {
    const room = Object.values(window.chatRooms).find(r => r.visitor_id === visitorId);
    if (room) {
        openChat(room.domain_id, visitorId);
    } else {
        const vd = visitorDetails[visitorId];
        let domainId = 'unknown';
        if (vd?.last_page_url) {
            try { domainId = new URL(vd.last_page_url).hostname.replace(/\./g, '_'); } catch {}
        }
        openChat(domainId, visitorId);
    }
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
        const d  = snap.val();
        const id = snap.key;
        if (renderedMsgIds.has(id)) return;
        renderedMsgIds.add(id);
        renderMessage(id, d);

        const roomKey = `${domainId}__${visitorId}`;
        if (window.chatRooms[roomKey]) {
            window.chatRooms[roomKey].last_message = d.message ?? '';
            window.chatRooms[roomKey].last_ts      = d.timestamp;
            
            if (activeDomainId !== domainId || activeVisitorId !== visitorId) {
                if (d.sender === 'visitor') {
                    window.chatRooms[roomKey].unread = (window.chatRooms[roomKey].unread ?? 0) + 1;
                }
            }
            
            // ✅ Use requestAnimationFrame to prevent UI locking/jerking
            requestAnimationFrame(() => renderChatList());
        }
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

    // ── IMAGE / FILE CONTENT ──
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

    // Image click to open in new tab
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
        if (attachedFile.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = async e => {
                await fbPush(fbRef(db, msgsPath), {
                    uid: AGENT_ID, agent_name: AGENT_NAME,
                    message: '[Image]', imageData: e.target.result,
                    timestamp: Date.now(), status: 'sent', sender: 'agent',
                });
            };
            reader.readAsDataURL(attachedFile);
        } else {
            await fbPush(fbRef(db, msgsPath), {
                uid: AGENT_ID, agent_name: AGENT_NAME,
                message: `[File: ${attachedFile.name}]`,
                timestamp: Date.now(), status: 'sent', sender: 'agent',
            });
        }
        clearFilePreview();
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
        visitor_name: window.chatRooms[`${activeDomainId}__${activeVisitorId}`]?.visitor_name ?? '',
        assigned_agent: AGENT_ID,
        is_active: true,
    };
    fbSet(fbRef(db, `${activeRoomPath}/meta`), metaData);
    fbSet(fbRef(db, `active_chats/${SITE_ID}/${activeDomainId}/${activeVisitorId}`), metaData);

    fbRemove(fbRef(db, `${activeRoomPath}/typing/${AGENT_ID}`));
}

// ── Send payment link ─────────────────────────────────────────────────────────
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

// ── Request visitor info ──────────────────────────────────────────────────────
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

// ── Update visitor name ───────────────────────────────────────────────────────
function updateVisitorName(domainId, visitorId, name, email) {
    const roomKey = `${domainId}__${visitorId}`;
    if (window.chatRooms[roomKey]) window.chatRooms[roomKey].visitor_name = name;
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

// ── Mark read ─────────────────────────────────────────────────────────────────
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

// ── Listen to typing (other agents) ───────────────────────────────────────────
function listenToTyping(domainId, visitorId) {
    if (!db) return;
    fbOnValue(fbRef(db, `chats/${domainId}/general/${visitorId}/typing`), snap => {
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

// ── NEW: Listen to visitor typing (live preview) ──────────────────────────────
function listenToVisitorTyping(domainId, visitorId) {
    if (!db) return;
    fbOnValue(fbRef(db, `chats/${domainId}/general/${visitorId}/visitor_typing`), snap => {
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

// ── Agent typing ──────────────────────────────────────────────────────────────
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
            const v   = data.visitor;
            const loc = [v.city, v.state, v.country].filter(Boolean).join(', ') || '-';

            document.getElementById('vi-name').textContent     = v.name ?? 'Unnamed';
            document.getElementById('vi-location').textContent = loc;
            document.getElementById('vi-email').textContent    = `Email: ${v.email ?? 'Not provided'}`;
            document.getElementById('vi-ip').textContent       = `IP: ${v.ip_address ?? '-'}`;
            document.getElementById('vi-visits').textContent   = `${v.visit_count} visits · ${v.chat_count} chats`;
            document.getElementById('vi-device').textContent   = `Device: ${v.device_type ?? '-'}`;
            document.getElementById('vi-browser').textContent  = `Browser: ${v.browser ?? '-'}`;
            document.getElementById('vi-os').textContent       = `OS: ${v.os ?? '-'}`;
            document.getElementById('vi-lastseen').textContent = `Last seen: ${v.last_seen_at ?? '-'}`;

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
        .catch(() => {});
}

// ── Update header ─────────────────────────────────────────────────────────────
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

// ── File handling ─────────────────────────────────────────────────────────────
function clearFilePreview() {
    attachedFile = null;
    document.getElementById('fileAttachmentInput').value = '';
    const container = document.querySelector('.file-preview-container');
    if (container) { container.innerHTML = ''; container.classList.add('d-none'); }
}

// ── Bind UI events ────────────────────────────────────────────────────────────
function bindUI() {
    // Send button
    document.getElementById('btn-send-msg').addEventListener('click', sendMessage);

    // Enter key — FIXED
    document.getElementById('agent-message-input').addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Typing
    document.getElementById('agent-message-input').addEventListener('input', setAgentTyping);

    // Request info
    document.getElementById('btn-request-info').addEventListener('click', requestVisitorInfo);

    // Stop chat
    document.getElementById('btn-stop-chat').addEventListener('click', () => {
        if (!activeDomainId || !db || isSuperviseMode) return;
        fbPush(fbRef(db, `${activeRoomPath}/messages`), {
            message:   'Agent ended the chat',
            type:      'system',
            timestamp: Date.now(),
            sender:    'system',
        });
    });

    // View in traffic
    document.getElementById('btn-view-traffic').addEventListener('click', () => {
        if (activeVisitorId) window.open(`/admin/traffic?visitor=${activeVisitorId}`, '_blank');
    });

    // Emoji — FIXED
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

    // File attachment
    document.querySelector('.attachment-btn').addEventListener('click', () => {
        document.getElementById('fileAttachmentInput').click();
    });

    document.getElementById('fileAttachmentInput').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        attachedFile = file;

        const container = document.querySelector('.file-preview-container');
        container.classList.remove('d-none');

        let imageURL = null; // 👈 define in outer scope

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

            // 👇 now it's accessible
            if (imageURL) {
                URL.revokeObjectURL(imageURL);
            }
        });
    });

    // Payment link
    document.getElementById('btn-generate-payment').addEventListener('click', () => {
        const amount   = document.getElementById('pay-amount').value;
        const desc     = document.getElementById('pay-desc').value || 'Payment';
        const currency = document.getElementById('pay-currency').value;
        if (!amount) { alert('Please enter an amount.'); return; }
        bootstrap.Modal.getInstance(document.getElementById('paymentLinkModal'))?.hide();
        sendPaymentLink(parseFloat(amount), desc, currency);
    });

    // Search
    document.getElementById('search-chat-desktop')?.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('.chat-user-item').forEach(el => {
            const name = el.querySelector('h6')?.textContent.toLowerCase() ?? '';
            el.parentElement.style.display = name.includes(val) ? '' : 'none';
        });
    });

    // Visitor info panel toggle
    document.querySelector('.chat-menu')?.addEventListener('click', function() {
        document.querySelector('.app-chat-offcanvas').classList.toggle('d-none');
        document.querySelector('.parent-chat-box')?.classList.toggle('app-chat-right');
    });
}

bindUI();

})();
</script>
@endpush