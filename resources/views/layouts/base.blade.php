<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" data-bs-theme="light" data-color-theme="Orange_Theme" data-layout="vertical">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon-->
    <link rel="icon" type="image/png" href="{{ asset('front-assets/images/fav/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('front-assets/images/fav/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('front-assets/images/fav/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('front-assets/images/fav/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="ChatDesk360" />
    <link rel="manifest" href="{{ asset('front-assets/images/fav/site.webmanifest') }}" />

    <title>@yield('title', config('app.name', 'Chatdesk360'))</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.35.0/tabler-icons.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/lib.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/web-styles.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chat.css') }}?v={{ time() }}" />
    
    @stack('styles')
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="{{ asset('front-assets/images/fav/favicon.svg') }}" alt="loader" class="lds-ripple img-fluid" />
    </div>
    
    @yield('header')        <!-- page heading / hero section -->

    @yield('content')

    @yield('footer')        <!-- footer -->
    
    <script src="{{ asset('assets/js/vendor.min.js')}}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/app.init.js')}}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/app.min.js')}}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/sidebarmenu.js')}}?v={{ time() }}"></script>
    
    {{-- <script src="{{ asset('assets/js/chat.js')}}?v={{ time() }}"></script> --}}
    @if(request()->routeIs(['chat.index', 'chats.archive']))
        <script src="{{ asset('assets/js/chat.js') }}?v={{ time() }}"></script>
    @endif
    <script src="{{ asset('assets/js/custom.js')}}?v={{ time() }}   "></script>
    
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    @stack('scripts')
    <!-- Live Chat Widget -->
    {{-- <script src="{{ asset('js/live-chat-widget.js') }}"></script> --}}

    {{-- <script src="{{ asset('js/chat-loader.js') }}"></script> --}}
    @if (!request()->routeIs('settings.widget.index'))
    {{-- <script>
        window.__cd=window.__cd||{};
        window.__cd.site_id="site_ZXTth2HlCT";
        (function(d,w){
            var s=d.createElement('script');
            s.async=!0;
            s.type='text/javascript';
            s.src='https://chatdesk360.com/livechat/loader.js?var=asd';
            d.head.appendChild(s);
        }(document,window));
    </script> --}}
    @endif
</body>
</html>