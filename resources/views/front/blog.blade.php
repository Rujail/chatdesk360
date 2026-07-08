@extends('front-layout.app')

@section('title', ' - Home')

@section('content')


<section class="our-banner inner-banner">
    <div class="container"> 
        <div class="row">
            <div class="col-md-12">
                <div class="info-banner text-center">
                    <h5>
                        Our Blog
                    </h5>
                    <h2>
                        Explore Archive
                    </h2>
                    <p>
                        Sollicitudin aliquam posuere urna parturient pretium sed sodales. Suscipit lacinia commodo odio phasellus nibh aptent mi et est ex. Vulputate elit torquent eros cubilia per inceptos ad elementum rhoncus.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section> 

<section class="our-blog">
    <div class="container">
        <ul class="nav nav-tabs tab-blog" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="blog1-tab" data-bs-toggle="tab" data-bs-target="#blog1" type="button" role="tab" aria-controls="blog1" aria-selected="true">
                    Chatbots
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="blog2-tab" data-bs-toggle="tab" data-bs-target="#blog2" type="button" role="tab" aria-controls="blog2" aria-selected="false">
                    Live Chat
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="blog3-tab" data-bs-toggle="tab" data-bs-target="#blog3" type="button" role="tab" aria-controls="blog3" aria-selected="false">
                    Helpdesk
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="blog4-tab" data-bs-toggle="tab" data-bs-target="#blog4" type="button" role="tab" aria-controls="blog4" aria-selected="false">
                     Customer Service
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="blog5-tab" data-bs-toggle="tab" data-bs-target="#blog5" type="button" role="tab" aria-controls="blog5" aria-selected="false">
                     Technology
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="blog6-tab" data-bs-toggle="tab" data-bs-target="#blog6" type="button" role="tab" aria-controls="blog6" aria-selected="false">
                     Apps and Integrations
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="blog7-tab" data-bs-toggle="tab" data-bs-target="#blog7" type="button" role="tab" aria-controls="blog7" aria-selected="false">
                     Marketing
                </button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade active  show " id="blog1" role="tabpanel" aria-labelledby="blog1-tab">
                <div class="row row-blog">
                    <div class="col-md-12">
                        <h3>
                            Most popular
                        </h3>
                    </div>
                    <div class="col-md-8">
                        <div class="main-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog-hero1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <h4>
                                    <a href="#">How to Create a Chatbot for a Website in Minutes</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-blogs">
                            <ul>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3>
                            Latest articles
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog2.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog3.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog4.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog5.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog6.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="btn-block text-center">

                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                <img src="{{ asset('front-assets/images/load.png') }}" alt="">
                                Load More Blogs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="blog2" role="tabpanel" aria-labelledby="blog2-tab">
                <div class="row row-blog">
                    <div class="col-md-12">
                        <h3>
                            Most popular
                        </h3>
                    </div>
                    <div class="col-md-8">
                        <div class="main-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog-hero1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <h4>
                                    <a href="#">How to Create a Chatbot for a Website in Minutes</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-blogs">
                            <ul>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3>
                            Latest articles
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog2.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog3.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog4.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog5.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog6.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="btn-block text-center">

                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                <img src="{{ asset('front-assets/images/load.png') }}" alt="">
                                Load More Blogs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="blog3" role="tabpanel" aria-labelledby="blog3-tab">
                <div class="row row-blog">
                    <div class="col-md-12">
                        <h3>
                            Most popular
                        </h3>
                    </div>
                    <div class="col-md-8">
                        <div class="main-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog-hero1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <h4>
                                    <a href="#">How to Create a Chatbot for a Website in Minutes</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-blogs">
                            <ul>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3>
                            Latest articles
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog2.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog3.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog4.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog5.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog6.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="btn-block text-center">

                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                <img src="{{ asset('front-assets/images/load.png') }}" alt="">
                                Load More Blogs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="blog4" role="tabpanel" aria-labelledby="blog4-tab">
                <div class="row row-blog">
                    <div class="col-md-12">
                        <h3>
                            Most popular
                        </h3>
                    </div>
                    <div class="col-md-8">
                        <div class="main-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog-hero1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <h4>
                                    <a href="#">How to Create a Chatbot for a Website in Minutes</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-blogs">
                            <ul>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3>
                            Latest articles
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog2.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog3.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog4.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog5.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog6.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="btn-block text-center">

                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                <img src="{{ asset('front-assets/images/load.png') }}" alt="">
                                Load More Blogs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="blog5" role="tabpanel" aria-labelledby="blog5-tab">
                <div class="row row-blog">
                    <div class="col-md-12">
                        <h3>
                            Most popular
                        </h3>
                    </div>
                    <div class="col-md-8">
                        <div class="main-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog-hero1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <h4>
                                    <a href="#">How to Create a Chatbot for a Website in Minutes</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-blogs">
                            <ul>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3>
                            Latest articles
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog2.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog3.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog4.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog5.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog6.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="btn-block text-center">

                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                <img src="{{ asset('front-assets/images/load.png') }}" alt="">
                                Load More Blogs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="blog6" role="tabpanel" aria-labelledby="blog6-tab">
                <div class="row row-blog">
                    <div class="col-md-12">
                        <h3>
                            Most popular
                        </h3>
                    </div>
                    <div class="col-md-8">
                        <div class="main-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog-hero1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <h4>
                                    <a href="#">How to Create a Chatbot for a Website in Minutes</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-blogs">
                            <ul>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3>
                            Latest articles
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog2.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog3.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog4.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog5.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog6.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="btn-block text-center">

                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                <img src="{{ asset('front-assets/images/load.png') }}" alt="">
                                Load More Blogs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="blog7" role="tabpanel" aria-labelledby="blog7-tab">
                <div class="row row-blog">
                    <div class="col-md-12">
                        <h3>
                            Most popular
                        </h3>
                    </div>
                    <div class="col-md-8">
                        <div class="main-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog-hero1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <h4>
                                    <a href="#">How to Create a Chatbot for a Website in Minutes</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-blogs">
                            <ul>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                                <li>
                                    <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                                    <span>
                                        <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3>
                            Latest articles
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog1.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog2.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog3.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog4.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog5.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-blog">
                            <figure>
                                <img src="{{ asset('front-assets/images/blog6.png') }}" alt="">
                            </figure>
                            <figcaption>
                                <ul>
                                    <li>
                                        February 30, 2026
                                    </li>
                                    <li>
                                        No Comments
                                    </li>
                                </ul>
                                <h4>
                                    <a href="#">The Future of Customer Communication: Trends to Watch</a>
                                </h4>
                                <p>
                                    Lorem ipsum dolor sit amet, consetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.
                                </p>
                            </figcaption>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="btn-block text-center">

                            <a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                                <img src="{{ asset('front-assets/images/load.png') }}" alt="">
                                Load More Blogs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('front-includes.testimonials')
@include('front-includes.prefoot')
@include('front-includes.faq')


@endsection

