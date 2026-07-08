@extends('front-layout.app')

@section('title', ' - Home')

@section('content')

<section class="our-banner inner-banner">
    <div class="container"> 
        <div class="row">
            <div class="col-md-12">
                <div class="info-banner text-center">
                    <h5>
                        Help
                    </h5>
                    <h2>
                        Hi, how can we help?
                    </h2>
                    <p>
                        Sollicitudin aliquam posuere urna parturient pretium sed sodales. Suscipit lacinia commodo odio phasellus nibh aptent mi et est ex. Vulputate elit torquent eros cubilia per inceptos ad elementum rhoncus.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section> 


<section class="our-help">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="box-help">
                    <span>
                        <img src="{{ asset('front-assets/images/help1.png') }}" alt="">
                    </span>
                    <h4>
                        Use ChatDesk360
                    </h4>
                    <p>
                        <b>Add ChatDesk360</b> to your site or app with these step-by-step tutorials
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-help">
                    <span>
                        <img src="{{ asset('front-assets/images/help2.png') }}" alt="">
                    </span>
                    <h4>
                        Best practices
                    </h4>
                    <p>
                        <b>Add ChatDesk360</b> to your site or app with these step-by-step tutorials
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-help">
                    <span>
                        <img src="{{ asset('front-assets/images/help3.png') }}" alt="">
                    </span>
                    <h4>
                        Get started
                    </h4>
                    <p>
                        <b>Add ChatDesk360</b> to your site or app with these step-by-step tutorials
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-help">
                    <span>
                        <img src="{{ asset('front-assets/images/help4.png') }}" alt="">
                    </span>
                    <h4>
                        Apps and integrations
                    </h4>
                    <p>
                        <b>Add ChatDesk360</b> to your site or app with these step-by-step tutorials
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-help">
                    <span>
                        <img src="{{ asset('front-assets/images/help5.png') }}" alt="">
                    </span>
                    <h4>
                        Product & Services
                    </h4>
                    <p>
                        <b>Add ChatDesk360</b> to your site or app with these step-by-step tutorials
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-help">
                    <span>
                        <img src="{{ asset('front-assets/images/help6.png') }}" alt="">
                    </span>
                    <h4>
                        Business Features
                    </h4>
                    <p>
                        <b>Add ChatDesk360</b> to your site or app with these step-by-step tutorials
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-help">
                    <span>
                        <img src="{{ asset('front-assets/images/help7.png') }}" alt="">
                    </span>
                    <h4>
                        Payment
                    </h4>
                    <p>
                        <b>Add ChatDesk360</b> to your site or app with these step-by-step tutorials
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-help">
                    <span>
                        <img src="{{ asset('front-assets/images/help8.png') }}" alt="">
                    </span>
                    <h4>
                        Privacy and security
                    </h4>
                    <p>
                        <b>Add ChatDesk360</b> to your site or app with these step-by-step tutorials
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-help">
                    <span>
                        <img src="{{ asset('front-assets/images/help9.png') }}" alt="">
                    </span>
                    <h4>
                        Other Questions
                    </h4>
                    <p>
                        <b>Add ChatDesk360</b> to your site or app with these step-by-step tutorials
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="admin-cta">
    <div class="container">
        <div class="bg-admincta"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="info-admincta">
                    <h5>
                        What’s new
                    </h5>
                    <h2>
                        Admins can now manage Subscription
                    </h2>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.
                    </p>
                    <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                        Get Started Now
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@include('front-includes.lg-counter')
@include('front-includes.faq')
@include('front-includes.prefoot')

@endsection

