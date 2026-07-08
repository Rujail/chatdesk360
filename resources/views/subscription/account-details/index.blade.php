@extends('layouts.app')

@section('title', 'Account Details')

@section('content')
<div class="body-wrapper pg-acc-details">
    <div class="container-fluid ">
        <x-breadcrumb title="Account Details" />

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card acc-details-card">
            <div class="card-body p-0">
                <div class="row g-4 details-layout-row">

                    <div class="col-lg-7">
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-12 owner-col">
                                <div class="card details-module-card owner-card">
                                    <div class="card-body owner-card-body">
                                        <h5 class="card-title module-heading">Account Owner</h5>
                                        <div class="d-flex align-items-center owner-info-block">
                                            <div class="owner-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                            <div class="owner-text-info">
                                                <h6 class="owner-name">{{ auth()->user()->name }}</h6>
                                                <p class="owner-email">{{ auth()->user()->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 🔹 DYNAMIC PAYMENT CARD (Desktop View) -->
                            <div class="col-md-6 payment-col-mobile">
                                <div class="card details-module-card payment-card payment-card-visual-dark">
                                    <div class="card-body payment-card-body">
                                        <h5 class="card-title module-heading card-heading-white">Payment card</h5>
                                        
                                        @if($paymentMethod)
                                            <p class="card-number-text mb-0">**** **** **** {{ $paymentMethod->card->last4 }}</p>
                                            <p class="card-expiry-text">Expires {{ sprintf('%02d', $paymentMethod->card->exp_month) }}/{{ substr($paymentMethod->card->exp_year, -2) }}</p>
                                        @else
                                            <p class="card-number-text mb-0">No card added</p>
                                            <p class="card-expiry-text">Please add a card</p>
                                        @endif

                                        <button class="btn btn-sm edit-card-btn" data-bs-toggle="modal" data-bs-target="#editCardModal">Edit card</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 company-col">
                                <div class="card details-module-card company-card">
                                    <div class="card-body company-card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title module-heading">Company details</h5>
                                            <iconify-icon icon="solar:widget-3-line-duotone" class="module-icon"></iconify-icon>
                                        </div>
                                        <ul class="list-unstyled detail-list">
                                            <li class="detail-item domain-name">{{ auth()->user()->site->name ?? 'N/A' }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 details-secondary-col">
                        <div class="row g-4">
                            <div class="col-12 payment-col-desktop">
                                <div class="card details-module-card payment-card payment-card-visual-dark">
                                    <div class="card-body payment-card-body">
                                        <h5 class="card-title module-heading card-heading-white">Payment card</h5>
                                        
                                        @if($paymentMethod)
                                            <p class="card-number-text mb-0">**** **** **** {{ $paymentMethod->card->last4 }}</p>
                                            <p class="card-expiry-text">Expires {{ sprintf('%02d', $paymentMethod->card->exp_month) }}/{{ substr($paymentMethod->card->exp_year, -2) }}</p>
                                        @else
                                            <p class="card-number-text mb-0">No card added</p>
                                            <p class="card-expiry-text">Please add a card</p>
                                        @endif

                                        <button class="btn btn-sm edit-card-btn" data-bs-toggle="modal" data-bs-target="#editCardModal">Edit card</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🔹 CANCEL SUBSCRIPTION SECTION -->
                <div class="cancellation-section mt-4">
                    <h6 class="cancel-heading">Cancel subscription</h6>
                    <p class="cancel-text">Do not cancel subscription if you just want to <a href="#" class="change-link">change team size or billing period</a>.</p>
                    
                    @if($subscription && $subscription->active())
                        <form action="{{ route('subscription.account-details.cancel') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger continue-cancel-link">Continue to cancel</button>
                        </form>
                    @else
                        <button type="button" class="btn btn-secondary" disabled>No Active Subscription</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 🔹 EDIT CARD MODAL (Stripe Elements) -->
    <div class="modal fade" id="editCardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content professional-modal-content">
                <div class="modal-header professional-modal-header">
                    <h5 class="modal-title module-heading">Update Payment Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="payment-error" class="alert alert-danger d-none"></div>
                    <!-- Stripe Elements Mount Point -->
                    <div id="stripe-card-element" class="form-control p-3" style="min-height: 50px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveCardBtn" class="btn btn-primary">Save Card</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    let elements;
    let cardElement;

    // Jab Modal open ho, tab Stripe Element initialize karo
    document.getElementById('editCardModal').addEventListener('shown.bs.modal', function () {
        fetch('{{ route("subscription.account-details.setup-intent") }}')
            .then(res => res.json())
            .then(data => {
                // Stripe Elements Setup
                const appearance = { theme: 'stripe' };
                elements = stripe.elements({ appearance, clientSecret: data.clientSecret });
                cardElement = elements.create('payment');
                cardElement.mount('#stripe-card-element');
            })
            .catch(err => console.error(err));
    });

    // Modal close hone par element clear karo
    document.getElementById('editCardModal').addEventListener('hidden.bs.modal', function () {
        if (cardElement) {
            cardElement.destroy();
        }
    });

    // Save Card Button Click
    document.getElementById('saveCardBtn').addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = 'Saving...';
        document.getElementById('payment-error').classList.add('d-none');

        stripe.confirmSetup({
            elements,
            redirect: 'if_required'
        }).then(function(result) {
            if (result.error) {
                // Agar card mein error hai (invalid number etc)
                document.getElementById('payment-error').textContent = result.error.message;
                document.getElementById('payment-error').classList.remove('d-none');
                btn.disabled = false;
                btn.innerHTML = 'Save Card';
            } else {
                // Card successfully verified! Payment Method ID nikalo
                const paymentMethodId = result.setupIntent.payment_method;

                // Backend ko bhejo save karne ke liye
                fetch('{{ route("subscription.account-details.payment-method") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ payment_method_id: paymentMethodId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload(); // Page reload kardo naya card show karne ke liye
                    } else {
                        throw new Error(data.error || 'Failed to save card.');
                    }
                })
                .catch(error => {
                    document.getElementById('payment-error').textContent = error.message;
                    document.getElementById('payment-error').classList.remove('d-none');
                    btn.disabled = false;
                    btn.innerHTML = 'Save Card';
                });
            }
        });
    });
</script>
@endpush