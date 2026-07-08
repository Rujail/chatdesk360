@extends('front-layout.app')

@section('title', ' - Home')

@section('content')
<section class="our-banner inner-banner">
    <div class="container"> 
        <div class="row">
            <div class="col-md-12">
                <div class="info-banner text-center">
                    <h5>
                        Who we are
                    </h5>
                    <h2>
                        Best Product, Best Team
                    </h2>
                    <p>
                        Sollicitudin aliquam posuere urna parturient pretium sed sodales. Suscipit lacinia commodo odio phasellus nibh aptent mi et est ex. Vulputate elit torquent eros cubilia per inceptos ad elementum rhoncus.
                    </p>
                    <img src="{{ asset('front-assets/images/abt-baner.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</section> 


<section class="our-vision">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="vision-info">
                    <img src="{{ asset('front-assets/images/mission.png') }}" alt="">
                    <h4>
                        Our Mission
                    </h4>
                    <p>
                        Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.   
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="vision-info">
                    <img src="{{ asset('front-assets/images/version.png') }}" alt="">
                    <h4>
                        Our Vision
                    </h4>
                    <p>
                        Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.   
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="vision-info">
                    <img src="{{ asset('front-assets/images/apro.png') }}" alt="">
                    <h4>
                        Our Approch
                    </h4>
                    <p>
                        Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.   
                    </p>
                </div>
            </div>
        </div>
        <div class="our-about">
            <div class="heading text-center">
                <h6 class="sub-head">
                    About chatdesk360
                </h6>
                <h2>
                    Increase Productivity,<br>
                    Growth More
                </h2>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                </p>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <div class="info-business">
                        <ul>
                            <li>
                                <b> 201+ </b>
                                <span>
                                    <i>Business Trust Us</i>
                                    <small>
                                        Trusted by various business in the world.
                                    </small>
                                </span>
                            </li>
                            <li>
                                <b> 68% </b>
                                <span>
                                    <i>Operational Cost Efficiency</i>
                                    <small>
                                        No need hiring a lot of customer service.
                                    </small>
                                </span>
                            </li>
                            <li>
                                <b> 99% </b>
                                <span>
                                    <i>Uptime Guarantee</i>
                                    <small>
                                        High quality service and always available.
                                    </small>
                                </span>
                            </li>
                            <li>
                                <b> 2M+ </b>
                                <span>
                                    <i>Chats Replied</i>
                                    <small>
                                        Automatically served by HAIchat seemlessly
                                    </small>
                                </span>
                            </li>
                        </ul>
                        <div class="btn-block text-center">
                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                Get Started Now
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
             </div>
        </div>
        <img class="aiagent" src="{{ asset('front-assets/images/aiage2.png') }}" alt="">
        <img class="crclshade" src="{{ asset('front-assets/images/crclshade.png') }}" alt="">
    </div>
</section>

<section class="our-value">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
               <div class="img-value">
                   <img src="{{ asset('front-assets/images/value-img.png') }}" alt="">
                   <figcaption>
                       <h4>
                            21M+
                       </h4>
                       <p>
                            Businesses <br>
                            Assisted
                       </p>
                   </figcaption>
               </div>
            </div>
            <div class="col-md-6">
               <div class="info-value">
                    <h6 class="sub-head ">
                        Our Value
                    </h6>
                    <h2>
                        A right place for the right solution
                    </h2>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore.
                    </p>
                    <ul>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Personalized interactions
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Chatbot Integration
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Scalable support
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Mobile Marketing
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Automated service
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Machine Learning
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Instant responses
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Build your work
                        </li>
                    </ul>
                    <div class="btn-block">
                        <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                            Get Started Now
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('front-includes.prefoot')
@include('front-includes.faq')

@endsection

