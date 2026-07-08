@extends('layouts.app')

@section('title', 'Banned Customers')

@push('styles')
<style>
.banned-header, .banned-row {
    display: grid;
    grid-template-columns: 1.3fr 1fr 1fr 1.2fr 1fr 0.6fr;
    align-items: center;
    padding: 12px 16px;
    gap: 8px;
}
.banned-header {
    background: #f9fafb;
    font-weight: 600;
    font-size: 13px;
    color: #64748b;
    border-bottom: 2px solid #e2e8f0;
    border-radius: 8px 8px 0 0;
}
.banned-row {
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    transition: background .12s;
}
.banned-row:hover { background: #f8fafc; }
.banned-row.expired { opacity: .5; }
.sortable { cursor: pointer; user-select: none; }
.sortable:hover { color: #2b60d0; }
.sort-icon { font-size: 9px; margin-left: 4px; }
.permanent-badge {
    background: #fef2f2; color: #dc2626;
    font-size: 10px; padding: 2px 8px; border-radius: 999px; font-weight: 600;
}
.active-badge {
    background: #f0fdf4; color: #16a34a;
    font-size: 10px; padding: 2px 8px; border-radius: 999px; font-weight: 600;
}
.expired-badge {
    background: #f1f5f9; color: #94a3b8;
    font-size: 10px; padding: 2px 8px; border-radius: 999px; font-weight: 600;
}
#ban-custom-end-wrap { display: none; }
</style>
@endpush

@section('content')
<div class="body-wrapper mb-0 pg-banned">
    <div class="container-fluid mw-100 pb-0">
        <x-breadcrumb title="Banned Customers" />

        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row mb-3">
                    <div class="col-md-4 col-xl-3">
                        <form class="position-relative">
                            <input type="text" class="form-control product-search ps-5"
                                id="input-search" placeholder="Search Banned Customers..." />
                            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </form>
                    </div>
                    <div class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0 gap-2">
                        <select class="form-select form-select-sm" id="filter-status" style="max-width:160px">
                            <option value="all">All</option>
                            <option value="active" selected>Active</option>
                            <option value="expired">Expired</option>
                            <option value="permanent">Permanent</option>
                        </select>
                        <a href="javascript:void(0)" id="btn-ban-customer"
                            class="btn btn-primary d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#banCustomerModal">
                            <i class="ti ti-ban me-1"></i> Ban Customer
                        </a>
                    </div>
                </div>

                <!-- Table Header -->
                <div class="banned-header">
                    <div class="sortable" data-sort="ip_address">IP address <span class="sort-icon">▲▼</span></div>
                    <div class="sortable" data-sort="start_date">Start date <span class="sort-icon">▲▼</span></div>
                    <div class="sortable" data-sort="end_date">End date <span class="sort-icon">▲▼</span></div>
                    <div class="sortable" data-sort="banned_by">Banned by <span class="sort-icon">▲▼</span></div>
                    <div class="sortable" data-sort="chat_id">Chat ID <span class="sort-icon">▲▼</span></div>
                    <div>Status</div>
                </div>

                <!-- Rows injected here -->
                <div id="banned-list">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm me-2"></div>Loading...
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Ban Customer Modal -->
<div class="modal fade" id="banCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti ti-ban me-2 text-danger"></i>Ban Customer</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="ban-customer-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="customer-ip" class="form-label">Customer IP</label>
                                <input type="text" id="customer-ip" name="ip_address"
                                    class="form-control" placeholder="e.g., 76.140.41.184" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="ban-duration" class="form-label">Duration</label>
                                <select id="ban-duration" name="duration" class="form-select">
                                    <option value="1">1 day</option>
                                    <option value="7" selected>7 days</option>
                                    <option value="30">30 days</option>
                                    <option value="permanent">Permanent</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3" id="ban-custom-end-wrap">
                        <label class="form-label">Custom end date</label>
                        <input type="date" name="custom_end" class="form-control" id="ban-custom-end">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <input type="text" name="reason" class="form-control"
                            placeholder="e.g. Spam, harassment..." />
                    </div>
                    <div class="form-group">
                        <h6>Note</h6>
                        <small class="text-muted">Banned customers will not see your chat widget, appear on your traffic list, or receive campaigns.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-light text-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-submit-ban">
                    <i class="ti ti-ban me-1"></i>Ban Customer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const LIST_URL  = '{{ route("banned-customers.list") }}';
    const STORE_URL = '{{ route("banned-customers.store") }}';

    // ★ Generate the destroy URL from the named route
    const INDEX_URL = '{{ route("settings.banned-customers.index") }}';
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;

    let allBans = [];
    let currentSort = { field: 'created_at', dir: 'desc' };

    // ── Load bans ────────────────────────────────────────────
    function loadBans() {
        fetch(LIST_URL, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => r.json())
        .then(data => {
            allBans = data.bans || [];
            renderBans();
        })
        .catch(err => {
            document.getElementById('banned-list').innerHTML =
                '<div class="text-center py-5 text-danger">Failed to load banned customers.</div>';
        });
    }

    // ── Render ───────────────────────────────────────────────
    function renderBans() {
        const search  = (document.getElementById('input-search').value || '').toLowerCase();
        const status  = document.getElementById('filter-status').value;

        let filtered = allBans.filter(b => {
            if (search) {
                const hay = [b.ip_address, b.visitor_id, b.chat_id, b.banned_by, b.reason].join(' ').toLowerCase();
                if (!hay.includes(search)) return false;
            }
            if (status === 'active'    && !b.is_active) return false;
            if (status === 'expired'   && b.is_active)  return false;
            if (status === 'permanent' && !b.is_permanent) return false;
            return true;
        });

        filtered.sort((a, b) => {
            let va = a[currentSort.field] || '';
            let vb = b[currentSort.field] || '';
            if (currentSort.field === 'start_date' || currentSort.field === 'end_date') {
                va = new Date(va || 0); vb = new Date(vb || 0);
            }
            if (va < vb) return currentSort.dir === 'asc' ? -1 : 1;
            if (va > vb) return currentSort.dir === 'asc' ? 1 : -1;
            return 0;
        });

        const container = document.getElementById('banned-list');

        if (!filtered.length) {
            container.innerHTML = '<div class="text-center py-5 text-muted">No banned customers found.</div>';
            return;
        }

        container.innerHTML = filtered.map(b => {
            const statusBadge = b.is_permanent
                ? '<span class="permanent-badge">Permanent</span>'
                : b.is_active
                    ? '<span class="active-badge">Active</span>'
                    : '<span class="expired-badge">Expired</span>';

            return `
            <div class="banned-row ${b.is_active ? '' : 'expired'}" data-id="${b.id}">
                <div>${b.ip_address || '-'}</div>
                <div>${b.start_date || '-'}</div>
                <div>
                    ${b.is_permanent ? '<span class="permanent-badge">Permanent</span>' : (b.end_date || '-')}
                </div>
                <div>${b.banned_by || '-'}</div>
                <div title="${b.visitor_id || ''}">${b.chat_id || b.visitor_id || '-'}</div>
                <div class="d-flex align-items-center gap-2 justify-content-end">
                    ${statusBadge}
                    <button class="btn btn-sm btn-outline-danger btn-unban" data-id="${b.id}" title="Unban">
                        <i class="ti ti-lock-open"></i>
                    </button>
                </div>
            </div>`;
        }).join('');

        container.querySelectorAll('.btn-unban').forEach(btn => {
            btn.addEventListener('click', function () {
                unbanCustomer(this.dataset.id);
            });
        });
    }

    function unbanCustomer(id) {
        const ban = allBans.find(b => b.id == id);
        const visitorId = ban?.visitor_id;

        Swal.fire({
            title: 'Unban this customer?',
            text: 'They will be able to use the chat widget again.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, unban!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            // ★ Use the named-route-generated URL
            fetch(`${INDEX_URL}/${id}`, {
                method: 'DELETE',
                headers: { 
                    'X-CSRF-TOKEN': CSRF, 
                    'Accept': 'application/json' 
                }
            })
            .then(r => {
                if (!r.ok) throw new Error('Failed to unban');
                return r.json();
            })
            .then(() => {
                loadBans();
                if (visitorId) {
                    removeFirebaseBan(visitorId);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Unbanned!',
                    text: 'Customer has been unbanned successfully.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to unban customer. Please try again.',
                });
            });
        });
    }

    // ★ Firebase remove for real-time unban
    function removeFirebaseBan(visitorId) {
        const FB_BASE = 'https://www.gstatic.com/firebasejs/10.12.0';
        Promise.all([
            import(`${FB_BASE}/firebase-app.js`),
            import(`${FB_BASE}/firebase-database.js`)
        ]).then(([{ initializeApp }, { getDatabase, ref, remove }]) => {
            let app;
            try {
                app = initializeApp({
                    apiKey: '{{ config("services.firebase.api_key") }}',
                    databaseURL: '{{ config("services.firebase.db_url") }}',
                    projectId: '{{ config("services.firebase.project_id") }}',
                    appId: '{{ config("services.firebase.app_id") }}',
                    authDomain: '{{ config("services.firebase.project_id") }}.firebaseapp.com',
                }, 'cd-admin-unban');
            } catch (e) {
                app = initializeApp({
                    apiKey: '{{ config("services.firebase.api_key") }}',
                    databaseURL: '{{ config("services.firebase.db_url") }}',
                    projectId: '{{ config("services.firebase.project_id") }}',
                    appId: '{{ config("services.firebase.app_id") }}',
                    authDomain: '{{ config("services.firebase.project_id") }}.firebaseapp.com',
                });
            }

            const db = getDatabase(app);
            const siteId = '{{ Auth::user()->site_id }}';

            remove(ref(db, `banned_visitors/${siteId}/${visitorId}`))
                .then(() => console.log('[Unban] Firebase node removed'))
                .catch(err => console.error('[Unban] Firebase error:', err));
        }).catch(err => {
            console.error('[Unban] Firebase import error:', err);
        });
    }

    // ── Ban (from modal) ─────────────────────────────────────
    document.getElementById('btn-submit-ban').addEventListener('click', async function () {
        const form = document.getElementById('ban-customer-form');
        const fd   = new FormData(form);
        const body = Object.fromEntries(fd.entries());

        this.disabled  = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Banning...';

        try {
            const res = await fetch(STORE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body),
            });
            const data = await res.json();
            
            if (!res.ok) {
                const errors = data.errors ? Object.values(data.errors).flat().join('<br>') : '';
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    html: data.message || errors || 'Something went wrong.',
                });
                return;
            }

            bootstrap.Modal.getInstance(document.getElementById('banCustomerModal'))?.hide();
            form.reset();
            loadBans();

            Swal.fire({
                icon: 'success',
                title: 'Banned!',
                text: 'Customer has been banned successfully.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });

        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
            });
        } finally {
            this.disabled  = false;
            this.innerHTML = '<i class="ti ti-ban me-1"></i>Ban Customer';
        }
    });

    // ── Duration toggle ──────────────────────────────────────
    document.getElementById('ban-duration').addEventListener('change', function () {
        document.getElementById('ban-custom-end-wrap').style.display =
            this.value === 'custom' ? 'block' : 'none';
    });

    // ── Search ───────────────────────────────────────────────
    document.getElementById('input-search').addEventListener('input', renderBans);

    // ── Filter ───────────────────────────────────────────────
    document.getElementById('filter-status').addEventListener('change', renderBans);

    // ── Sort ─────────────────────────────────────────────────
    document.querySelectorAll('.sortable').forEach(el => {
        el.addEventListener('click', function () {
            const field = this.dataset.sort;
            if (currentSort.field === field) {
                currentSort.dir = currentSort.dir === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = { field, dir: 'asc' };
            }
            renderBans();
        });
    });

    // ── Init ─────────────────────────────────────────────────
    loadBans();

})();
</script>
@endpush