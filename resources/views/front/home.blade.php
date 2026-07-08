@extends('front-layout.app')

@section('title', ' - Home')

@section('content')
<section class="our-banner">
    <div class="container">       
        <div class="row">
            <div class="col-md-6">
                <div class="info-banner">
                    <h1> 
                        The <span>live chat</span>
                        software that gets 
                        the job done
                    </h1>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.
                    </p>
                    <ul>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">  
                            Increase online sales
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">  
                            Improve customer satisfaction
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">  
                            Automate customer service
                        </li>
                    </ul>
                    <form action="/order/mail.php" method="post" class="validate-banner">
                        <input type="email" name="em" class="form-control required" required="" placeholder="Enter your business email" aria-required="true">
                        <button type="submit" name="send" class="btn btn-form">
                            Sign Up
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-md-5 offset-md-1">
                <div class="move-vectors">
                    <div class="bundle-img">
                        <div id="scene">
                            <div class="layer" data-depth="0.1"><img src="{{ asset('front-assets/images/vec1.png') }}" alt=""></div>
                            <div class="layer" data-depth="0.4"><img src="{{ asset('front-assets/images/vec2.png') }}" alt=""></div>
                            <div class="layer" data-depth="0.5"><img src="{{ asset('front-assets/images/vec3.png') }}" alt=""></div>
                            <div class="layer" data-depth="0.7"><img src="{{ asset('front-assets/images/vec4.png') }}" alt=""></div>
                            <div class="layer" data-depth="0.2"><img src="{{ asset('front-assets/images/vec5.png') }}" alt=""></div>
                            <div class="layer" data-depth="0.2"><img src="{{ asset('front-assets/images/vec6.png') }}" alt=""></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 
@include('front-includes.site-logo')

<section class="our-aiagent">
    <img class="meet-bg" src="{{ asset('front-assets/images/meet-bg1.png') }}" alt="">
    <div class="container">
        <div class="heading text-center">
            <h2>
                An AI agent that sells like <br>
                your best rep
            </h2>
            <p>
                Resolving doubts and recommending relevant products in real time, the AI agent knows your products and policies and helps shoppers complete their purchase with ease.
            </p>
            <ul>
                <li>
                    <img src="{{ asset('front-assets/images/aiagent-ic1.png') }}" alt="">
                    Best Quality Chat
                </li>
                <li>
                    <img src="{{ asset('front-assets/images/aiagent-ic2.png') }}" alt="">
                    Social Media Posts
                </li>
                <li>
                    <img src="{{ asset('front-assets/images/aiagent-ic3.png') }}" alt="">
                    Pricing Tables
                </li>
                <li>
                    <img src="{{ asset('front-assets/images/aiagent-ic4.png') }}" alt="">
                    Train Chatbot
                </li>
                <li>
                    <img src="{{ asset('front-assets/images/aiagent-ic5.png') }}" alt="">
                    Manage Dataset
                </li>
                <li>
                    <img src="{{ asset('front-assets/images/aiagent-ic6.png') }}" alt="">
                    Tag suggestions
                </li>
                <li>
                    <img src="{{ asset('front-assets/images/aiagent-ic7.png') }}" alt="">
                    Chat summary
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-10">
                <div class="img-aiagent">
                    <img src="{{ asset('front-assets/images/aiagent-img1.png') }}" alt="">
                    <img class="aiagent-bg1" src="{{ asset('front-assets/images/aiagent-bg1.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

@include('front-includes.cta')

<section class="our-sales">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="info-sale">
                    <h6 class="sub-head">
                        INCREASE ONLINE SALES
                    </h6>
                    <h2>
                        Engage with live chat, sell with ease
                    </h2>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.
                    </p>
                </div>
            </div>
            <div class="col-md-4 offset-md-2">
                <div class="info-sale">
                    <ul>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Increase conversion rates by 25-40%
                        </li>         
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Reduce training time by 60%
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Improve customer satisfaction scores
                        </li>         
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Automate call quality assurance
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Identify top performer strategies
                        </li>                 
                        <li>
                            <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                            Ensure compliance automatically
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                <div class="img-sales">
                    <img src="{{ asset('front-assets/images/sale-img.png') }}" alt="">
                    <figcaption>
                        <span class="chat-icon">
                            <img src="{{ asset('front-assets/images/chat-ic.png') }}" alt="">
                        </span>
                        <p>
                            Hi joily! You can return your purchase <br>
                            within 7 days.
                        </p>
                    </figcaption>
                </div>
                <div class="btn-block text-center">
                    <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                        Efficient support features
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<main class="bg-purp">
    
    <section class="our-offer">
        <div class="container">
            <div class="heading text-center">
                <h6 class="sub-head">
                    AUTOMATE CUSTOMER SERVICE
                </h6>
                <h2>
                    We Offer <span>ChatDesk 360</span> Services <br>
                    for Any Industry
                </h2>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="info-offer">
                        <img src="{{ asset('front-assets/images/offer1.png') }}" alt="">
                        <h4>
                            E-commerce Integration
                        </h4>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur dipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua Ut enim.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-offer">
                        <img src="{{ asset('front-assets/images/offer2.png') }}" alt="">
                        <h4>
                            Customer Support
                        </h4>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur dipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua Ut enim.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-offer">
                        <img src="{{ asset('front-assets/images/offer3.png') }}" alt="">
                        <h4>
                            Lead Generation
                        </h4>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur dipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua Ut enim.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="our-customer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-customer">
                        <h6 class="sub-head">
                            IMPROVE CUSTOMER SATISFACTION
                        </h6>
                        <h2>
                            Make premium <br>
                            support your new <br>
                            standard
                        </h2>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do iusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi.
                        </p>
                        <ul>
                            <li>
                                <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                                Actionable Insights
                            </li>
                            <li>
                                <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                                Enhanced Decision-Making
                            </li>
                            <li>
                                <img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
                                Improved Efficiency
                            </li>
                        </ul>
                        @include('front-includes.main-btn')
                    </div>
                </div>
                <div class="col-md-6">
                   <!--  <div class="img-cust">
                        <img src="{{ asset('front-assets/images/cust-img.png') }}" alt="">
                    </div> -->
                    <div class="move-vectors cust-vector">
                        <div class="bundle-img">
                            <div id="scene2">
                                <div class="layer" data-depth="0.1"><img src="{{ asset('front-assets/images/cust1.png') }}" alt=""></div>
                                <div class="layer" data-depth="0.6"><img src="{{ asset('front-assets/images/cust-2.png') }}" alt=""></div>
                                <div class="layer" data-depth="0.3"><img src="{{ asset('front-assets/images/cust3.png') }}" alt=""></div>
                                <div class="layer" data-depth="0.7"><img src="{{ asset('front-assets/images/cust4.png') }}" alt=""></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <img class="cust-bg-1" src="{{ asset('front-assets/images/cust-bg-1.png') }}" alt="">       
        </div>
    </section>

    <section class="our-tool">
        <div class="container">
            <img class="tool-bg1" src="{{ asset('front-assets/images/tool-bg1.png') }}" alt="">
            <div class="heading text-center">
                <h6 class="sub-head">
                    plug-and-play integrations
                </h6>
                <h2>
                    Connect with tools that <br>
                    support your business growth
                </h2>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="img-tl1">
                        <img src="{{ asset('front-assets/images/tl1.png') }}" alt="">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="img-tl2">
                        <img src="{{ asset('front-assets/images/tl2.png') }}" alt="">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="img-tl3">
                        <img src="{{ asset('front-assets/images/tl3.png') }}" alt="">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="info-tool">
                        <h3>
                            200+
                        </h3>
                        <p>
                            Mix and match from <b>200+ integrations</b> like WordPress, Squarespace, and Mailchimp to create a tailored solution. The tools manage the details, simplify your tasks, and operate seamlessly in the background.
                        </p>
                        <div class="btn-block text-center">
                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                Efficient support features
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<section class="our-chatcta">
    <div class="container">
        <div class="bg-chatcta"></div>
        <div class="row">
            <div class="col-md-6 offset-md-6">
                <div class="info-chatcta">
                    <h2>
                        <b>ChatDesk 360</b> <br>
                        completes your <br>
                        customer shopping <br>
                        experience
                    </h2>
                    <p>
                        See how our top ecommerce integrations can impact your sales and enhance customer experience.
                    </p>
                    <ul>
                        <li>
                            <img src="{{ asset('front-assets/images/chat-lg1.png') }}" alt="">
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/chat-lg2.png') }}" alt="">
                        </li>
                    </ul>
                    @include('front-includes.banner-ulbtn')
                    <img class="chat-bg1" src="{{ asset('front-assets/images/chat-bg1.png') }}" alt="">
                    <img class="chat-bg2" src="{{ asset('front-assets/images/chat-bg2.png') }}" alt="">
                </div>
            </div>
        </div>
        
    </div>
</section>


<section class="steps">
    <div class="container">
        <div class="heading text-center">
            <h6 class="sub-head ">
                How it Works
            </h6>
            <h2>
                Test and <span>implement</span> with <br>
                speed and confidence
            </h2>
            <p>
                AI agent learns directly from your existing knowledge sources. Your <br>
                team can test it, and benefit from it immediately.
            </p>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="stp-info">
                    <img src="{{ asset('front-assets/images/stparow.png') }}" alt="">
                    <h5>
                        STEP 01
                    </h5>
                    <h4>
                        Sign Up
                    </h4>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor inc ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis.
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stp-info">
                    <img src="{{ asset('front-assets/images/stparow.png') }}" alt="">
                    <h5>
                        STEP 02
                    </h5>
                    <h4>
                        Customize Your Chatbot
                    </h4>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor inc ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis.
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stp-info">
                    <img src="{{ asset('front-assets/images/stparow.png') }}" alt="">
                    <h5>
                        STEP 03
                    </h5>
                    <h4>
                       Train Chatbot
                    </h4>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor inc ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis.
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stp-info">
                    <img src="{{ asset('front-assets/images/stparow.png') }}" alt="">
                    <h5>
                        STEP 04
                    </h5>
                    <h4>
                        Deploy and Engage
                    </h4>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor inc ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis.
                    </p>
                </div>
            </div>
            <div class="col-md-12">
                @include('front-includes.main-btn')
            </div>
        </div>
    </div>
</section>

@include('front-includes.counter')


<section class="our-meet">
    <img class="meet-bg" src="{{ asset('front-assets/images/meet-bg1.png') }}" alt="">
    <div class="container">
        <div class="meet-bg"></div>
        <div class="heading text-center">
            <h6 class="sub-head ">
                multichannel marketing strategy
            </h6>
            <h2>
                Meet your new intelligent <br>
                AI-assistant
            </h2>
            <p>
                AI agent learns directly from your existing knowledge sources. Your <br>
                team can test it, and benefit from it immediately.
            </p>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="info-meet">
                    <img class="img-meet1" src="{{ asset('front-assets/images/meet1.png') }}" alt="">
                    <h4>
                        Monitor how live chat impacts your business
                    </h4>
                    <p>
                        chatbot TheAI-driven chatbot in the past allowing you to focus more on your business or simply leaving pen-and-paper.
                    </p>
                    <a class="btn btn-grd various" href="javascript:;" data-fancybox data-src="#popupform">
                        Get Start
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="info-meet meet2">
                    <img class="img-meet2" src="{{ asset('front-assets/images/meet2.png') }}" alt="">
                    <h4>
                        Monitor how live chat impacts your business
                    </h4>
                    <div class="in-meet">
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labor.
                        </p>
                        <a class="btn btn-grd various" href="javascript:;" data-fancybox data-src="#popupform">
                            Get Start
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-meet meet3">
                    <img class="img-meet3" src="{{ asset('front-assets/images/meet3.png') }}" alt="">
                    <h4>
                        Boost your team’s performance 
                        with Copilot inside the app
                    </h4>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim.
                    </p>
                </div>
                <div class="info-meet meet4">
                    <img class="img-meet4" src="{{ asset('front-assets/images/meet4.png') }}" alt="">
                    <h4>
                        Automate support and sales with AI customer service chatbots
                    </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="btn-block text-center">
                    <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                        Automate ChatDesk 360 with ChatBot
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>


<section class="ready-cta">
    <div class="container">
        <img class="ready-bg" src="{{ asset('front-assets/images/ready-bg.png') }}" alt="">
        <div class="info-readycta info-banner">
            <h2>
                Ready to <span>increase sales,</span> provide a <br>
                premium experience, and enjoy <br>
                <b><img src="{{ asset('front-assets/images/headimg.png') }}" alt=""></b> 
                seamless automation?
            </h2>
            <form action="/order/mail.php" method="post" class="validate-footform">
                <input type="email" name="em" class="form-control required" required="" placeholder="Enter your business email" aria-required="true">
                <button type="submit" name="send" class="btn btn-form">
                    Sign Up
                </button>
            </form>
            <img  class="ready-act"  src="{{ asset('front-assets/images/active.png') }}" alt="">
        </div>
    </div>
</section>


@include('front-includes.testimonials')
@include('front-includes.prefoot')
@include('front-includes.faq')

@endsection

@push('scripts')
    <script>
        var scene = document.getElementById('scene');
        var parallax = new Parallax(scene);
        var scene2 = document.getElementById('scene2');
        var parallax = new Parallax(scene2)
    </script>
@endpush