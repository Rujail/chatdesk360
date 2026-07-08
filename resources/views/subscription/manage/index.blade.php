@extends('layouts.app')
@section('title', 'Checkout')

@push('styles')
<style>

</style>
@endpush

@section('content')
<div class="body-wrapper mb-0 pg-manage">
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-12">
                <div class="subscribe-wrap">
                    <h2 class="page-title">Your subscription</h2>

                    <div id="payment-error" class="alert alert-danger d-none"></div>

                    <div class="checkout-grid">

                        <!-- ============ LEFT: PLAN CARD (STEP 1) ============ -->
                        <div id="step-1">
                            <div class="plan-card">
                                <div class="plan-card-header">
                                    <div class="plan-title-group">
                                        <div class="plan-icon">
                                            <iconify-icon icon="solar:checklist-minimalistic-linear" class="fs-7"></iconify-icon>
                                        </div>
                                        <h3 class="plan-title">{{ $package->title }} plan</h3>
                                    </div>
                                    <div class="plan-price">
                                        <span class="amount" id="plan-price-display">{{ $package->formatted_price }} / mo</span>
                                        @if($package->per_agent)
                                            <span class="unit">per agent</span>
                                        @endif
                                    </div>
                                </div>

                                <form id="configForm">
                                    <input type="hidden" name="package_id" value="{{ $package->id }}">

                                    <div class="plan-controls-row">
                                        <div class="filters form-group">
                                            <select class="custom-selectoption" name="billing_cycle" id="billingCycle" placeholder="Select Option">
                                                <option value="monthly">Billed monthly</option>
                                                <option value="annual">
                                                    Billed annually
                                                    @if($package->has_annual_discount)
                                                        (Save {{ rtrim(rtrim(number_format($package->annual_discount, 1), '0'), '.') }}%)
                                                    @endif
                                                </option>
                                            </select>
                                        </div>

                                        @if($package->has_annual_discount)
                                            <span class="save-link" id="saveAnnualLink" data-per-agent-savings="{{ $package->annual_savings }}">
                                                Save ${{ number_format($package->annual_savings / 100, 2) }} annually
                                            </span>
                                        @endif
                                    </div>

                                    <div class="agentrows">
                                        <div class="qty-box @if(!$package->per_agent) is-disabled @endif"
                                             @if(!$package->per_agent)
                                                 data-bs-toggle="tooltip"
                                                 data-bs-placement="top"
                                                 title="{{ $package->title }} plan is only for 1 agent. Change your plan or decrease the number of agents."
                                             @endif>
                                            <input type="number" id="agentCount" name="quantity" min="1" value="1"
                                                   @if(!$package->per_agent) disabled @endif>
                                            <div class="qty-arrows">
                                                <i class="ti ti-chevron-up qty-plus @if(!$package->per_agent) is-disabled @endif"></i>
                                                <i class="ti ti-chevron-down qty-minus @if(!$package->per_agent) is-disabled @endif"></i>
                                            </div>
                                        </div>
                                        <span class="agent-label" id="agentLabel">agent</span>
                                    </div>

                                    <hr class="plan-divider">

                                    @if(Route::has('subscription.index'))
                                        <a href="{{ route('subscription.index') }}" class="change-plan-link">Change plan</a>
                                    @else
                                        <a href="#" class="change-plan-link">Change plan</a>
                                    @endif

                                    <!-- Saved Card Option -->
                                    @if($paymentMethod)
                                    <div class="mt-4 p-3 border rounded bg-light">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="payment_option" id="use_saved_card" value="saved" checked>
                                            <label class="form-check-label fw-bold" for="use_saved_card">
                                                Use Saved Card
                                            </label>
                                        </div>
                                        <div class="ms-4 text-muted">
                                            <i class="ti ti-credit-card"></i>
                                            Card ending in <strong>{{ $paymentMethod->card->last4 }}</strong>
                                            (Exp: {{ sprintf('%02d', $paymentMethod->card->exp_month) }}/{{ substr($paymentMethod->card->exp_year, -2) }})
                                        </div>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="radio" name="payment_option" id="use_new_card" value="new">
                                            <label class="form-check-label fw-bold" for="use_new_card">
                                                Add a New Card
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- ============ STEP 2: Payment (hidden initially) ============ -->
                        <div class="d-none" id="step-2-wrap">
                            <div class="plan-card" id="step-2">
                                <h4 class="fs-5 fw-semibold mb-3">Add Payment Method</h4>
                                <form id="paymentForm">
                                    <div class="mb-3">
                                        <label class="form-label">Card Details</label>
                                        <div id="payment-element" class="form-control p-2" style="min-height: 44px;"></div>
                                    </div>
                                    <button type="submit" id="submitPaymentBtn" class="checkout-btn">Pay Now</button>
                                    <button type="button" id="backToConfigBtn" class="btn btn-outline-dark w-100 mt-2">Back</button>
                                </form>
                            </div>
                        </div>

                        <!-- ============ RIGHT: SUMMARY CARD ============ -->
                        <div class="summary-card" id="summaryWrap"
                             data-current-cycle="{{ $currentSubscription->billing_cycle ?? '' }}"
                             data-current-qty="{{ $currentSubscription->quantity ?? '' }}">
                            <div class="summary-title">Summary</div>

                            @isset($currentSubscription)
                                <div class="summary-section-label">Current subscription</div>
                                <div class="current-sub-row">
                                    <div>
                                        <div class="name">{{ $currentSubscription->package->title ?? 'N/A' }} plan</div>
                                        <div class="sub-line">Agents: {{ $currentSubscription->quantity }}</div>
                                    </div>
                                    <div class="price-block">
                                        @if($currentSubscription->billing_cycle === 'annual' && ($currentSubscription->package->has_annual_discount ?? false))
                                            <span class="price-strike">${{ number_format($currentSubscription->package->full_annual_price / 100, 0) }}</span>
                                            <span class="price-now">${{ number_format($currentSubscription->package->discounted_annual_price / 100, 0) }}</span>
                                            <span> / yr</span>
                                            <div class="saving-pill">saving {{ rtrim(rtrim(number_format($currentSubscription->package->annual_discount, 2), '0'), '.') }}%</div>
                                        @else
                                            <span class="price-now">{{ $currentSubscription->package->formatted_price ?? '' }}</span>
                                            <span> / {{ $currentSubscription->billing_cycle === 'annual' ? 'yr' : 'mo' }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endisset

                            <div class="summary-section-label new-sub-label">New subscription</div>

                            <div class="current-sub-row" id="newSubRow">
                                <div>
                                    <div class="name">{{ $package->title }} plan</div>
                                    <div class="sub-line">Agents: <span id="newSubAgents">1</span></div>
                                </div>
                            </div>

                            <div class="changes-box d-none" id="changesBox">
                                <div class="title">
                                    <i class="ti ti-info-circle"></i> Immediate changes
                                </div>
                                <div class="change-line" id="changeLines"></div>
                            </div>

                            <div class="summary-line">
                                <span class="label" id="cycleTotalLabel">
                                    Monthly total
                                    <i class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Total based on selected billing cycle and agent count"></i>
                                </span>
                                <span class="value">
                                    <span class="price-strike d-none" id="cycleTotalStrike"></span>
                                    <span id="summary-cycle-total">$0.00</span>
                                </span>
                            </div>
                            <div class="cycle-total-pill-wrap d-none" id="cycleSavingPillWrap">
                                <div class="saving-pill" id="cycleSavingPill"></div>
                            </div>

                            <div class="summary-line">
                                <span class="label">
                                    Prorated charge due today
                                    <i class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Estimated — final amount confirmed by Stripe at checkout"></i>
                                </span>
                                <span class="value" id="summary-prorated">$0.00</span>
                            </div>

                            <div class="summary-line">
                                <span class="label">Billed on renewal</span>
                                <span class="value" id="summary-renewal">$0.00</span>
                            </div>

                            <div class="summary-line total">
                                <span class="label">Billed now</span>
                                <span class="value" id="summary-total">$0.00</span>
                            </div>

                            <button type="button" id="goToPaymentBtn" class="checkout-btn">
                                @if($paymentMethod) Pay Now @else Go to checkout @endif
                            </button>

                            <div class="disclaimer">
                                Your subscription will continue until you cancel.
                            </div>
                        </div>

                    </div>
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
        let currentSubscriptionId = null;

        const packagePrice = {{ $package->price }};                 // monthly price, in cents
        const annualDiscount = {{ $package->annual_discount }};     // e.g. 16.95
        const fullAnnualCents = {{ $package->full_annual_price }};  // price * 12, no discount
        const discountedAnnualCents = {{ $package->discounted_annual_price }}; // actual annual charge

        const $summaryWrap = $('#summaryWrap');
        const currentCycle = $summaryWrap.data('current-cycle') || null;
        const currentQty = $summaryWrap.data('current-qty') || null;

        // Init all Bootstrap tooltips (info icons + disabled qty box)
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

        function pluralizeAgent(count) {
            return count > 1 ? 'agents' : 'agent';
        }

        function updateSummary() {
            const agents = parseInt($('#agentCount').val()) || 1;
            const cycle = $('#billingCycle').val();

            $('#newSubAgents').text(agents);

            // ADD THIS — recalculate "Save $X annually" text based on current agent count
            const $saveLink = $('#saveAnnualLink');
            if ($saveLink.length) {
                const perAgentSavingsCents = parseInt($saveLink.data('per-agent-savings')) || 0;
                const totalSavingsCents = perAgentSavingsCents * agents;
                $saveLink.text('Save $' + (totalSavingsCents / 100).toFixed(2) + ' annually');
            }

            let unitPriceCents, cycleTotalCents, renewalLabel;

            if (cycle === 'annual') {
                unitPriceCents = discountedAnnualCents;
                cycleTotalCents = unitPriceCents * agents;
                renewalLabel = '$' + (unitPriceCents / 100).toFixed(2) + ' / yr';
                $('#cycleTotalLabel').contents().first().replaceWith('Annual total ');
                $('#plan-price-display').text('$' + (packagePrice / 100).toFixed(2) + ' / mo');

                // Show strike-through original price + saving pill when a discount exists
                if (annualDiscount > 0) {
                    const originalTotalCents = fullAnnualCents * agents;
                    $('#cycleTotalStrike').removeClass('d-none').text('$' + (originalTotalCents / 100).toFixed(2));
                    $('#cycleSavingPill').text('saving ' + annualDiscount.toFixed(2).replace(/\.?0+$/, '') + '%');
                    $('#cycleSavingPillWrap').removeClass('d-none');
                } else {
                    $('#cycleTotalStrike').addClass('d-none');
                    $('#cycleSavingPillWrap').addClass('d-none');
                }
            } else {
                unitPriceCents = packagePrice;
                cycleTotalCents = unitPriceCents * agents;
                renewalLabel = '$' + (unitPriceCents / 100).toFixed(2) + ' / mo';
                $('#cycleTotalLabel').contents().first().replaceWith('Monthly total ');
                $('#plan-price-display').text('$' + (packagePrice / 100).toFixed(2) + ' / mo');

                // No discount concept on monthly billing
                $('#cycleTotalStrike').addClass('d-none');
                $('#cycleSavingPillWrap').addClass('d-none');
            }

            $('#agentLabel').text(pluralizeAgent(agents));

            $('#summary-cycle-total').text('$' + (cycleTotalCents / 100).toFixed(2));
            $('#summary-prorated').text('$' + (cycleTotalCents / 100).toFixed(2)); // placeholder — replace with real Stripe preview amount
            $('#summary-renewal').text(renewalLabel);
            $('#summary-total').text('$' + (cycleTotalCents / 100).toFixed(2));

            $('#saveAnnualLink').toggle(cycle === 'monthly');

            let changeLines = [];
            if (currentCycle && currentCycle !== cycle) {
                changeLines.push(`Billing: ${currentCycle} &rarr; ${cycle}`);
            }
            if (currentQty && parseInt(currentQty) !== agents) {
                changeLines.push(`Agents: ${currentQty} &rarr; ${agents}`);
            }

            if (changeLines.length > 0) {
                $('#changeLines').html(changeLines.join('<br>'));
                $('#changesBox').removeClass('d-none');
            } else {
                $('#changesBox').addClass('d-none');
            }
        }

        updateSummary();
        $('#agentCount, #billingCycle').on('change keyup', updateSummary);

        // "Save $X annually" must drive the CUSTOM dropdown UI too, not just the raw <select>.
        // Simplest reliable way: find the plugin-generated <li><a> for "Billed annually" and
        // simulate a real click on it — this reuses the plugin's own logic (label text,
        // active state, filled class, selecting the option, and firing the native change event).
        $('#saveAnnualLink').on('click', function() {
            const $dropdown = $('#billingCycle').closest('.selectDropdown');
            const $annualLink = $dropdown.find('ul li a').filter(function() {
                return $(this).text().trim().indexOf('Billed annually') === 0;
            });

            if ($annualLink.length) {
                $annualLink.trigger('click');
            } else {
                // Fallback: plugin markup not found for some reason — update raw select directly
                $('#billingCycle').val('annual').trigger('change');
            }
        });

        $('.qty-plus').on('click', function() {
            if ($(this).hasClass('is-disabled')) return;
            let input = $('#agentCount');
            let val = parseInt(input.val()) || 1;
            input.val(val + 1).trigger('change');
        });

        $('.qty-minus').on('click', function() {
            if ($(this).hasClass('is-disabled')) return;
            let input = $('#agentCount');
            let val = parseInt(input.val()) || 1;
            if (val > 1) {
                input.val(val - 1).trigger('change');
            }
        });

        $('#goToPaymentBtn').click(function() {
            const btn = $(this);
            const useSavedCard = $('input[name="payment_option"]:checked').val() === 'saved';

            btn.prop('disabled', true).text('Processing...');

            $.ajax({
                url: '{{ route("subscription.pay") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    package_id: '{{ $package->id }}',
                    quantity: $('#agentCount').val(),
                    billing_cycle: $('#billingCycle').val(),
                    use_saved_card: useSavedCard ? 1 : 0
                },
                success: function(res) {
                    if (res.error) {
                        alert(res.error);
                        btn.prop('disabled', false).text('Go to checkout');
                        return;
                    }

                    if (res.directSuccess) {
                        window.location.href = '{{ route("subscription.success") }}?subscription_id=' + res.subscriptionId;
                        return;
                    }

                    currentSubscriptionId = res.subscriptionId;
                    $('#step-1').addClass('d-none');
                    $('#step-2-wrap').removeClass('d-none');
                    initStripeElements(res.setupIntentSecret);
                    btn.prop('disabled', false).text('Go to checkout');
                },
                error: function(err) {
                    let errorMsg = 'Error creating payment session.';
                    if (err.responseJSON && err.responseJSON.error) {
                        errorMsg = err.responseJSON.error;
                    }
                    alert(errorMsg);
                    btn.prop('disabled', false).text('Go to checkout');
                }
            });
        });

        $('#backToConfigBtn').click(function() {
            $('#step-1').removeClass('d-none');
            $('#step-2-wrap').addClass('d-none');
        });

        function initStripeElements(setupIntentSecret) {
            const appearance = { theme: 'stripe' };
            elements = stripe.elements({ appearance, clientSecret: setupIntentSecret });

            const paymentElement = elements.create('payment', {
                defaultValues: {
                    billingDetails: { address: { country: 'US' } }
                }
            });

            paymentElement.mount('#payment-element');
        }

        $('#paymentForm').submit(function(e) {
            e.preventDefault();
            const btn = $('#submitPaymentBtn');
            btn.prop('disabled', true).text('Processing...');

            stripe.confirmSetup({
                elements,
                confirmParams: {
                    return_url: window.location.origin + '{{ route("subscription.success", [], false) }}?subscription_id=' + currentSubscriptionId,
                },
                redirect: 'if_required',
            }).then(function(result) {
                if (result.error) {
                    $('#payment-error').removeClass('d-none').text(result.error.message);
                    btn.prop('disabled', false).text('Pay Now');
                    return;
                }

                const paymentMethodId = result.setupIntent.payment_method;

                $.ajax({
                    url: '{{ route("subscription.confirm") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        payment_method_id: paymentMethodId,
                        subscription_id: currentSubscriptionId,
                    },
                    success: function(res) {
                        if (res.error) {
                            $('#payment-error').removeClass('d-none').text(res.error);
                            btn.prop('disabled', false).text('Pay Now');
                            return;
                        }

                        if (res.requires_action) {
                            stripe.confirmCardPayment(res.client_secret).then(function(r) {
                                if (r.error) {
                                    $('#payment-error').removeClass('d-none').text(r.error.message);
                                    btn.prop('disabled', false).text('Pay Now');
                                } else {
                                    window.location.href = '{{ route("subscription.success") }}?subscription_id=' + currentSubscriptionId;
                                }
                            });
                        } else {
                            window.location.href = '{{ route("subscription.success") }}?subscription_id=' + currentSubscriptionId;
                        }
                    },
                    error: function(err) {
                        const msg = err.responseJSON?.error || 'Payment confirmation failed.';
                        $('#payment-error').removeClass('d-none').text(msg);
                        btn.prop('disabled', false).text('Pay Now');
                    }
                });
            });
        });
    </script>
@endpush