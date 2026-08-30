@php
    $dashboardUrl = Auth::user()->hasRole('admin') ? route('dashboard.admin')
        : (Auth::user()->hasRole('tresorier') ? route('dashboard.tresorier')
        : (Auth::user()->hasRole('superviseur') ? route('superviseur.dashboard')
        : route('dashboard.acct')));
@endphp

<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
    <div class="container-xxl">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
            <a href="{{ $dashboardUrl }}" class="app-brand-link gap-2">
                <img src="{{ asset('assets/img/logo.png') }}" alt="DGTCP" style="max-height:32px;">
                <span class="app-brand-text fw-bold text-heading">DGTCP-MALI</span>
            </a>
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="icon-base ti tabler-x"></i>
            </a>
        </div>

        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-2 d-xl-none">
            <a class="nav-link px-0" href="javascript:void(0)">
                <i class="icon-base ti tabler-menu-2 icon-md"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center justify-content-end flex-grow-1" id="navbar-collapse">
            @include('partials.vuexy.navbar-user')
        </div>
    </div>
</nav>
