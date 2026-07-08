@extends('layouts.app')
@section('title', 'Subscription')
@section('content')
<div class="body-wrapper mb-0 pg-subscription">
    <div class="container-fluid">
        @auth
            @if(auth()->user()->subscribed('default'))
                <x-breadcrumb title="Subscription" />
            @endif
        @endauth
        
        <div class="row mt-4">
            <div class="col-12">
                <h3 class="mb-4">Choose your plan</h3>
                
                {{-- 🔹 Check if user already has an active subscription --}}
                @php
                    $isSubscribed = auth()->check() && auth()->user()->subscribed('default');
                @endphp
                
                <div class="row justify-content-center pricing-plan">
                    {{-- 🔹 SORT PACKAGES BY PRICE (LOW TO HIGH) --}}
                    @foreach($packages->sortBy('price') as $package)
                    
                        {{-- 🔹 Check if this is the user's current package --}}
                        @php
                            $isCurrentPackage = ($isSubscribed && $package->id == $currentPackageId);
                        @endphp
                        
                        <div class="col-lg-4 col-md-6 plan-list {{ $package->recommended && !$isCurrentPackage ? 'recommended-choice' : '' }}">
                            
                            {{-- 🔹 Card styling: Agar current package hai toh opacity-50 lagao --}}
                            <div class="card h-100 {{ $isCurrentPackage ? ' position-relative' : '' }}">
                                
                                {{-- 🔹 Badges: Current Package ya Recommended --}}
                                @if($isCurrentPackage)
                                    <span class="recommended-badge bg-success text-white">Current Package</span>
                                @elseif($package->recommended)
                                    <span class="recommended-badge">Recommended</span>
                                @endif
                                
                                <div class="card-body">
                                    <h4 class="card-title">{{ $package->title }}</h4>
                                    <p class="card-text">{{ $package->description }}</p>
                                    <h1 class="">{{ $package->formatted_price }} <small class="">per agent / mo</small></h1>
                                    
                                    {{-- 🔹 DYNAMIC BUTTON LOGIC --}}
                                    @if($isCurrentPackage)
                                        {{-- Agar yeh current package hai --}}
                                        <button class="btn btn-success w-100" disabled>
                                            <i class="ti ti-check me-1"></i> Current plan
                                        </button>
                                    @else
                                        {{-- Agar user naya hai --}}
                                        <a href="{{ route('subscription.manage.index', $package->id) }}" class="btn btn-primary w-100">
                                            Choose plan
                                        </a>
                                    @endif
                                    {{-- 🔹 LOGIC END --}}

                                    <div class="feature-list mt-3">
                                        <h6 class="">Key features:</h6>
                                        <ul class="list-unstyled subscription-features">
                                            @foreach($package->feature_list as $feature)
                                            <li><i class="ti ti-check"></i> <b>{!! $feature !!}</b></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection