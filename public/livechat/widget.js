(function () {
    'use strict';

    /* ── 0. Read site_id from window.__cd ───────────────── */
    const SITE_ID = (window.__cd && window.__cd.site_id) ? window.__cd.site_id : null;

    if (!SITE_ID) {
        console.warn('[ChatDesk360] window.__cd.site_id missing. Widget not loaded.');
        return;
    }

    /* ── 1. API base — widget.js ke src se auto detect ──── */
    // widget.js jis domain pe hai usi se API call hogi
    const scripts   = document.querySelectorAll('script[src]');
    let API_BASE    = '';
    scripts.forEach(s => {
        if (s.src && s.src.includes('widget.js')) {
            const u  = new URL(s.src);
            API_BASE = u.origin + '/api';
        }
    });
    // Fallback: agar detect na ho
    if (!API_BASE) API_BASE = window.__cd.api || '';

    /* ── 2. Visitor ID ───────────────────────────────────── */
    let visitorId = localStorage.getItem('lc_visitor_id');
    if (!visitorId) {
        visitorId = 'v_' + Math.random().toString(36).substr(2, 12) + '_' + Date.now();
        localStorage.setItem('lc_visitor_id', visitorId);
    }

    /* ── 3. IDs ──────────────────────────────────────────── */
    const DOMAIN_ID = window.location.hostname.replace(/\./g, '_');
    const CONV_ID   = 'general';

    /* ── 4. Track visitor ────────────────────────────────── */
    function trackVisitor(extra = {}) {
        if (!API_BASE) return;
        fetch(API_BASE + '/visitor/track', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body:    JSON.stringify({
                visitor_id:   visitorId,
                site_id:      SITE_ID,
                page_url:     window.location.href,
                referrer_url: document.referrer || null,
                ...extra
            })
        }).catch(() => {});
    }
    trackVisitor();

    /* ── 5. Inject CSS ───────────────────────────────────── */
    const CSS = `
#cd-wrap * { box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
#cd-wrap {
    --primary:       #2b60d0;
    --primary-hover: #1f4cb8;
    --popup-bg:      #f7f7f7;
    --popup-text:    #1f2937;
    --bot-bg:        #ffffff;
    --bot-text:      #1f2937;
    --user-bg:       var(--primary);
    --user-text:     #ffffff;
    --header-icon:   #1f2937;
    --input-bg:      #ffffff;
    --input-text:    #374151;
    --footer-bg:     #ffffff;
    --footer-icon:   #9ca3af;
    --bubble-btn-bg: #1f2937;
    --bubble-btn-ic: #ffffff;
    --muted:         #9ca3af;
    --success:       #10b981;
    --shadow:        rgba(0,0,0,0.15);
}
#cd-wrap.dark {
    --popup-bg:      #1e1e1e;
    --popup-text:    #f5f5f5;
    --bot-bg:        #2b2b2b;
    --bot-text:      #e6e6e6;
    --header-icon:   #ffffff;
    --input-bg:      #2b2b2b;
    --input-text:    #f2f2f2;
    --footer-bg:     #2b2b2b;
    --footer-icon:   #e6e6e6;
    --bubble-btn-bg: #111111;
    --shadow:        rgba(0,0,0,0.6);
}
#cd-wrap svg { color:var(--header-icon); }


#cd-wrap svg, #chat-popup svg, #chat-widget-container svg {
    color: var(--header-icon);
    stroke: var(--header-icon) !important;
}

#cd-bubble svg {
    width: 32px;
    height: 32px;
    color: var(--bubble-button-icon);
    stroke: var(--bubble-button-icon) !important;
}

#cd-bubble {
    position:fixed; bottom:20px; right:20px; z-index:9999;
    width:60px; height:60px; border-radius:50%;
    background:var(--bubble-btn-bg);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,0.2);
    transition:transform .2s; border:none;
}
#cd-bubble:hover { transform:scale(1.07); }
#cd-bubble svg { width:28px; height:28px; color:var(--bubble-btn-ic); stroke:var(--bubble-btn-ic)!important; }
#cd-badge {
    position:absolute; top:-4px; right:-4px;
    background:#ef4444; color:#fff; border-radius:50%;
    width:20px; height:20px; font-size:11px; font-weight:700;
    display:none; align-items:center; justify-content:center; border:2px solid #fff;
}
#cd-badge.show { display:flex; }
#cd-popup {
    position:fixed; bottom:90px; right:20px; z-index:9998;
    width:380px; height:650px; max-height:85vh;
    background:var(--popup-bg); color:var(--popup-text);
    border-radius:12px; box-shadow:0 5px 25px var(--shadow);
    display:flex; flex-direction:column; overflow:hidden;
    transform:scale(0.88) translateY(20px); opacity:0; pointer-events:none;
    transition:transform .25s ease,opacity .25s ease;
    transform-origin:bottom right;
}
#cd-popup.open { transform:scale(1) translateY(0); opacity:1; pointer-events:all; }
#cd-home { flex:1; display:flex; flex-direction:column; padding:20px; background:var(--popup-bg); }
.cd-home-header { font-size:28px; font-weight:300; color:var(--popup-text); margin-top:20px; }
.cd-home-title  { font-size:36px; font-weight:700; color:var(--popup-text); margin-bottom:30px; }
.cd-admin-card  { background:var(--bot-bg); padding:20px; border-radius:12px; box-shadow:0 4px 8px rgba(0,0,0,0.04); color:var(--bot-text); }
.cd-admin-info  { font-size:14px; color:var(--bot-text); margin:15px 0; }
.cd-start-btn {
    width:100%; padding:12px; background:var(--primary); color:#fff;
    border:none; border-radius:8px; font-size:16px; font-weight:600;
    cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:background .15s;
}
.cd-start-btn:hover { background:var(--primary-hover); }
.cd-start-btn svg { transform:rotate(90deg); stroke:#fff!important; }
.cd-admin-badge { background:var(--bot-bg); padding:4px 7px; border-radius:20px; display:flex; align-items:center; gap:8px; }
.cd-avatar { width:28px; height:28px; background:#6a5acd; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; position:relative; }
.cd-status-dot { position:absolute; bottom:0; right:0; width:8px; height:8px; background:var(--success); border-radius:50%; border:2px solid #fff; }
.cd-admin-name { font-weight:600; font-size:14px; color:var(--popup-text); }
#cd-conv { display:flex; flex-direction:column; height:100%; flex:1; }
#cd-header {
    display:flex; justify-content:space-between; align-items:center;
    padding:15px 15px 40px; background:var(--popup-bg);
    mask:linear-gradient(black 70%,rgba(0,0,0,0) 100%);
    backdrop-filter:blur(8px); z-index:10;
}
.cd-hdr-side { display:flex; gap:8px; }
.cd-icon-btn { background:none; border:none; cursor:pointer; padding:6px; color:var(--header-icon); display:flex; align-items:center; justify-content:center; border-radius:50%; transition:background .12s; }
.cd-icon-btn:hover { background:rgba(0,0,0,0.06); }
.cd-icon-btn svg { stroke:var(--header-icon)!important; }
#cd-options-menu { position:absolute; top:60px; left:20px; width:220px; background:var(--bot-bg); border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); padding:10px; z-index:200; }
.cd-opt-item { display:flex; align-items:center; padding:12px; color:var(--bot-text); cursor:pointer; border-radius:8px; font-size:14px; gap:10px; transition:background .12s; }
.cd-opt-item:hover { background:rgba(0,0,0,0.04); }
.cd-opt-item span:nth-child(2) { flex:1; }
.cd-switch { position:relative; display:inline-block; width:36px; height:20px; }
.cd-switch input { opacity:0; width:0; height:0; }
.cd-slider { position:absolute; cursor:pointer; inset:0; background:#bfc6cc; transition:.3s; border-radius:999px; }
.cd-slider:before { position:absolute; content:''; height:16px; width:16px; left:2px; bottom:2px; background:#fff; transition:.3s; border-radius:50%; }
.cd-switch input:checked + .cd-slider { background:var(--primary); }
.cd-switch input:checked + .cd-slider:before { transform:translateX(16px); }
#cd-name-bar { padding:6px 16px; background:var(--popup-bg); border-bottom:1px solid rgba(0,0,0,0.06); display:flex; align-items:center; gap:8px; }
#cd-name-bar label { font-size:11px; color:var(--muted); white-space:nowrap; }
#cd-name-input { flex:1; padding:5px 9px; border:1px solid rgba(0,0,0,0.12); border-radius:8px; font-size:13px; outline:none; background:var(--input-bg); color:var(--input-text); }
#cd-name-input:focus { border-color:var(--primary); }
#cd-messages { flex:1; display:flex; flex-direction:column; gap:15px; padding:20px; overflow-y:auto; overscroll-behavior:contain; }
#cd-messages::-webkit-scrollbar { width:4px; }
#cd-messages::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:4px; }
.cd-msg-wrap { display:flex; flex-direction:column; max-width:80%; }
.cd-msg-wrap.user { align-self:flex-end; align-items:flex-end; }
.cd-msg-wrap.bot  { align-self:flex-start; }
.cd-bubble-msg { padding:12px 16px; font-size:14px; line-height:1.4; word-wrap:break-word; box-shadow:0 1px 2px rgba(0,0,0,0.04); }
.cd-bubble-msg.bot  { background:var(--bot-bg); color:var(--bot-text); border-radius:12px 12px 12px 2px; }
.cd-bubble-msg.user { background:var(--user-bg); color:var(--user-text); border-radius:12px 12px 2px 12px; }
.cd-chat-img { max-width:100%; border-radius:8px; margin-top:5px; max-height:200px; cursor:pointer; display:block; }
.cd-read { font-size:10px; color:var(--muted); margin-top:4px; text-align:right; }
.cd-tick { font-size:11px; color:#a5b4fc; }
.cd-tick.read { color:#38bdf8; }
#cd-typing { padding:4px 20px; min-height:20px; font-size:11px; color:var(--muted); font-style:italic; }
.cd-typing-dots span { display:inline-block; width:4px; height:4px; background:var(--muted); border-radius:50%; margin:0 1px; animation:cdbounce 1.2s infinite; }
.cd-typing-dots span:nth-child(2){animation-delay:.2s}
.cd-typing-dots span:nth-child(3){animation-delay:.4s}
@keyframes cdbounce{0%,80%,100%{transform:translateY(0)}40%{transform:translateY(-4px)}}
#cd-file-preview { background:var(--bot-bg); z-index:15; margin:0 20px; border-radius:24px; box-shadow:0 6px 20px rgba(0,0,0,0.08); color:var(--bot-text); position:absolute; bottom:74px; left:0; right:0; }
#cd-file-preview.collapsed .cd-preview-list { max-height:0; padding-top:0; padding-bottom:0; overflow:hidden; transition:max-height .28s ease-out,padding .28s ease-out; }
.cd-preview-hdr { display:flex; justify-content:space-between; align-items:center; padding:15px 20px; border-bottom:1px solid rgba(0,0,0,0.04); font-size:14px; font-weight:500; cursor:pointer; color:var(--bot-text); }
.cd-preview-count { display:flex; align-items:center; gap:5px; position:relative; }
.cd-preview-count::before { content:''; display:inline-block; width:16px; height:16px; background:var(--success); border-radius:50%; }
.cd-preview-count::after { content:'✓'; color:#fff; font-size:10px; position:absolute; left:4px; top:3px; }
.cd-preview-list { display:flex; gap:10px; overflow-x:auto; max-height:160px; padding:15px 20px; transition:max-height .28s ease-out,padding .28s ease-out; }
.cd-prev-item { position:relative; width:120px; height:120px; flex-shrink:0; border-radius:8px; overflow:hidden; box-shadow:0 2px 5px rgba(0,0,0,0.08); }
.cd-prev-item img { width:100%; height:100%; object-fit:cover; display:block; }
.cd-del-btn { position:absolute; top:5px; right:5px; width:25px; height:25px; border-radius:50%; border:none; cursor:pointer; background:var(--bot-bg) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24'%3E%3Cpath fill='%23666' d='M2.75 6.167c0-.46.345-.834.771-.834h2.665c.529-.015.996-.378 1.176-.916l.03-.095l.115-.372c.07-.228.131-.427.217-.605c.338-.702.964-1.189 1.687-1.314c.184-.031.377-.031.6-.031h3.478c.223 0 .417 0 .6.031c.723.125 1.35.612 1.687 1.314c.086.178.147.377.217.605l.115.372l.03.095c.18.538.74.902 1.27.916h2.57c.427 0 .772.373.772.834S20.405 7 19.979 7H3.52c-.426 0-.771-.373-.771-.833M11.607 22h.787c2.707 0 4.06 0 4.941-.863c.88-.864.97-2.28 1.15-5.111l.26-4.081c.098-1.537.147-2.305-.295-2.792s-1.187-.487-2.679-.487H8.23c-1.491 0-2.237 0-2.679.487s-.392 1.255-.295 2.792l.26 4.08c.18 2.833.27 4.248 1.15 5.112S8.9 22 11.607 22'/%3E%3C/svg%3E") center/14px no-repeat; display:flex; align-items:center; justify-content:center; }
#cd-attach-menu,#cd-emoji-menu { position:absolute; bottom:80px; left:20px; background:var(--bot-bg); border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.12); padding:8px; z-index:100; width:200px; color:var(--bot-text); }
#cd-emoji-menu { left:auto; right:20px; width:250px; }
.cd-menu-item { display:flex; align-items:center; gap:10px; width:100%; padding:10px; border:none; background:none; cursor:pointer; border-radius:8px; color:var(--bot-text); font-size:14px; transition:background .12s; }
.cd-menu-item:hover { background:rgba(0,0,0,0.04); }
.cd-menu-item svg { stroke:var(--bot-text)!important; }
.cd-emoji-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:5px; }
.cd-emoji-grid span { font-size:20px; padding:5px; cursor:pointer; text-align:center; border-radius:4px; }
.cd-emoji-grid span:hover { background:rgba(0,0,0,0.04); }
#cd-input-wrap { padding:10px 20px; background:var(--popup-bg); z-index:10; }
.cd-input-pill { background:var(--input-bg); border-radius:30px; display:flex; align-items:center; padding:6px; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
#cd-input { flex:1; border:none; outline:none; font-size:14px; color:var(--input-text); margin:0 10px; background:transparent; }
#cd-input::placeholder { color:var(--muted); }
.cd-action-btn { background:transparent; border:none; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--footer-icon); transition:background .12s; margin-right:5px; }
.cd-action-btn:hover,.cd-action-btn.active { background:rgba(0,0,0,0.06); }
.cd-action-btn svg { stroke:var(--footer-icon)!important; }
.cd-send-btn { background:#e5e7eb; border:none; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#6b7280; transition:all .12s; }
.cd-send-btn:hover { background:#d1d5db; color:#374151; }
.cd-send-btn svg { stroke:#6b7280!important; }
#cd-footer { display:flex; justify-content:space-around; align-items:center; padding:10px 0; background:var(--footer-bg); border-top:1px solid rgba(0,0,0,0.04); margin:0 10px; border-radius:20px; }
.cd-tab-btn { display:flex; flex-direction:column; align-items:center; background:none; border:none; cursor:pointer; color:var(--footer-icon); font-size:12px; font-weight:500; padding:5px 15px; transition:color .12s; }
.cd-tab-btn.active { color:var(--primary); }
.cd-tab-btn svg { width:24px; height:24px; margin-bottom:2px; stroke:currentColor!important; }
#cd-img-modal { position:fixed; inset:0; background:rgba(0,0,0,0.9); display:flex; align-items:center; justify-content:center; z-index:99999; }
#cd-modal-img { max-width:90%; max-height:90vh; object-fit:contain; }
#cd-modal-close { position:absolute; top:20px; right:30px; color:#fff; font-size:40px; font-weight:700; background:none; border:none; cursor:pointer; opacity:.85; line-height:1; }
#cd-modal-close:hover { opacity:1; }
.cd-hidden { display:none!important; }
/* loading spinner */
#cd-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:12px; color:var(--muted); font-size:13px; }
.cd-spinner { width:32px; height:32px; border:3px solid rgba(0,0,0,0.1); border-top-color:var(--primary); border-radius:50%; animation:cdspin .7s linear infinite; }
@keyframes cdspin { to { transform:rotate(360deg); } }
@media(max-width:480px){
    #cd-popup { position:fixed; inset:0; width:100%; height:100%; max-height:100%; border-radius:0; bottom:0; right:0; }
    #cd-bubble { bottom:16px; right:16px; }
}
    `;
    const styleEl = document.createElement('style');
    styleEl.textContent = CSS;
    document.head.appendChild(styleEl);

    /* ── 6. Inject HTML ──────────────────────────────────── */
    const wrap = document.createElement('div');
    wrap.id = 'cd-wrap';
    wrap.innerHTML = `
    <button id="cd-bubble">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <span id="cd-badge">0</span>
    </button>

    <div id="cd-popup">

        <!-- Loading state -->
        <div id="cd-loading">
            <div class="cd-spinner"></div>
            <span>Connecting...</span>
        </div>

        <!-- HOME SCREEN -->
        <div id="cd-home" class="cd-hidden">
            <div class="cd-home-header">Welcome!</div>
            <div class="cd-home-title">Text us</div>
            <div class="cd-admin-card">
                <div class="cd-admin-badge">
                    <div class="cd-avatar">A<span class="cd-status-dot"></span></div>
                    <span class="cd-admin-name">Admin</span>
                </div>
                <div class="cd-admin-info">Hello. How may I help you?</div>
                <button id="cd-start-btn" class="cd-start-btn">
                    Start chat
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                </button>
            </div>
        </div>

        <!-- CONVERSATION -->
        <div id="cd-conv" class="cd-hidden">
            <div id="cd-header">
                <div class="cd-hdr-side">
                    <button class="cd-icon-btn" id="cd-back-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    </button>
                    <button class="cd-icon-btn" id="cd-options-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                    </button>
                </div>
                <div class="cd-admin-badge">
                    <div class="cd-avatar">A<span class="cd-status-dot"></span></div>
                    <span class="cd-admin-name" id="cd-online-label">Admin</span>
                </div>
                <div class="cd-hdr-side">
                    <button class="cd-icon-btn" id="cd-minimize-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <button class="cd-icon-btn" id="cd-close-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>
            <div id="cd-options-menu" class="cd-hidden">
                <div class="cd-opt-item" id="cd-transcript-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>Send transcript</span>
                </div>
                <div class="cd-opt-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                    <span style="flex:1">Sounds</span>
                    <label class="cd-switch"><input type="checkbox" id="cd-sound-toggle" checked><span class="cd-slider"></span></label>
                </div>
            </div>
            <div id="cd-name-bar">
                <label>Your name:</label>
                <input type="text" id="cd-name-input" placeholder="Enter name..." maxlength="50">
            </div>
            <div id="cd-messages">
                <div class="cd-msg-wrap bot">
                    <div class="cd-bubble-msg bot">Chat started 👋</div>
                </div>
            </div>
            <div id="cd-typing"></div>
            <div id="cd-file-preview" class="cd-hidden">
                <div class="cd-preview-hdr">
                    <span class="cd-preview-count">0 of 5 uploaded</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="cd-preview-list"></div>
            </div>
            <div id="cd-attach-menu" class="cd-hidden">
                <button class="cd-menu-item" id="cd-send-file-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
                    <span>Send a file</span>
                </button>
                <input type="file" id="cd-file-input" style="display:none" multiple>
                <button class="cd-menu-item" id="cd-screenshot-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <span>Add screenshot</span>
                </button>
            </div>
            <div id="cd-emoji-menu" class="cd-hidden">
                <div class="cd-emoji-grid">
                    <span>🙂</span><span>😀</span><span>😂</span><span>😉</span><span>😍</span>
                    <span>😐</span><span>😕</span><span>😓</span><span>😢</span><span>😭</span>
                    <span>🎉</span><span>❤️</span><span>👌</span><span>👍</span><span>🙏</span>
                </div>
            </div>
            <div id="cd-input-wrap">
                <div class="cd-input-pill">
                    <button class="cd-action-btn" id="cd-attach-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <input type="text" id="cd-input" placeholder="Write a message...">
                    <button class="cd-action-btn" id="cd-emoji-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                    </button>
                    <button class="cd-send-btn" id="cd-send-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div id="cd-footer" class="cd-hidden">
            <button class="cd-tab-btn active" id="cd-home-tab">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Home</span>
            </button>
            <button class="cd-tab-btn" id="cd-chat-tab">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span>Chat</span>
            </button>
        </div>

    </div>

    <!-- Image Modal -->
    <div id="cd-img-modal" class="cd-hidden">
        <button id="cd-modal-close">&times;</button>
        <img id="cd-modal-img" src="" alt="Preview">
    </div>
    `;
    document.body.appendChild(wrap);

    /* ── 7. Element refs ─────────────────────────────────── */
    const popup       = document.getElementById('cd-popup');
    const badge       = document.getElementById('cd-badge');
    const homeScreen  = document.getElementById('cd-home');
    const convScreen  = document.getElementById('cd-conv');
    const footer      = document.getElementById('cd-footer');
    const loadingEl   = document.getElementById('cd-loading');
    const messagesBox = document.getElementById('cd-messages');
    const typingBar   = document.getElementById('cd-typing');
    const nameInput   = document.getElementById('cd-name-input');
    const chatInput   = document.getElementById('cd-input');
    const filePreview = document.getElementById('cd-file-preview');
    const attachMenu  = document.getElementById('cd-attach-menu');
    const emojiMenu   = document.getElementById('cd-emoji-menu');
    const optionsMenu = document.getElementById('cd-options-menu');
    const imgModal    = document.getElementById('cd-img-modal');

    /* ── 8. State ────────────────────────────────────────── */
    let isOpen      = false;
    let view        = 'home';
    let unread      = 0;
    let filesToSend = [];
    let fbReady     = false;

    /* ── 9. View switch ──────────────────────────────────── */
    function showView(v) {
        view = v;
        loadingEl.classList.add('cd-hidden');
        footer.classList.remove('cd-hidden');
        if (v === 'home') {
            homeScreen.classList.remove('cd-hidden');
            convScreen.classList.add('cd-hidden');
            document.getElementById('cd-home-tab').classList.add('active');
            document.getElementById('cd-chat-tab').classList.remove('active');
        } else {
            homeScreen.classList.add('cd-hidden');
            convScreen.classList.remove('cd-hidden');
            footer.classList.add('cd-hidden');
            document.getElementById('cd-chat-tab').classList.add('active');
            document.getElementById('cd-home-tab').classList.remove('active');
            chatInput.focus();
        }
        closeAllMenus();
    }

    /* ── 10. Popup toggle ────────────────────────────────── */
    function togglePopup() {
        isOpen = !isOpen;
        popup.classList.toggle('open', isOpen);
        if (isOpen) {
            showView(view);
            unread = 0;
            updateBadge();
            if (window._cdMarkRead) window._cdMarkRead();
        } else {
            closeAllMenus();
        }
    }

    document.getElementById('cd-bubble').addEventListener('click', togglePopup);
    document.getElementById('cd-close-btn').addEventListener('click', togglePopup);
    document.getElementById('cd-minimize-btn').addEventListener('click', togglePopup);
    document.getElementById('cd-start-btn').addEventListener('click', () => showView('chat'));
    document.getElementById('cd-back-btn').addEventListener('click', () => showView('home'));
    document.getElementById('cd-home-tab').addEventListener('click', () => showView('home'));
    document.getElementById('cd-chat-tab').addEventListener('click', () => showView('chat'));

    /* ── 11. Badge ───────────────────────────────────────── */
    function updateBadge() {
        if (unread > 0) { badge.textContent = unread > 99 ? '99+' : unread; badge.classList.add('show'); }
        else badge.classList.remove('show');
    }

    /* ── 12. Menus ───────────────────────────────────────── */
    function closeAllMenus() {
        [attachMenu, emojiMenu, optionsMenu].forEach(m => m.classList.add('cd-hidden'));
        ['cd-attach-btn','cd-emoji-btn','cd-options-btn'].forEach(id => document.getElementById(id)?.classList.remove('active'));
    }
    function toggleMenu(menu, btnId) {
        const wasHidden = menu.classList.contains('cd-hidden');
        closeAllMenus();
        if (wasHidden) { menu.classList.remove('cd-hidden'); document.getElementById(btnId).classList.add('active'); }
    }
    document.getElementById('cd-attach-btn').addEventListener('click',  e => { e.stopPropagation(); toggleMenu(attachMenu,  'cd-attach-btn');  });
    document.getElementById('cd-emoji-btn').addEventListener('click',   e => { e.stopPropagation(); toggleMenu(emojiMenu,   'cd-emoji-btn');   });
    document.getElementById('cd-options-btn').addEventListener('click', e => { e.stopPropagation(); toggleMenu(optionsMenu, 'cd-options-btn'); });
    document.addEventListener('click', e => {
        if (!e.target.closest('#cd-attach-btn,#cd-attach-menu,#cd-emoji-btn,#cd-emoji-menu,#cd-options-btn,#cd-options-menu'))
            closeAllMenus();
    });

    /* ── 13. Emoji ───────────────────────────────────────── */
    document.querySelectorAll('.cd-emoji-grid span').forEach(em => {
        em.addEventListener('click', () => { chatInput.value += em.textContent; chatInput.focus(); closeAllMenus(); });
    });

    /* ── 14. Files ───────────────────────────────────────── */
    document.getElementById('cd-send-file-btn').addEventListener('click', () => { closeAllMenus(); document.getElementById('cd-file-input').click(); });
    document.getElementById('cd-file-input').addEventListener('change', function () {
        Array.from(this.files).forEach(f => { if (filesToSend.length < 5) filesToSend.push(f); });
        renderFilePreview(); this.value = '';
    });
    filePreview.querySelector('.cd-preview-hdr').addEventListener('click', () => filePreview.classList.toggle('collapsed'));
    function renderFilePreview() {
        const list  = filePreview.querySelector('.cd-preview-list');
        const count = filePreview.querySelector('.cd-preview-count');
        if (!filesToSend.length) { filePreview.classList.add('cd-hidden'); list.innerHTML = ''; return; }
        filePreview.classList.remove('cd-hidden','collapsed');
        count.textContent = `${filesToSend.length} of 5 uploaded`;
        list.innerHTML = '';
        filesToSend.forEach((file, i) => {
            const item = document.createElement('div');
            item.className = 'cd-prev-item';
            if (file.type.startsWith('image/')) {
                const r = new FileReader();
                r.onload = e => { item.innerHTML = `<img src="${e.target.result}" alt="">`; addDel(item, i); };
                r.readAsDataURL(file);
            } else {
                item.innerHTML = `<div style="background:#6a5acd;color:#fff;height:100%;display:flex;align-items:center;justify-content:center;font-size:12px;padding:5px;text-align:center">.${file.name.split('.').pop().toUpperCase()}</div>`;
                addDel(item, i);
            }
            list.appendChild(item);
        });
    }
    function addDel(item, idx) {
        const btn = document.createElement('button');
        btn.className = 'cd-del-btn';
        btn.addEventListener('click', e => { e.stopPropagation(); filesToSend.splice(idx, 1); renderFilePreview(); });
        item.appendChild(btn);
    }

    /* ── 15. Screenshot ──────────────────────────────────── */
    document.getElementById('cd-screenshot-btn').addEventListener('click', async () => {
        closeAllMenus();
        try {
            const stream = await navigator.mediaDevices.getDisplayMedia({ video: true });
            const video  = document.createElement('video');
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                video.play();
                setTimeout(() => {
                    const canvas = document.createElement('canvas');
                    canvas.width  = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    stream.getTracks().forEach(t => t.stop());
                    canvas.toBlob(blob => {
                        if (filesToSend.length < 5) { filesToSend.push(new File([blob], 'screenshot.png', { type: 'image/png' })); renderFilePreview(); }
                    }, 'image/png');
                }, 500);
            };
        } catch (e) {}
    });

    /* ── 16. Modal ───────────────────────────────────────── */
    document.getElementById('cd-modal-close').addEventListener('click', () => imgModal.classList.add('cd-hidden'));

    /* ── 17. Misc ────────────────────────────────────────── */
    document.getElementById('cd-transcript-btn').addEventListener('click', () => { closeAllMenus(); appendBotMsg('Transcript sent to your email!'); });

    /* ── 18. Render helpers ──────────────────────────────── */
    function appendBotMsg(text) {
        const w = document.createElement('div');
        w.className = 'cd-msg-wrap bot';
        w.innerHTML = `<div class="cd-bubble-msg bot">${esc(text)}</div>`;
        messagesBox.appendChild(w);
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }
    function renderMessage(msgId, d, isMine) {
        if (document.getElementById('cdm_' + msgId)) return;
        const w = document.createElement('div');
        w.className = 'cd-msg-wrap ' + (isMine ? 'user' : 'bot');
        w.id = 'cdm_' + msgId;
        const time = new Date(d.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const content = d.imageData
            ? `<img src="${d.imageData}" class="cd-chat-img" alt="image">`
            : esc(d.message);
        w.innerHTML = `
            <div class="cd-bubble-msg ${isMine ? 'user' : 'bot'}">${content}</div>
            <div class="cd-read">${time}${isMine ? ` <span class="cd-tick" id="cdtick_${msgId}">✓</span>` : ''}</div>`;
        if (d.imageData) {
            const img = w.querySelector('.cd-chat-img');
            img?.addEventListener('click', () => { document.getElementById('cd-modal-img').src = d.imageData; imgModal.classList.remove('cd-hidden'); });
        }
        messagesBox.appendChild(w);
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }
    function updateTick(msgId, status) {
        const t = document.getElementById('cdtick_' + msgId);
        if (!t) return;
        if (status === 'sent')      { t.textContent = '✓';  t.classList.remove('read'); }
        if (status === 'delivered') { t.textContent = '✓✓'; t.classList.remove('read'); }
        if (status === 'read')      { t.textContent = '✓✓'; t.classList.add('read'); }
    }
    function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    /* ── 19. Restore name ────────────────────────────────── */
    const savedName = localStorage.getItem('lc_visitor_name');
    if (savedName) nameInput.value = savedName;

    /* ── 20. FETCH FIREBASE CONFIG FROM LARAVEL ──────────── */
    fetch(API_BASE + '/chat/config', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body:    JSON.stringify({ site_id: SITE_ID })
    })
    .then(r => r.json())
    .then(fbConfig => {
        // Config mil gayi — ab Firebase load karo
        initFirebase(fbConfig);
    })
    .catch(() => {
        loadingEl.innerHTML = '<span style="color:#ef4444">Connection failed. Please try again.</span>';
    });

    /* ── 21. Firebase init (after config fetch) ──────────── */
    function initFirebase(fbConfig) {
        const FB_BASE = 'https://www.gstatic.com/firebasejs/10.12.0';
        Promise.all([
            import(`${FB_BASE}/firebase-app.js`),
            import(`${FB_BASE}/firebase-database.js`)
        ]).then(([{ initializeApp }, {
            getDatabase, ref, push, set, remove,
            onChildAdded, onChildChanged, onValue,
            query, orderByChild, limitToLast, onDisconnect
        }]) => {

            const app = initializeApp(fbConfig, 'cd-widget-' + SITE_ID);
            const db  = getDatabase(app);

            const basePath     = `chats/${DOMAIN_ID}/${CONV_ID}`;
            const msgsPath     = `${basePath}/messages`;
            const typingPath   = `${basePath}/typing`;
            const presencePath = `${basePath}/presence`;

            /* presence */
            const myRef = ref(db, `${presencePath}/${visitorId}`);
            set(myRef, true);
            onDisconnect(myRef).remove();

            /* online count */
            onValue(ref(db, presencePath), snap => {
                const n = Object.keys(snap.val() || {}).length;
                document.getElementById('cd-online-label').textContent = `Admin · ${n} online`;
            });

            /* typing */
            onValue(ref(db, typingPath), snap => {
                const others = Object.entries(snap.val() || {})
                    .filter(([k, v]) => k !== visitorId && v.typing)
                    .map(([, v]) => v.name || 'Agent');
                typingBar.innerHTML = others.length
                    ? `${esc(others.join(', '))} is typing <span class="cd-typing-dots"><span></span><span></span><span></span></span>`
                    : '';
            });

            /* messages */
            const msgQ = query(ref(db, msgsPath), orderByChild('timestamp'), limitToLast(50));
            onChildAdded(msgQ, snap => {
                const d = snap.val(), id = snap.key, mine = d.uid === visitorId;
                renderMessage(id, d, mine);
                if (!mine) {
                    if (!isOpen || view !== 'chat') { unread++; updateBadge(); }
                    else set(ref(db, `${msgsPath}/${id}/status`), 'read');
                }
            });
            onChildChanged(ref(db, msgsPath), snap => {
                const d = snap.val();
                if (d.uid === visitorId) updateTick(snap.key, d.status);
            });

            /* mark read */
            window._cdMarkRead = () => {
                onValue(ref(db, msgsPath), snap => {
                    Object.entries(snap.val() || {}).forEach(([id, d]) => {
                        if (d.uid !== visitorId && d.status !== 'read')
                            set(ref(db, `${msgsPath}/${id}/status`), 'read');
                    });
                }, { onlyOnce: true });
            };

            /* typing send */
            let typingTimer;
            function setTyping() {
                const name = nameInput.value.trim();
                if (!name) return;
                set(ref(db, `${typingPath}/${visitorId}`), { name, typing: true });
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => remove(ref(db, `${typingPath}/${visitorId}`)), 1500);
            }
            window.addEventListener('beforeunload', () => remove(ref(db, `${typingPath}/${visitorId}`)));

            /* SEND MESSAGE */
            async function sendMessage() {
                const name = nameInput.value.trim();
                const msg  = chatInput.value.trim();
                if (!name) { nameInput.focus(); nameInput.style.borderColor = '#ef4444'; return; }
                nameInput.style.borderColor = '';

                if (filesToSend.length) {
                    filesToSend.forEach(file => {
                        if (file.type.startsWith('image/')) {
                            const r = new FileReader();
                            r.onload = async e => {
                                await push(ref(db, msgsPath), { uid: visitorId, username: name, message: '[Image]', imageData: e.target.result, timestamp: Date.now(), status: 'sent', sender: 'visitor' });
                            };
                            r.readAsDataURL(file);
                        } else {
                            push(ref(db, msgsPath), { uid: visitorId, username: name, message: `[File: ${file.name}]`, timestamp: Date.now(), status: 'sent', sender: 'visitor' });
                        }
                    });
                    filesToSend = [];
                    renderFilePreview();
                }

                if (!msg) return;
                chatInput.value = '';
                localStorage.setItem('lc_visitor_name', name);
                trackVisitor({ name });

                await push(ref(db, msgsPath), { uid: visitorId, username: name, message: msg, timestamp: Date.now(), status: 'sent', sender: 'visitor' });
                set(ref(db, `${basePath}/meta`), { last_message: msg, last_timestamp: Date.now(), domain_id: DOMAIN_ID, visitor_id: visitorId });
            }

            document.getElementById('cd-send-btn').addEventListener('click', sendMessage);
            chatInput.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); } });
            chatInput.addEventListener('input', setTyping);

            /* Firebase ready — show UI */
            fbReady = true;
            showView('home');

        }).catch(() => {
            loadingEl.innerHTML = '<span style="color:#ef4444">Chat unavailable.</span>';
        });
    }

})();