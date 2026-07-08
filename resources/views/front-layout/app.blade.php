@extends('front-layout.base')

@section('body')
        
    <!-- Include Header -->
    @include('front-includes.header')

    <main>
        @yield('content')
    </main>

    <!-- Include Footer -->
    @include('front-includes.footer')

@endsection