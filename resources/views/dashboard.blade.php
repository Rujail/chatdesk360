@extends('layouts.app')

@section('title', 'ChatDesk')

@section('header')
    <div class="bg-light py-4 border-bottom">
        <div class="container">
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Your Role: {{ auth()->user()->role }}</h5>
                        <p class="card-text">
                            @if(auth()->user()->isAdmin())
                                Admin controls here...
                            @elseif(auth()->user()->isAgent())
                                Agent dashboard...
                            @else
                                Basic user view...
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar ya extra column -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Quick Links</h5>
                        <!-- links -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection