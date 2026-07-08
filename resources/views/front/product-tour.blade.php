@extends('front-layout.app')

@section('title', ' - Home')

@section('content')


<section class="our-banner inner-banner product-banner">
    <div class="container"> 
        <div class="row">
            <div class="col-md-12">
                <div class="info-banner text-center">
                    <h5>
                        ChatDesk360 OVERVIEW - PRODUCT TOUR
                    </h5>
                    <h2>
                        See ChatDesk360 <br>from the inside
                    </h2>
                    <p>
                        Sollicitudin aliquam posuere urna parturient pretium sed sodales. Suscipit lacinia commodo odio phasellus nibh aptent mi et est ex. Vulputate elit torquent eros cubilia per inceptos ad elementum rhoncus.
                    </p>
                    <form action="/order/mail.php" method="post" class="validate-banner">
                        <input type="email" name="em" class="form-control required" required="" placeholder="Enter your business email" aria-required="true">
                        <button type="submit" name="send" class="btn btn-form">
                            Sign Up
                        </button>
                    </form>
                    <ul class="in-banul">
                        <li>
                            <img src="{{ asset('front-assets/images/tick2.png') }}" alt="">
                            Free 14-day trial
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick2.png') }}" alt="">
                            AI-driven features
                        </li>
                        <li>
                            <img src="{{ asset('front-assets/images/tick2.png') }}" alt="">
                            No credit card required
                        </li>
                    </ul>
                    <img class="prod-bg1" src="{{ asset('front-assets/images/prod-bg1.png') }}" alt="">
                </div>
            </div>
        </div>
        <img class="por-img" src="{{ asset('front-assets/images/pro-img.png') }}" alt="">
    </div>
</section> 

<section class="product">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<div class="product-info">
					<h2>
						Chat Widget
					</h2>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt.				
					</p>
					<p>
						Culpa qui officia deserunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.
					</p>
					<div class="btn-block">
                    	<a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                       	 	Get Started Now
                        <i class="fa-solid fa-arrow-right"></i>
                    	</a>
                	</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="product-img">
					<img src="{{ asset('front-assets/images/pro1.png') }}" alt="">
				</div>
			</div>
		</div>
		
		<div class="row">
		    <div class="col-md-6">
				<div class="product-img">
					<img src="{{ asset('front-assets/images/pro2.png') }}" alt="">
				</div>
			</div>
			<div class="col-md-6">
				<div class="product-info">
					<h2>
						Home
					</h2>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt.				
					</p>
					<p>
						Culpa qui officia deserunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.
					</p>
					<div class="btn-block">
                    	<a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                       	 	Get Started Now
                        <i class="fa-solid fa-arrow-right"></i>
                    	</a>
                	</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="product-info">
					<h2>
						Chats
					</h2>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt.				
					</p>
					<p>
						Culpa qui officia deserunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.
					</p>
					<div class="btn-block">
                    	<a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                       	 	Get Started Now
                        <i class="fa-solid fa-arrow-right"></i>
                    	</a>
                	</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="product-img">
					<img src="{{ asset('front-assets/images/pro3.png') }}" alt="">
				</div>
			</div>
		</div>
		
		<div class="row">
		    <div class="col-md-6">
				<div class="product-img">
					<img src="{{ asset('front-assets/images/pro4.png') }}" alt="">
				</div>
			</div>
			<div class="col-md-6">
				<div class="product-info">
					<h2>
						Archives
					</h2>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt.				
					</p>
					<p>
						Culpa qui officia deserunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.
					</p>
					<div class="btn-block">
                    	<a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                       	 	Get Started Now
                        <i class="fa-solid fa-arrow-right"></i>
                    	</a>
                	</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="product-info">
					<h2>
						Team
					</h2>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt.				
					</p>
					<p>
						Culpa qui officia deserunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.
					</p>
					<div class="btn-block">
                    	<a class="btn various" href="javascript:;" data-fancybox data-src="#popupform">
                       	 	Get Started Now
                        <i class="fa-solid fa-arrow-right"></i>
                    	</a>
                	</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="product-img">
					<img src="{{ asset('front-assets/images/pro5.png') }}" alt="">
				</div>
			</div>
		</div>
		
		<div class="row">
		    <div class="col-md-6">
				<div class="product-img">
					<img src="{{ asset('front-assets/images/pro6.png') }}" alt="">
				</div>
			</div>
			<div class="col-md-6">
				<div class="product-info">
					<h2>
						Reports
					</h2>
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt.				
					</p>
					<p>
						Culpa qui officia deserunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.
					</p>
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

@include('front-includes.lg-counter')
@include('front-includes.prefoot')
@include('front-includes.faq')

@endsection

