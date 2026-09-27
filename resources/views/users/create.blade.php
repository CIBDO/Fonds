@extends('layouts.master')

@section('title', 'Créer un Utilisateur')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('users.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<x-vuexy.card title="Informations de l'Utilisateur" icon="tabler-user-plus">
    @if ($errors->any())
        <x-vuexy.alert type="danger" class="mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-vuexy.alert>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Prénoms & Nom <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="name" value="{{ old('name') }}" placeholder="Entrez le nom complet" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="exemple@domain.com" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                <input class="form-control" type="password" name="password" placeholder="Mot de passe sécurisé" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                <input class="form-control" type="password" name="password_confirmation" placeholder="Confirmez le mot de passe" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Rôle <span class="text-danger">*</span></label>
                <select class="form-select" name="role" required>
                    <option value="">Choisir un rôle</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="tresorier" {{ old('role') == 'tresorier' ? 'selected' : '' }}>Trésorier</option>
                    <option value="acct" {{ old('role') == 'acct' ? 'selected' : '' }}>ACCT</option>
                    <option value="accd" {{ old('role') == 'accd' ? 'selected' : '' }}>ACCD</option>
                    <option value="superviseur" {{ old('role') == 'superviseur' ? 'selected' : '' }}>Superviseur</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Statut <span class="text-danger">*</span></label>
                <select class="form-select" name="active" required>
                    <option value="1" {{ old('active') == '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Poste <span class="text-danger" id="poste-requis">*</span></label>
                <select class="form-select" name="poste_id" id="poste_id">
                    <option value="">Choisir un poste</option>
                    @foreach ($postes as $poste)
                        <option value="{{ $poste->id }}" {{ old('poste_id') == $poste->id ? 'selected' : '' }}>{{ $poste->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-label-secondary">Annuler</a>
            <button class="btn btn-primary" type="submit">
                <i class="icon-base ti tabler-user-plus me-1"></i>Créer le Compte
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
(function () {
    const role = document.querySelector('select[name="role"]');
    const poste = document.getElementById('poste_id');
    const marque = document.getElementById('poste-requis');
    if (!role || !poste) return;
    const sync = () => {
        const accd = role.value === 'accd';
        poste.required = !accd;
        if (marque) marque.style.display = accd ? 'none' : '';
    };
    role.addEventListener('change', sync);
    sync();
})();
</script>
@endpush
