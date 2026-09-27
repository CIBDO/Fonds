<!doctype html>
@php $menuLayout = config('ui.menu_layout', 'vertical'); @endphp
<html lang="fr"
    class="layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="ltr"
    data-skin="default"
    data-assets-path="{{ asset('vuexy/assets/') }}/"
    data-template="{{ $menuLayout === 'horizontal' ? 'horizontal-menu-template-no-customizer' : 'vertical-menu-template-no-customizer' }}"
    data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion des Fonds') — DGTCP</title>
    <meta name="description" content="Plateforme de gestion des fonds DGTCP">

    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jetbrains-mono.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/node-waves/node-waves.css') }}">
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('vuexy/assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">

    @yield('vendor-style')
    @stack('vendor-style')

    <link rel="stylesheet" href="{{ asset('vuexy/assets/css/dgtcp-overrides.css') }}?v=7">
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
    @yield('page-style')
    @stack('styles')

    <script src="{{ asset('vuexy/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('vuexy/assets/js/config-dgtcp.js') }}"></script>
</head>

<body>
@if($menuLayout === 'horizontal')
    {{-- Layout horizontal : pleine largeur pour les données --}}
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            @include('partials.vuexy.navbar-horizontal')

            <div class="layout-page">
                <div class="content-wrapper">
                    @include('partials.vuexy.menu-horizontal')

                    <div class="container-xxl flex-grow-1 container-p-y">
                        @include('sweetalert::alert')
                        @yield('content')
                    </div>

                    @include('partials.vuexy.footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
@else
    {{-- Layout vertical : sidebar latérale --}}
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('partials.vuexy.sidebar')

            <div class="menu-mobile-toggler d-xl-none rounded-1">
                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                    <i class="ti tabler-menu icon-base"></i>
                    <i class="ti tabler-chevron-right icon-base"></i>
                </a>
            </div>

            <div class="layout-page">
                @include('partials.vuexy.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @include('sweetalert::alert')
                        @yield('content')
                    </div>

                    @include('partials.vuexy.footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
@endif

<script src="{{ asset('vuexy/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('vuexy/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('vuexy/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('vuexy/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('vuexy/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('vuexy/assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('vuexy/assets/vendor/js/menu.js') }}"></script>

@yield('vendor-script')
@stack('vendor-script')

<script src="{{ asset('vuexy/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('vuexy/assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/notifications.js') }}"></script>

@yield('page-script')
@stack('scripts')
</body>
</html>
