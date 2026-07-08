<svg style="visibility: hidden; position: absolute;" width="0" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1">
  <defs>
        <filter id="round-svg">
            <feGaussianBlur in="SourceGraphic" stdDeviation="8" result="blur" />    
            <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo" />
            <feComposite in="SourceGraphic" in2="goo" operator="atop"/>
        </filter>
    </defs>
</svg>

<svg style="visibility: hidden; position: absolute;" width="0" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1">
  <defs>
        <filter id="round-svg2">
            <feGaussianBlur in="SourceGraphic" stdDeviation="15" result="blur" />    
            <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo" />
            <feComposite in="SourceGraphic" in2="goo" operator="atop"/>
        </filter>
    </defs>
</svg>

<div class="top-bar">
    <div class="container">
        <p>
            Free 14-day trial - No credit card required  -  <a href="javascript:;">Try it now <i class="fa-solid fa-arrow-right"></i> </a>
        </p>
    </div>
</div>
<header>
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <a href="{{ route('front.home') }}" class="logo">
                    <img src="{{ asset('front-assets/images/logo.png') }}" alt="">
                </a>
            </div>
            <div class="col-md-10">
                <div class="menu-box">
                    <nav class="navbar-expand-md main-menu text-center"> 
                        <ul class="menu">
                            <li>
                                <a href="{{ route('front.about') }}"> About us </a>
                            </li>
                            <li>
                                <a href="">Services</a>
                            <li>
                                <a href="">Resources</a>
                            </li>
                            <li>
                                <a href="{{ route('front.pricing') }}">Pricing</a>
                            </li>
                            <li>
                                <a href="{{ route('front.contact') }}">Contact</a>
                            </li>
                        </ul>
                    </nav>
                    <a href="javascript:" class="menu-bottom">
                        <span></span>
                        <span></span>
                        <span></span>
                    </a>
                    <div class="head-btn text-end">
                        <a class="btn btn-white" href="javascript:;">
                            Login
                        </a>
                        <a class="btn btn-grd" href="javascript:;" data-fancybox data-src="#popupform">
                            Sign Up Now
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</header>