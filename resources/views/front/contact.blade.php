@extends('front-layout.app')

@section('title', ' - Home')

@push('styles')
    <style>
        .error-text {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            min-height: 14px;
        }
        .form-control.is-invalid {
            border-color: #dc3545 !important;
            background-color: #fff5f5;
        }
        .error-text:empty {
            display: none;
        }
    </style>
@endpush

@section('content')

<section class="our-banner inner-banner cont-banner">
    <div class="container"> 
        <div class="row">
            <div class="col-md-12">
                <div class="info-banner text-center">
                    <h5>
                        Contact Us
                    </h5>
                    <h2>
                        We're Here to Help You!
                    </h2>
                    <p>
                        Sollicitudin aliquam posuere urna parturient pretium sed sodales. Suscipit lacinia commodo odio phasellus nibh aptent mi et est ex. Vulputate elit torquent eros cubilia per inceptos ad elementum rhoncus.
                    </p>
                    <img src="{{ asset('front-assets/images/cont-img1.png') }}" alt="">
                </div>
            </div>
        </div>
        <img class="cont-bg1" src="{{ asset('front-assets/images/cont-bg1.png') }}" alt="">
    </div>
</section>
        
<section class="our-contact">
    <div class="container">
        <div class="heading text-center">
            <h6 class="sub-head">
                About chatdesk360
            </h6>
            <h2>
                Increase Productivity, <br>
                Growth More
            </h2>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="box-cont">
                    <span>
                        <img src="{{ asset('front-assets/images/cont-ic1.png') }}" alt="">
                    </span>
                    <h4>
                        Book an Appointment
                    </h4>
                    <a href="mailto:support@chatdesk360.com">support@chatdesk360.com</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-cont">
                    <span>
                        <img src="{{ asset('front-assets/images/cont-ic2.png') }}" alt="">
                    </span>
                    <h4>
                        Talk to our expert
                    </h4>
                    <a href="tel:+990-737-621-432">+990-737-621-432</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-cont">
                    <span>
                        <img src="{{ asset('front-assets/images/cont-ic3.png') }}" alt="">
                    </span>
                    <h4>
                        Our Approch
                    </h4>
                    <p>
                        121 King Street, Melbourne Victoria <br>
                        3000 Australia
                    </p>
                </div>
            </div>
        </div>
        <div class="row row-cont">
            <div class="col-md-5">
                <div class="info-cont">
                    <h6 class="sub-head">
                        Schedule a call
                    </h6>
                    <h2>
                        It’s Time to <br>
                        Hire <span>ChatDesk360</span> <br>
                        Customer Services
                    </h2>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.
                    </p>
                </div>
            </div>
            <div class="col-md-7">
                <div class="box-contform">
                    <img class="cont-bg2" src="{{ asset('front-assets/images/cont-bg2.png') }}" alt="">
                    <img class="cont-bg3" src="{{ asset('front-assets/images/cont-bg3.png') }}" alt="">
                    <h4>
                        Send us a message
                    </h4>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.
                    </p>
                    <form id="contactForm" method="POST" class="validate-popupform">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="cn" id="cn" class="form-control" required>
                                    <small class="text-danger error-text" data-error="cn"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="lname" id="lname" class="form-control" required>
                                    <small class="text-danger error-text" data-error="lname"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="em" id="em" class="form-control" required>
                                    <small class="text-danger error-text" data-error="em"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="pn" id="pn" class="form-control" required>
                                    <small class="text-danger error-text" data-error="pn"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea name="msg" id="msg" class="form-control" rows="5" required></textarea>
                                    <small class="text-danger error-text" data-error="msg"></small>
                                </div>

                                <!-- General response / success message -->
                                <div id="form-response" class="mt-3"></div>

                                <button type="submit" name="send" class="btn btn-grd">
                                    Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@include('front-includes.testimonials')
@include('front-includes.prefoot')
@include('front-includes.faq')


@endsection

@push('scripts')
<script>
 $(document).ready(function() {
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let url = "{{ route('front.contact.send') }}";
        let submitBtn = form.find('button[type="submit"]');
        let responseDiv = $('#form-response');

        // Clear previous errors
        $('.error-text').text('');
        responseDiv.html('');

        submitBtn.prop('disabled', true).text('Sending...');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                responseDiv.html(
                    '<div class="alert alert-success">' + response.message + '</div>'
                );
                form[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors — show below each field
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function(key, message) {
                        // key will be: cn, lname, em, pn, msg
                        $('[data-error="' + key + '"]').text(message[0]);

                        // Optional: add red border to the input
                        $('[name="' + key + '"]').addClass('is-invalid');
                    });
                } else {
                    responseDiv.html(
                        '<div class="alert alert-danger">Something went wrong. Please try again.</div>'
                    );
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Send Message');
            }
        });
    });

    // Remove error styling when user starts typing again
    $('#contactForm input, #contactForm textarea').on('input change', function() {
        let name = $(this).attr('name');
        $(this).removeClass('is-invalid');
        $('[data-error="' + name + '"]').text('');
    });
});
</script>
@endpush