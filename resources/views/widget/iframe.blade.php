<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatDesk360</title>
    <style>
        /* Iframe base resets */
        html, body { 
            margin: 0; padding: 0; overflow: hidden; 
            background: transparent; height: 100%; width: 100%; 
        }
    </style>
</head>
<body>

{{-- ★ Custom Audio Files for Widget --}}
<audio id="audio-new-message" src="https://chatdesk360.com/sounds/message.wav" preload="auto"></audio>

<script>
(function () {
    'use strict';

     // ★★★ SECURITY: Block direct URL access ★★★
    try {
        if (window.self === window.top) {
            document.body.innerHTML = '';
            window.location.replace('about:blank');
            return;
        }
    } catch (e) {
        // Cross-origin check failed — we're in a cross-origin iframe, that's OK
    }

    /* ── Sound Helper ────────────────────────────────────── */
    function playSound(audioId) {
        const audio = document.getElementById(audioId);
        if (audio) {
            audio.currentTime = 0;
            audio.play().catch(() => {}); // Ignore autoplay policy errors
        }
    }

    /* ── 0. Config injected by Laravel ────────────────── */
    const SITE_ID = '{{ $siteId }}';

    const urlParams = new URLSearchParams(window.location.search);
    const PARENT_ORIGIN = urlParams.get('po') || '';

    /* ── 1. API base — Hardcoded because we are on your domain ── */
    const API_BASE = window.location.origin + '/api';

    /* ── IFRAME COMMUNICATION ─────────────────────────── */
    let currentParentUrl = '';
    let currentParentTitle = '';

    function notifyParentResize() {
        const side = WS.position === 'left' ? 'left' : 'right';
        const sidePx = parseInt(WS.side_spacing) || 24;
        const botPx = parseInt(WS.bottom_spacing) || 24;

        const data = {
            type: '__cd_resize',
            bottom: botPx + 'px',
            right: side === 'right' ? sidePx + 'px' : 'auto',
            left: side === 'left' ? sidePx + 'px' : 'auto'
        };

        if (isOpen) {
            data.width = '380px';
            data.height = '600px';
            data.borderRadius = '12px';
            data.boxShadow = '0 5px 25px rgba(0,0,0,0.15)';
        } else {
            data.width = '60px';
            data.height = '60px';
            data.borderRadius = '50%';
            data.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
        }

        window.parent.postMessage(data, '*');
    }

    // Listen for Page Tracking & Visibility from Parent
    window.addEventListener('message', function(e) {
        if (!e.data) return;
        if (e.data.type === '__cd_page') {
            currentParentUrl = e.data.url;
            currentParentTitle = e.data.title;
            
            try {
                const parentHost = new URL(currentParentUrl).hostname.replace(/\./g, '_');
                if (DOMAIN_ID !== parentHost) {
                    const oldDomain = DOMAIN_ID;
                    DOMAIN_ID = parentHost;
                    
                    if (fbReady && activeRoomPath && (oldDomain === 'unknown_domain' || oldDomain !== DOMAIN_ID)) {
                        reinitFirebasePaths();
                    }
                }
            } catch(err) {}
            
            trackPageStart();
        }
        if (e.data.type === '__cd_visibility') {
            if (e.data.state === 'visible') {
                trackVisitor();
            } else {
                sendLeave();
            }
        }
    });

    /* ── 2. Visitor ID ───────────────────────────────────── */
    let visitorId = localStorage.getItem('lc_visitor_id');
    if (!visitorId) {
        visitorId = 'v_' + Math.random().toString(36).substr(2, 12) + '_' + Date.now();
        localStorage.setItem('lc_visitor_id', visitorId);
    }

    /* ── 3. IDs ──────────────────────────────────────────── */
    let DOMAIN_ID = (function() {
        try {
            if (PARENT_ORIGIN) {
                const parentHost = new URL(PARENT_ORIGIN).hostname.replace(/\./g, '_');
                return parentHost;
            }
        } catch(e) {}

        try {
            if (document.referrer) {
                const parentHost = new URL(document.referrer).hostname.replace(/\./g, '_');
                return parentHost;
            }
        } catch(e) {}

        return 'unknown_domain';
    })();
    const CONV_ID   = 'general';

    /* ── Default widget settings ─────────────────────────── */
    const DEFAULT_SETTINGS = {
        minimized_style     : 'bubble',
        theme               : 'light',
        primary_color       : '#2b60d0',
        primary_hover       : '#1f4cb8',
        use_custom_colors   : false,
        widget_bg_color     : '#f7f7f7',
        widget_text_color   : '#1f2937',
        position            : 'right',
        side_spacing        : 24,
        bottom_spacing      : 24,
        show_logo           : false,
        logo_url            : '',
        show_agent_photo    : true,
        sound_notifications : true,
        allow_rating        : true,
        allow_transcripts   : true,
        white_label         : false,
        eye_catcher_image   : '',
        welcome_header      : 'Welcome!',
        welcome_title       : 'Text us',
        admin_name          : 'Admin',
        welcome_message     : 'Hello. How may I help you?',
    };

    let WS = { ...DEFAULT_SETTINGS };       
    let postChatConfig = { enabled: false, fields: [] };
    let chatHasMessages = false;             
    let postChatShown   = false;
    let postChatDisplayed = false;
    let initialStatusLoaded = false; // ★ Prevents stale 'ended' status on reload

    /* ── Post-chat eligibility check ── */
    function tryShowPostChat() {
        if (postChatDisplayed || postChatShown) return false;
        const alreadyFilled = localStorage.getItem('lc_postchat_filled_' + visitorId);
        if (!alreadyFilled && chatHasMessages && postChatConfig.enabled) {
            postChatDisplayed = true;
            showView('postchat');
            return true;
        }
        return false;
    }

    /* ── 4. Track visitor ────────────────────────────────── */
    function trackVisitor(extra = {}) {
        if (!authorized || !API_BASE) return Promise.resolve();
        return fetch(API_BASE + '/visitor/track', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body:    JSON.stringify({
                visitor_id:   visitorId,
                site_id:      SITE_ID,
                domain_id:    DOMAIN_ID,
                page_url:     currentParentUrl || window.location.href,
                referrer_url: document.referrer || null,
                ...extra
            })
        }).catch(() => {});
    }
    
    function updateVisitorStatus(status) {
        if (!authorized || !API_BASE) return Promise.resolve();
        return fetch(API_BASE + '/visitor/status', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body:    JSON.stringify({
                visitor_id: visitorId,
                site_id:    SITE_ID,
                status:     status
            })
        }).catch(() => {});
    }

    function sendHeartbeat() {
        if (!authorized || !API_BASE) return;
        fetch(API_BASE + '/visitor/heartbeat', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ visitor_id: visitorId })
        }).catch(() => {});
    }

    function sendLeave() {
        if (!authorized || !API_BASE) return; 
        const data = JSON.stringify({ visitor_id: visitorId, timestamp: Date.now() });
        const blob = new Blob([data], { type: 'application/json' });
        navigator.sendBeacon(API_BASE + '/visitor/leave', blob);
    }

    /* ── Page Tracking ───────────────────────────────────── */
    let lastTrackedUrl    = null;
    let pageStartTime     = Date.now();
    let lastTrackedTitle  = null;

    function trackPageStart() {
        const currentUrl = currentParentUrl || window.location.href;
        if (lastTrackedUrl === currentUrl) return;

        if (lastTrackedUrl !== null) {
            const timeSpent = Math.floor((Date.now() - pageStartTime) / 1000);
            if (timeSpent >= 2) {
                const pageData = JSON.stringify({
                    visitor_id: visitorId, url: lastTrackedUrl,
                    title: lastTrackedTitle || document.title, time_spent: timeSpent
                });
                navigator.sendBeacon(API_BASE + '/visitor/page', new Blob([pageData], { type: 'application/json' }));
            }
        }

        lastTrackedUrl   = currentUrl;
        lastTrackedTitle = currentParentTitle || document.title;
        pageStartTime    = Date.now();

        if (!API_BASE) return;

        fetch(API_BASE + '/visitor/page-start', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ visitor_id: visitorId, url: currentUrl, title: lastTrackedTitle })
        }).catch(() => {});
    }

        // ★★★ ADD THIS: Heartbeat to update time_spent every 10 seconds ★★★
    setInterval(() => {
        if (!authorized || !API_BASE || !lastTrackedUrl) return;

        const timeSpent = Math.floor((Date.now() - pageStartTime) / 1000);
        if (timeSpent >= 10) {
            fetch(API_BASE + '/visitor/page', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    visitor_id: visitorId,
                    url: lastTrackedUrl,
                    title: lastTrackedTitle || document.title,
                    time_spent: timeSpent
                })
            }).catch(() => {});
        }
    }, 10000);
    // ★★★ END ADD ★★★

    /* ── 5. Inject CSS ─────────────────────────────────── */
    const CSS = `
#cd-wrap * { box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
#cd-wrap {
    --primary:       var(--cd-primary-color, #2b60d0);
    --primary-hover: var(--cd-primary-hover, #1f4cb8);
    --popup-bg:      var(--cd-popup-bg, #f7f7f7);
    --popup-text:    var(--cd-popup-text, #1f2937);
    --bot-bg:        var(--cd-bot-bg, #ffffff);
    --bot-text:      var(--cd-bot-text, #1f2937);
    --user-bg:       var(--cd-primary-color, #2b60d0);
    --user-text:     #ffffff;
    --header-icon:   var(--cd-header-icon, #1f2937);
    --input-bg:      var(--cd-input-bg, #ffffff);
    --input-text:    var(--cd-input-text, #374151);
    --footer-bg:     var(--cd-footer-bg, #ffffff);
    --footer-icon:   var(--cd-footer-icon, #9ca3af);
    --bubble-btn-bg: var(--cd-bubble-btn-bg, #1f2937);
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
    color: var(--header-icon); stroke: var(--header-icon) !important;
}
#cd-bubble svg { width: 32px; height: 32px; color: var(--bubble-btn-ic); stroke: var(--bubble-btn-ic) !important; }

#cd-bubble {
    position:fixed; bottom:0; right:0; z-index:9999;
    width:60px; height:60px; border-radius:50%;
    background:var(--bubble-btn-bg);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,0.2);
    transition:transform .2s, opacity .2s; border:none;
}
#cd-bubble:hover { transform:scale(1.07); }
#cd-badge {
    position:absolute; top:-4px; right:-4px;
    background:#ef4444; color:#fff; border-radius:50%;
    width:20px; height:20px; font-size:11px; font-weight:700;
    display:none; align-items:center; justify-content:center; border:2px solid #fff;
}
#cd-badge.show { display:flex; }

#cd-popup {
    position:fixed; top:0; left:0; right:0; bottom:0; 
    width:100%; height:100%; max-height:100%;
    background:var(--popup-bg); color:var(--popup-text);
    border-radius:0; box-shadow:none;
    display:flex; flex-direction:column; overflow:hidden;
    transform:scale(0.9) translateY(15px); opacity:0; pointer-events:none;
    transition:transform .3s cubic-bezier(0.4, 0, 0.2, 1), opacity .3s cubic-bezier(0.4, 0, 0.2, 1);
    transform-origin:bottom right;
}
#cd-popup.open { transform:scale(1) translateY(0); opacity:1; pointer-events:all;    z-index: 9999; }

#cd-logo { text-align:center; padding:10px 20px 0; }
#cd-logo img { max-height:48px; max-width:100%; object-fit:contain; }
#cd-branding {
    text-align: center;
    padding: 10px 0;
    font-size: 12px;
    color: var(--muted);
    background: var(--popup-bg);
}
#cd-branding a { color:var(--primary); text-decoration:none; font-weight:600; }
#cd-branding a:hover { text-decoration:underline; }
#cd-home { flex:1; display:flex; flex-direction:column; padding:20px; background:var(--popup-bg); overflow-y:auto; }
.cd-home-header { font-size:28px; font-weight:300; color:var(--popup-text); margin-top:10px; }
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
#cd-postchat { display:none; flex-direction:column; flex:1; overflow-y:auto; background:var(--popup-bg); }
#cd-postchat.active { display:flex; }
.cd-pc-field { margin:0 20px 14px; }
.cd-pc-label { font-size:13px; font-weight:600; color:var(--popup-text); margin-bottom:6px; }
.cd-pc-input, .cd-pc-textarea, .cd-pc-select {
    width:100%; padding:9px 12px; border:1px solid rgba(0,0,0,0.12);
    border-radius:8px; font-size:13px; outline:none;
    background:var(--input-bg); color:var(--input-text);
}
.cd-pc-textarea { resize:vertical; min-height:60px; }
.cd-pc-input:focus, .cd-pc-textarea:focus, .cd-pc-select:focus { border-color:var(--primary); }
.cd-pc-radio-group, .cd-pc-check-group { display:flex; flex-direction:column; gap:6px; }
.cd-pc-radio-label, .cd-pc-check-label {
    display:flex; align-items:center; gap:6px; font-size:13px; color:var(--popup-text); cursor:pointer;
}
.cd-pc-rating-group { display:flex; gap:12px; align-items:center; }
.cd-pc-rate-btn {
    width:44px; height:44px; border:2px solid rgba(0,0,0,0.1); border-radius:50%;
    background:var(--bot-bg); font-size:22px; cursor:pointer; transition:all .12s;
    display:flex; align-items:center; justify-content:center;
}
.cd-pc-rate-btn:hover { border-color:var(--primary); }
.cd-pc-rate-btn.selected { border-color:var(--primary); background:var(--primary); }
.cd-pc-submit {
    margin:10px 20px 20px; padding:12px; background:var(--primary); color:#fff;
    border:none; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer;
}
.cd-pc-submit:hover { background:var(--primary-hover); }
.cd-pc-thankyou { text-align:center; padding:40px 20px; color:var(--popup-text); }
.cd-pc-thankyou h3 { font-size:20px; margin-bottom:8px; }
.cd-pc-thankyou p { color:var(--muted); font-size:14px; }

#cd-conv { display:flex; flex-direction:column; height:93%; flex:1; } 
#cd-header {
    display:flex; justify-content:space-between; align-items:center;
    padding:15px 15px 40px; background:var(--popup-bg);
    mask:linear-gradient(black 70%,rgba(0,0,0,0) 100%);
    backdrop-filter:blur(8px); z-index:10; flex-shrink:0;
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
#cd-name-bar { padding:6px 16px; background:var(--popup-bg); border-bottom:1px solid rgba(0,0,0,0.06); display:none; align-items:center; gap:8px; }
#cd-name-bar label { font-size:11px; color:var(--muted); white-space:nowrap; }
#cd-name-input { flex:1; padding:5px 9px; border:1px solid rgba(0,0,0,0.12); border-radius:8px; font-size:13px; outline:none; background:var(--input-bg); color:var(--input-text); }
#cd-name-input:focus { border-color:var(--primary); }
#cd-info-request-banner { margin:8px 16px; padding:12px 14px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; font-size:13px; color:#1e40af; display:none; }
#cd-info-request-banner p { margin:0 0 8px; font-weight:600; }
#cd-info-req-name, #cd-info-req-email { width:100%; padding:7px 10px; border:1px solid #bfdbfe; border-radius:8px; margin-bottom:7px; font-size:13px; outline:none; box-sizing:border-box; background:var(--input-bg); color:var(--input-text); }
#cd-info-req-name:focus, #cd-info-req-email:focus { border-color:var(--primary); }
#cd-info-req-submit { width:100%; background:var(--primary); color:#fff; border:none; border-radius:8px; padding:8px; font-size:13px; cursor:pointer; font-weight:600; }
#cd-info-req-submit:hover { background:var(--primary-hover); }
#cd-messages { flex:1; display:flex; flex-direction:column; gap:15px; padding:20px; overflow-y:auto; overscroll-behavior:contain; }
#cd-messages::-webkit-scrollbar { width:4px; }
#cd-messages::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:4px; }
.cd-msg-wrap { display:flex; flex-direction:column; max-width:80%; }
.cd-msg-wrap.user { align-self:flex-end; align-items:flex-end; }
.cd-msg-wrap.bot  { align-self:flex-start; }
.cd-msg-wrap.system { align-self:center; max-width:90%; }
.cd-bubble-msg { padding:12px 16px; font-size:14px; line-height:1.4; word-wrap:break-word; box-shadow:0 1px 2px rgba(0,0,0,0.04); }
.cd-bubble-msg.bot  { background:var(--bot-bg); color:var(--bot-text); border-radius:12px 12px 12px 2px; }
.cd-bubble-msg.user { background:var(--user-bg); color:var(--user-text); border-radius:12px 12px 2px 12px; }
.cd-bubble-msg.system-msg { background:#f0fdf4; color:#166534; border-radius:8px; font-size:12px; text-align:center; padding:6px 14px; border:1px solid #bbf7d0; }
.cd-chat-img { max-width:100%; border-radius:8px; margin-top:5px; max-height:200px; cursor:pointer; display:block; }
.cd-read { font-size:10px; color:var(--muted); margin-top:4px; text-align:right; }
.cd-tick { font-size:11px; color:#a5b4fc; }
.cd-tick.read { color:#38bdf8; }
#cd-typing { padding:4px 20px; min-height:20px; font-size:11px; color:var(--muted); font-style:italic; }
.cd-typing-dots span { display:inline-block; width:4px; height:4px; background:var(--muted); border-radius:50%; margin:0 1px; animation:cdbounce 1.2s infinite; }
.cd-typing-dots span:nth-child(2){animation-delay:.2s}
.cd-typing-dots span:nth-child(3){animation-delay:.4s}
@keyframes cdbounce{0%,80%,100%{transform:translateY(0)}40%{transform:translateY(-4px)}}
#cd-file-preview { background:var(--bot-bg); z-index:15; margin:0 20px; border-radius:24px; box-shadow:0 6px 20px rgba(0,0,0,0.08); color:var(--bot-text); position:absolute; bottom:100px; left:0; right:0; }
#cd-file-preview.collapsed .cd-preview-list { max-height:0; padding-top:0; padding-bottom:0; overflow:hidden; transition:max-height .28s ease-out,padding .28s ease-out; }
.cd-preview-hdr { display:flex; justify-content:space-between; align-items:center; padding:15px 20px; font-size:14px; font-weight:500; cursor:pointer; color:var(--bot-text); }
.cd-preview-count { display:flex; align-items:center; gap:5px; position:relative; }
.cd-preview-count::before { content:''; display:inline-block; width:16px; height:16px; background:var(--success); border-radius:50%; }
.cd-preview-count::after { content:'✓'; color:#fff; font-size:10px; position:absolute; left:4px; top:3px; }
.cd-preview-list { display:flex; gap:10px; overflow-x:auto; max-height:160px; padding:15px 20px; transition:max-height .28s ease-out,padding .28s ease-out; }
.cd-prev-item { position:relative; width:120px; height:120px; flex-shrink:0; border-radius:8px; overflow:hidden; box-shadow:0 2px 5px rgba(0,0,0,0.08); }
.cd-prev-item img { width:100%; height:100%; object-fit:cover; display:block; }
.cd-del-btn { position:absolute; top:5px; right:5px; width:25px; height:25px; border-radius:50%; border:none; cursor:pointer; background:var(--bot-bg); display:flex; align-items:center; justify-content:center; }
.cd-del-btn::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  
  /* The visible color of your SVG shape */
    background-color: #ff4500; 
  
  /* The SVG Mask */
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='100%25' height='100%25' fill='currentColor'%3E%3Cpath d='M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8ZM9 11H11V17H9V11ZM13 11H15V17H13V11ZM9 4V6H15V4H9Z'%3E%3C/path%3E%3C/svg%3E");
    mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='100%25' height='100%25' fill='currentColor'%3E%3Cpath d='M17 6H22V8H20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V8H2V6H7V3C7 2.44772 7.44772 2 8 2H16C16.5523 2 17 2.44772 17 3V6ZM18 8H6V20H18V8ZM9 11H11V17H9V11ZM13 11H15V17H13V11ZM9 4V6H15V4H9Z'%3E%3C/path%3E%3C/svg%3E");
  
  /* Mask configurations */
    -webkit-mask-repeat: no-repeat;
    mask-repeat: no-repeat;
    -webkit-mask-size: contain;
    mask-size: 18px;
    -webkit-mask-position: center;
    mask-position: center;
}
#cd-attach-menu,#cd-emoji-menu { position:absolute; bottom:80px; left:20px; background:var(--bot-bg); border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.12); padding:8px; z-index:100; width:200px; color:var(--bot-text); }
#cd-emoji-menu { left:auto; right:20px; width:250px; }
.cd-menu-item { display:flex; align-items:center; gap:10px; width:100%; padding:10px; border:none; background:none; cursor:pointer; border-radius:8px; color:var(--bot-text); font-size:14px; transition:background .12s; }
.cd-menu-item:hover { background:rgba(0,0,0,0.04); }
.cd-emoji-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:5px; }
.cd-emoji-grid span { font-size:20px; padding:5px; cursor:pointer; text-align:center; border-radius:4px; }
.cd-emoji-grid span:hover { background:rgba(0,0,0,0.04); }
#cd-input-wrap { padding:10px 20px; background:var(--popup-bg); z-index:10; flex-shrink:0; }
.cd-input-pill {background:var(--input-bg);border-radius:30px;display:flex;align-items:center;padding: 0;box-shadow:0 2px 6px rgba(0,0,0,0.05);position: relative;}
#cd-input {appearance: none;resize: none;height: 1.5em;line-height: 1.5em;min-width: 0px;width: 100%;font-size: 1em;-webkit-box-flex: 1;flex-grow: 1;max-width: 100%;font-family: inherit;padding: 11px 84px 11px 48px;overflow-y: auto;margin: 0px;color: rgb(17, 17, 17);background-color: rgb(251, 251, 251);border-radius: 32px;border: 1px solid rgb(227, 227, 227);box-shadow: rgba(0, 0, 0, 0.075) 0px 0.602187px 2.04744px -1.33333px, rgba(0, 0, 0, 0.067) 0px 2.28853px 7.78101px -2.66667px, rgba(0, 0, 0, 0.02) 0px 10px 34px -4px;transition: 0.15s;min-height: 46px;max-height: 120px;scrollbar-width: none;flex:1;border:none;outline:none;font-size:14px;color:var(--input-text);margin:0 10px;background:transparent;margin: 0;}
#cd-input::placeholder { color:var(--muted); }
.cd-action-btn {background: var(--popup-bg);border:none;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--footer-icon);transition:background .12s;margin-right:5px;}
.cd-action-btn:hover,.cd-action-btn.active { background:rgba(0,0,0,0.06); }
.cd-send-btn { background:var(--primary); border:none; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .12s; }
.cd-send-btn svg { stroke:#fff!important; }
#cd-footer { display:flex; justify-content:space-around; align-items:center; padding:10px 0; background:var(--footer-bg); border-top:1px solid rgba(0,0,0,0.04); margin:0 10px; border-radius:20px; flex-shrink:0; }
.cd-tab-btn { display:flex; flex-direction:column; align-items:center; background:none; border:none; cursor:pointer; color:var(--footer-icon); font-size:12px; font-weight:500; padding:5px 15px; transition:color .12s; }
.cd-tab-btn.active { color:var(--primary); }
.cd-tab-btn svg { width:24px; height:24px; margin-bottom:2px; stroke:currentColor!important; }
#cd-img-modal { position:fixed; inset:0; background:rgba(0,0,0,0.9); display:flex; align-items:center; justify-content:center; z-index:99999; }
#cd-modal-img { max-width:90%; max-height:90vh; object-fit:contain; }
#cd-modal-close { position:absolute; top:20px; right:30px; color:#fff; font-size:40px; font-weight:700; background:none; border:none; cursor:pointer; opacity:.85; line-height:1; }
#cd-modal-close:hover { opacity:1; }
.cd-hidden { display:none!important; }
.cd-input-pill #cd-attach-btn {position: absolute;left: 5px;}
.con-sub {position: absolute;right: 5px;display: flex;}
#cd-send-btn svg {color: var(--bubble-btn-ic);stroke: var(--bubble-btn-ic) !important;}
#cd-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:12px; color:var(--muted); font-size:13px; }
.cd-spinner { width:32px; height:32px; border:3px solid rgba(0,0,0,0.1); border-top-color:var(--primary); border-radius:50%; animation:cdspin .7s linear infinite; }
@keyframes cdspin { to { transform:rotate(360deg); } }
#cd-confirm-overlay {
    position:absolute; inset:0;
    background:rgba(0,0,0,0.45);
    display:flex; align-items:center; justify-content:center;
    z-index:300;
}
#cd-confirm-box {
    background: var(--bot-bg);
    border-radius: 14px;
    padding: 28px 24px;
    width: 290px;
    text-align: center;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
    animation: cdPopIn .2s ease;
}
@keyframes cdPopIn { from{transform:scale(.9);opacity:0} to{transform:scale(1);opacity:1} }
#cd-confirm-title {
    font-size:18px; font-weight:700;
    color:var(--popup-text); margin-bottom:8px;
}
#cd-confirm-msg {
    font-size:13px; color:var(--muted); margin-bottom:22px; line-height:1.5;
}
#cd-confirm-actions { display:flex; gap:10px; justify-content:center; }
.cd-confirm-cancel-btn {
    padding:9px 22px; border:1px solid rgba(0,0,0,0.12);
    border-radius:8px; background:#ebebeb;
    color:var(--popup-text); cursor:pointer; font-size:14px;
    transition:background .12s;
}
.cd-confirm-cancel-btn:hover { background:rgba(0,0,0,0.06); }
.cd-confirm-ok-btn {
    padding:9px 22px; border:none; border-radius:8px;
    background:var(--primary); color:#fff; cursor:pointer;
    font-size:14px; font-weight:600; transition:background .12s;
}
.cd-confirm-ok-btn:hover { background:var(--primary-hover); }
.cd-msg-wrap.system.post-form-wrap {
  width: 100%;
}
.cd-bubble-msg.system-msg.post-form {
  text-align: left;
  background: #fff;
  border-color: #000;
  padding: 10px 18px;
  color: #000;
}
.cd-bubble-msg.system-msg.post-form > strong {
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
#cd-home #cd-header {
    padding: 0;
    justify-content: flex-end;
}
#cd-email-transcript { display:none; }
#cd-email-transcript.active { display:flex!important; }
    `;

    /* ── 6. Inject HTML ──────────────────────────────────── */
    const wrap = document.createElement('div');
    wrap.id = 'cd-wrap';
    wrap.innerHTML = `
    <button id="cd-bubble">
        <svg id="cd-icon-chat" xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <span id="cd-badge">0</span>
    </button>

    <div id="cd-popup" class="">

        <div id="cd-loading" class="cd-hidden">
            <div class="cd-spinner"></div>
            <span>Connecting...</span>
        </div>

        <!-- HOME SCREEN -->
        <div id="cd-home" class="cd-hidden">
            <div id="cd-header">
                <div class="cd-hdr-side">
                    <button class="cd-icon-btn cd-minimize-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14" /></svg>
                    </button>
                </div>
            </div>
            <div id="cd-logo" class="cd-hidden"></div>
            <div class="cd-home-header">Welcome!</div>
            <div class="cd-home-title">Text us</div>
            <div class="cd-admin-card">
                <div class="cd-admin-badge">
                    <div class="cd-avatar cd-agent-avatar">A<span class="cd-status-dot"></span></div>
                    <span class="cd-admin-name">Admin</span>
                </div>
                <div class="cd-admin-info">Hello. How may I help you?</div>
                <button id="cd-start-btn" class="cd-start-btn">
                    Start chat
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                </button>
            </div>
        </div>

        <!-- POST-CHAT FORM -->
        <div id="cd-postchat"></div>

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
                    <div class="cd-avatar cd-agent-avatar">A<span class="cd-status-dot"></span></div>
                    <span class="cd-admin-name" id="cd-online-label">Admin</span>
                </div>
                <div class="cd-hdr-side">
                    <button class="cd-icon-btn cd-minimize-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14" /></svg>
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
                <div class="cd-opt-item" id="cd-update-info-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Update my info</span>
                </div>
                <div class="cd-opt-item" id="cd-close-chat-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span>Close chat</span>
                </div>
                <div class="cd-opt-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                    <span style="flex:1">Sounds</span>
                    <label class="cd-switch"><input type="checkbox" id="cd-sound-toggle" checked><span class="cd-slider"></span></label>
                </div>
            </div>
            
            <div id="cd-confirm-overlay" class="cd-hidden">
                <div id="cd-confirm-box">
                    <div id="cd-confirm-title">End Chat?</div>
                    <div id="cd-confirm-msg">Are you sure you want to end this conversation? You won't be able to continue this chat.</div>
                    <div id="cd-confirm-actions">
                        <button id="cd-confirm-cancel" class="cd-confirm-cancel-btn">Cancel</button>
                        <button id="cd-confirm-ok" class="cd-confirm-ok-btn">End Chat</button>
                    </div>
                </div>
            </div>

            <div id="cd-name-bar">
                <label>Your name:</label>
                <input type="text" id="cd-name-input" placeholder="Enter name..." maxlength="50">
            </div>

            <div id="cd-info-request-banner">
                <p>👋 The agent would like to know you better</p>
                <input type="text" id="cd-info-req-name" placeholder="Your name *" maxlength="80">
                <input type="email" id="cd-info-req-email" placeholder="Your email (optional)" maxlength="120">
                <button id="cd-info-req-submit">Submit</button>
            </div>

            <div id="cd-messages"></div>

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
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button><textarea id="cd-input" placeholder="Write a message..."></textarea>
                    
                    <div class="con-sub">
                        <button class="cd-action-btn" id="cd-emoji-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
                        </button>
                        <button class="cd-send-btn" id="cd-send-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- EMAIL TRANSCRIPT POPUP -->
            <div id="cd-email-transcript" class="cd-hidden" style="position:absolute;inset:0;background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;z-index:300;">
                <div style="background:var(--bot-bg);border-radius:14px;padding:28px 24px;width:300px;text-align:center;box-shadow:0 12px 40px rgba(0,0,0,0.3);animation:cdPopIn .2s ease;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <button id="cd-et-close" style="background:none;border:none;cursor:pointer;color:var(--popup-text);font-size:20px;line-height:1;">✕</button>
                    </div>
                    <h3 style="font-size:16px;font-weight:700;color:var(--popup-text);margin-bottom:6px;">Send transcript</h3>
                    <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">Send the chat transcript to your e-mail.</p>
                    <input type="email" id="cd-et-email" placeholder="your@email.com" style="width:100%;padding:10px 14px;border:1px solid rgba(0,0,0,0.15);border-radius:8px;font-size:14px;outline:none;background:var(--input-bg);color:var(--input-text);margin-bottom:4px;">
                    <p id="cd-et-error" style="color:#ef4444;font-size:12px;text-align:left;margin:0 0 12px;display:none;">Please fill in required fields.</p>
                    <button id="cd-et-send" style="width:100%;padding:11px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:background .15s;">Send</button>
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

        <!-- BRANDING -->
        <div id="cd-branding">Powered By <a href="https://chatdesk360.com" target="_blank" rel="noopener">ChatDesk360</a></div>

    </div>

    <div id="cd-img-modal" class="cd-hidden">
        <button id="cd-modal-close">&times;</button>
        <img id="cd-modal-img" src="" alt="Preview">
    </div>
    `;

    /* ── 7. State ────────────────────────────────────────── */
    let isOpen      = false;
    let view        = 'home'; 
    let unread      = 0;
    let filesToSend = [];
    let fbReady     = false;
    let activeRoomPath = null;
    let visitorTypingTimer = null;
    let initialMessagesLoaded = false;
    let authorized  = false;
    let heartbeatTimer;

    let fbDb = null;
    let fbRefFn = null;
    let fbSetFn = null;
    let fbPushFn = null;
    let fbRemoveFn = null;

    /* ── 8. Element refs ─────────────────────────────────── */
    let popup, badge, bubble, homeScreen, convScreen, footer, loadingEl;
    let messagesBox, typingBar, nameInput, chatInput;
    let filePreview, attachMenu, emojiMenu, optionsMenu, imgModal;
    let infoRequestBanner, postChatEl, brandingEl;

    /* ── 9. APPLY WIDGET SETTINGS ────────────────────────── */
    function applyWidgetSettings() {
        const w = document.getElementById('cd-wrap');
        if (!w) return;

        const root = w;
        function shade(hex, pct) {
            if (!hex || hex.length < 7) return hex;
            let r = parseInt(hex.substring(1, 3), 16);
            let g = parseInt(hex.substring(3, 5), 16);
            let b = parseInt(hex.substring(5, 7), 16);
            r = Math.min(255, Math.max(0, Math.round(r * (100 + pct) / 100)));
            g = Math.min(255, Math.max(0, Math.round(g * (100 + pct) / 100)));
            b = Math.min(255, Math.max(0, Math.round(b * (100 + pct) / 100)));
            return '#' + [r, g, b].map(c => c.toString(16).padStart(2, '0')).join('');
        }

        const isDark = WS.theme === 'dark';
        root.classList.toggle('dark', isDark);

        root.style.setProperty('--cd-primary-color', WS.primary_color);
        root.style.setProperty('--cd-primary-hover', shade(WS.primary_color, -18));
        root.style.setProperty('--cd-bubble-btn-bg', WS.primary_color);

        if (WS.use_custom_colors) {
            root.style.setProperty('--cd-popup-bg', WS.widget_bg_color);
            root.style.setProperty('--cd-popup-text', WS.widget_text_color);
            root.style.setProperty('--cd-bot-bg', WS.widget_bg_color);
            root.style.setProperty('--cd-bot-text', WS.widget_text_color);
        } else {
            if (isDark) {
                root.style.setProperty('--cd-popup-bg', '#1e1e1e');
                root.style.setProperty('--cd-popup-text', '#f5f5f5');
                root.style.setProperty('--cd-bot-bg', '#2b2b2b');
                root.style.setProperty('--cd-bot-text', '#e6e6e6');
                root.style.setProperty('--cd-header-icon', '#ffffff');
                root.style.setProperty('--cd-input-bg', '#2b2b2b');
                root.style.setProperty('--cd-input-text', '#f2f2f2');
                root.style.setProperty('--cd-footer-bg', '#2b2b2b');
                root.style.setProperty('--cd-footer-icon', '#e6e6e6');
                root.style.setProperty('--cd-bubble-btn-bg', '#111111');
            } else {
                root.style.setProperty('--cd-popup-bg', '#f7f7f7');
                root.style.setProperty('--cd-popup-text', '#1f2937');
                root.style.setProperty('--cd-bot-bg', '#ffffff');
                root.style.setProperty('--cd-bot-text', '#1f2937');
                root.style.setProperty('--cd-header-icon', '#1f2937');
                root.style.setProperty('--cd-input-bg', '#ffffff');
                root.style.setProperty('--cd-input-text', '#374151');
                root.style.setProperty('--cd-footer-bg', '#ffffff');
                root.style.setProperty('--cd-footer-icon', '#9ca3af');
                root.style.setProperty('--cd-bubble-btn-bg', WS.primary_color);
            }
        }

        const hdr = document.querySelector('.cd-home-header');
        const ttl = document.querySelector('.cd-home-title');
        const adm = document.querySelectorAll('.cd-admin-name');
        const msg = document.querySelector('.cd-admin-info');
        if (hdr) hdr.textContent = WS.welcome_header || 'Welcome!';
        if (ttl) ttl.textContent = WS.welcome_title || 'Text us';
        adm.forEach(el => el.textContent = WS.admin_name || 'Admin');
        if (msg) msg.textContent = WS.welcome_message || 'Hello. How may I help you?';

        const logoEl = document.getElementById('cd-logo');
        if (logoEl) {
            if (WS.show_logo && WS.logo_url) {
                logoEl.innerHTML = '<img src="' + WS.logo_url + '" alt="Logo">';
                logoEl.classList.remove('cd-hidden');
            } else {
                logoEl.classList.add('cd-hidden');
            }
        }

        document.querySelectorAll('.cd-agent-avatar').forEach(el => {
            el.style.display = WS.show_agent_photo ? '' : 'none';
        });

        if (brandingEl) {
            brandingEl.style.display = WS.white_label ? 'none' : '';
        }

        const transcriptBtn = document.getElementById('cd-transcript-btn');
        if (transcriptBtn) {
            transcriptBtn.style.display = WS.allow_transcripts ? '' : 'none';
        }

        const soundToggle = document.getElementById('cd-sound-toggle');
        if (soundToggle) soundToggle.checked = WS.sound_notifications;
        
        notifyParentResize();
    }

    /* ── 10. View switch ─────────────────────────────────── */
    function showView(v) {
        view = v;
        loadingEl.classList.add('cd-hidden');
        footer.classList.remove('cd-hidden');
    
        postChatEl.classList.remove('active');
    
        if (v === 'home') {
            homeScreen.classList.remove('cd-hidden');
            convScreen.classList.add('cd-hidden');
            document.getElementById('cd-home-tab').classList.add('active');
            document.getElementById('cd-chat-tab').classList.remove('active');
            updateVisitorStatus('browsing');
        } else if (v === 'chat') {
            homeScreen.classList.add('cd-hidden');
            convScreen.classList.remove('cd-hidden');
            footer.classList.add('cd-hidden');
            document.getElementById('cd-chat-tab').classList.add('active');
            document.getElementById('cd-home-tab').classList.remove('active');
            chatInput.focus();
            updateVisitorStatus('chatting');
        } else if (v === 'postchat') {
            homeScreen.classList.add('cd-hidden');
            convScreen.classList.add('cd-hidden');
            footer.classList.add('cd-hidden');
            renderPostChatForm();
            postChatEl.classList.add('active');
        }
        closeAllMenus();
    }

    /* ── 11. Popup toggle ─────────────────────────────── */
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
            // ★ Don't set view = 'postchat' here — it causes post-chat to show on next open
        }
        notifyParentResize();
    }

    /* ── 12. Badge ───────────────────────────────────────── */
    function updateBadge() {
        if (unread > 0) {
            badge.textContent = unread > 99 ? '99+' : unread;
            badge.classList.add('show');
        } else {
            badge.classList.remove('show');
        }
    }

    /* ── 13. Menus ───────────────────────────────────────── */
    function closeAllMenus() {
        [attachMenu, emojiMenu, optionsMenu].forEach(m => m.classList.add('cd-hidden'));
        ['cd-attach-btn','cd-emoji-btn','cd-options-btn'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.remove('active');
        });
    }

    function toggleMenu(menu, btnId) {
        const wasHidden = menu.classList.contains('cd-hidden');
        closeAllMenus();
        if (wasHidden) {
            menu.classList.remove('cd-hidden');
            document.getElementById(btnId).classList.add('active');
        }
    }

    /* ── 14. Render helpers ──────────────────────────────── */
    function esc(s) {
        const d = document.createElement('div');
        d.textContent = s ?? '';
        return d.innerHTML;
    }

    function appendBotMsg(text) {
        const w = document.createElement('div');
        w.className = 'cd-msg-wrap bot';
        w.innerHTML = `<div class="cd-bubble-msg bot">${esc(text)}</div>`;
        messagesBox.appendChild(w);
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function renderMessage(msgId, d, isMine) {
        if (document.getElementById('cdm_' + msgId)) return;

        if (d.type === 'info_request' || d.type === 'info_filled' || d.type === 'system' || d.type === 'post_chat_response') {
            const w = document.createElement('div');
            w.id = 'cdm_' + msgId;
            w.className = 'cd-msg-wrap system';
            let txt = '';
            
            if (d.type === 'post_chat_response') {
                w.className = 'cd-msg-wrap system post-form-wrap';
                const data = d.response_data || {};
                let html = '<strong>Post-chat Feedback</strong>';
                for (const [question, answer] of Object.entries(data)) {
                    const displayAnswer = Array.isArray(answer) ? answer.join(', ') : answer;
                    html += `<div class="post-form-field"><p class="question"><strong>${esc(question)}:</strong></p> <div class="answer">${esc(displayAnswer || '-')}</div></div>`;
                }
                w.innerHTML = `<div class="cd-bubble-msg system-msg post-form">${html}</div>`;
                messagesBox.appendChild(w);
                messagesBox.scrollTop = messagesBox.scrollHeight;
                return;
            }
            
            if (d.type === 'info_request') txt = '🔔 Agent is asking for your name & email';
            else if (d.type === 'info_filled') txt = `✅ Info shared: ${esc(d.visitor_name ?? '')}${d.visitor_email ? ' · ' + esc(d.visitor_email) : ''}`;
            else txt = esc(d.message ?? '');
            w.innerHTML = `<div class="cd-bubble-msg system-msg">${txt}</div>`;
            messagesBox.appendChild(w);
            messagesBox.scrollTop = messagesBox.scrollHeight;
            return;
        }

        const w = document.createElement('div');
        w.className = 'cd-msg-wrap ' + (isMine ? 'user' : 'bot');
        w.id = 'cdm_' + msgId;
        const time = new Date(d.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        let content = '';
        
        if (d.fileUrl && d.fileType === 'image') {
            let fileUrl = d.fileUrl;
            if (fileUrl.includes('/storage/chat-files/')) {
                fileUrl = fileUrl.replace('/storage/chat-files/', '/api/chat-files/');
            }
            const fullUrl = fileUrl.startsWith('http') ? fileUrl : API_BASE.replace('/api', '') + fileUrl;
            content = `<img src="${fullUrl}" class="cd-chat-img" alt="${esc(d.fileName || 'image')}">`;
        } else if (d.fileUrl && d.fileType === 'file') {
            let fileUrl = d.fileUrl;
            if (fileUrl.includes('/storage/chat-files/')) {
                fileUrl = fileUrl.replace('/storage/chat-files/', '/api/chat-files/');
            }
            const fullUrl = fileUrl.startsWith('http') ? fileUrl : API_BASE.replace('/api', '') + fileUrl;
            content = `
                <a href="${fullUrl}" target="_blank" style="color:inherit;text-decoration:underline;display:flex;align-items:center;gap:6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    ${esc(d.fileName || d.message)}
                </a>`;
        } else if (d.imageData) {
            content = `<img src="${d.imageData}" class="cd-chat-img" alt="image">`;
        } else {
            content = esc(d.message);
        }

        let senderLabel = '';
        if (!isMine) {
            const agentName = d.agent_name || 'Support';
            senderLabel = `<div style="font-size:10px;color:var(--primary);margin-bottom:3px;font-weight:600;">${esc(agentName)}</div>`;
        }

        w.innerHTML = `
            ${senderLabel}
            <div class="cd-bubble-msg ${isMine ? 'user' : 'bot'}">${content}</div>
            <div class="cd-read">${time}${isMine ? ` <span class="cd-tick" id="cdtick_${msgId}">✓</span>` : ''}</div>`;

        const chatImg = w.querySelector('.cd-chat-img');
        if (chatImg) {
            let fileUrl = d.fileUrl || '';
            if (fileUrl.includes('/storage/chat-files/')) {
                fileUrl = fileUrl.replace('/storage/chat-files/', '/api/chat-files/');
            }
            const imgSrc = fileUrl
                ? (fileUrl.startsWith('http') ? fileUrl : API_BASE.replace('/api', '') + fileUrl)
                : d.imageData;
            chatImg.addEventListener('click', () => {
                window.parent.postMessage({
                    type: '__cd_image_preview',
                    url:  imgSrc
                }, '*');
            });
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

    /* ── 15. POST-CHAT FORM ──────────────────────────────── */
    function renderPostChatForm() {
        if (!postChatEl || !postChatConfig.enabled || !postChatConfig.fields || !postChatConfig.fields.length) {
            showView('home');
            return;
        }

        postChatEl.innerHTML = '';
        const headerDiv = document.createElement('div');
        headerDiv.style.cssText = 'padding:20px 20px 10px; font-size:20px; font-weight:700; color:var(--popup-text);';
        headerDiv.textContent = 'How was your chat?';
        postChatEl.appendChild(headerDiv);

        const fields = postChatConfig.fields;
        fields.forEach((field, idx) => {
            const wrap = document.createElement('div');
            wrap.className = 'cd-pc-field';

            if (field.type === 'thankyou') {
                wrap.innerHTML = `<div class="cd-pc-label">${esc(field.text || 'Thank you for chatting with us!')}</div>`;
            } else if (field.type === 'question') {
                wrap.innerHTML = `<div class="cd-pc-label">${esc(field.label || '')}</div><input class="cd-pc-input" type="text" data-field-idx="${idx}" placeholder="Your answer...">`;
            } else if (field.type === 'message') {
                wrap.innerHTML = `<div class="cd-pc-label">${esc(field.label || '')}</div><textarea class="cd-pc-textarea" data-field-idx="${idx}" placeholder="Write your message..." rows="3"></textarea>`;
            } else if (field.type === 'choice') {
                const opts = (field.options || ['Yes', 'No']).map((o, oi) =>
                    `<label class="cd-pc-radio-label"><input type="radio" name="pc_field_${idx}" value="${esc(o)}" data-field-idx="${idx}">${esc(o)}</label>`
                ).join('');
                wrap.innerHTML = `<div class="cd-pc-label">${esc(field.label || '')}</div><div class="cd-pc-radio-group">${opts}</div>`;
            } else if (field.type === 'dropdown') {
                const opts = (field.options || []).map(o => `<option>${esc(o)}</option>`).join('');
                wrap.innerHTML = `<div class="cd-pc-label">${esc(field.label || '')}</div><select class="cd-pc-select" data-field-idx="${idx}"><option value="">Select...</option>${opts}</select>`;
            } else if (field.type === 'multiple') {
                const opts = (field.options || []).map((o, oi) =>
                    `<label class="cd-pc-check-label"><input type="checkbox" name="pc_field_${idx}" value="${esc(o)}" data-field-idx="${idx}">${esc(o)}</label>`
                ).join('');
                wrap.innerHTML = `<div class="cd-pc-label">${esc(field.label || '')}</div><div class="cd-pc-check-group">${opts}</div>`;
            } else if (field.type === 'rating') {
                wrap.innerHTML = `<div class="cd-pc-label">${esc(field.label || 'How would you rate this chat?')}</div><div class="cd-pc-rating-group"><button type="button" class="cd-pc-rate-btn" data-value="up" data-field-idx="${idx}">👍</button><button type="button" class="cd-pc-rate-btn" data-value="down" data-field-idx="${idx}">👎</button></div>`;
            }
            postChatEl.appendChild(wrap);
        });

        const submitBtn = document.createElement('button');
        submitBtn.className = 'cd-pc-submit';
        submitBtn.textContent = 'Submit';
        submitBtn.addEventListener('click', submitPostChatForm);
        postChatEl.appendChild(submitBtn);

        postChatEl.querySelectorAll('.cd-pc-rate-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const group = this.closest('.cd-pc-rating-group');
                group.querySelectorAll('.cd-pc-rate-btn').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    }

    function submitPostChatForm() {
        const fields = postChatConfig.fields || [];
        const responseData = {};

        fields.forEach((field, idx) => {
            if (field.type === 'thankyou') return;
            if (field.type === 'question' || field.type === 'message') {
                const inp = postChatEl.querySelector(`[data-field-idx="${idx}"]`);
                responseData[field.label || `field_${idx}`] = inp ? inp.value.trim() : '';
            } else if (field.type === 'choice') {
                const checked = postChatEl.querySelector(`input[name="pc_field_${idx}"]:checked`);
                responseData[field.label || `field_${idx}`] = checked ? checked.value : '';
            } else if (field.type === 'dropdown') {
                const sel = postChatEl.querySelector(`select[data-field-idx="${idx}"]`);
                responseData[field.label || `field_${idx}`] = sel ? sel.value : '';
            } else if (field.type === 'multiple') {
                const checked = postChatEl.querySelectorAll(`input[name="pc_field_${idx}"]:checked`);
                responseData[field.label || `field_${idx}`] = Array.from(checked).map(c => c.value);
            } else if (field.type === 'rating') {
                const selected = postChatEl.querySelector(`.cd-pc-rate-btn.selected[data-field-idx="${idx}"]`);
                responseData[field.label || `field_${idx}`] = selected ? selected.dataset.value : '';
            }
        });

        if (API_BASE) {
            fetch(API_BASE + '/widget/post-chat-response', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ site_id: SITE_ID, visitor_id: visitorId, response_data: responseData })
            }).catch(() => {});
        }

        postChatShown = true;
        postChatDisplayed = true;
        localStorage.setItem('lc_postchat_filled_' + visitorId, '1');

        if (fbReady && activeRoomPath) {
            if (window._cdPushPostChat) window._cdPushPostChat(responseData);
        }

        postChatEl.innerHTML = `
            <div class="cd-pc-thankyou">
                <h3>Thank you! 🎉</h3>
                <p>Your feedback has been submitted.</p>
                <button class="cd-start-btn" style="margin-top:20px;" id="cd-postchat-close-btn">Close</button>
            </div>
        `;

        document.getElementById('cd-postchat-close-btn').addEventListener('click', () => {
            postChatEl.classList.remove('active');
            isOpen = false;
            popup.classList.remove('open');
            notifyParentResize();
        });
    }

    /* ── 16. File preview ────────────────────────────────── */
    function renderFilePreview() {
        const list  = filePreview.querySelector('.cd-preview-list');
        const count = filePreview.querySelector('.cd-preview-count');
        if (!filesToSend.length) {
            filePreview.classList.add('cd-hidden');
            list.innerHTML = '';
            return;
        }
        filePreview.classList.remove('cd-hidden', 'collapsed');
        count.textContent = `${filesToSend.length} of 5 uploaded`;
        list.innerHTML = '';
        filesToSend.forEach((file, i) => {
            const item = document.createElement('div');
            item.className = 'cd-prev-item';
            if (file.type.startsWith('image/')) {
                const r = new FileReader();
                r.onload = e => {
                    item.innerHTML = `<img src="${e.target.result}" alt="">`;
                    addDel(item, i);
                };
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
        btn.addEventListener('click', e => {
            e.stopPropagation();
            filesToSend.splice(idx, 1);
            renderFilePreview();
        });
        item.appendChild(btn);
    }

    /* ── 17. Info request form ───────────────────────────── */
    let _fulfillInfoRequest = null;

    function showInfoRequestForm() {
        if (!infoRequestBanner) return;

        const savedName  = localStorage.getItem('lc_visitor_name') || '';
        const savedEmail = localStorage.getItem('lc_visitor_email') || '';

        const nameInp  = document.getElementById('cd-info-req-name');
        const emailInp = document.getElementById('cd-info-req-email');
        if (nameInp)  nameInp.value  = savedName;
        if (emailInp) emailInp.value = savedEmail;

        infoRequestBanner.style.display = 'block';
        messagesBox.scrollTop = messagesBox.scrollHeight;

        const submitBtn = document.getElementById('cd-info-req-submit');
        const newBtn = submitBtn.cloneNode(true);
        submitBtn.parentNode.replaceChild(newBtn, submitBtn);

        newBtn.addEventListener('click', () => {
            const name  = document.getElementById('cd-info-req-name').value.trim();
            const email = document.getElementById('cd-info-req-email').value.trim();

            if (!name) {
                document.getElementById('cd-info-req-name').style.borderColor = '#ef4444';
                return;
            }
            document.getElementById('cd-info-req-name').style.borderColor = '';

            infoRequestBanner.style.display = 'none';
            if (nameInput) nameInput.value = name;

            localStorage.setItem('lc_visitor_name', name);
            if (email) localStorage.setItem('lc_visitor_email', email);

            if (typeof _fulfillInfoRequest === 'function') {
                _fulfillInfoRequest(name, email);
            }
        });
    }

    /* ── 18. Bind all UI events ──────────────────────────── */
    function bindUIEvents() {
        document.getElementById('cd-bubble').addEventListener('click', togglePopup);
        document.getElementById('cd-close-btn').addEventListener('click', () => {
            if (chatHasMessages) {
                document.getElementById('cd-confirm-overlay').classList.remove('cd-hidden');
            } else {
                togglePopup();
            }
        });
        document.querySelectorAll('.cd-minimize-btn').forEach(function(btn) {
            btn.addEventListener('click', togglePopup);
        });
        document.getElementById('cd-start-btn').addEventListener('click', () => {
            showView('chat');
            if (typeof window._cdInitActiveChat === 'function') {
                window._cdInitActiveChat();
            }
        });
        document.getElementById('cd-back-btn').addEventListener('click', () => showView('home'));
        document.getElementById('cd-home-tab').addEventListener('click', () => showView('home'));
        document.getElementById('cd-chat-tab').addEventListener('click', () => showView('chat'));

        document.getElementById('cd-attach-btn').addEventListener('click', e => {
            e.stopPropagation();
            toggleMenu(attachMenu, 'cd-attach-btn');
        });
        document.getElementById('cd-emoji-btn').addEventListener('click', e => {
            e.stopPropagation();
            toggleMenu(emojiMenu, 'cd-emoji-btn');
        });
        document.getElementById('cd-options-btn').addEventListener('click', e => {
            e.stopPropagation();
            toggleMenu(optionsMenu, 'cd-options-btn');
        });

        document.addEventListener('click', e => {
            if (!e.target.closest('#cd-attach-btn,#cd-attach-menu,#cd-emoji-btn,#cd-emoji-menu,#cd-options-btn,#cd-options-menu'))
                closeAllMenus();
        });

        document.querySelectorAll('.cd-emoji-grid span').forEach(em => {
            em.addEventListener('click', () => {
                chatInput.value += em.textContent;
                chatInput.focus();
                closeAllMenus();
            });
        });
        
        chatInput.addEventListener('input', function() {
            this.style.transition = 'none';
            this.style.height = '0px';
            var newHeight = Math.max(46, Math.min(this.scrollHeight, 120));
            this.style.height = newHeight + 'px';
            requestAnimationFrame(() => { this.style.transition = ''; });
        });

        document.getElementById('cd-send-file-btn').addEventListener('click', () => {
            closeAllMenus();
            document.getElementById('cd-file-input').click();
        });

        document.getElementById('cd-file-input').addEventListener('change', function () {
            Array.from(this.files).forEach(f => {
                if (filesToSend.length < 5) filesToSend.push(f);
            });
            renderFilePreview();
            this.value = '';
        });

        filePreview.querySelector('.cd-preview-hdr').addEventListener('click', () => {
            filePreview.classList.toggle('collapsed');
        });

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
                            if (filesToSend.length < 5) {
                                filesToSend.push(new File([blob], 'screenshot.png', { type: 'image/png' }));
                                renderFilePreview();
                            }
                        }, 'image/png');
                    }, 500);
                };
            } catch (e) {}
        });

        window.addEventListener('message', function(e) {
            if (e.data && e.data.type === '__cd_image_closed') {
                if (imgModal) imgModal.classList.add('cd-hidden');
            }
        });

        document.getElementById('cd-transcript-btn').addEventListener('click', () => {
            closeAllMenus();
            const etPopup = document.getElementById('cd-email-transcript');
            etPopup.classList.add('active');
            etPopup.classList.remove('cd-hidden');
            etPopup.style.display = 'flex';
            document.getElementById('cd-et-email').value = '';
            document.getElementById('cd-et-error').style.display = 'none';
        });

        document.getElementById('cd-et-close').addEventListener('click', () => {
            const etPopup = document.getElementById('cd-email-transcript');
            etPopup.classList.remove('active');
            etPopup.style.display = 'none';
        });

        document.getElementById('cd-et-send').addEventListener('click', () => {
            const email = document.getElementById('cd-et-email').value.trim();
            const errorEl = document.getElementById('cd-et-error');

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errorEl.style.display = 'block';
                return;
            }
            errorEl.style.display = 'none';

            const requestBody = {
                site_id: SITE_ID,
                visitor_id: visitorId,
                email: email,
                domain_id: DOMAIN_ID,
            };

            fetch(API_BASE + '/widget/send-transcript', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(requestBody)
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                const etPopup = document.getElementById('cd-email-transcript');
                etPopup.classList.remove('active');
                etPopup.style.display = 'none';
                appendBotMsg('📧 Transcript sent to ' + email);
            })
            .catch(err => {
                console.error('📧 Transcript API Error:', err);
            });
        });

        document.getElementById('cd-update-info-btn').addEventListener('click', () => {
            closeAllMenus();
            showInfoRequestForm();
        });
        
        document.getElementById('cd-close-chat-btn').addEventListener('click', () => {
            closeAllMenus();
            if (!chatHasMessages) {
                isOpen = false;
                popup.classList.remove('open');
                notifyParentResize();
                return;
            }
            document.getElementById('cd-confirm-overlay').classList.remove('cd-hidden');
        });
        
        document.getElementById('cd-confirm-cancel').addEventListener('click', () => {
            document.getElementById('cd-confirm-overlay').classList.add('cd-hidden');
        });
        
        document.getElementById('cd-confirm-ok').addEventListener('click', () => {
            document.getElementById('cd-confirm-overlay').classList.add('cd-hidden');

            // ★ Set status in Firebase (for agent dashboard)
            if (fbReady && activeRoomPath) {
                try { if (window._cdSetStatus) window._cdSetStatus('ended'); } catch(e) {}
            }

            // ★ SIMPLE: Show post-chat form directly if enabled
            var alreadyFilled = localStorage.getItem('lc_postchat_filled_' + visitorId);
            if (postChatConfig.enabled && postChatConfig.fields && postChatConfig.fields.length && !alreadyFilled && !postChatShown) {
                postChatDisplayed = true;
                showView('postchat');
                return;
            }

            // ★ No post-chat — go home and close after delay
            showView('home');
            setTimeout(() => {
                isOpen = false;
                popup.classList.remove('open');
                notifyParentResize();
            }, 2000);
        });
    }

    function getConfigFromParent() {
        return new Promise(function (resolve, reject) {
            var timeout = setTimeout(function () {
                reject(new Error('Config timeout — no response from parent'));
            }, 10000);

            window.parent.postMessage({ type: '__cd_request_config' }, '*');

            function handler(e) {
                if (e.data && e.data.type === '__cd_config' && e.data.config) {
                    clearTimeout(timeout);
                    window.removeEventListener('message', handler);
                    resolve(e.data.config);
                }
            }
            window.addEventListener('message', handler);
        });
    }

    /* ── 19. FETCH FIREBASE CONFIG + WIDGET SETTINGS ─────── */
    Promise.all([
        getConfigFromParent(),

        fetch(API_BASE + '/visitor/check-ban', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body:    JSON.stringify({ visitor_id: visitorId, site_id: SITE_ID })
        }).then(r => r.json()).catch(() => ({ banned: false })),

        fetch(API_BASE + '/widget/settings', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body:    JSON.stringify({ site_id: SITE_ID })
        }).then(r => r.json()).catch(() => ({ settings: {} })),

        fetch(API_BASE + '/widget/post-chat-config', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body:    JSON.stringify({ site_id: SITE_ID })
        }).then(r => r.json()).then(data => {
            return data;
        }).catch(err => {
            console.error('[ChatDesk360] Post-chat config error:', err);
            return { enabled: false, fields: [] };
        }),
    ])
    .then(([config, banData, widgetSettingsData, postChatData]) => {
        if (!config.apiKey || !config.projectId) throw new Error('Invalid config response');
    
        authorized = true;
    
        if (widgetSettingsData && widgetSettingsData.settings) {
            WS = { ...DEFAULT_SETTINGS, ...widgetSettingsData.settings };
        }
    
        if (postChatData && postChatData.enabled) {
            postChatConfig = { enabled: true, fields: postChatData.fields || [] };
        }
    
        if (banData.banned) {
            startUnbanPoll();
            return;
        }
    
        initWidget(config);
    })
    .catch(() => {});

    /* ── Widget hide/show helpers ─────────────────────────── */
    let widgetHidden = false;

    function hideWidget() {
        if (widgetHidden) return;
        widgetHidden = true;
        const wrapEl = document.getElementById('cd-wrap');
        if (wrapEl) wrapEl.style.display = 'none';
        if (popup) popup.classList.remove('open');
        isOpen = false;
        notifyParentResize();
        clearInterval(heartbeatTimer);
    }

    function showWidget() {
        if (!widgetHidden) return;
        widgetHidden = false;
        const wrapEl = document.getElementById('cd-wrap');
        if (wrapEl) wrapEl.style.display = '';
        heartbeatTimer = setInterval(sendHeartbeat, 30000);
        notifyParentResize();
    }

    function startUnbanPoll() {
        const pollId = setInterval(() => {
            fetch(API_BASE + '/visitor/check-ban', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body:    JSON.stringify({ visitor_id: visitorId, site_id: SITE_ID })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.banned) {
                    clearInterval(pollId);
                    window.parent.postMessage({ type: '__cd_request_config' }, '*');
                    
                    function configHandler(e) {
                        if (e.data && e.data.type === '__cd_config' && e.data.config) {
                            window.removeEventListener('message', configHandler);
                            var config = e.data.config;
                            if (config.apiKey && config.projectId) initWidget(config);
                        }
                    }
                    window.addEventListener('message', configHandler);
                }
            })
            .catch(() => {});
        }, 15000);
    }

    /* ── 20. INIT WIDGET ─────────────────────────────────── */
    function initWidget(config) {
        if (!document.getElementById('cd-styles')) {
            const styleEl = document.createElement('style');
            styleEl.id = 'cd-styles';
            styleEl.textContent = CSS;
            document.head.appendChild(styleEl);
        }
    
        if (!document.getElementById('cd-wrap')) {
            document.body.appendChild(wrap);
        } else {
            document.getElementById('cd-wrap').style.display = '';
            widgetHidden = false;
        }
    
        heartbeatTimer = setInterval(sendHeartbeat, 30000);

        popup              = document.getElementById('cd-popup');
        badge              = document.getElementById('cd-badge');
        bubble              = document.getElementById('cd-bubble');
        homeScreen         = document.getElementById('cd-home');
        convScreen         = document.getElementById('cd-conv');
        footer             = document.getElementById('cd-footer');
        loadingEl          = document.getElementById('cd-loading');
        messagesBox        = document.getElementById('cd-messages');
        typingBar          = document.getElementById('cd-typing');
        nameInput          = document.getElementById('cd-name-input');
        chatInput          = document.getElementById('cd-input');
        filePreview        = document.getElementById('cd-file-preview');
        attachMenu         = document.getElementById('cd-attach-menu');
        emojiMenu          = document.getElementById('cd-emoji-menu');
        optionsMenu        = document.getElementById('cd-options-menu');
        imgModal           = document.getElementById('cd-img-modal');
        infoRequestBanner  = document.getElementById('cd-info-request-banner');
        postChatEl         = document.getElementById('cd-postchat');
        brandingEl         = document.getElementById('cd-branding');

        const savedName = localStorage.getItem('lc_visitor_name');
        if (savedName) {
            nameInput.value = savedName;
            document.getElementById('cd-name-bar').style.display = 'flex';
        }

        bindUIEvents();
        applyWidgetSettings();

        (async () => {
            await trackVisitor();
            trackPageStart();
        })();

        initFirebase(config);
    }

    // ★ Reinitialize Firebase paths when DOMAIN_ID changes
    function reinitFirebasePaths() {
        if (!fbReady || !fbDb || !fbRefFn || !fbSetFn) return;
        
        const basePath = `chats/${DOMAIN_ID}/general/${visitorId}`;
        activeRoomPath = basePath;
        
        const trafficRef = fbRefFn(fbDb, `presence/${SITE_ID}/${visitorId}`);
        fbSetFn(trafficRef, {
            online: true, last_seen: Date.now(), visitor_id: visitorId,
            domain_id: DOMAIN_ID,
        });
        
        trackVisitor();
    }

    /* ── 21. Firebase init ───────────────────────────────── */
    function initFirebase(fbConfig) {
        const FB_BASE = 'https://www.gstatic.com/firebasejs/10.12.0';
        Promise.all([
            import(`${FB_BASE}/firebase-app.js`),
            import(`${FB_BASE}/firebase-database.js`)
        ]).then(([{ initializeApp }, {
            getDatabase, ref, push, set, update, remove,
            onChildAdded, onChildChanged, onValue,
            query, orderByChild, limitToLast, onDisconnect
        }]) => {

            const app = initializeApp(fbConfig, 'cd-widget-' + SITE_ID);
            const db  = getDatabase(app);

            fbDb = db;
            fbRefFn = ref;
            fbSetFn = set;
            fbPushFn = push;
            fbRemoveFn = remove;

            const basePath   = `chats/${DOMAIN_ID}/general/${visitorId}`;
            activeRoomPath   = basePath;
            const msgsPath     = `${basePath}/messages`;
            const typingPath   = `${basePath}/typing`;
            const presencePath = `${basePath}/presence`;

            onValue(ref(db, `banned_visitors/${SITE_ID}/${visitorId}`), snap => {
                if (snap.val() === true) hideWidget();
                else showWidget();
            });

            const myRef = ref(db, `${presencePath}/${visitorId}`);
            set(myRef, true);
            onDisconnect(myRef).remove();

            const trafficRef = ref(db, `presence/${SITE_ID}/${visitorId}`);
            set(trafficRef, {
                online: true, last_seen: Date.now(), visitor_id: visitorId,
                domain_id: DOMAIN_ID,
            });
            onDisconnect(trafficRef).remove();

            onValue(ref(db, presencePath), snap => {
                const n = Object.keys(snap.val() || {}).length;
                const label = document.getElementById('cd-online-label');
                if (label) label.textContent = `${WS.admin_name || 'Admin'} · ${n} online`;
            });

            onValue(ref(db, typingPath), snap => {
                const others = Object.entries(snap.val() || {})
                    .filter(([k, v]) => k !== visitorId && v.typing)
                    .map(([, v]) => v.name || 'Agent');
                typingBar.innerHTML = others.length
                    ? `${esc(others.join(', '))} is typing <span class="cd-typing-dots"><span></span><span></span><span></span></span>`
                    : '';
            });

            onValue(ref(db, `${basePath}/info_request`), snap => {
                const data = snap.val();
                if (data && !data.fulfilled) {
                    showInfoRequestForm();
                    document.getElementById('cd-name-bar').style.display = 'flex';
                }
            });

            // ★ Status listener — skips stale 'ended' on reload
            onValue(ref(db, `${basePath}/status`), snap => {
                const status = snap.val();
                
                // ★ Skip the initial fire — it contains stale data from previous session
                if (!initialStatusLoaded) {
                    initialStatusLoaded = true;
                    // If status is already 'ended' from a previous session, mark as handled
                    if (status === 'ended') {
                        postChatDisplayed = true;
                        postChatShown = true;
                    }
                    return;
                }
                
                if (status === 'ended') {
                    const overlay = document.getElementById('cd-confirm-overlay');
                    if (overlay) overlay.classList.add('cd-hidden');
            
                    // ★ If visitor already triggered post-chat from confirm-ok, skip
                    if (postChatDisplayed) return;

                    // ★ Agent ended the chat — show post-chat or close
                    var alreadyFilled = localStorage.getItem('lc_postchat_filled_' + visitorId);
                    if (postChatConfig.enabled && postChatConfig.fields && postChatConfig.fields.length && !alreadyFilled && !postChatShown) {
                        postChatDisplayed = true;
                        showView('postchat');
                        if (!isOpen) {
                            isOpen = true;
                            popup.classList.add('open');
                            notifyParentResize();
                        }
                        return;
                    }

                    // ★ No post-chat — go home and close after delay
                    showView('home');
                    if (!isOpen) return;
                    setTimeout(() => {
                        isOpen = false;
                        popup.classList.remove('open');
                        notifyParentResize();
                    }, 2000);
                }
            });

            _fulfillInfoRequest = function (name, email) {
                push(ref(db, msgsPath), {
                    uid: visitorId, message: `${name}${email ? ' · ' + email : ''}`,
                    type: 'info_filled', visitor_name: name, visitor_email: email || null,
                    timestamp: Date.now(), sender: 'system',
                });
                set(ref(db, `${basePath}/info_request/fulfilled`), true);
                trackVisitor({ name, email: email || undefined });
                localStorage.setItem('lc_visitor_name', name);
                if (nameInput) nameInput.value = name;
                update(ref(db, `active_chats/${SITE_ID}/${DOMAIN_ID}/${visitorId}`), {
                    visitor_id: visitorId, visitor_name: name,
                    last_message: `Info shared: ${name}`, last_timestamp: Date.now(),
                    site_id: SITE_ID, domain_id: DOMAIN_ID,
                });
            };

            initialMessagesLoaded = false;
            const msgQ = query(ref(db, msgsPath), orderByChild('timestamp'), limitToLast(50));

            onChildAdded(msgQ, snap => {
                const d = snap.val(), id = snap.key, mine = d.uid === visitorId;
                renderMessage(id, d, mine);

                if (mine && d.sender === 'visitor') chatHasMessages = true;

                if (initialMessagesLoaded && !mine && d.sender !== 'system') {
                    const soundToggle = document.getElementById('cd-sound-toggle');
                    if (soundToggle && soundToggle.checked && WS.sound_notifications) {
                        playSound('audio-new-message');
                    }

                    if (!isOpen || view !== 'chat') {
                        unread++;
                        updateBadge();
                    } else {
                        set(ref(db, `${msgsPath}/${id}/status`), 'read');
                    }
                }
            });

            onValue(msgQ, snap => {
                if (!initialMessagesLoaded) {
                    initialMessagesLoaded = true;
                    if (!isOpen) {
                        const msgs = snap.val() || {};
                        let count = 0;
                        Object.values(msgs).forEach(d => {
                            if (d.uid !== visitorId && d.sender !== 'system' && d.status !== 'read') count++;
                        });
                        if (count > 0) { unread = count; updateBadge(); }
                    }
                }
            }, { onlyOnce: true });

            onChildChanged(ref(db, msgsPath), snap => {
                const d = snap.val();
                if (d.uid === visitorId) updateTick(snap.key, d.status);
            });

            window._cdMarkRead = () => {
                onValue(ref(db, msgsPath), snap => {
                    Object.entries(snap.val() || {}).forEach(([id, d]) => {
                        if (d.uid !== visitorId && d.status !== 'read')
                            set(ref(db, `${msgsPath}/${id}/status`), 'read');
                    });
                }, { onlyOnce: true });
            };

            let typingTimer;
            function setTyping() {
                const name = nameInput.value.trim() || 'Visitor';
                set(ref(db, `${typingPath}/${visitorId}`), { name, typing: true });
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => remove(ref(db, `${typingPath}/${visitorId}`)), 1500);
            }

            window.addEventListener('beforeunload', () => {
                set(ref(db, `active_chats/${SITE_ID}/${DOMAIN_ID}/${visitorId}/is_active`), false);
                remove(ref(db, `${typingPath}/${visitorId}`));
                sendLeave();
            });

            async function sendMessage() {
                const name = nameInput.value.trim() || localStorage.getItem('lc_visitor_name') || 'Visitor';
                const msg  = chatInput.value.trim();

                if (!localStorage.getItem('chat_started')) {
                    localStorage.setItem('chat_started', '1');
                    fetch(API_BASE + '/visitor/chat-start', {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body:    JSON.stringify({ visitor_id: visitorId })
                    }).catch(() => {});
                }

                chatHasMessages = true;

                if (fbReady && activeRoomPath) {
                    remove(ref(db, `${activeRoomPath}/visitor_typing`));
                }

                if (filesToSend.length) {
                    const filesToUpload = [...filesToSend];
                    filesToSend = [];
                    renderFilePreview();
                
                    for (const file of filesToUpload) {
                        try {
                            const formData = new FormData();
                            formData.append('file', file);
                            formData.append('site_id', SITE_ID);
                            formData.append('visitor_id', visitorId);
                
                            const resp = await fetch(API_BASE + '/widget/upload-file', {
                                method: 'POST',
                                body: formData
                            });
                            const result = await resp.json();
                
                            if (result.success && result.file_url) {
                                await push(ref(db, msgsPath), {
                                    uid: visitorId,
                                    username: name,
                                    message: result.file_type === 'image' ? '[Image]' : `[File: ${result.file_name}]`,
                                    fileUrl: result.file_url,
                                    fileType: result.file_type,
                                    fileName: result.file_name,
                                    timestamp: Date.now(),
                                    status: 'sent',
                                    sender: 'visitor',
                                });
                            } else {
                                appendBotMsg('File upload failed. Please try again.');
                            }
                        } catch (err) {
                            console.error('[ChatDesk360] Upload error:', err);
                            appendBotMsg('File upload failed. Please try again.');
                        }
                    }
                }

                if (!msg) return;
                chatInput.value = '';
                localStorage.setItem('lc_visitor_name', name);
                trackVisitor({ name });

                await push(ref(db, msgsPath), {
                    uid: visitorId, username: name, message: msg,
                    timestamp: Date.now(), status: 'sent', sender: 'visitor',
                });

                const soundToggle = document.getElementById('cd-sound-toggle');
                if (soundToggle && soundToggle.checked && WS.sound_notifications) {
                    playSound('audio-new-message');
                }

                update(ref(db, `${basePath}/meta`), {
                    last_message: msg, last_timestamp: Date.now(),
                    domain_id: DOMAIN_ID, visitor_id: visitorId,
                    visitor_name: name, site_id: SITE_ID,
                });

                update(ref(db, `active_chats/${SITE_ID}/${DOMAIN_ID}/${visitorId}`), {
                    visitor_id: visitorId, visitor_name: name,
                    last_message: msg, last_timestamp: Date.now(),
                    site_id: SITE_ID, domain_id: DOMAIN_ID, is_active: true,
                });
            }

            document.getElementById('cd-send-btn').addEventListener('click', sendMessage);
            chatInput.addEventListener('keydown', e => {
                if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
            });
            chatInput.addEventListener('input', function() {
                if (!fbReady || !activeRoomPath) return;
                set(ref(db, `${activeRoomPath}/visitor_typing`), { text: this.value, timestamp: Date.now() });
                clearTimeout(visitorTypingTimer);
                visitorTypingTimer = setTimeout(() => {
                    if (fbReady && activeRoomPath) remove(ref(db, `${activeRoomPath}/visitor_typing`));
                }, 2000);
            });

            fbReady = true;

            window._cdInitActiveChat = function () {
                if (!fbReady || !activeRoomPath) return;
                const name = nameInput.value.trim()
                        || localStorage.getItem('lc_visitor_name')
                        || 'Visitor';
                set(ref(db, `active_chats/${SITE_ID}/${DOMAIN_ID}/${visitorId}`), {
                    visitor_id:    visitorId,
                    visitor_name:  name,
                    last_message:  '',
                    last_timestamp: Date.now(),
                    site_id:       SITE_ID,
                    domain_id:     DOMAIN_ID,
                    is_active:     true,
                    assigned_agent: null,
                    agent_name:     null,
                });
                
                set(ref(db, `presence/${SITE_ID}/${visitorId}`), {
                    online: true, last_seen: Date.now(), visitor_id: visitorId,
                    domain_id: DOMAIN_ID,
                });
            };
            
            window._cdSetStatus = function(status) {
                if (!fbReady || !activeRoomPath) return;
                set(ref(db, `${activeRoomPath}/status`), status);
            };
            
            window._cdPushPostChat = function(responseData) {
                if (!fbReady || !activeRoomPath) return;
                push(ref(db, msgsPath), {
                    uid: visitorId,
                    type: 'post_chat_response',
                    response_data: responseData,
                    message: 'Post-chat feedback submitted',
                    timestamp: Date.now(),
                    sender: 'system',
                });
            };
            showView('home');

        }).catch(() => {
            if (loadingEl) loadingEl.innerHTML = '<span style="color:#ef4444">Chat unavailable.</span>';
        });
    }

})();
</script>
</body>
</html>