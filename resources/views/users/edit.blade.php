@extends('layouts.master')

@section('content')
<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <div class="loginbox">
                <div class="login-left">
                    {{-- <img class="img-fluid" src="{{ asset('assets/img/login.jpg') }}" alt="Logo"> --}}
                </div>
                <div class="login-right">
                    <div class="login-right-wrap">
                        <h1>Modifier le Compte</h1>
                        <p class="account-subtitle">Modifier les informations de l'utilisateur</p>

                        <!-- Formulaire de mise à jour d'utilisateur -->
                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Champ Username -->
                            <div class="form-group">
                                <label>Prénoms & Nom <span class="login-danger">*</span></label>
                                <input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}">
                                <span class="profile-views"><i class="fas fa-user-circle"></i></span>
                            </div>

                            <!-- Champ Email -->
                            <div class="form-group">
                                <label>Email <span class="login-danger">*</span></label>
                                <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}">
                                <span class="profile-views"><i class="fas fa-envelope"></i></span>
                            </div>

                            <!-- Champ Password -->
                            <div class="form-group">
                                <label>Nouveau Password <span class="login-danger">*</span></label>
                                <input class="form-control pass-input" type="password" name="password">
                                <span class="profile-views feather-eye toggle-password"></span>
                            </div>

                            <!-- Champ Confirm Password -->
                            <div class="form-group">
                                <label>Confirmer Nouveau Password <span class="login-danger">*</span></label>
                                <input class="form-control pass-confirm" type="password" name="password_confirmation">
                                <span class="profile-views feather-eye reg-toggle-password"></span>
                            </div>

                            <!-- Champ Rôle -->
                            <div class="form-group custom-select">
                                <label>Rôle <span class="login-danger">*</span></label>
                                <select class="form-control" name="role"
                                        {{ auth()->user()->role === 'tresorier' ? 'disabled' : '' }}>
                                    <option value="">Choisir un rôle</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="tresorier" {{ old('role', $user->role) == 'tresorier' ? 'selected' : '' }}>Trésorier</option>
                                    <option value="acct" {{ old('role', $user->role) == 'acct' ? 'selected' : '' }}>ACCT</option>
                                    <option value="superviseur" {{ old('role', $user->role) == 'superviseur' ? 'selected' : '' }}>Superviseur</option>
                                </select>
                            </div>

                            <!-- Champ Statut -->
                            <div class="form-group custom-select">
                                <label>Statut <span class="login-danger">*</span></label>
                                <select class="form-control" name="active"
                                        {{ auth()->user()->role === 'tresorier' ? 'disabled' : '' }}>
                                    <option value="1" {{ old('active', $user->active) == '1' ? 'selected' : '' }}>Actif</option>
                                    <option value="0" {{ old('active', $user->active) == '0' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>

                            <!-- Champ Poste -->
                            <div class="form-group custom-select">
                                <label>Poste <span class="login-danger">*</span></label>
                                <select class="form-select" name="poste_id"
                                        {{ auth()->user()->role === 'tresorier' ? 'disabled' : '' }}>
                                    <option value="">Choisir un poste</option>
                                    @foreach ($postes as $poste)
                                        <option value="{{ $poste->id }}" {{ old('poste_id', $user->poste_id) == $poste->id ? 'selected' : '' }}>{{ $poste->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bouton Update -->
                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block" type="submit">Mettre à jour</button>
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
