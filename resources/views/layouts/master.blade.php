<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Gestion des Fonds</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Police personnalisée JetBrains Mono -->
    <link rel="stylesheet" href="{{ asset('assets/css/jetbrains-mono.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/flags/flags.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard-improvements.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pcs-improvements.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Inclure DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

    <link href="{{ asset('assets/css/notifications.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/mobile-responsive.css') }}?v=4">

    {{-- Styles critiques sidebar mobile (ne dépend pas du cache externe) --}}
    <style>
        html.mobile-layout .page-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        html.mobile-layout #sidebar.sidebar {
            position: fixed !important;
            top: 60px !important;
            left: 0 !important;
            bottom: 0 !important;
            width: min(300px, 88vw) !important;
            max-width: 300px !important;
            margin-left: 0 !important;
            z-index: 1042 !important;
            transform: translate3d(-100%, 0, 0) !important;
            transition: transform 0.3s ease !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        html.mobile-layout.nav-open #sidebar.sidebar {
            transform: translate3d(0, 0, 0) !important;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.18) !important;
        }
        html.mobile-layout #mobile_btn,
        html.mobile-layout .dgtcp-mobile-btn {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
        html.mobile-layout .sidebar-overlay.opened {
            display: block !important;
        }
        html.mobile-layout:not(.nav-open) .sidebar-overlay {
            display: none !important;
            pointer-events: none !important;
        }
        html.menu-opened,
        html.nav-open,
        body.nav-open {
            overflow: hidden !important;
        }
        html.mobile-layout:not(.nav-open) body {
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }
    </style>

    <script>
        (function () {
            var mq = window.matchMedia('(max-width: 1199.98px)');

            function applyLayout() {
                document.documentElement.classList.toggle('mobile-layout', mq.matches);
                if (!mq.matches) {
                    document.documentElement.classList.remove('nav-open');
                    if (document.body) {
                        document.body.style.overflow = '';
                        document.body.classList.remove('nav-open');
                    }
                }
            }

            function resetNavState() {
                document.documentElement.classList.remove('nav-open');
                document.documentElement.style.overflow = '';
                if (!document.body) {
                    return;
                }
                document.body.classList.remove('nav-open');
                document.body.style.overflow = '';
                document.body.style.position = '';
            }

            function initNavLayout() {
                resetNavState();
                applyLayout();
            }

            if (document.body) {
                initNavLayout();
            } else {
                document.addEventListener('DOMContentLoaded', initNavLayout, { once: true });
            }

            if (mq.addEventListener) {
                mq.addEventListener('change', applyLayout);
            } else if (mq.addListener) {
                mq.addListener(applyLayout);
            }
        })();
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="main-wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        @include('sweetalert::alert')
        <div class="page-wrapper">
            @yield('content')
            @include('partials.footer')
        </div>
    </div>

    <!-- Scripts JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>

    <!-- Inclure DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets/js/datatables-fr.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/mobile-sidebar.js') }}?v=3"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/notifications.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    @yield('add-js')
    @stack('scripts')
    <link rel="stylesheet" href="{{ asset('assets/css/dgtcp-responsive-fixes.css') }}?v=1">
</body>
</html>
