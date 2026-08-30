<!doctype html>
<html lang="fr" class="layout-wide customizer-hide" dir="ltr" data-assets-path="{{ asset('vuexy/assets/') }}/" data-template="vertical-menu-template-no-customizer" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Erreur serveur — DGTCP</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('vuexy/assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('vuexy/assets/vendor/css/pages/page-misc.css') }}">
    <script src="{{ asset('vuexy/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('vuexy/assets/js/config-dgtcp.js') }}"></script>
</head>
<body>
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper text-center">
            <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem">500</h1>
            <h4 class="mb-2 mx-2">Erreur serveur</h4>
            <p class="mb-6 mx-2">Nous avons rencontré un problème technique. Veuillez réessayer plus tard.</p>
            <a href="{{ url('/') }}" class="btn btn-primary mb-10">Retour à l'accueil</a>
            <div class="mt-4">
                <img src="{{ asset('vuexy/assets/img/illustrations/page-misc-error.png') }}" alt="Erreur" width="225" class="img-fluid">
            </div>
        </div>
    </div>
    <div class="container-fluid misc-bg-wrapper">
        <img src="{{ asset('vuexy/assets/img/illustrations/bg-shape-image-light.png') }}" height="355" alt="">
    </div>
    <script src="{{ asset('vuexy/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vuexy/assets/vendor/js/bootstrap.js') }}"></script>
</body>
</html>
