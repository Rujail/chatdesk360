@extends('layouts.app')

@section('title', 'Trusted domains')


@section('content')
<div class="body-wrapper mb-0 pg-tdomain">
    <div class="container-fluid">
        <x-breadcrumb title="Trusted domains" />

        <div class="card card-body trusted-domains-card">
            <div class="trusted-domains-intro mb-4">
                <h4 class="intro-heading">Trusted domains</h4>
                <p class="intro-description">
                    Protect your LiveChat widget from being added to unauthorized sites by creating a list of trusted domains.
                    Trusting a domain automatically trusts all its subdomains. Trusting a subdomain doesn't trust its core domain.
                    <a href="#" class="text-primary fw-semibold learn-more-link">Learn more</a>
                </p>
            </div>

            <div class="domain-management-section">
                <h5 class="management-heading">Manage domains</h5>

                {{-- ── Tabs ── --}}
                <ul class="nav nav-tabs domain-tabs mb-4" id="domainTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active tab-button" id="trusted-tab"
                            data-bs-toggle="tab" data-bs-target="#trusted"
                            type="button" role="tab">
                            Trusted
                            <span class="badge bg-secondary-subtle text-secondary tab-count">
                                {{ $trustedCount }}
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link tab-button" id="detected-tab"
                            data-bs-toggle="tab" data-bs-target="#detected"
                            type="button" role="tab">
                            Detected
                            @if($detectedCount > 0)
                                <span class="badge bg-danger-subtle text-danger tab-count">
                                    {{ $detectedCount }}
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary tab-count">
                                    {{ $detectedCount }}
                                </span>
                            @endif
                        </button>
                    </li>
                </ul>

                {{-- ── Add Domain Form ── --}}
                <form action="{{ route('settings.trusted-domains.store') }}" method="POST"
                      class="input-form-container d-flex align-items-center mb-5">
                    @csrf
                    <input type="text"
                           name="domain"
                           class="form-control domain-input me-3 @error('domain') is-invalid @enderror"
                           placeholder="yourdomain.com"
                           value="{{ old('domain') }}">
                    <button type="submit" class="btn btn-primary add-domain-btn fw-bold">
                        Add to trusted
                    </button>
                </form>

                {{-- ── Validation Errors ── --}}
                @if($errors->any())
                    <input type="hidden" id="validation-error" value="{{ $errors->first() }}">
                @endif

                {{-- ── Tab Content ── --}}
                <div class="tab-content domain-tab-content">

                    {{-- ══════════════════════════════════════ --}}
                    {{-- TRUSTED TAB --}}
                    {{-- ══════════════════════════════════════ --}}
                    <div class="tab-pane fade show active" id="trusted" role="tabpanel">

                        @if($trustedDomains->isEmpty())
                            <p class="text-muted">No trusted domains yet. Add your first domain above.</p>
                        @else
                            <p class="text-muted list-status-text">
                                Your LiveChat widget will only work on domains listed below.
                            </p>
                            <div class="table-responsive domain-table-container">
                                <table class="table align-middle domain-table">
                                    <thead class="table-light domain-table-head">
                                        <tr>
                                            <th class="domain-col">Domain name</th>
                                            <th class="added-by-col">Added by</th>
                                            <th class="date-col">Date</th>
                                            <th class="actions-col"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="domain-table-body">
                                        @foreach($trustedDomains as $td)
                                        <tr class="domain-row">
                                            <td class="domain-name text-dark fw-semibold">
                                                {{ $td->domain }}
                                            </td>
                                            <td class="added-by text-muted">
                                                {{ $td->added_by ?? '—' }}
                                            </td>
                                            <td class="date text-muted">
                                                {{ $td->created_at->format('d F Y') }}
                                            </td>
                                            <td class="actions">
                                                <form action="{{ route('settings.trusted-domains.destroy', $td->id) }}"
                                                      method="POST" class="delete-domain-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-primary delete-btn" data-domain="{{ $td->domain }}">
                                                        <iconify-icon icon="solar:trash-bin-trash-broken"></iconify-icon>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- ══════════════════════════════════════ --}}
                    {{-- DETECTED TAB --}}
                    {{-- ══════════════════════════════════════ --}}
                    <div class="tab-pane fade" id="detected" role="tabpanel">

                        @if($detectedDomains->isEmpty())
                            <p class="text-muted">No unauthorized domains detected recently.</p>
                        @else
                            <p class="text-muted list-status-text">
                                These domains tried to use your LiveChat widget but are not trusted.
                            </p>
                            <div class="table-responsive domain-table-container">
                                <table class="table align-middle domain-table">
                                    <thead class="table-light domain-table-head">
                                        <tr>
                                            <th class="domain-col">Domain name</th>
                                            <th class="ip-col">IP address</th>
                                            <th class="attempts-col">Attempts</th>
                                            <th class="date-col">Last attempt</th>
                                            <th class="actions-col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="domain-table-body">
                                        @foreach($detectedDomains as $dd)
                                        <tr class="domain-row">
                                            <td class="domain-name text-dark fw-semibold">
                                                <span class="text-danger me-1">⚠</span>
                                                {{ $dd->domain }}
                                            </td>
                                            <td class="ip-address text-muted" style="font-family: monospace; font-size: 13px;">
                                                {{ $dd->ip_address ?? '—' }}
                                            </td>
                                            <td class="attempts">
                                                @if($dd->attempt_count > 5)
                                                    <span class="badge bg-danger">{{ $dd->attempt_count }}</span>
                                                @elseif($dd->attempt_count > 1)
                                                    <span class="badge bg-warning text-dark">{{ $dd->attempt_count }}</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ $dd->attempt_count }}</span>
                                                @endif
                                            </td>
                                            <td class="date text-muted">
                                                {{ $dd->last_attempt_at?->format('d M Y, H:i') ?? '—' }}
                                            </td>
                                            <td class="actions">
                                                <div class="d-flex gap-2">
                                                    {{-- Trust Button --}}
                                                    <form action="{{ route('settings.detected-domains.trust', $dd->id) }}"
                                                          method="POST" class="trust-domain-form">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" data-domain="{{ $dd->domain }}">
                                                            <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
                                                            Trust
                                                        </button>
                                                    </form>

                                                    {{-- Dismiss Button --}}
                                                    <form action="{{ route('settings.detected-domains.dismiss', $dd->id) }}"
                                                          method="POST" class="dismiss-domain-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" data-domain="{{ $dd->domain }}">
                                                            <iconify-icon icon="solar:close-circle-linear"></iconify-icon>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')    
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ★ Success Message Toast
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    @endif

    // ★ Validation Error Alert
    const validationError = document.getElementById('validation-error');
    if (validationError) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: validationError.value,
            confirmButtonColor: '#2b60d0'
        });
    }

    // ★ Delete Domain Confirm
    document.querySelectorAll('.delete-domain-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const domainName = this.querySelector('button').dataset.domain;
            
            Swal.fire({
                title: 'Remove Domain?',
                html: `Are you sure you want to remove <strong>${domainName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

    // ★ Trust Detected Domain Confirm
    document.querySelectorAll('.trust-domain-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const domainName = this.querySelector('button').dataset.domain;
            
            Swal.fire({
                title: 'Trust This Domain?',
                html: `Are you sure you want to add <strong>${domainName}</strong> to your trusted list?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, trust it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

    // ★ Dismiss Detected Domain Confirm
    document.querySelectorAll('.dismiss-domain-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const domainName = this.querySelector('button').dataset.domain;
            
            Swal.fire({
                title: 'Dismiss Domain?',
                html: `Are you sure you want to dismiss <strong>${domainName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#e0e0e0',
                confirmButtonText: 'Yes, dismiss it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

});
</script>
@endpush