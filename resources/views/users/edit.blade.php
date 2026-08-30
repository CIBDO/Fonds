@extends('layouts.master')

@section('title', 'Modifier l\'Utilisateur')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('users.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<x-vuexy.card title="Modification des Informations" icon="tabler-user-edit">
    @if ($errors->any())
        <x-vuexy.alert type="danger" class="mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-vuexy.alert>
    @endif

    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Prénoms & Nom <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nouveau mot de passe</label>
                <input class="form-control" type="password" name="password" placeholder="Laisser vide pour conserver">
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmer le nouveau mot de passe</label>
                <input class="form-control" type="password" name="password_confirmation">
            </div>
            <div class="col-md-6">
                <label class="form-label">Rôle <span class="text-danger">*</span></label>
                <select class="form-select" name="role" {{ auth()->user()->role === 'tresorier' ? 'disabled' : '' }} required>
                    <option value="">Choisir un rôle</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="tresorier" {{ old('role', $user->role) == 'tresorier' ? 'selected' : '' }}>Trésorier</option>
                    <option value="acct" {{ old('role', $user->role) == 'acct' ? 'selected' : '' }}>ACCT</option>
                    <option value="superviseur" {{ old('role', $user->role) == 'superviseur' ? 'selected' : '' }}>Superviseur</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Statut <span class="text-danger">*</span></label>
                <select class="form-select" name="active" {{ auth()->user()->role === 'tresorier' ? 'disabled' : '' }} required>
                    <option value="1" {{ old('active', $user->active) == '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ old('active', $user->active) == '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Poste <span class="text-danger">*</span></label>
                <select class="form-select" name="poste_id" {{ auth()->user()->role === 'tresorier' ? 'disabled' : '' }} required>
                    <option value="">Choisir un poste</option>
                    @foreach ($postes as $poste)
                        <option value="{{ $poste->id }}" {{ old('poste_id', $user->poste_id) == $poste->id ? 'selected' : '' }}>{{ $poste->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-label-secondary">Annuler</a>
            <button class="btn btn-primary" type="submit">
                <i class="icon-base ti tabler-device-floppy me-1"></i>Mettre à jour
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection
