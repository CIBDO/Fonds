<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Connexion — Plateforme de gestion des fonds DGTCP">
    <title>Connexion | Gestion des Fonds — DGTCP</title>

    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jetbrains-mono.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>
<body>
    <main class="login-page">
        <div class="login-card">
            <div class="login-card__bar" aria-hidden="true"></div>

            <div class="login-card__body">
                <header class="login-card__brand">
                    <div class="login-card__logo">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo DGTCP">
                    </div>
                    <h1>Connexion</h1>
                    <p>Gestion et suivi des demandes de fonds</p>
                    <span class="org">DGTCP</span>

                </header>

                @if ($errors->any())
                    <div class="login-alert" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
                    @csrf

                    <div class="login-field">
                        <label for="email">Adresse e-mail <span class="req" aria-hidden="true">*</span></label>
                        <div class="login-input-wrap">
                            <input
                                id="email"
                                type="email"
                                name="email"
                                class="login-input @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="votre.email@tresor.gov.ml"
                                required
                                autocomplete="email"
                                autofocus
                            >
                            <i class="fas fa-at field-icon" aria-hidden="true"></i>
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password">Mot de passe <span class="req" aria-hidden="true">*</span></label>
                        <div class="login-input-wrap">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="login-input @error('password') is-invalid @enderror"
                                placeholder="Votre mot de passe"
                                required
                                autocomplete="current-password"
                            >
                            <i class="fas fa-key field-icon" aria-hidden="true"></i>
                            <button type="button" class="login-toggle-pwd" id="toggle-password" aria-label="Afficher le mot de passe">
                                <i class="fas fa-eye" id="toggle-password-icon" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="login-submit" id="login-submit">
                        <i class="fas fa-right-to-bracket" aria-hidden="true"></i>
                        <span>Se connecter</span>
                    </button>
                </form>
            </div>

            <div class="login-card__footer">
                <i class="fas fa-headset" aria-hidden="true"></i>
                Besoin d'aide ? <a href="mailto:dsi@dgtcp.gov.ml">DGTCP-DSI</a>
            </div>
        </div>
    </main>

    <footer class="login-page-footer">
        <strong>Version 1.0.0</strong> — &copy; {{ date('Y') }} DGTCP. Tous droits réservés.
    </footer>

    <script>
        (function () {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('toggle-password');
            const toggleIcon = document.getElementById('toggle-password-icon');
            const form = document.getElementById('login-form');
            const submitBtn = document.getElementById('login-submit');

            toggleBtn.addEventListener('click', function () {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                toggleIcon.classList.toggle('fa-eye', !isHidden);
                toggleIcon.classList.toggle('fa-eye-slash', isHidden);
                toggleBtn.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            });

            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.querySelector('span').textContent = 'Connexion en cours…';
            });
        })();
    </script>
</body>
</html>
