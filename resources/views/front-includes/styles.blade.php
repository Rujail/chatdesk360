<link rel="stylesheet" href="{{ asset('front-assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('front-assets/css/style-web.css') }}?v={{ filemtime(public_path('front-assets/css/style-web.css')) }}">
<link rel="stylesheet" href="{{ asset('front-assets/css/responsive.css') }}?v={{ filemtime(public_path('front-assets/css/responsive.css')) }}">

@stack('styles')