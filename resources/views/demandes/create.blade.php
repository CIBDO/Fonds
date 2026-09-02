@extends('layouts.master')

@section('title', 'Nouvelle Demande de Fonds')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('demandes-fonds.index') }}" class="btn btn-label-secondary">
            <i class="ti tabler-arrow-left me-1"></i>Retour à la liste
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti tabler-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error') || session('message_erreur'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti tabler-alert-circle me-2"></i>{{ session('error') ?? session('message_erreur') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('demandes-fonds.store') }}" id="demandeForm">
        @csrf

        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 text-heading">
                    <i class="ti tabler-info-circle me-2 text-body-secondary"></i>Informations Générales
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date de Demande <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="date" class="form-control"
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="date_reception" class="form-label">Date Réception Salaire <span class="text-danger">*</span></label>
                        <input type="date" name="date_reception" id="date_reception" class="form-control"
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="mois" class="form-label">Mois <span class="text-danger">*</span></label>
                        <select name="mois" id="mois" class="form-select" required>
                            @foreach($moisList as $m)
                                <option value="{{ $m }}" {{ ($moisCourant ?? '') === $m ? 'selected' : '' }}>
                                    {{ $m === 'Fevrier' ? 'Février' : ($m === 'Aout' ? 'Août' : ($m === 'Decembre' ? 'Décembre' : $m)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="annee" class="form-label">Année <span class="text-danger">*</span></label>
                        <input type="number" name="annee" id="annee" class="form-control"
                               value="{{ $anneeCourante ?? now()->format('Y') }}" min="2020" max="2099" required>
                    </div>

                    <div class="col-md-6">
                        <label for="poste" class="form-label">Poste / Service</label>
                        <select name="poste_id" class="form-select" disabled>
                            @foreach($postes as $poste)
                                <option value="{{ $poste->id }}" {{ Auth::user()->poste_id == $poste->id ? 'selected' : '' }}>
                                    {{ $poste->nom }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="poste_id" value="{{ Auth::user()->poste_id }}">
                        <input type="hidden" name="status" value="en_attente">
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    </div>

                    <div class="col-md-6">
                        <label for="user" class="form-label">Agent Traitant</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 text-heading">
                    <i class="ti tabler-users me-2 text-body-secondary"></i>Détails des Salaires par Catégorie
                </h5>
            </div>
            <div class="card-body p-0">
                @include('demandes._form')
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 text-heading">
                    <i class="ti tabler-calculator me-2 text-body-secondary"></i>Calcul du Solde
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="montant_disponible" class="form-label">
                            Recettes en Douanes (FCFA) <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="montant_disponible" name="montant_disponible"
                               class="form-control" value="0" placeholder="Montant disponible" required>
                        <small class="text-body-secondary">Montant total disponible pour les salaires</small>
                    </div>

                    <div class="col-md-6">
                        <label for="solde" class="form-label">Montant de la Demande (FCFA)</label>
                        <input type="text" id="solde" name="solde"
                               class="form-control bg-label-secondary" value="0" readonly>
                        <small class="text-body-secondary">Solde = Total Mois Courant − Recettes en Douanes</small>
                    </div>
                </div>

                <div class="mt-3">
                    <div id="solde-indicator" class="alert alert-info mb-0">
                        <i class="ti tabler-info-circle me-2"></i>
                        <strong>Information :</strong> Le solde sera calculé automatiquement après la saisie des montants.
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="total_net" name="total_net" value="0">
        <input type="hidden" id="total_revers" name="total_revers" value="0">
        <input type="hidden" id="total_courant" name="total_courant" value="0">

        <div class="alert alert-warning mb-4">
            <div class="d-flex align-items-start gap-3">
                <i class="ti tabler-alert-triangle flex-shrink-0 mt-1"></i>
                <div>
                    <strong>Attention — Vérification obligatoire</strong>
                    <p class="mb-0 mt-1">
                        Veuillez vérifier minutieusement toutes les informations saisies avant de soumettre cette demande.
                    </p>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
            <a href="{{ route('demandes-fonds.index') }}" class="btn btn-label-secondary">
                <i class="ti tabler-x me-1"></i>Annuler
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="ti tabler-send me-1"></i>Soumettre la Demande
            </button>
        </div>
    </form>

@push('styles')
<style>
    .form-control:focus, .form-select:focus {
        border-color: var(--bs-border-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 89, 113, 0.1);
    }

    #solde-indicator { border-left: 3px solid var(--bs-info); }
    #solde-indicator.solde-positif { border-left-color: var(--bs-success); }
    #solde-indicator.solde-negatif { border-left-color: var(--bs-danger); }
</style>
@endpush

@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('demandeForm');
        const soldeField = document.getElementById('solde');
        const soldeIndicator = document.getElementById('solde-indicator');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
                event.preventDefault();
            }
        });

        const observeSolde = new MutationObserver(updateSoldeIndicator);
        if (soldeField) {
            observeSolde.observe(soldeField, { attributes: true, attributeFilter: ['value'] });
            soldeField.addEventListener('input', updateSoldeIndicator);
        }

        function updateSoldeIndicator() {
            const soldeValue = parseFloat(soldeField.value.replace(/\s/g, '')) || 0;

            if (soldeValue > 0) {
                soldeIndicator.className = 'alert alert-danger solde-negatif mb-0';
                soldeIndicator.innerHTML = `
                    <i class="ti tabler-alert-triangle me-2"></i>
                    <strong>Déficit :</strong> Il manque ${formatNumber(soldeValue)} FCFA pour couvrir les salaires.
                `;
            } else if (soldeValue < 0) {
                soldeIndicator.className = 'alert alert-success solde-positif mb-0';
                soldeIndicator.innerHTML = `
                    <i class="ti tabler-circle-check me-2"></i>
                    <strong>Surplus :</strong> Il reste ${formatNumber(Math.abs(soldeValue))} FCFA après paiement des salaires.
                `;
            } else {
                soldeIndicator.className = 'alert alert-info mb-0';
                soldeIndicator.innerHTML = `
                    <i class="ti tabler-equal me-2"></i>
                    <strong>Solde équilibré :</strong> Les recettes couvrent exactement les besoins en salaires.
                `;
            }
        }

        function formatNumber(number) {
            return new Intl.NumberFormat('fr-FR').format(number);
        }

        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';
            submitBtn.disabled = true;
        });

        updateSoldeIndicator();
    });
})();
</script>
@endpush
