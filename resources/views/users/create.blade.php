@extends('layouts.master') 

@section('content')
<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <div class="loginbox">
                
                <div class="login-right">
                    <div class="login-right-wrap">
                        <h1>Créer un Compte</h1>
                        <p class="account-subtitle">Saisir les informations de l'utilisateur</p>

                        <!-- Formulaire de création d'utilisateur -->
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf

                            <!-- Affichage des erreurs -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Champ Username -->
                            <div class="form-group">
                                <label>Prénoms & Nom <span class="login-danger">*</span></label>
                                <input class="form-control" type="text" name="name" value="{{ old('name') }}">
                                <span class="profile-views"><i class="fas fa-user-circle"></i></span>
                            </div>

                            <!-- Champ Email -->
                            <div class="form-group">
                                <label>Email <span class="login-danger">*</span></label>
                                <input class="form-control" type="email" name="email" value="{{ old('email') }}">
                                <span class="profile-views"><i class="fas fa-envelope"></i></span>
                            </div>

                            <!-- Champ Password -->
                            <div class="form-group">
                                <label>Password <span class="login-danger">*</span></label>
                                <input class="form-control pass-input" type="password" name="password">
                                <span class="profile-views feather-eye toggle-password"></span>
                            </div>

                            <!-- Champ Confirm Password -->
                            <div class="form-group">
                                <label>Confirmer Password <span class="login-danger">*</span></label>
                                <input class="form-control pass-confirm" type="password" name="password_confirmation">
                                <span class="profile-views feather-eye reg-toggle-password"></span>
                            </div>
                            <!-- Champ Rôle -->
                            <div class="form-group custom-select">
                                <label>Rôle <span class="login-danger">*</span></label>
                                <select class="select2" name="role">
                                    <option value="">Choisir un rôle</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="tresorier" {{ old('role') == 'tresorier' ? 'selected' : '' }}>Trésorier</option>
                                    <option value="acct" {{ old('role') == 'acct' ? 'selected' : '' }}>ACCT</option>
                                    <option value="superviseur" {{ old('role') == 'superviseur' ? 'selected' : '' }}>Superviseur</option>
                                    <!-- Ajoutez d'autres rôles si nécessaire -->
                                </select>
                            </div>
                            <div class="form-group custom-select">
                                <label>Statut <span class="login-danger">*</span></label>
                                <select class="form-select" name="active">
                                    <option value="1" {{ old('active') == '1' ? 'selected' : '' }}>Actif</option>
                                    <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Inactif</option>
                                    <span class="profile-views"><i class="fas fa-envelope"></i></span>
                                </select>
                            </div>
                            <div class="form-group custom-select">
                                <label>Poste <span class="login-danger">*</span></label>
                                <select class="form-select" name="poste_id">
                                    <option value="">Choisir un poste</option>
                                    @foreach ($postes as $poste)
                                        <option value="{{ $poste->id }}">{{ $poste->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="dont-have">Avez-vous dun compte <a href="{{ route('login') }}">Se Connecter</a></div>

                            <!-- Bouton Register -->
                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block" type="submit">S'inscrire</button>
                            </div>
                        </form>

                        <div class="login-or">
                            <span class="or-line"></span>
                            <span class="span-or">or</span>
                        </div>

                        <!-- Social Login -->
                        <div class="social-login">
                            <a href="#"><i class="fab fa-google-plus-g"></i></a>
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
