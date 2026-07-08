@extends('layouts.app')
@section('title', 'Payment Success')

@section('content')
<div class="body-wrapper mb-0 pg-invoices">
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="ti ti-check-circle me-2 fs-4"></i>
                    <h5 class="mb-0">Payment Successful! Your account has been activated.</h5>
                </div>

                <div class="card p-4">
                    <h3 class="card-title fs-5 fw-semibold mb-3">Latest Invoice</h3>
                    
                    @if($invoice)
                    <div class="table-responsive">
                        <table class="table modern-table">
                            <thead>
                                <tr>
                                    <th>Invoice No.</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><div class='fw-bold'>{{ $invoice->number }}</div></td>
                                    <td>{{ \Carbon\Carbon::parse($invoice->created)->format('M d, Y') }}</td>
                                    <td><span class='fw-bold'>${{ number_format($invoice->total / 100, 2) }}</span></td>
                                    <td>
                                        <span class='status-badge {{ $invoice->status }}'>
                                            <span class='status-dot'></span> {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ $invoice->invoice_pdf }}" target="_blank" class='action-btn btn-view'>
                                            <i class='ti ti-download me-1'></i> Download PDF
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p>No invoice generated yet. It may take a moment to appear in your account.</p>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('agents.index') }}" class="btn btn-primary">Go to Agents Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection