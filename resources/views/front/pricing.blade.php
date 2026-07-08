@extends('front-layout.app')

@section('title', ' - Home')

@section('content')

<section class="our-banner inner-banner price-banner">
    <div class="container"> 
        <div class="row">
            <div class="col-md-12">
                <div class="info-banner text-center">
                    <h5>
                        Pricing
                    </h5>
                    <h2>
                        Upgrade Your Chat Services <br>
                        with Our Tailored Pricing Plans!
                    </h2>
                </div>
            </div>
        </div>
    </div>
</section> 


<section class="our-roi"> 
    <img class="roi-bg1" src="{{ asset('front-assets/images/roi-bg1.png') }}" alt="">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="img-roi">
                    <img src="{{ asset('front-assets/images/price-img1.png') }}" alt="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-roi count-box">
                    <h6 class="sub-head">
                        Get ROI Fast
                    </h6>
                    <h2>
                        Automate  <br>
                        Business, Stream <br>
                        More Revenue
                    </h2>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore aliqua.
                    </p>
                    <ul>
                        <li>
                            <span>82%</span>
                            <small>
                                Operating Cost Eficiency
                            </small>
                        </li>
                        <li>
                            <span>353+</span>
                            <small>
                                Business Trust Us
                            </small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- <section class="our-pricing">
    <div class="container">
        <div class="heading text-center">
            <h2>
                Choose Affordable Prices
            </h2>
            <p>
                Each package ensure your <b>ChatDesk360</b> <br>
                scales with you.
            </p>
        </div>
        <div class="row">
            <div class="col-md-10">
                <div class="row row-in">
                    <div class="col-md-3">
                        <div class="info-price">
                            <h3>
                                Starter
                            </h3>
                            <p>
                                Small business
                            </p>
                            <h4>
                                <sup>$</sup> 19 <span>/mo</span>
                            </h4>
                            <p>
                                per person billed annually
                            </p>
                            <h6>
                                Key features:
                            </h6>
                            <ul class="mCustomScrollbar">
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Text Intelligence
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Track up to 100 visitors
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    1 recurring campaign
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    60-day chat history
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Basic widget customization
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    24/7/365 Support
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    1 user
                                </li>
                            </ul>
                            <a class="btn btn-trans various" href="javascript:;" data-fancybox data-src="#popupform">
                                Contact Sales
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-price">
                            <h3>
                                Team
                            </h3>
                            <p>
                                Small business
                            </p>
                            <h4>
                                <sup>$</sup> 19 <span>/mo</span>
                            </h4>
                            <p>
                                per person billed annually
                            </p>
                            <h6>
                                Key features:
                            </h6>
                            <ul class="mCustomScrollbar">
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Text Intelligence
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Track up to 100 visitors
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    1 recurring campaign
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    60-day chat history
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Basic widget customization
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    24/7/365 Support
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    1 user
                                </li>
                            </ul>
                            <a class="btn btn-trans various" href="javascript:;" data-fancybox data-src="#popupform">
                                Contact Sales
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-price mvp">
                            <h5>
                                Most Popular
                            </h5>
                            <h3>
                                Business
                            </h3>
                            <p>
                                Small business
                            </p>
                            <h4>
                                <sup>$</sup> 19 <span>/mo</span>
                            </h4>
                            <p>
                                per person billed annually
                            </p>
                            <h6>
                                Key features:
                            </h6>
                            <ul class="mCustomScrollbar">
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Text Intelligence
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Track up to 100 visitors
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    1 recurring campaign
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    60-day chat history
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Basic widget customization
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    24/7/365 Support
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    1 user
                                </li>
                            </ul>
                            <a class="btn btn-trans various" href="javascript:;" data-fancybox data-src="#popupform">
                                Contact Sales
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-price">
                            <h3>
                                Enterprise
                            </h3>
                            <p>
                                Small business
                            </p>
                            <h4>
                                <sup>$</sup> 19 <span>/mo</span>
                            </h4>
                            <p>
                                per person billed annually
                            </p>
                            <h6>
                                Key features:
                            </h6>
                            <ul class="mCustomScrollbar">
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Text Intelligence
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Track up to 100 visitors
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    1 recurring campaign
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    60-day chat history
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    Basic widget customization
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    24/7/365 Support
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                    1 user
                                </li>
                            </ul>
                            <a class="btn btn-trans various" href="javascript:;" data-fancybox data-src="#popupform">
                                Contact Sales
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="btn-block">
                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                View All Plans
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}

<section class="our-pricing">
    <div class="container">
        <div class="heading text-center">
            <h2>
                Choose Affordable Prices
            </h2>
            <p>
                Each package ensure your <b>ChatDesk360</b> <br>
                scales with you.
            </p>
        </div>
        <div class="row">
            <div class="col-md-10">
                <div class="row row-in">
                    
                    <!-- 🔹 DYNAMIC PRICING LOOP -->
                    @foreach($packages as $package)
                        <div class="col">
                            <!-- Add 'mvp' class if it's the recommended package -->
                            <div class="info-price {{ $package->recommended ? 'mvp' : '' }}">
                                
                                @if($package->recommended)
                                    <h5>Most Popular</h5>
                                @endif

                                <h3>{{ $package->title }}</h3>
                                <p>{{ $package->description }}</p>
                                
                                <h4>
                                    {{ $package->formatted_price }} <span>/mo</span>
                                </h4>
                                
                                <p>
                                    {{ $package->per_agent ? 'per person billed annually' : 'billed annually' }}
                                </p>
                                
                                <h6>Key features:</h6>
                                <ul class="mCustomScrollbar">
                                    @foreach($package->feature_list as $feature)
                                        <li>
                                            <img src="{{ asset('front-assets/images/tick3.png') }}" alt="">
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                                
                                @php
                                    $queryParams = http_build_query([
                                        'source_id' => 'plan_' . $package->slug . '_button',
                                        'source_type' => 'pricing_page',
                                        'package' => $package->slug,
                                        'redirect_uri' => url('/admin/dashboard'),
                                        'source_url' => url()->current()
                                    ]);
                                @endphp

                                <a href="{{ route('register') }}?{{ $queryParams }}" class="btn btn-trans various">
                                    Choose Plan
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach

                    {{-- <div class="col-md-12">
                        <div class="btn-block">
                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                View All Plans
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</section>


<section class="price-cta">
    <img class="price-bg1" src="{{ asset('front-assets/images/price-bg1.png') }}" alt="">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="info-pricecta">
                    <h2>
                        Ready to Supercharge <br>
                        Your Customer <br>
                        Experience?
                    </h2>
                    <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                        Sign up now
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-5">
                <div class="box-pricecta">
                    <div class="info-price">
                        <p>
                            <b>ChatDesk360</b> starts at
                        </p>
                        <h4>
                            <sup>$</sup>79<span>/mo</span>
                        </h4>
                        <p>
                            billed annually
                        </p>
                    </div>
                    <img class="price-img2" src="{{ asset('front-assets/images/price-img2.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

@include('front-includes.lg-counter')
@include('front-includes.faq')



@endsection

