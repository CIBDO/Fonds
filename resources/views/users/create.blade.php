@extends('layouts.master')

@section('content')
<style>
    /* Animations et styles modernes - Charte graphique cohérente */
    .fade-in {
        animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card-stats {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    }

    .form-container {
        background: #f8f9ff;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .form-input {
        border-radius: 25px;
        border: 2px solid #e3e6f0;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-create {
        border-radius: 25px;
        padding: 12px 30px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .form-card {
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        border: none;
    }

    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .breadcrumb-modern {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-modern .breadcrumb-item a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }

    .breadcrumb-modern .breadcrumb-item.active {
        color: white;
        font-weight: 600;
    }

    .stats-icon {
        font-size: 3rem;
        opacity: 0.3;
        position: absolute;
        right: 20px;
        top: 20px;
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .form-group-modern {
        margin-bottom: 20px;
    }

    .form-group-modern label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
        display: block;
    }

    .required-star {
        color: #e53e3e;
        font-weight: bold;
    }

    .alert-modern {
        border-radius: 15px;
        border: none;
        padding: 20px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
        color: #742a2a;
    }

    .login-link {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .login-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .login-link a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="fade-in">
    <!-- En-tête moderne avec gradient -->
    <div class="form-header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-2">
                    <i class="fas fa-user-plus me-3"></i>
                    Création d'Utilisateur
                </h2>
                <ul class="breadcrumb breadcrumb-modern">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Utilisateurs</a></li>
                    <li class="breadcrumb-item active">Créer</li>
                </ul>
            </div>
            <div class="col-auto">
                <div class="stats-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques en cartes -->
    {{-- <div class="row mb-4 fade-in">
        <div class="col-md-4">
            <div class="card card-stats pulse">
                <div class="card-body position-relative">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title mb-1">Nouveau</h4>
                            <p class="card-text mb-0">Utilisateur</p>
                        </div>
                        <div class="col-4 text-end">
                            <i class="fas fa-user-plus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body position-relative">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title mb-1">Gestion</h4>
                            <p class="card-text mb-0">des Rôles</p>
                        </div>
                        <div class="col-4 text-end">
                            <i class="fas fa-users-cog fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="card-body position-relative">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title mb-1">Sécurité</h4>
                            <p class="card-text mb-0">Avancée</p>
                        </div>
                        <div class="col-4 text-end">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Formulaire moderne -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-header" style="background: linear-gradient(135deg, #f8f9ff 0%, #e3e6f0 100%); border-bottom: 2px solid #667eea;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-user-edit me-2" style="color: #667eea;"></i>
                                Informations de l'Utilisateur
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-container">
                        <!-- Affichage des erreurs -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-modern">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Erreurs détectées :</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Formulaire de création d'utilisateur -->
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf

                            <div class="form-row">
                                <!-- Champ Nom -->
                                <div class="form-group-modern">
                                    <label><i class="fas fa-user me-2" style="color: #667eea;"></i>Prénoms & Nom <span class="required-star">*</span></label>
                                    <input class="form-control form-input" type="text" name="name" value="{{ old('name') }}" placeholder="Entrez le nom complet">
                                </div>

                                <!-- Champ Email -->
                                <div class="form-group-modern">
                                    <label><i class="fas fa-envelope me-2" style="color: #667eea;"></i>Email <span class="required-star">*</span></label>
                                    <input class="form-control form-input" type="email" name="email" value="{{ old('email') }}" placeholder="exemple@domain.com">
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- Champ Password -->
                                <div class="form-group-modern">
                                    <label><i class="fas fa-lock me-2" style="color: #667eea;"></i>Mot de passe <span class="required-star">*</span></label>
                                    <input class="form-control form-input" type="password" name="password" placeholder="Mot de passe sécurisé">
                                </div>

                                <!-- Champ Confirm Password -->
                                <div class="form-group-modern">
                                    <label><i class="fas fa-lock me-2" style="color: #667eea;"></i>Confirmer le mot de passe <span class="required-star">*</span></label>
                                    <input class="form-control form-input" type="password" name="password_confirmation" placeholder="Confirmez le mot de passe">
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- Champ Rôle -->
                                <div class="form-group-modern">
                                    <label><i class="fas fa-user-tag me-2" style="color: #667eea;"></i>Rôle <span class="required-star">*</span></label>
                                    <select class="form-control form-input" name="role">
                                        <option value="">Choisir un rôle</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="tresorier" {{ old('role') == 'tresorier' ? 'selected' : '' }}>Trésorier</option>
                                        <option value="acct" {{ old('role') == 'acct' ? 'selected' : '' }}>ACCT</option>
                                        <option value="superviseur" {{ old('role') == 'superviseur' ? 'selected' : '' }}>Superviseur</option>
                                    </select>
                                </div>

                                <!-- Champ Statut -->
                                <div class="form-group-modern">
                                    <label><i class="fas fa-toggle-on me-2" style="color: #667eea;"></i>Statut <span class="required-star">*</span></label>
                                    <select class="form-control form-input" name="active">
                                        <option value="1" {{ old('active') == '1' ? 'selected' : '' }}>Actif</option>
                                        <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Champ Poste -->
                            <div class="form-group-modern">
                                <label><i class="fas fa-briefcase me-2" style="color: #667eea;"></i>Poste <span class="required-star">*</span></label>
                                <select class="form-control form-input" name="poste_id">
                                    <option value="">Choisir un poste</option>
                                    @foreach ($postes as $poste)
                                        <option value="{{ $poste->id }}">{{ $poste->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bouton de soumission -->
                            <div class="text-center mt-4">
                                <button class="btn btn-create px-5 py-3" type="submit">
                                    <i class="fas fa-user-plus me-2"></i>Créer le Compte
                                </button>
                            </div>

                            <!-- Lien de connexion -->
                            <div class="login-link">
                                <p>Vous avez déjà un compte ? <a href="{{ route('login') }}">Se Connecter</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation d'entrée des éléments
        const elements = document.querySelectorAll('.form-group-modern');
        elements.forEach((element, index) => {
            element.style.animationDelay = `${index * 0.1}s`;
            element.classList.add('fade-in');
        });

        // Validation en temps réel
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.style.borderColor = '#28a745';
                } else {
                    this.style.borderColor = '#e3e6f0';
                }
            });
        });

        // Animation au survol des cartes
        $('.card-stats').hover(
            function() {
                $(this).addClass('pulse');
            },
            function() {
                $(this).removeClass('pulse');
            }
        );
    });
</script>
@endsection
