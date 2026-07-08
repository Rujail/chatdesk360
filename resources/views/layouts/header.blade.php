
<div id="main-wrapper">

    <!-- Sidebar Start -->
    <aside class="side-mini-panel with-vertical">
        <!-- ---------------------------------- -->
        <!-- Start Vertical Layout Sidebar -->
        <!-- ---------------------------------- -->
        <div class="iconbar">
            <div>
                <div class="mini-nav">
                    <div class="brand-logo d-flex align-items-center justify-content-center">
                        <a class="nav-link" id="" href="{{ route('home') }}">
                            <img src="{{ asset('front-assets/images/logo-icon.svg') }}" alt="loader" class="" />
                        </a>
                    </div>
                    @auth
                        @if(auth()->user()->hasActiveSubscription())
                        <ul class="mini-nav-ul" data-simplebar>
                            <li class="mini-nav-item ">
                                <a href="{{ route('home') }}" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="Home">
                                    <iconify-icon icon="solar:home-angle-outline" width="24" height="24"></iconify-icon>
                                </a>
                            </li>
                            <li class="mini-nav-item" id="">
                                <a href="{{ route('traffic.index') }}" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="Traffic">
                                    <iconify-icon icon="solar:chat-square-2-broken" class="fs-7"></iconify-icon>

                                </a>
                            </li>
                            <li class="mini-nav-item" id="">
                                <a href="{{ route('chats.index') }}" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="Chats">
                                    <iconify-icon icon="solar:chat-dots-broken" class="fs-7"></iconify-icon>

                                </a>
                            </li>
                            <li class="mini-nav-item" id="">
                                <a href="{{ route('chats.archive') }}" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="Archives">
                                    <iconify-icon icon="solar:archive-broken" class="fs-7"></iconify-icon>

                                </a>
                            </li>
                            <li class="mini-nav-item" id="mini-1">
                                <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="Setting">
                                    <iconify-icon icon="solar:tuning-square-2-line-duotone" class="fs-7"></iconify-icon>

                                </a>
                            </li>
                        </ul>
                        @endif
                    @endauth
                </div>
                @auth
                    @if(auth()->user()->hasActiveSubscription())
                    <div class="sidebarmenu">
                        <div class="brand-logo d-flex align-items-center nav-logo">
                            <a href="{{ route('home') }}" class="text-nowrap logo-img">
                                <img src="{{ asset('front-assets/images/logo-icon.svg') }}" alt="Logo" />
                            </a>
                        </div>
                        <nav class="sidebar-nav scroll-sidebar" id="menu-right-mini-1" data-simplebar>
                            <ul class="sidebar-menu" id="sidebarnav">
                                <li class="nav-small-cap">
                                    <span class="hide-menu">Setting</span>
                                </li>
                                @if(auth()->check() && auth()->user()->isAdmin())
                                <li class="sidebar-item">
                                    <a href="{{ route('agents.index') }}" class="sidebar-link">
                                        <iconify-icon icon="solar:users-group-two-rounded-broken"></iconify-icon>
                                        <span class="hide-menu">Agents</span>
                                    </a>
                                </li>
                                
                                <li class="sidebar-item">
                                    <a href="{{ route('settings.banned-customers.index') }}" class="sidebar-link">
                                        <iconify-icon icon="solar:user-block-rounded-line-duotone"></iconify-icon>
                                        <span class="hide-menu">Banned customers</span>
                                    </a>
                                </li>
                                @endif
                                <li class="sidebar-item">
                                    <a href="{{ route('settings.shortcut.index') }}" class="sidebar-link">
                                        <iconify-icon icon="solar:square-share-line-broken"></iconify-icon>
                                        <span class="hide-menu">Shortcut</span>
                                    </a>
                                </li>
                                @if(auth()->check() && auth()->user()->isAdmin())
                                <li class="sidebar-item">
                                    <a href="{{ route('settings.widget.index') }}" class="sidebar-link">
                                        <iconify-icon icon="solar:chat-round-dots-broken"></iconify-icon>
                                        <span class="hide-menu">Website Widget</span>
                                        <span class="has-arrow-submenu toggle-arrow"></span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse first-level">
                                        <li class="sidebar-item">
                                            <a href="{{ route('settings.chat-install.index') }}" class="sidebar-link">
                                                <span class="icon-small"></span>
                                                <span class="hide-menu">Install Chat</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a href="{{ route('settings.post-chat-form.index') }}" class="sidebar-link">
                                                <span class="icon-small"></span>
                                                <span class="hide-menu">Post-chat form</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('settings.trusted-domains.index') }}" class="sidebar-link">
                                        <iconify-icon icon="solar:link-circle-broken"></iconify-icon>
                                        <span class="hide-menu">Trusted domains</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="{{ route('settings.country-restrictions.index') }}" class="sidebar-link">
                                        <iconify-icon icon="solar:global-broken"></iconify-icon>
                                        <span class="hide-menu">Country Restriction</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    @endif
                @endauth
            </div>
        </div>
    </aside>
    <!--  Sidebar End -->
    <div class="page-wrapper">
        <!--  Header Start -->
        <header class="topbar">
            <div class="with-vertical">
                @auth
                    @if(auth()->user()->hasActiveSubscription())
                        <nav class="navbar navbar-expand-lg p-0">
                            <ul class="navbar-nav">
                                <li class="nav-item d-flex d-xl-none">
                                    <a class="nav-link nav-icon-hover-bg rounded-circle  sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                                        <iconify-icon icon="solar:hamburger-menu-line-duotone" class="fs-6"></iconify-icon>
                                    </a>
                                </li>
                                <li class="nav-item d-none d-xl-flex nav-icon-hover-bg rounded-circle">
                                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        <iconify-icon icon="solar:magnifer-linear" class="fs-6"></iconify-icon>
                                    </a>
                                </li>
                            </ul>

                            <div class="d-block d-lg-none py-9 py-xl-0">
                                <img src="{{ asset('assets/images/favicon-white.png')}}" alt="matdash-img" />
                            </div>
                            <a class="navbar-toggler p-0 border-0 nav-icon-hover-bg rounded-circle" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                <iconify-icon icon="solar:menu-dots-bold-duotone" class="fs-6"></iconify-icon>
                            </a>
                            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                                <div class="d-flex align-items-center justify-content-between">
                                    <ul class="navbar-nav flex-row mx-auto ms-lg-auto align-items-center justify-content-center">
                                        <li class="nav-item">
                                            <a class="nav-link moon dark-layout nav-icon-hover-bg rounded-circle" href="javascript:void(0)">
                                                <iconify-icon icon="solar:moon-line-duotone" class="moon fs-6"></iconify-icon>
                                            </a>
                                            <a class="nav-link sun light-layout nav-icon-hover-bg rounded-circle" href="javascript:void(0)" style="display: none">
                                                <iconify-icon icon="solar:sun-2-line-duotone" class="sun fs-6"></iconify-icon>
                                            </a>
                                        </li>
                                        <li class="nav-item d-block d-xl-none">
                                            <a class="nav-link nav-icon-hover-bg rounded-circle" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                <iconify-icon icon="solar:magnifer-line-duotone" class="fs-6"></iconify-icon>
                                            </a>
                                        </li>
                                        <li class="nav-item dropdown nav-icon-hover-bg rounded-circle">
                                            <a class="nav-link position-relative" href="javascript:void(0)" id="drop2" aria-expanded="false">
                                                <iconify-icon icon="solar:bell-bing-line-duotone" class="fs-6"></iconify-icon>
                                            </a>
                                            <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                                                <div class="d-flex align-items-center justify-content-between py-3 px-7">
                                                    <h5 class="mb-0 fs-5 fw-semibold">Notifications</h5>
                                                    <span class="badge text-bg-primary rounded-4 px-3 py-1 lh-sm">5 new</span>
                                                </div>
                                                <div class="message-body" data-simplebar>
                                                    <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item gap-3">
                                                        <span class="flex-shrink-0 bg-danger-subtle rounded-circle round d-flex align-items-center justify-content-center fs-6 text-danger">
                                                            <iconify-icon icon="solar:widget-3-line-duotone"></iconify-icon>
                                                        </span>
                                                        <div class="w-75">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mb-1 fw-semibold">Launch Admin</h6>
                                                                <span class="d-block fs-2">9:30 AM</span>
                                                            </div>
                                                            <span class="d-block text-truncate text-truncate fs-11">Just see the my new admin!</span>
                                                        </div>
                                                    </a>
                                                    <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item gap-3">
                                                        <span class="flex-shrink-0 bg-primary-subtle rounded-circle round d-flex align-items-center justify-content-center fs-6 text-primary">
                                                            <iconify-icon icon="solar:calendar-line-duotone"></iconify-icon>
                                                        </span>
                                                        <div class="w-75">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mb-1 fw-semibold">Event today</h6>
                                                                <span class="d-block fs-2">9:15 AM</span>
                                                            </div>
                                                            <span class="d-block text-truncate text-truncate fs-11">Just a reminder that you have event</span>
                                                        </div>
                                                    </a>
                                                    <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item gap-3">
                                                        <span class="flex-shrink-0 bg-secondary-subtle rounded-circle round d-flex align-items-center justify-content-center fs-6 text-secondary">
                                                            <iconify-icon icon="solar:settings-line-duotone"></iconify-icon>
                                                        </span>
                                                        <div class="w-75">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mb-1 fw-semibold">Settings</h6>
                                                                <span class="d-block fs-2">4:36 PM</span>
                                                            </div>
                                                            <span class="d-block text-truncate text-truncate fs-11">You can customize this template as you want</span>
                                                        </div>
                                                    </a>
                                                    <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item gap-3">
                                                        <span class="flex-shrink-0 bg-warning-subtle rounded-circle round d-flex align-items-center justify-content-center fs-6 text-warning">
                                                            <iconify-icon icon="solar:widget-4-line-duotone"></iconify-icon>
                                                        </span>
                                                        <div class="w-75">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mb-1 fw-semibold">Launch Admin</h6>
                                                                <span class="d-block fs-2">9:30 AM</span>
                                                            </div>
                                                            <span class="d-block text-truncate text-truncate fs-11">Just see the my new admin!</span>
                                                        </div>
                                                    </a>
                                                    <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item gap-3">
                                                        <span class="flex-shrink-0 bg-primary-subtle rounded-circle round d-flex align-items-center justify-content-center fs-6 text-primary">
                                                            <iconify-icon icon="solar:calendar-line-duotone"></iconify-icon>
                                                        </span>
                                                        <div class="w-75">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mb-1 fw-semibold">Event today</h6>
                                                                <span class="d-block fs-2">9:15 AM</span>
                                                            </div>
                                                            <span class="d-block text-truncate text-truncate fs-11">Just a reminder that you have event</span>
                                                        </div>
                                                    </a>
                                                    <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item gap-3">
                                                        <span class="flex-shrink-0 bg-secondary-subtle rounded-circle round d-flex align-items-center justify-content-center fs-6 text-secondary">
                                                            <iconify-icon icon="solar:settings-line-duotone"></iconify-icon>
                                                        </span>
                                                        <div class="w-75">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <h6 class="mb-1 fw-semibold">Settings</h6>
                                                                <span class="d-block fs-2">4:36 PM</span>
                                                            </div>
                                                            <span class="d-block text-truncate text-truncate fs-11">You can customize this template as you want</span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="py-6 px-7 mb-1">
                                                    <button class="btn btn-primary w-100">See All Notifications</button>
                                                </div>

                                            </div>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="nav-link" href="javascript:void(0)" id="drop1" aria-expanded="false">
                                                <div class="d-flex align-items-center gap-2 lh-base">
                                                    <img src="{{ asset('assets/images/user-1.jpg') }}" class="rounded-circle" width="35" height="35" alt="matdash-img" />
                                                    <iconify-icon icon="solar:alt-arrow-down-bold" class="fs-2"></iconify-icon>
                                                </div>
                                            </a>
                                            <div class="dropdown-menu profile-dropdown dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                                                <div class="position-relative px-4 pt-3 pb-2">
                                                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom gap-6">
                                                        <img src="{{ asset('assets/images/user-1.jpg') }}" class="rounded-circle" width="56" height="56" alt="user-img" />

                                                        <div>
                                                            <h5 class="mb-0 fs-12">
                                                                {{ auth()->user()->name }}
                                                            </h5>

                                                            <p class="mb-0 text-dark">
                                                                {{ auth()->user()->email }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="message-body">
                                                        <a href="{{ route('agents.edit', auth()->user()->id) }}" class="p-2 dropdown-item h6 rounded-1">
                                                            My Profile
                                                        </a>
                                                        @if(auth()->check() && auth()->user()->isAdmin())
                                                        <a href="{{ route('subscription.index') }}" class="p-2 dropdown-item h6 rounded-1">
                                                            My Subscription
                                                        </a>
                                                        <a href="{{ route('subscription.invoices.index') }}" class="p-2 dropdown-item h6 rounded-1">
                                                            My Invoice
                                                            <!-- <span class="badge bg-danger-subtle text-danger rounded ms-8">4</span> -->
                                                        </a>
                                                        <a href="{{ route('subscription.account-details.index') }}" class="p-2 dropdown-item h6 rounded-1">
                                                            Account Settings
                                                        </a>
                                                        @endif
                                                        <a class="p-2 dropdown-item h6 rounded-1" href="{{ route('logout') }}"
                                                        onclick="event.preventDefault(); document.getElementById('signout').submit();">
                                                            {{ __('Sign Out') }}
                                                        </a>

                                                        <form method="POST" action="{{ route('logout') }}" class="d-none" id="signout">
                                                            @csrf
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    @else 
                    <nav class="navbar navbar-expand-lg p-0">
                            <ul class="navbar-nav">
                            </ul>
                            
                            <a class="navbar-toggler p-0 border-0 nav-icon-hover-bg rounded-circle" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                <iconify-icon icon="solar:menu-dots-bold-duotone" class="fs-6"></iconify-icon>
                            </a>
                            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                                <div class="d-flex align-items-center justify-content-between">
                                    <ul class="navbar-nav flex-row mx-auto ms-lg-auto align-items-center justify-content-center">
                                        <li class="nav-item">
                                            <a class="p-2 btn btn-primary h6 rounded-1" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('signout').submit();">
                                                {{ __('Sign Out') }}
                                            </a>

                                            <form method="POST" action="{{ route('logout') }}" class="d-none" id="signout">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    @endif
                @endauth
            </div>
        </header>
        <!--  Header End -->