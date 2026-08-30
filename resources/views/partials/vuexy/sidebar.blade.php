@php
    $dashboardUrl = Auth::user()->hasRole('admin') ? route('dashboard.admin')
        : (Auth::user()->hasRole('tresorier') ? route('dashboard.tresorier')
        : (Auth::user()->hasRole('superviseur') ? route('superviseur.dashboard')
        : route('dashboard.acct')));
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ $dashboardUrl }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="DGTCP" style="max-height:32px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">DGTCP Fonds</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        @include('partials.vuexy.menu-items')
    </ul>
</aside>
