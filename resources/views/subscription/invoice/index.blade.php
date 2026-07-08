@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="body-wrapper mb-0 pg-invoices">
    <div class="container-fluid ">
        <x-breadcrumb title="Invoices" /> 
        
        <!-- 🔹 CURRENT SUBSCRIPTION CARD -->
        @if($subscriptionDetails)
        <div class="card mb-4">
            <div class="card-body">
                
                <!-- 🔹 WARNING: Agar payment fail hui hai -->
                @if($subscriptionDetails['status_badge'] == 'pending')
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="ti ti-alert-triangle me-2 fs-4"></i>
                    <div>
                        <strong>Action Required!</strong> Your last payment failed. Please update your payment method to avoid service interruption.
                    </div>
                </div>
                @endif

                <h3 class="card-title fs-5 fw-semibold mb-4">Current Subscription</h3>
                <div class="row">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <p class="text-muted mb-1">Plan Name</p>
                        <h5 class="fw-bold mb-0">{{ $subscriptionDetails['plan_name'] }}</h5>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <p class="text-muted mb-1">Billing Cycle</p>
                        <h5 class="fw-bold mb-0">{{ $subscriptionDetails['billing_cycle'] }}</h5>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <p class="text-muted mb-1">
                            @if($subscriptionDetails['ends_at'])
                                Ends On
                            @else
                                Next Billing Date
                            @endif
                        </p>
                        <h5 class="fw-bold mb-0">
                            {{ $subscriptionDetails['ends_at'] ?? $subscriptionDetails['next_billing_date'] }}
                        </h5>
                    </div>
                    <div class="col-md-3">
                        <p class="text-muted mb-1">Status</p>
                        <span class='status-badge {{ $subscriptionDetails['status_badge'] }}'>
                            <span class='status-dot'></span> {{ $subscriptionDetails['status'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- 🔹 NEXT INVOICE CARD (Sirf tab dikhao jab payment past_due na ho) -->
        @if($upcomingInvoice && (!isset($subscriptionDetails['status_badge']) || $subscriptionDetails['status_badge'] == 'paid'))
        <div class="card mb-4 border-primary">
            <div class="card-body">
                <h3 class="card-title fs-5 fw-semibold mb-4 text-primary">Next Invoice Details</h3>
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <p class="text-muted mb-1">Next Billing Date</p>
                        <h5 class="fw-bold mb-0">
                            @if($upcomingInvoice->next_payment_attempt)
                                {{ \Carbon\Carbon::createFromTimestamp($upcomingInvoice->next_payment_attempt)->format('M d, Y') }}
                            @else
                                N/A
                            @endif
                        </h5>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <p class="text-muted mb-1">Amount Due</p>
                        <h5 class="fw-bold mb-0">
                            ${{ number_format($upcomingInvoice->total / 100, 2) }}
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted mb-1">Status</p>
                        <span class='status-badge pending'>
                            <span class='status-dot'></span> Upcoming
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Invoice Table Card -->
        <div class="card invoice-table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($processedInvoices as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold" style="color: var(--gray-900);">
                                            {{ $item['number'] }}
                                        </div>
                                    </td>
                                    <td>{{ $item['date'] }}</td>
                                    <td>
                                        <span class="fw-bold" style="color: var(--gray-900);">
                                            ${{ $item['total'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $item['status_class'] }}">
                                            <span class="status-dot"></span> {{ $item['status_text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="action-btn btn-view viewInvoiceBtn"
                                                data-invoice="{{ json_encode($item['invoice_data']) }}">
                                            <i class="ti ti-eye me-1"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div class="modal fade invoice-modal" id="invoiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h5 class="modal-title mb-0">Invoice Preview</h5>
                <div class="modal-actions">
                    <button class="icon-btn" onclick="printInvoice()" title="Print">
                        <i class="ti ti-printer"></i>
                    </button>
                    <button class="icon-btn" onclick="downloadInvoice()" title="Download">
                        <i class="ti ti-download"></i>
                    </button>
                    <button class="icon-btn" data-bs-dismiss="modal" title="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="invoice-preview" id="invoiceContent">
                    <!-- Invoice content will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.viewInvoiceBtn').forEach((btn) => {
            btn.addEventListener('click', function () {
                const invoice = JSON.parse(this.dataset.invoice);
                displayInvoice(invoice);
                const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
                modal.show();
            });
        });
    });

    function displayInvoice(inv) {
        let subtotal = 0;
        inv.items.forEach(item => subtotal += item.qty * item.price);

        let itemsHTML = '';
        inv.items.forEach((item, index) => {
            const itemTotal = item.qty * item.price;
            itemsHTML += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.desc}</td>
                <td align="center">${item.qty}</td>
                <td align="right">$${item.price.toFixed(2)}</td>
                <td align="right">$${itemTotal.toFixed(2)}</td>
            </tr>`;
        });

        document.getElementById('invoiceContent').innerHTML = `
        <table class="header-table" width="100%">
            <tr>
                <td width="60%">
                    <h2 class="company-title">ChatDesk360</h2>
                    <p>
                        123 Business Street<br>
                        San Francisco, CA 94122<br>
                        United States<br>
                        <strong>Email:</strong> hello@chatdesk360.com
                    </p>
                </td>
                <td width="40%" align="left">
                    <div class="detail-box">
                        <h4 class="section-title">Invoice Details</h4>
                        <p><strong>Invoice #:</strong> ${inv.id}</p>
                        <p><strong>Invoice Date:</strong> ${inv.date}</p>
                        <p><strong>Service Period:</strong> ${inv.period_start} — ${inv.period_end}</p>
                        <p><strong>Status:</strong> 
                            <span class="status-badge ${inv.status}">
                                <span class="status-dot"></span> ${inv.statusText}
                            </span>
                        </p>
                    </div>
                </td>
            </tr>
        </table>

        <table width="100%" style="margin-bottom: 15px;">
            <tr>
                <td width="50%">
                    <div class="detail-box">
                        <h4 class="section-title">Bill To</h4>
                        <p><strong>${inv.client}</strong></p>
                        <p>${inv.email}</p>
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table" width="100%">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>Description</th>
                    <th width="70" align="center">Qty</th>
                    <th width="80" align="right">Unit Price</th>
                    <th width="90" align="right">Total</th>
                </tr>
            </thead>
            <tbody>${itemsHTML}</tbody>
        </table>

        <table class="total-table">
            <tr>
                <td class="total-label">Total:</td>
                <td class="total-value"><strong>$${subtotal.toFixed(2)}</strong></td>
            </tr>
        </table>

        <div class="invoice-footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
        </div>`;
    }

    function printInvoice() {
        const divContents = document.getElementById('invoiceContent').innerHTML;
        const width = 800, height = 750;
        const left = screen.width / 2 - width / 2;
        const top  = screen.height / 2 - height / 2;
        const printWindow = window.open('', '_blank', `height=${height},width=${width},top=${top},left=${left},scrollbars=yes`);

        let styles = '';
        document.querySelectorAll('link[rel="stylesheet"], style').forEach(sheet => {
            styles += sheet.tagName === 'LINK'
                ? `<link rel="stylesheet" href="${sheet.href}">`
                : `<style>${sheet.innerHTML}</style>`;
        });

        if (printWindow) {
            printWindow.document.write(`
                <html><head>
                    <title>Invoice</title>
                    <meta charset="UTF-8">
                    ${styles}
                    <style>
                        body { margin: 0; padding: 20px; background: white; }
                        .invoice-preview { margin: 0 auto; box-shadow: none !important; border: none !important; }
                        table { width: 100% !important; border-collapse: collapse !important; }
                    </style>
                </head>
                <body><div class="invoice-preview">${divContents}</div></body>
                </html>`);
            printWindow.document.close();
            printWindow.onload = () => setTimeout(() => { printWindow.focus(); printWindow.print(); }, 500);
        } else {
            alert('Popups blocked! Please allow popups to print.');
        }
    }

    function downloadInvoice() {
        html2pdf().set({
            margin: 10,
            filename: 'invoice.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(document.getElementById('invoiceContent')).save();
    }
</script>
@endpush