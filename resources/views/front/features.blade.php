@extends('front-layout.app')

@section('title', ' - Home')

@section('content')

<section class="our-banner inner-banner product-banner feat-banner">
    <div class="container"> 
        <div class="row">
            <div class="col-md-12">
                <div class="info-banner text-center">
                    <h5>
                        Our Features
                    </h5>
                    <h2>
                        ChatDesk360 <br> features
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
                    <img class="feat-arr2" src="{{ asset('front-assets/images/feat-arr2.png') }}" alt="">
                </div>
            </div>
        </div>
        <img class="feat-bg1" src="{{ asset('front-assets/images/feat-bg1.png') }}" alt="">
    </div>
</section> 

<section class="our-feature">
	<div class="container">
		<div class="heading text-center">
			<h6 class="sub-head">
                Features
            </h6>
			<h2>
                It's all about making <br>
				your business goals a reality.
            </h2>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod <br> 
                tempor incididunt ut labore et dolore aliqua.
            </p>
		</div>
		<div class="row row-feat">
			<div class="col-md-3">
				<div class="feat-list">
					<h4>
						Chat tools
					</h4>
					<ul>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Message sneak-peek
						</li>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Canned responses
						</li>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							File sharing
						</li>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Chat archives
						</li>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Chat transfer
						</li>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Chat ratings
						</li>
					</ul>
				</div>
			</div>
			<div class="col-md-5">
				<div class="feat-list">
					<h4>
						Reports & analytics
					</h4>
					<ul>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Data summary
						</li>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Chat reports (Total chats, Missed chats, Greetings conversion, Chat satisfaction, Chat engagement, Chat surveys)
						</li>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Customer reports (customer ip, customer country, total chats, customer satisfaction)
						</li>
					</ul>
				</div>
			</div>
			<div class="col-md-4">
				<div class="feat-list">
					<h4>
						Customer engagement
					</h4>
					<ul>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Eye-catchers
						</li>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Chat history
						</li>
						<li>
							<img src="{{ asset('front-assets/images/tick1.png') }}" alt="">
							Chat transcripts
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="img-feat">
					<img src="{{ asset('front-assets/images/feat-bg3.png') }}" alt="">
					<img class="feat-bg2" src="{{ asset('front-assets/images/feat-bg2.png') }}" alt="">
				</div>
			</div>
		</div>
	</div>
</section>


@include('front-includes.lg-counter')
@include('front-includes.faq')
@include('front-includes.prefoot')

@endsection