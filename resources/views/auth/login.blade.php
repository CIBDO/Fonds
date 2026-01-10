<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<title>Gestion des Fonds</title>

<link rel="shortcut icon" href="assets/img/favicon.png">

<!-- Police personnalisée JetBrains Mono -->
<link rel="stylesheet" href="{{ asset('assets/css/jetbrains-mono.css') }}">

<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
<!-- CDN Font Awesome en fallback -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Styles professionnels pour la page de connexion DGTCP -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        overflow-x: hidden;
        width: 100%;
    }

    body {
        font-family: 'JetBrains Mono', monospace, sans-serif !important;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f7fa;
        position: relative;
        overflow-x: hidden;
        padding: 10px;
        width: 100%;
        max-width: 100vw;
    }

    /* Arrière-plan adouci */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 50%, #e3f2fd 100%);
        z-index: -1;
    }

    /* Container principal */
    .login-wrapper {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        padding: 40px 20px;
        position: relative;
        z-index: 1;
        box-sizing: border-box;
    }

    /* Carte de connexion */
    .login-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow:
            0 10px 40px rgba(0, 0, 0, 0.1),
            0 0 0 1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 480px;
        position: relative;
        animation: cardFadeIn 0.8s ease-out;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    @keyframes cardFadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Section gauche - Information institutionnelle */
    .login-info-section {
        background: linear-gradient(135deg, #009739 0%, #007A2E 100%);
        color: #ffffff;
        padding: 35px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }

    /* Motif de fond subtil */
    .login-info-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image:
            radial-gradient(circle at 20% 80%, rgba(253, 197, 0, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
        opacity: 0.6;
    }

    .login-info-section::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(253, 197, 0, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        animation: pulseGlow 8s ease-in-out infinite;
    }

    @keyframes pulseGlow {
        0%, 100% {
            transform: scale(1) translateY(0);
            opacity: 0.3;
        }
        50% {
            transform: scale(1.1) translateY(-20px);
            opacity: 0.5;
        }
    }

    .info-content {
        position: relative;
        z-index: 2;
    }

    .institution-header {
        margin-bottom: 30px;
    }

    .institution-logo {
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .institution-logo:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .institution-logo img {
        width: 80%;
        height: 80%;
        object-fit: contain;
    }

    .institution-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
        line-height: 1.2;
        text-align: center;
    }

    .institution-subtitle {
        font-size: 14px;
        opacity: 0.95;
        line-height: 1.6;
        font-weight: 400;
        text-align: center;
    }

    .info-features {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 0;
        padding: 16px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        width: 100%;
        box-sizing: border-box;
    }

    .feature-item:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: translateX(5px);
    }

    .feature-icon {
        width: 40px;
        height: 40px;
        background: rgba(253, 197, 0, 0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .feature-icon i {
        font-size: 18px;
        color: #FDC500;
    }

    .feature-content h4 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .feature-content p {
        font-size: 12px;
        opacity: 0.85;
        line-height: 1.4;
        margin: 0;
    }

    .info-footer {
        position: relative;
        z-index: 2;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        font-size: 11px;
        opacity: 0.8;
        text-align: center;
    }

    /* Section droite - Formulaire de connexion */
    .login-form-section {
        padding: 35px 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        width: 100%;
        box-sizing: border-box;
    }

    .form-container {
        width: 100%;
        max-width: 450px;
        box-sizing: border-box;
    }

    .form-header {
        text-align: center;
        margin-bottom: 35px;
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 12px;
        letter-spacing: -1px;
    }

    .form-subtitle {
        font-size: 15px;
        color: #666;
        font-weight: 400;
    }

    .security-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #E8F5E9;
        border-radius: 20px;
        font-size: 13px;
        color: #009739;
        font-weight: 500;
        margin-top: 12px;
    }

    .security-badge i {
        font-size: 14px;
    }

    /* Messages d'alerte */
    .alert {
        border-radius: 12px;
        border: none;
        margin-bottom: 30px;
        padding: 16px 20px;
        font-size: 14px;
        animation: alertSlideIn 0.3s ease-out;
    }

    @keyframes alertSlideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-danger {
        background: #FFF5F5;
        color: #D72631;
        border-left: 4px solid #D72631;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    .alert li {
        margin: 4px 0;
    }

    /* Formulaire */
    .form-group {
        margin-bottom: 24px;
        position: relative;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        letter-spacing: 0.2px;
    }

    .form-label .required {
        color: #D72631;
        margin-left: 2px;
    }

    .input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 18px;
        color: #009739;
        font-size: 18px;
        z-index: 2;
        transition: all 0.3s ease;
    }

    .form-control {
        width: 100%;
        height: 50px;
        border: 2px solid #E5E7EB;
        border-radius: 12px;
        padding: 0 55px;
        font-size: 14px;
        font-family: 'JetBrains Mono', monospace, sans-serif !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #F9FAFB;
        color: #1a1a1a;
    }

    .form-control:focus {
        border-color: #009739;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(0, 151, 57, 0.08);
        outline: none;
    }

    .form-control:focus ~ .input-icon {
        color: #00B04F;
        transform: scale(1.1);
    }

    .form-control::placeholder {
        color: #9CA3AF;
    }

    .toggle-password {
        position: absolute;
        right: 18px;
        cursor: pointer;
        color: #6B7280;
        font-size: 18px;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.3s ease;
        z-index: 2;
    }

    .toggle-password:hover {
        color: #009739;
        background: rgba(0, 151, 57, 0.08);
    }

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .remember-me {
        display: flex;
        align-items: center;
    }

    .remember-me input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        cursor: pointer;
        accent-color: #009739;
    }

    .remember-me label {
        font-size: 14px;
        color: #4B5563;
        cursor: pointer;
        user-select: none;
    }

    .forgot-password {
        font-size: 14px;
        color: #009739;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .forgot-password:hover {
        color: #007A2E;
        text-decoration: underline;
    }

    .btn-login {
        width: 100%;
        height: 50px;
        background: linear-gradient(135deg, #009739 0%, #00B04F 100%);
        border: none;
        border-radius: 12px;
        color: #ffffff;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 151, 57, 0.3);
    }

    .btn-login::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 151, 57, 0.4);
    }

    .btn-login:hover::before {
        left: 100%;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .btn-login i {
        margin-right: 10px;
    }

    .divider {
        text-align: center;
        margin: 35px 0;
        position: relative;
    }

    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #E5E7EB;
    }

    .divider span {
        position: relative;
        background: #ffffff;
        padding: 0 20px;
        color: #9CA3AF;
        font-size: 13px;
        font-weight: 500;
    }

    .help-text {
        text-align: center;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid #E5E7EB;
    }

    .help-text p {
        font-size: 13px;
        color: #6B7280;
        margin: 0;
    }

    .help-text a {
        color: #009739;
        text-decoration: none;
        font-weight: 600;
    }

    .help-text a:hover {
        text-decoration: underline;
    }

    /* Version et copyright en bas à droite */
    .page-footer {
        position: fixed;
        bottom: 20px;
        right: 30px;
        font-size: 11px;
        color: rgba(255, 255, 255, 0.6);
        z-index: 10;
        text-align: right;
    }

    .page-footer .version {
        font-weight: 600;
        color: rgba(255, 255, 255, 0.8);
    }

    /* Responsive */
    /* Tablettes et écrans moyens */
    @media (max-width: 992px) {
        body {
            padding: 15px;
        }

        .login-card {
            grid-template-columns: 1fr !important;
            min-height: auto;
        }

        .login-info-section {
            min-height: auto;
            padding: 30px 25px;
            order: 1;
        }

        .login-form-section {
            padding: 30px 25px;
            order: 2;
        }

        .info-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 20px;
        }

        .feature-item {
            margin-bottom: 0;
            padding: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            min-width: 0;
        }

        .feature-icon {
            margin-right: 0;
            margin-bottom: 10px;
        }

        .feature-content {
            width: 100%;
        }

        .feature-content h4 {
            font-size: 12px;
        }

        .feature-content p {
            font-size: 10px;
        }

        .institution-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }

        .institution-title {
            font-size: 22px;
        }

        .institution-subtitle {
            font-size: 13px;
        }

        .form-title {
            font-size: 26px;
        }

        .form-subtitle {
            font-size: 14px;
        }
    }

    @media (max-width: 768px) {
        body {
            padding: 10px;
        }

        .login-wrapper {
            padding: 15px 10px;
            max-width: 100%;
        }

        .login-card {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .login-info-section {
            padding: 25px 20px;
            min-height: auto;
        }

        .login-form-section {
            padding: 25px 20px;
        }

        .institution-header {
            margin-bottom: 20px;
        }

        .institution-logo {
            width: 70px;
            height: 70px;
            margin-bottom: 12px;
        }

        .institution-title {
            font-size: 20px;
            margin-bottom: 6px;
        }

        .institution-subtitle {
            font-size: 12px;
        }

        .info-features {
            display: flex !important;
            flex-direction: column !important;
            grid-template-columns: 1fr !important;
            gap: 12px;
            margin-top: 15px;
        }

        .feature-item {
            padding: 12px;
            margin-bottom: 0;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            width: 100%;
        }

        .feature-icon {
            width: 35px;
            height: 35px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .feature-icon i {
            font-size: 16px;
        }

        .feature-content h4 {
            font-size: 13px;
        }

        .feature-content p {
            font-size: 11px;
        }

        .info-footer {
            padding-top: 15px;
            font-size: 10px;
            margin-top: 15px;
        }

        .form-header {
            margin-bottom: 25px;
        }

        .form-title {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 13px;
        }

        .security-badge {
            padding: 6px 12px;
            font-size: 12px;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 13px;
            margin-bottom: 8px;
        }

        .form-control {
            height: 48px;
            padding: 0 50px;
            font-size: 14px;
        }

        .input-icon {
            left: 15px;
            font-size: 16px;
        }

        .toggle-password {
            right: 15px;
            font-size: 16px;
            padding: 6px;
        }

        .form-options {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 25px;
        }

        .btn-login {
            height: 48px;
            font-size: 14px;
        }

        .help-text {
            margin-top: 25px;
            padding-top: 20px;
        }

        .help-text p {
            font-size: 12px;
        }

        .page-footer {
            position: relative;
            bottom: auto;
            right: auto;
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            font-size: 10px;
        }

        .alert {
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }
    }

    @media (max-width: 480px) {
        body {
            padding: 5px;
        }

        .login-wrapper {
            padding: 10px 5px;
        }

        .login-card {
            border-radius: 12px;
        }

        .login-info-section {
            padding: 20px 15px;
        }

        .login-form-section {
            padding: 20px 15px;
        }

        .institution-logo {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        .institution-title {
            font-size: 18px;
        }

        .institution-subtitle {
            font-size: 11px;
        }

        .info-features {
            display: flex !important;
            flex-direction: column !important;
            gap: 10px;
            margin-top: 12px;
        }

        .feature-item {
            padding: 10px;
            flex-direction: row;
            align-items: flex-start;
            width: 100%;
        }

        .feature-icon {
            width: 32px;
            height: 32px;
            margin-right: 10px;
            margin-bottom: 0;
            flex-shrink: 0;
        }

        .feature-content h4 {
            font-size: 12px;
            margin-bottom: 3px;
        }

        .feature-content p {
            font-size: 10px;
            line-height: 1.3;
        }

        .info-footer {
            font-size: 9px;
            padding-top: 12px;
        }

        .form-header {
            margin-bottom: 20px;
        }

        .form-title {
            font-size: 22px;
            margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 12px;
        }

        .security-badge {
            padding: 5px 10px;
            font-size: 11px;
            gap: 6px;
        }

        .security-badge i {
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            font-size: 12px;
            margin-bottom: 6px;
        }

        .form-control {
            height: 46px;
            padding: 0 45px;
            font-size: 14px;
            border-radius: 10px;
        }

        .input-icon {
            left: 12px;
            font-size: 15px;
        }

        .toggle-password {
            right: 12px;
            font-size: 15px;
            padding: 5px;
        }

        .btn-login {
            height: 46px;
            font-size: 14px;
            border-radius: 10px;
        }

        .help-text {
            margin-top: 20px;
            padding-top: 15px;
        }

        .help-text p {
            font-size: 11px;
        }

        .page-footer {
            font-size: 9px;
            padding: 12px;
        }

        .alert {
            padding: 10px 14px;
            font-size: 12px;
            border-radius: 10px;
        }

        .alert ul {
            padding-left: 18px;
        }

        .alert li {
            font-size: 11px;
            margin: 3px 0;
        }
    }

    @media (max-width: 360px) {
        .login-wrapper {
            padding: 5px;
        }

        .login-card {
            border-radius: 10px;
        }

        .login-info-section,
        .login-form-section {
            padding: 15px 12px;
        }

        .institution-logo {
            width: 50px;
            height: 50px;
        }

        .institution-title {
            font-size: 16px;
        }

        .form-title {
            font-size: 20px;
        }

        .form-control,
        .btn-login {
            height: 44px;
            font-size: 13px;
        }
    }

    /* Orientation landscape sur mobile */
    @media (max-width: 768px) and (orientation: landscape) {
        .login-wrapper {
            padding: 10px;
        }

        .login-card {
            min-height: auto;
        }

        .login-info-section {
            min-height: auto;
            padding: 20px 15px;
        }

        .info-features {
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .feature-item {
            padding: 8px;
        }

        .feature-content h4 {
            font-size: 11px;
        }

        .feature-content p {
            font-size: 9px;
        }
    }
</style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Section gauche - Informations -->
            <div class="login-info-section">
                <div class="info-content">
                    <div class="institution-header">
                        <div class="institution-logo">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="DGTCP Logo">
                        </div>
                    </div>

                    <div class="info-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="feature-content">
                                <h4>💼 Sécurité Renforcée</h4>
                                <p>Système certifié garantissant la confidentialité et l'intégrité des opérations financières.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="feature-content">
                                <h4>📊 Suivi en Temps Réel</h4>
                                <p>Contrôle instantané de l'état des demandes, validations et transferts de fonds.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="feature-content">
                                <h4>🤝 Gestion Collaborative</h4>
                                <p>Processus intégrés favorisant la coordination entre les services du Trésor.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-footer">
                    <p>Plateforme de Gestion et de Suivi des Demandes de Fonds</p>
                </div>
            </div>

            <!-- Section droite - Formulaire -->
            <div class="login-form-section">
                <div class="form-container">
                    <div class="form-header">
                        <h2 class="form-title">Connexion</h2>
                        <p class="form-subtitle">Accédez à votre espace sécurisé</p>
                        <div class="security-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Connexion Sécurisée</span>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <label class="form-label" for="email">
                                Adresse e-mail <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    id="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="email"
                                    autofocus
                                    placeholder="votre.email@tresor.gov.ml"
                                >
                                <i class="fas fa-at input-icon"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">
                                Mot de passe <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input
                                    id="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••••••"
                                >
                                <i class="fas fa-key input-icon"></i>
                                <span class="toggle-password" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>

                        {{-- <div class="form-options">
                            <div class="remember-me">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">Se souvenir de moi</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="forgot-password">
                                Mot de passe oublié ?
                            </a>
                        </div> --}}

                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i>
                            Se connecter
                        </button>
                    </form>

                    <div class="help-text">
                        <p>
                            <i class="fas fa-phone-alt"></i>
                            Besoin d'aide ? Contactez le support technique
                            <a href="">DGTCP-DSI</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-footer">
        <div class="version">Version 1.0.0</div>
        <div>&copy; {{ date('Y') }} DGTCP - Tous droits réservés</div>
    </div>

<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Animation d'entrée progressive
document.addEventListener('DOMContentLoaded', function() {
    const formGroups = document.querySelectorAll('.form-group');

    formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(20px)';

        setTimeout(() => {
            group.style.transition = 'all 0.5s ease';
            group.style.opacity = '1';
            group.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
});
</script>
</body>
</html>
