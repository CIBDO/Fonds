<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<title>Connexion - Trésor Public</title>

<link rel="shortcut icon" href="assets/img/favicon.png">

<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,400;1,500;1,700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">

<link rel="stylesheet" href="assets/plugins/feather/feather.css">

<link rel="stylesheet" href="assets/plugins/icons/flags/flags.css">

<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

<link rel="stylesheet" href="assets/css/style.css">

<!-- Styles personnalisés pour la page de connexion DGTCP -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #4ECDC4 0%, #44A08D 100%);
        font-family: 'Roboto', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-x: hidden;
    }

    .login-container {
        position: relative;
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }

    .loginbox {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        display: flex;
        min-height: 600px;
        position: relative;
        z-index: 2;
    }

    .login-left {
        background: linear-gradient(135deg, #009739 0%, #00B04F 100%);
        color: #fff;
        padding: 60px 40px;
        flex: 1 1 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .login-left::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
        animation: float 20s infinite linear;
    }

    .login-left::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 30% 70%, rgba(253, 197, 0, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 70% 30%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
        animation: pulse 4s ease-in-out infinite alternate;
    }

    /* Bulles flottantes dans la partie verte */
    .floating-bubbles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 1;
    }

    .bubble {
        position: absolute;
        border-radius: 50%;
        opacity: 0.6;
        animation: bubbleFloat 8s infinite linear;
    }

    .bubble.white {
        background: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
    }

    .bubble.yellow {
        background: rgba(253, 197, 0, 0.4);
        box-shadow: 0 0 10px rgba(253, 197, 0, 0.3);
    }

    .bubble.red {
        background: rgba(215, 38, 49, 0.4);
        box-shadow: 0 0 10px rgba(215, 38, 49, 0.3);
    }

    .bubble:nth-child(1) {
        width: 20px;
        height: 20px;
        left: 10%;
        animation-delay: 0s;
        animation-duration: 6s;
    }

    .bubble:nth-child(2) {
        width: 15px;
        height: 15px;
        left: 20%;
        animation-delay: 1s;
        animation-duration: 8s;
    }

    .bubble:nth-child(3) {
        width: 25px;
        height: 25px;
        left: 30%;
        animation-delay: 2s;
        animation-duration: 7s;
    }

    .bubble:nth-child(4) {
        width: 18px;
        height: 18px;
        left: 40%;
        animation-delay: 3s;
        animation-duration: 9s;
    }

    .bubble:nth-child(5) {
        width: 22px;
        height: 22px;
        left: 50%;
        animation-delay: 4s;
        animation-duration: 6.5s;
    }

    .bubble:nth-child(6) {
        width: 16px;
        height: 16px;
        left: 60%;
        animation-delay: 5s;
        animation-duration: 8.5s;
    }

    .bubble:nth-child(7) {
        width: 24px;
        height: 24px;
        left: 70%;
        animation-delay: 6s;
        animation-duration: 7.5s;
    }

    .bubble:nth-child(8) {
        width: 19px;
        height: 19px;
        left: 80%;
        animation-delay: 7s;
        animation-duration: 9.5s;
    }

    .bubble:nth-child(9) {
        width: 21px;
        height: 21px;
        left: 90%;
        animation-delay: 8s;
        animation-duration: 6.8s;
    }

    @keyframes bubbleFloat {
        0% {
            bottom: -50px;
            transform: translateX(0) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 0.6;
        }
        90% {
            opacity: 0.6;
        }
        100% {
            bottom: 110%;
            transform: translateX(-20px) rotate(360deg);
            opacity: 0;
        }
    }

    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); }
        100% { transform: translateY(-100px) rotate(360deg); }
    }

    @keyframes pulse {
        0% { opacity: 0.3; }
        100% { opacity: 0.7; }
    }

    .logo-section {
        position: relative;
        z-index: 2;
        margin-bottom: 30px;
    }

    .logo-circle {
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.3);
        animation: logoFloat 3s ease-in-out infinite alternate;
        transition: all 0.3s ease;
    }

    .logo-circle:hover {
        transform: scale(1.05);
        box-shadow: 0 0 30px rgba(253, 197, 0, 0.4);
    }

    .logo-circle i {
        font-size: 48px;
        color: #FDC500;
        animation: iconGlow 2s ease-in-out infinite alternate;
    }

    @keyframes logoFloat {
        0% { transform: translateY(0px); }
        100% { transform: translateY(-5px); }
    }

    @keyframes iconGlow {
        0% { text-shadow: 0 0 10px rgba(253, 197, 0, 0.5); }
        100% { text-shadow: 0 0 20px rgba(253, 197, 0, 0.8), 0 0 30px rgba(253, 197, 0, 0.4); }
    }

    .login-left h3 {
        font-weight: 700;
        font-size: 28px;
        margin-bottom: 10px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .login-left p {
        font-size: 16px;
        line-height: 1.5;
        opacity: 0.9;
        max-width: 280px;
    }

    .login-right {
        flex: 1 1 50%;
        background: #fff;
        padding: 60px 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-right-wrap {
        width: 100%;
        max-width: 400px;
    }

    .login-right-wrap h2 {
        color: #009739;
        font-weight: 700;
        font-size: 32px;
        margin-bottom: 40px;
        text-align: center;
        position: relative;
    }

    .login-right-wrap h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #009739, #FDC500);
        border-radius: 2px;
        animation: underlineGlow 2s ease-in-out infinite alternate;
    }

    @keyframes underlineGlow {
        0% {
            box-shadow: 0 0 5px rgba(0, 151, 57, 0.3);
            width: 60px;
        }
        100% {
            box-shadow: 0 0 15px rgba(253, 197, 0, 0.5);
            width: 80px;
        }
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-group label {
        font-size: 14px;
        color: #666;
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        height: 50px;
        border: 2px solid #e1e5e9;
        border-radius: 10px;
        padding: 0 20px 0 50px;
        font-size: 16px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f8f9fa;
        position: relative;
        font-family: 'Roboto', sans-serif;
    }

    .form-control[type="password"] {
        font-family: 'Roboto', sans-serif;
        letter-spacing: 2px;
    }

    .form-control[type="text"].password-visible {
        font-family: 'Roboto', sans-serif !important;
        letter-spacing: normal !important;
        font-weight: 500 !important;
    }

    .form-control:focus {
        border-color: #009739;
        box-shadow: 0 0 0 3px rgba(0, 151, 57, 0.1), 0 0 20px rgba(0, 151, 57, 0.05);
        background: #fff;
        transform: translateY(-1px);
    }

    .form-control:hover:not(:focus) {
        border-color: #009739;
        transform: translateY(-0.5px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-views {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #009739;
        font-size: 18px;
        transition: all 0.3s ease;
        animation: iconBreathe 3s ease-in-out infinite;
    }

    .form-control:focus + .profile-views {
        color: #00B04F;
        transform: translateY(-50%) scale(1.1);
    }

    @keyframes iconBreathe {
        0%, 100% { opacity: 0.7; }
        50% { opacity: 1; }
    }

    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        font-size: 18px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 5px;
        border-radius: 50%;
        background: transparent;
    }

    .toggle-password:hover {
        color: #009739;
        background: rgba(0, 151, 57, 0.1);
        transform: translateY(-50%) scale(1.1);
    }

    .toggle-password:active {
        transform: translateY(-50%) scale(0.95);
    }

    .toggle-password.password-visible {
        color: #009739;
        background: rgba(0, 151, 57, 0.1);
        animation: eyeGlow 0.5s ease-out;
    }

    @keyframes eyeGlow {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 151, 57, 0.4);
            transform: translateY(-50%) scale(1);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(0, 151, 57, 0);
            transform: translateY(-50%) scale(1.1);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(0, 151, 57, 0);
            transform: translateY(-50%) scale(1);
        }
    }

    .forgotpass {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .remember-me {
        display: flex;
        align-items: center;
    }

    .custom_check {
        font-size: 14px;
        color: #666;
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .custom_check input[type="checkbox"] {
        margin-right: 8px;
        transform: scale(1.2);
    }

    .forgotpass a {
        color: #009739;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .forgotpass a:hover {
        color: #026627;
        text-decoration: underline;
    }

    .btn-primary {
        background: linear-gradient(135deg, #009739 0%, #00B04F 100%);
        border: none;
        height: 50px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 151, 57, 0.3);
    }

    .login-or {
        text-align: center;
        margin: 30px 0;
        position: relative;
    }

    .or-line {
        display: block;
        height: 1px;
        background: #e1e5e9;
        position: relative;
    }

    .span-or {
        background: #fff;
        color: #666;
        padding: 0 20px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 14px;
    }

    .social-login {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .social-login a {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 18px;
    }

    .social-login a:nth-child(1) {
        background: #db4437;
        color: #fff;
    }

    .social-login a:nth-child(2) {
        background: #3b5998;
        color: #fff;
    }

    .social-login a:nth-child(3) {
        background: #1da1f2;
        color: #fff;
    }

    .social-login a:nth-child(4) {
        background: #0077b5;
        color: #fff;
    }

    .social-login a:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    /* Éléments décoratifs flottants */
    .floating-elements {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }

    .coin {
        position: absolute;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #FDC500 0%, #FFD700 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: bold;
        font-size: 18px;
        animation: coinFloat 6s infinite ease-in-out;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .coin:nth-child(1) {
        top: 10%;
        left: 10%;
        animation-delay: 0s;
    }

    .coin:nth-child(2) {
        top: 20%;
        right: 15%;
        animation-delay: 2s;
    }

    .coin:nth-child(3) {
        bottom: 30%;
        left: 5%;
        animation-delay: 4s;
    }

    .coin:nth-child(4) {
        bottom: 10%;
        right: 10%;
        animation-delay: 1s;
    }

    @keyframes coinFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    @keyframes passwordReveal {
        0% {
            opacity: 0.5;
            transform: scale(0.98);
            filter: blur(2px);
        }
        100% {
            opacity: 1;
            transform: scale(1);
            filter: blur(0px);
        }
    }

    @keyframes passwordHide {
        0% {
            opacity: 1;
            filter: blur(0px);
        }
        100% {
            opacity: 0.8;
            filter: blur(1px);
        }
    }

    .alert {
        border-radius: 10px;
        border: none;
        margin-bottom: 25px;
    }

    .alert-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .loginbox {
            flex-direction: column;
            margin: 10px;
        }

        .login-left, .login-right {
            flex: 1 1 100%;
        }

        .login-left {
            padding: 40px 30px;
            min-height: 300px;
        }

        .login-right {
            padding: 40px 30px;
        }

        .login-right-wrap h2 {
            font-size: 28px;
            margin-bottom: 30px;
        }

        .floating-elements {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 10px;
        }

        .login-left, .login-right {
            padding: 30px 20px;
        }

        .form-control {
            height: 45px;
            padding: 0 15px 0 45px;
        }

        .btn-primary {
            height: 45px;
        }
    }
</style>
</head>
<body>
<div class="login-container">
    <!-- Éléments décoratifs flottants -->
    <div class="floating-elements">
        <div class="coin">€</div>
        <div class="coin">$</div>
        <div class="coin">₣</div>
        <div class="coin">¥</div>
    </div>

    <div class="loginbox">
        <div class="login-left">
            <!-- Bulles flottantes -->
            <div class="floating-bubbles">
                <div class="bubble white"></div>
                <div class="bubble yellow"></div>
                <div class="bubble red"></div>
                <div class="bubble white"></div>
                <div class="bubble yellow"></div>
                <div class="bubble red"></div>
                <div class="bubble white"></div>
                <div class="bubble yellow"></div>
                <div class="bubble red"></div>
            </div>

            <div class="logo-section">
                <div class="logo-circle">
                    <i class="fas fa-university"></i>
                </div>
                <h3>Trésor Public du Mali</h3>
                <p>Direction Générale du Trésor &amp; de la Comptabilité Publique (DGTCP)</p>
            </div>
        </div>

        <div class="login-right">
            <div class="login-right-wrap">
                <h2>S'identifier</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label>Email <span class="login-danger">*</span></label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Votre adresse email">
                        <span class="profile-views"><i class="fas fa-envelope"></i></span>
                    </div>

                    <div class="form-group">
                        <label>Mot de passe <span class="login-danger">*</span></label>
                        <input id="password" type="password" class="form-control pass-input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Saisissez votre mot de passe">
                        <span class="profile-views"><i class="fas fa-lock"></i></span>
                        <span class="toggle-password" title="Afficher le mot de passe"><i class="fas fa-eye"></i></span>
                    </div>

                    {{-- <div class="forgotpass">
                        <div class="remember-me">
                            <label class="custom_check">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                Se souvenir de moi
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}">Mot de passe oublié?</a>
                    </div> --}}

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                            Connexion
                        </button>
                    </div>
                </form>

                <div class="login-or">
                    <span class="or-line"></span>
                    <span class="span-or">ou</span>
                </div>

                <div class="social-login">
                    <a href="#" title="Google"><i class="fab fa-google"></i></a>
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password i');
    const toggleButton = document.querySelector('.toggle-password');

    // Vérifier que les éléments existent
    if (!passwordInput || !toggleIcon || !toggleButton) {
        return;
    }

    if (passwordInput.type === 'password') {
        // Afficher le mot de passe en texte clair
        passwordInput.type = 'text';
        passwordInput.classList.add('password-visible');
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
        toggleButton.classList.add('password-visible');
        toggleButton.title = 'Masquer le mot de passe';

        // Styles pour le mode visible
        passwordInput.style.background = 'linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%)';
        passwordInput.style.borderColor = '#009739';
        passwordInput.style.color = '#009739';
        passwordInput.style.fontWeight = '500';
        passwordInput.style.fontFamily = "'Roboto', sans-serif";
        passwordInput.style.letterSpacing = 'normal';
        passwordInput.style.animation = 'passwordReveal 0.5s ease-out';

        // Forcer le focus pour voir le contenu
        passwordInput.blur();
        setTimeout(() => passwordInput.focus(), 50);

    } else {
        // Masquer le mot de passe
        passwordInput.type = 'password';
        passwordInput.classList.remove('password-visible');
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
        toggleButton.classList.remove('password-visible');
        toggleButton.title = 'Afficher le mot de passe';

        // Retour au style normal
        passwordInput.style.background = '#f8f9fa';
        passwordInput.style.borderColor = '#e1e5e9';
        passwordInput.style.color = '#333';
        passwordInput.style.fontWeight = 'normal';
        passwordInput.style.letterSpacing = '2px';
        passwordInput.style.animation = 'passwordHide 0.3s ease-out';
    }
}

// Event listener robuste
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.querySelector('.toggle-password');
    if (toggleButton) {
        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            togglePassword();
        });
        toggleButton.title = 'Afficher le mot de passe';
    }
});

// Animation d'entrée
document.addEventListener('DOMContentLoaded', function() {
    const loginBox = document.querySelector('.loginbox');
    const formGroups = document.querySelectorAll('.form-group');
    const socialIcons = document.querySelectorAll('.social-login a');

    // Animation de la boîte principale
    loginBox.style.opacity = '0';
    loginBox.style.transform = 'translateY(30px)';

    setTimeout(() => {
        loginBox.style.transition = 'all 0.6s ease';
        loginBox.style.opacity = '1';
        loginBox.style.transform = 'translateY(0)';
    }, 100);

    // Animation séquentielle des éléments du formulaire
    formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateX(-20px)';

        setTimeout(() => {
            group.style.transition = 'all 0.4s ease';
            group.style.opacity = '1';
            group.style.transform = 'translateX(0)';
        }, 300 + (index * 100));
    });

    // Animation des icônes sociales
    socialIcons.forEach((icon, index) => {
        icon.style.opacity = '0';
        icon.style.transform = 'translateY(20px)';

        setTimeout(() => {
            icon.style.transition = 'all 0.3s ease';
            icon.style.opacity = '1';
            icon.style.transform = 'translateY(0)';
        }, 800 + (index * 50));
    });

    // Animation de typing pour le titre
    const title = document.querySelector('.login-right-wrap h2');
    const titleText = title.textContent;
    title.textContent = '';
    title.style.opacity = '1';

    let i = 0;
    const typeWriter = () => {
        if (i < titleText.length) {
            title.textContent += titleText.charAt(i);
            i++;
            setTimeout(typeWriter, 100);
        }
    };

    setTimeout(typeWriter, 500);
});
</script>
</body>
</html>

