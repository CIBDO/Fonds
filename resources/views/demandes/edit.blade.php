@extends('layouts.master')

@section('title', 'Modifier la Demande de Fonds')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('demandes-fonds.index') }}" class="btn btn-label-secondary">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('demandes-fonds.update', $demande->id) }}">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 text-heading">
                <i class="icon-base ti tabler-info-circle me-2 text-body-secondary"></i>Informations Générales
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $demande->date }}" required>
                </div>
                <div class="col-md-4">
                    <label for="date_reception" class="form-label">Date de Réception Salaire</label>
                    <input type="date" name="date_reception" class="form-control" value="{{ $demande->date_reception }}" required>
                </div>
                <div class="col-md-4">
                    <label for="mois" class="form-label">Mois</label>
                    <select name="mois" class="form-select" required>
                        @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'] as $m)
                            <option value="{{ $m }}" {{ $demande->mois == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="annee" class="form-label">Année</label>
                    <input type="number" name="annee" class="form-control" value="{{ $demande->annee }}" required>
                </div>
                <div class="col-md-4">
                    <label for="poste_id" class="form-label">Poste / Service</label>
                    <select name="poste_id" class="form-select" required>
                        @foreach($postes as $poste)
                            <option value="{{ $poste->id }}" {{ $demande->poste_id == $poste->id ? 'selected' : '' }}>{{ $poste->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="montant_disponible" class="form-label">Recettes Douanières</label>
                    <input type="text" id="montant_disponible" name="montant_disponible" class="form-control"
                           value="{{ number_format($demande->montant_disponible ?? 0, 0, ',', ' ') }}" required>
                </div>
                <div class="col-md-4">
                    <label for="solde" class="form-label">Solde</label>
                    <input type="text" id="solde" name="solde" class="form-control bg-label-secondary"
                           value="{{ number_format($demande->solde ?? 0, 0, ',', ' ') }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Agent</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                </div>
                <input type="hidden" name="status" value="{{ $demande->status }}">
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0 text-heading">
                <i class="icon-base ti tabler-users me-2 text-body-secondary"></i>Détails des Salaires
            </h5>
        </div>
        <div class="card-body p-0">
            @include('demandes._edit')
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
        <a href="{{ route('demandes-fonds.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-x me-1"></i>Annuler
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-device-floppy me-1"></i>Enregistrer
        </button>
    </div>
</form>
@endsection
