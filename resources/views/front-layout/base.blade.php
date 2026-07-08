<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seoTitle ?? config('app.name', 'ChatDesk360') }}</title>
    <meta name="description" content="{{ $seoDescription ?? 'Default description for your live chat application.' }}">
    <meta name="keywords" content="{{ $seoKeywords ?? 'live chat, customer support, chatbot' }}">
    
    <!-- 🔹 Open Graph / Social Media Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seoTitle ?? config('app.name') }}">
    <meta property="og:description" content="{{ $seoDescription ?? '' }}">
    {{-- <meta property="og:image" content="{{ $seoImage ?? asset('assets/front/images/og-default.jpg') }}"> --}}
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="icon" type="image/png" href="{{ asset('front-assets/images/fav/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('front-assets/images/fav/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('front-assets/images/fav/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('front-assets/images/fav/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="ChatDesk" />
    <link rel="manifest" href="{{ asset('front-assets/images/fav/site.webmanifest') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@100..800&display=swap" rel="stylesheet">
    <!-- Include Styles -->
    @include('front-includes.styles')  <!-- Pulls in <link> tags -->
</head>
<body>
    
    @yield('body')
    
    <!-- Include Scripts -->
    @include('front-includes.scripts') <!-- Pulls in <script> tags -->
    
    {{-- @stack('scripts') --}}
</body>
</html>