@extends('layouts.app')

@section('title', 'Traffic')

@push('styles')
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #oc-map {
        height: 160px;
        border-radius: 0;
        z-index: 1;
    }
    .leaflet-container { background: #2d2d2d; }

    /* Activity badge colors */
    .activity-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 500; padding: 3px 8px;
        border-radius: 20px;
    }
    .activity-browsing  { background: #e8f5e9; color: #2e7d32; }
    .activity-chatting  { background: #e3f2fd; color: #1565c0; }
    .activity-left      { background: #f5f5f5; color: #757575; }

    .activity-dot {
        width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0;
    }
    .dot-browsing { background: #43a047; }
    .dot-chatting { background: #1e88e5; animation: pulse 1.2s infinite; }
    .dot-left     { background: #bdbdbd; }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .3; }
    }

    /* Total time live counter */
    .live-timer { font-variant-numeric: tabular-nums; }
</style>
@endpush

@section('content')
<div class="body-wrapper mb-0 pg-traffic">
    <div class="container-fluid mw-100">
        <x-breadcrumb title="Traffic" />

        <div class="card m-0 traffic-det">
            <div class="d-flex align-items-center justify-content-between">
                <ul class="nav nav-pills user-profile-tab" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-3 active"
                            id="pills-all-cus-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-all-cus" type="button" role="tab">
                            <span class="">
                                All Customers (<span id="visitor-count">0</span>)
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-3"
                            id="pills-chatting-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-chatting" type="button" role="tab">
                            <span class="">
                                Chatting (<span id="chatting-count">0</span>)
                            </span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="pills-tabContent">

                    {{-- ALL CUSTOMERS TAB --}}
                    <div class="tab-pane fade active show" id="pills-all-cus" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="traffic-list-offcanvas">
                                    <div class="data-grid-container">
                                        <div class="data-grid-header">
                                            <div class="grid-item">Name</div>
                                            <div class="grid-item">Email</div>
                                            <div class="grid-item">Actions</div>
                                            <div class="grid-item">Activity</div>
                                            <div class="grid-item">Chatting with</div>
                                            <div class="grid-item">Time on all pages</div>
                                            <div class="grid-item">Country</div>
                                            <div class="grid-item">State</div>
                                            <div class="grid-item">City</div>
                                            <div class="grid-item">Came from</div>
                                            <div class="grid-item">Device</div>
                                            <div class="grid-item">Last seen</div>
                                            <div class="grid-item">No. of visits</div>
                                            <div class="grid-item">Last page</div>
                                            <div class="grid-item">No. of chats</div>
                                            <div class="grid-item">Operating System</div>
                                            <div class="grid-item">Browser</div>
                                        </div>

                                        <div class="data-item-lists" id="visitor-rows">
                                            <div class="text-center py-4 text-muted" id="loading-row">
                                                Connecting to live feed...
                                            </div>
                                        </div>
                                    </div>

                                    {{-- OFFCANVAS SIDEBAR --}}
                                    <div class="traffic-offcanvas" id="trafficCustomerOffcanvas">
                                        <div class="custom-app-scroll" data-simplebar>
                                            <div class="accordion accordion-flush" id="trafficCustomerDetails">

                                                {{-- Customer Info --}}
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#traffic-collapseOne">
                                                            <div class="cus-infocard">
                                                                <div class="cus-img"><i class="ti ti-user-circle"></i></div>
                                                                <h5 id="oc-name">-</h5>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="traffic-collapseOne" class="accordion-collapse collapse"
                                                        data-bs-parent="#trafficCustomerDetails">
                                                        <div class="accordion-body">
                                                            <ul>
                                                                <li><p id="oc-location">-</p></li>
                                                                <li><p id="oc-email">-</p></li>
                                                                <li><p id="oc-ip">-</p></li>
                                                            </ul>
                                                            {{-- Leaflet Map --}}
                                                            <div id="oc-map" class="mt-2"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Additional Info --}}
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#traffic-collapseTwo">
                                                            <div class="cus-infocard"><h5>Additional info</h5></div>
                                                        </button>
                                                    </h2>
                                                    <div id="traffic-collapseTwo" class="accordion-collapse collapse"
                                                        data-bs-parent="#trafficCustomerDetails">
                                                        <div class="accordion-body">
                                                            <ul>
                                                                <li><p id="oc-visits">-</p></li>
                                                                <li><p id="oc-lastseen">-</p></li>
                                                                <li><p id="oc-device">-</p></li>
                                                                <li><p id="oc-browser">-</p></li>
                                                                <li><p id="oc-os">-</p></li>
                                                                <li><p id="oc-totaltime">-</p></li>
                                                                <li><p id="oc-lastpage">-</p></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Visited Pages --}}
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#traffic-collapseThree">
                                                            <div class="cus-infocard"><h5>Visited pages</h5></div>
                                                        </button>
                                                    </h2>
                                                    <div id="traffic-collapseThree" class="accordion-collapse collapse"
                                                        data-bs-parent="#trafficCustomerDetails">
                                                        <div class="accordion-body">
                                            <ul class="list-come" id="oc-pages">
                                                                <li class="text-muted">Select a visitor</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    {{-- end offcanvas --}}

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CHATTING TAB --}}
                    <div class="tab-pane fade" id="pills-chatting" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="data-grid-container">
                                    <div class="data-grid-header">
                                        <div class="grid-item">Name</div>
                                        <div class="grid-item">Email</div>
                                        <div class="grid-item">Actions</div>
                                        <div class="grid-item">Country</div>
                                        <div class="grid-item">Time on site</div>
                                        <div class="grid-item">Last page</div>
                                        <div class="grid-item">Device</div>
                                    </div>
                                    <div class="data-item-lists" id="chatting-rows">
                                        <div class="text-center py-4 text-muted">No active chats</div>
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
{{-- Audio for new visitor --}}
<audio id="audio-new-visitor" src="/sounds/new-visitor.wav" preload="auto"></audio>
@endsection

@push('scripts')
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ── Config ────────────────────────────────────────────────────────────────────
const SHOW_URL    = '{{ url("/admin/traffic") }}';
const AGENT_ID    = '{{ Auth::id() }}';
const AGENT_NAME  = '{{ Auth::user()->name }}';

// ── State ─────────────────────────────────────────────────────────────────────
let activeVisitorId = null;
let leafletMap    = null;
let leafletMarker = null;
let visitorsCache   = {};
let liveTimers      = {};
let timerInterval   = null;
let knownVisitorIds = new Set();
let fbDb = null;
let fbRefFn = null;
let fbSetFn = null;

// ── Helpers ───────────────────────────────────────────────────────────────────
function fmtTime(s) {
    if (!s || s < 1) return '0s';
    if (s < 60) return s + 's';
    const m = Math.floor(s / 60), sec = s % 60;
    return sec > 0 ? `${m}m ${sec}s` : `${m}m`;
}

function timeAgo(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;

    const now = new Date();
    const diffMs = now - date;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffMinutes = Math.floor(diffMs / (1000 * 60));

    if (diffDays === 0) {
        if (diffHours === 0) {
            return diffMinutes <= 1 ? 'just now' : `${diffMinutes} minutes ago`;
        }
        return diffHours === 1 ? '1 hour ago' : `${diffHours} hours ago`;
    } else if (diffDays === 1) {
        return 'yesterday';
    } else if (diffDays <= 6) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    }
}

function shortUrl(url) {
    if (!url) return '-';
    try { return new URL(url).pathname || '/'; }
    catch { return url; }
}

// Add after shortUrl() function
function esc(s) {
    const d = document.createElement('div');
    d.textContent = s ?? '';
    return d.innerHTML;
}

function activityBadge(status) {
    const map = {
        'browsing':     ['dot-browsing', 'activity-browsing', '● Browsing'],
        'chatting':     ['dot-chatting', 'activity-chatting', '● Chatting'],
        'left_website': ['dot-left',     'activity-left',     '● Left website'],
    };
    const [dot, cls, label] = map[status] ?? map['browsing'];
    return `<span class="activity-badge ${cls}">
                <span class="activity-dot ${dot}"></span>
                ${label.slice(2)}
            </span>`;
}

// ── Live timer ticker ─────────────────────────────────────────────────────────
function startTimerTick() {
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        const now = Date.now();
        Object.keys(liveTimers).forEach(vid => {
            const { baseTime, startedAt, el } = liveTimers[vid];
            if (!el || !document.body.contains(el)) return;
            const elapsed = Math.floor((now - startedAt) / 1000);
            el.textContent = fmtTime(baseTime + elapsed);
        });
    }, 1000);
}

// ── Render rows ───────────────────────────────────────────────────────────────
function renderRows(visitors) {
    const container = document.getElementById('visitor-rows');
    const chattingContainer = document.getElementById('chatting-rows');
    document.getElementById('visitor-count').textContent = visitors.length;

    const chattingVisitors = visitors.filter(v => v.status === 'chatting');
    document.getElementById('chatting-count').textContent = chattingVisitors.length;

    liveTimers = {};

    if (!visitors.length) {
        container.innerHTML = '<div class="text-center py-4 text-muted">No active visitors</div>';
        return;
    }

    container.innerHTML = visitors.map(v => {
        // console.log(v);
        visitorsCache[v.visitor_id] = v;

        const isActive = v.visitor_id === activeVisitorId ? 'active-row' : '';
        const timerHtml = `<span class="live-timer" data-vid="${v.visitor_id}">${fmtTime(v.total_time)}</span>`;

        // ✅ Agent name display
        const agentDisplay = v.assign_userName 
            ? esc(v.assign_userName) 
            : (v.assign_userID ? `Agent #${v.assign_userID}` : '-');

        // ✅ Action buttons — new logic
        let actionBtns = '';
        if (v.status === 'chatting') {
            if (v.assign_userID && v.assign_userID == AGENT_ID) {
                // This agent is assigned
                actionBtns = `
                    <button class="btn btn-sm btn-primary open-chat-btn me-1" data-id="${v.visitor_id}">
                        Open Chat
                    </button>`;
            } else if (v.assign_userID && v.assign_userID != AGENT_ID) {
                // Another agent is assigned
                actionBtns = `
                    <button class="btn btn-sm btn-outline-warning supervise-chat-btn me-1" data-id="${v.visitor_id}">
                        <i class="ti ti-eye"></i> Supervise
                    </button>
                    <span class="badge bg-secondary ms-1">With ${esc(v.assign_userName || 'Agent #' + v.assign_userID)}</span>`;
            } else {
                // No agent assigned yet — visitor waiting
                actionBtns = `
                    <span class="badge bg-warning text-dark me-1">
                        <i class="ti ti-clock"></i> Waiting for reply
                    </span>
                    <button class="btn btn-sm btn-outline-primary take-chat-btn" data-id="${v.visitor_id}">
                        Take Chat
                    </button>`;
            }
        } else if (v.status !== 'left_website') {
            actionBtns = `
                <button class="btn btn-sm btn-outline-primary start-chat-btn" data-id="${v.visitor_id}">
                    Start Chat
                </button>`;
        } else {
            actionBtns = `
                <button class="btn btn-sm btn-outline-secondary view-chat-btn" data-id="${v.visitor_id}">
                    <i class="ti ti-history"></i> View Chat
                </button>`;
        }

        return `
        <div class="data-grid-row ${isActive}" data-id="${v.visitor_id}" style="cursor:pointer">
            <div class="grid-item fw-semibold">${esc(v.name)}</div>
            <div class="grid-item">${esc(v.email || '-')}</div>
            <div class="grid-item">${actionBtns}</div>
            <div class="grid-item">${activityBadge(v.status)}</div>
            <div class="grid-item agent-name">${agentDisplay}</div>
            <div class="grid-item">${timerHtml}</div>
            <div class="grid-item">${v.country ? `<img src="https://flagcdn.com/16x12/${v.countryCode ?? '-'}.png" class="me-1"> ${esc(v.country)}` : '-'}</div>
            <div class="grid-item">${esc(v.state ?? '-')}</div>
            <div class="grid-item">${esc(v.city ?? '-')}</div>
            <div class="grid-item">${v.referrer_url ? shortUrl(v.referrer_url) : 'Direct'}</div>
            <div class="grid-item">${esc(v.device_type ?? '-')}</div>
            <div class="grid-item">${timeAgo(v.last_seen_at)}</div>
            <div class="grid-item">${v.visit_count}</div>
            <div class="grid-item">
                <a href="${v.last_page_url ?? '#'}" target="_blank" class="link text-truncate d-block" style="max-width:200px" title="${esc(v.last_page_url || '')}">
                    ${esc(v.last_page_title || v.last_page_url || '-')}
                </a>
            </div>
            <div class="grid-item">${v.chat_count}</div>
            <div class="grid-item">${esc(v.os ?? '-')}</div>
            <div class="grid-item">${esc(v.browser ?? '-')}</div>
        </div>`;
    }).join('');

    // Set live timer references
    visitors.forEach(v => {
        const el = container.querySelector(`[data-vid="${v.visitor_id}"]`);
        liveTimers[v.visitor_id] = {
            baseTime:  v.total_time,
            startedAt: Date.now(),
            el,
        };
    });

    // Chatting tab
    if (chattingVisitors.length) {
        chattingContainer.innerHTML = chattingVisitors.map(v => {
            const agentDisplay = v.assign_userName 
                ? esc(v.assign_userName) 
                : (v.assign_userID ? `Agent #${v.assign_userID}` : '-');

            let actions = '';
            if (v.assign_userID && v.assign_userID == AGENT_ID) {
                actions = `<button class="btn btn-sm btn-primary open-chat-btn me-1" data-id="${v.visitor_id}">Open Chat</button>`;
            } else if (v.assign_userID && v.assign_userID != AGENT_ID) {
                actions = `
                    <button class="btn btn-sm btn-outline-warning supervise-chat-btn me-1" data-id="${v.visitor_id}">
                        <i class="ti ti-eye"></i> Supervise
                    </button>
                    <span class="badge bg-secondary ms-1">With ${esc(v.assign_userName || 'Agent #' + v.assign_userID)}</span>`;
            } else {
                actions = `
                    <span class="badge bg-warning text-dark me-1">
                        <i class="ti ti-clock"></i> Waiting
                    </span>
                    <button class="btn btn-sm btn-outline-primary take-chat-btn" data-id="${v.visitor_id}">Take Chat</button>`;
            }

            return `
        <div class="data-grid-row" data-id="${v.visitor_id}" style="cursor:pointer">
            <div class="grid-item fw-semibold">${esc(v.name)}</div>
            <div class="grid-item">${esc(v.email || '-')}</div>
            <div class="grid-item">${actions}</div>
            <div class="grid-item">${v.country ? esc(v.country) : '-'}</div>
            <div class="grid-item">${fmtTime(v.total_time)}</div>
            <div class="grid-item">
                <a href="${v.last_page_url ?? '#'}" target="_blank" class="link text-truncate d-block" style="max-width:180px" title="${esc(v.last_page_url || '')}">
                    ${esc(v.last_page_title || v.last_page_url || '-')}
                </a>
            </div>
            <div class="grid-item">${esc(v.device_type ?? '-')}</div>
        </div>`;
        }).join('');
    } else {
        chattingContainer.innerHTML = '<div class="text-center py-4 text-muted">No active chats</div>';
    }

    // ── Bind row events ──
    document.querySelectorAll('.data-grid-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('a')) return;
            openOffcanvas(this.dataset.id);
        });
    });

    // Start chat
    document.querySelectorAll('.start-chat-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            // window.location.href = `/chats?visitor=${btn.dataset.id}&action=start`;

            assignToMe(btn.dataset.id);
        });
    });

    // Open chat
    document.querySelectorAll('.open-chat-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            window.location.href = `/admin/chats?visitor=${btn.dataset.id}`;
        });
    });

    // View archived chat
    document.querySelectorAll('.view-chat-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            window.location.href = `/admin/chats/archive?visitor=${btn.dataset.id}`;
        });
    });

    // ✅ Take Chat (replaces "Assign to me")
    document.querySelectorAll('.take-chat-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            assignToMe(btn.dataset.id);
        });
    });

    // Supervise chat
    document.querySelectorAll('.supervise-chat-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            window.location.href = `/admin/chats?visitor=${btn.dataset.id}&mode=supervise`;
        });
    });
}

// ── Assign to me ──────────────────────────────────────────────────────────────
function assignToMe(visitorId) {
    fetch(`/api/visitor/assign`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ visitor_id: visitorId, agent_id: AGENT_ID })
    })
    .then(r => r.json())
    .then((data) => {
        console.log('[Traffic] Assigned:', data);

        // ★ Update Firebase active_chats with agent assignment
        if (fbDb && fbRefFn && fbSetFn) {
            const vd = visitorsCache[visitorId];
            let domainId = vd?.domain_id || null;

            if ((!domainId || domainId === 'unknown' || domainId === 'unknown_domain') && vd?.last_page_url) {
                try { domainId = new URL(vd.last_page_url).hostname.replace(/\./g, '_'); } catch {}
            }

            if (domainId && domainId !== 'unknown' && domainId !== 'unknown_domain') {
                fbSetFn(fbRefFn(fbDb, `active_chats/${SITE_ID}/${domainId}/${visitorId}/assigned_agent`), AGENT_ID);
                fbSetFn(fbRefFn(fbDb, `active_chats/${SITE_ID}/${domainId}/${visitorId}/agent_name`), AGENT_NAME);
            }
        }

        // ✅ Redirect to chat page
        window.location.href = `/admin/chats?visitor=${visitorId}&action=start`;
    })
    .catch((err) => {
        console.error('[Traffic] Assign failed:', err);
        window.location.href = `/admin/chats?visitor=${visitorId}&action=start`;
    });
}

// ── Offcanvas ─────────────────────────────────────────────────────────────────
function openOffcanvas(visitorId) {
    activeVisitorId = visitorId;

    document.querySelectorAll('.data-grid-row').forEach(r => {
        r.classList.toggle('active-row', r.dataset.id === visitorId);
    });

    const oc = document.getElementById('trafficCustomerOffcanvas');
    oc.classList.add('active');

    // Reset fields
    ['oc-name','oc-location','oc-email','oc-ip','oc-visits',
     'oc-lastseen','oc-device','oc-browser','oc-os','oc-totaltime'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = 'Loading...';
    });
    document.getElementById('oc-pages').innerHTML = '<li class="text-muted">Loading...</li>';

    fetch(`${SHOW_URL}/${visitorId}`)
        .then(r => r.json())
        .then(data => {
            const v   = data.visitor;
            const loc = [v.city, v.state, v.country].filter(Boolean).join(', ') || '-';

            document.getElementById('oc-name').textContent      = v.name ?? 'Unnamed Customer';
            document.getElementById('oc-location').textContent  = loc;
            document.getElementById('oc-email').textContent     = v.email ?? 'No email';
            document.getElementById('oc-ip').textContent        = `IP: ${v.ip_address ?? '-'}`;
            document.getElementById('oc-visits').textContent    = `${v.visit_count} visits, ${v.chat_count} chats`;
            document.getElementById('oc-lastseen').textContent  = `Last seen: ${timeAgo(v.last_seen_at) ?? '-'}`;
            document.getElementById('oc-device').textContent    = `Device: ${v.device_type ?? '-'}`;
            document.getElementById('oc-browser').textContent   = `Browser: ${v.browser ?? '-'}`;
            document.getElementById('oc-os').textContent        = `OS: ${v.os ?? '-'}`;
            document.getElementById('oc-totaltime').textContent = `Total time: ${fmtTime(data.total_time)}`;

            const lastPageEl = document.getElementById('oc-lastpage');
            if (lastPageEl) {
                const title = v.last_page_title || shortUrl(v.last_page_url);
                const url = v.last_page_url || '#';
                lastPageEl.innerHTML = `Last page: <a href="${url}" target="_blank" class="link">${esc(title)}</a>`;
            }

            renderMap(v.lat, v.lon, loc);

            const pagesList = document.getElementById('oc-pages');
            if (!data.pages.length) {
                pagesList.innerHTML = '<li class="text-muted">No pages recorded</li>';
            } else {
                pagesList.innerHTML = data.pages.map(p => `
                    <li title="${p.url}">
                        <i class="ti ti-point fs-4 me-2 text-primary"></i>
                        <div class="comefrom">
                            <h6>${p.title || shortUrl(p.url)}</h6>
                            <div class="cometime">${fmtTime(p.time_spent)} · ${p.visited_at}</div>
                        </div>
                    </li>
                `).join('');
            }
        })
        .catch(() => {
            document.getElementById('oc-name').textContent = 'Error loading data';
        });
}

// ── Leaflet Map render ────────────────────────────────────────────────────────
function renderMap(lat, lon, label) {
    const container = document.getElementById('oc-map');

    if (!lat || !lon || isNaN(parseFloat(lat)) || isNaN(parseFloat(lon))) {
        container.innerHTML = '<div class="text-muted text-center py-3 small">📍 Location not available</div>';
        if (leafletMap) {
            leafletMap.remove();
            leafletMap = null;
        }
        return;
    }

    const latF = parseFloat(lat);
    const lonF = parseFloat(lon);
    const fixedZoom = 12;

    if (leafletMap) {
        leafletMap.setView([latF, lonF], fixedZoom);
        if (leafletMarker) leafletMarker.setLatLng([latF, lonF]);
        return;
    }

    leafletMap = L.map('oc-map', {
        center: [latF, lonF],
        zoom: fixedZoom,
        dragging: false,
        zoomControl: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        touchZoom: false,
        boxZoom: false,
        keyboard: false,
        tap: false,
        attributionControl: true
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        minZoom: 1
    }).addTo(leafletMap);

    L.Icon.Default.prototype.options.imagePath = 'https://unpkg.com/leaflet@1.9.4/dist/images/';

    leafletMarker = L.marker([latF, lonF]).addTo(leafletMap);

    L.circle([latF, lonF], {
        color: '#3388ff',
        weight: 1,
        fillColor: '#3388ff',
        fillOpacity: 0.15,
        radius: 300
    }).addTo(leafletMap);

    setTimeout(() => leafletMap.invalidateSize(), 100);
}

// ── Firebase realtime listener ────────────────────────────────────────────────
const LIVE_POLL_URL = '{{ route("traffic.live") }}';

function initFirebaseTraffic() {
    const FB_BASE = 'https://www.gstatic.com/firebasejs/10.12.0';
    Promise.all([
        import(`${FB_BASE}/firebase-app.js`),
        import(`${FB_BASE}/firebase-database.js`)
    ]).then(([{ initializeApp }, { getDatabase, ref, onValue, set }]) => {

        const fbConfig = {
            apiKey:            "{{ env('FIREBASE_API_KEY') }}",
            authDomain:        "{{ env('FIREBASE_AUTH_DOMAIN') }}",
            databaseURL:       "{{ env('FIREBASE_DATABASE_URL') }}",
            projectId:         "{{ env('FIREBASE_PROJECT_ID') }}",
            storageBucket:     "{{ env('FIREBASE_STORAGEBUCKET') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGINGSENDERID') }}",
            appId:             "{{ env('FIREBASE_APP_ID') }}",
        };

        const app = initializeApp(fbConfig, 'traffic-admin');
        const db  = getDatabase(app);

        fbDb = db;
        fbRefFn = ref;
        fbSetFn = set;

        console.log('[Traffic] Firebase connected');

        const presenceRef = ref(db, `presence/{{ Auth::user()->site_id ?? '' }}`);
        onValue(presenceRef, () => {
            fetchVisitors();
        });

        fetchVisitors();
        setInterval(fetchVisitors, 10000);

    }).catch(() => {
        console.warn('[Traffic] Firebase failed, falling back to polling');
        fetchVisitors();
        setInterval(fetchVisitors, 10000);
    });
}

// ── Sound Helper ──────────────────────────────────────────────────────────────
function playSound(audioId) {
    const audio = document.getElementById(audioId);
    if (audio) {
        audio.currentTime = 0; // Rewind to start
        audio.play().catch(e => {}); // Ignore autoplay errors
    }
}

// ── Fetch visitors from Laravel ───────────────────────────────────────────────
function fetchVisitors() {
    fetch(LIVE_POLL_URL)
        .then(r => r.json())
        .then(data => {
            // ★ NEW VISITOR SOUND LOGIC
            if (data.visitors && data.visitors.length > 0) {
                const newVisitors = data.visitors.filter(v => !knownVisitorIds.has(v.visitor_id));
                
                // Play sound only if it's not the very first load
                if (newVisitors.length > 0 && knownVisitorIds.size > 0) {
                    playSound('audio-new-visitor');
                }
                
                // Update known list
                data.visitors.forEach(v => knownVisitorIds.add(v.visitor_id));
            }
            
            renderRows(data.visitors);
        })
        .catch(console.error);
}

// ── Boot ──────────────────────────────────────────────────────────────────────
startTimerTick();
initFirebaseTraffic();

// Close offcanvas on outside click
document.addEventListener('click', function(e) {
    const oc = document.getElementById('trafficCustomerOffcanvas');
    if (oc.classList.contains('active') &&
        !oc.contains(e.target) &&
        !e.target.closest('.data-grid-row')) {
        oc.classList.remove('active');
        activeVisitorId = null;
        document.querySelectorAll('.data-grid-row').forEach(r => r.classList.remove('active-row'));
    }
});

document.getElementById('traffic-collapseOne')?.addEventListener('shown.bs.collapse', () => {
    if (leafletMap) leafletMap.invalidateSize();
});
</script>
@endpush