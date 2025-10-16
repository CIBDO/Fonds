@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page moderne -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-primary">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Nouvelle Demande de Fonds
                    </h3>
                    <p class="text-muted mb-0">Saisie d'une demande de fonds pour salaires</p>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('demandes-fonds.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <!-- Messages d'alerte -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error') || session('message_erreur'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') ?? session('message_erreur') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulaire principal -->
    <form method="POST" action="{{ route('demandes-fonds.store') }}" id="demandeForm">
        @csrf

        <!-- Section 1: Informations Générales -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informations Générales
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Date de demande -->
                    <div class="col-md-3">
                        <label for="date" class="form-label fw-bold">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>Date de Demande <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="date"
                               id="date"
                               class="form-control"
                               value="{{ now()->format('Y-m-d') }}"
                               required>
                    </div>

                    <!-- Date de réception -->
                    <div class="col-md-3">
                        <label for="date_reception" class="form-label fw-bold">
                            <i class="fas fa-calendar-check text-success me-1"></i>Date Réception Salaire <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="date_reception"
                               id="date_reception"
                               class="form-control"
                               value="{{ now()->format('Y-m-d') }}"
                               required>
                    </div>

                    <!-- Mois -->
                    <div class="col-md-3">
                        <label for="mois" class="form-label fw-bold">
                            <i class="fas fa-calendar text-info me-1"></i>Mois <span class="text-danger">*</span>
                        </label>
                        <select name="mois" id="mois" class="form-select" required>
                            <option value="" disabled selected>Sélectionnez...</option>
                            <option value="Janvier">Janvier</option>
                            <option value="Fevrier">Février</option>
                            <option value="Mars">Mars</option>
                            <option value="Avril">Avril</option>
                            <option value="Mai">Mai</option>
                            <option value="Juin">Juin</option>
                            <option value="Juillet">Juillet</option>
                            <option value="Aout">Août</option>
                            <option value="Septembre">Septembre</option>
                            <option value="Octobre">Octobre</option>
                            <option value="Novembre">Novembre</option>
                            <option value="Decembre">Décembre</option>
                        </select>
                    </div>

                    <!-- Année -->
                    <div class="col-md-3">
                        <label for="annee" class="form-label fw-bold">
                            <i class="fas fa-calendar-year text-warning me-1"></i>Année <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="annee"
                               id="annee"
                               class="form-control"
                               value="{{ now()->format('Y') }}"
                               min="2020"
                               max="2099"
                               required>
                    </div>

                    <!-- Poste/Service -->
                    <div class="col-md-6">
                        <label for="poste" class="form-label fw-bold">
                            <i class="fas fa-building text-primary me-1"></i>Poste / Service
                        </label>
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

                    <!-- Agent traitant -->
                    <div class="col-md-6">
                        <label for="user" class="form-label fw-bold">
                            <i class="fas fa-user text-secondary me-1"></i>Agent Traitant
                        </label>
                        <input type="text"
                               class="form-control"
                               value="{{ Auth::user()->name }}"
                               readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Détails des Salaires -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Détails des Salaires par Catégorie
                </h5>
            </div>
            <div class="card-body p-0">
                @include('demandes._form')
            </div>
        </div>

        <!-- Section 3: Montants et Solde -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>Calcul du Solde
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Recettes en douanes -->
                    <div class="col-md-6">
                        <label for="montant_disponible" class="form-label fw-bold">
                            <i class="fas fa-coins text-warning me-1"></i>Recettes en Douanes (FCFA) <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="montant_disponible"
                               name="montant_disponible"
                               class="form-control form-control-lg"
                               value="0"
                               placeholder="Montant disponible"
                               required>
                        <small class="text-muted">Montant total disponible pour les salaires</small>
                    </div>

                    <!-- Solde calculé -->
                    <div class="col-md-6">
                        <label for="solde" class="form-label fw-bold">
                            <i class="fas fa-balance-scale text-info me-1"></i>Montant de la Demande (FCFA)
                        </label>
                        <input type="text"
                               id="solde"
                               name="solde"
                               class="form-control form-control-lg bg-light"
                               value="0"
                               readonly>
                        <small class="text-muted">Solde = Total Mois Courant - Recettes en Douanes</small>
                    </div>
                </div>

                <!-- Indicateur visuel du solde -->
                <div class="mt-3">
                    <div id="solde-indicator" class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Information :</strong> Le solde sera calculé automatiquement après la saisie des montants.
                    </div>
                </div>
            </div>
        </div>

        <!-- Champs cachés pour les totaux -->
        <input type="hidden" id="total_net" name="total_net" value="0">
        <input type="hidden" id="total_revers" name="total_revers" value="0">
        <input type="hidden" id="total_courant" name="total_courant" value="0">

        <!-- Message d'avertissement -->
        <div class="alert alert-warning border-warning">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h5 class="alert-heading mb-2">
                        <i class="fas fa-shield-alt me-1"></i>Attention - Vérification Obligatoire
                    </h5>
                    <p class="mb-0">
                        Veuillez vérifier <strong>minutieusement</strong> toutes les informations saisies avant de soumettre cette demande.
                        Une fois soumise, <strong class="text-danger">la demande ne pourra plus être modifiée</strong> et devra passer par le processus de validation.
                    </p>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-center mb-5">
            <a href="{{ route('demandes-fonds.index') }}" class="btn btn-outline-secondary btn-lg px-5">
                <i class="fas fa-times me-2"></i>Annuler
            </a>
            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                <i class="fas fa-paper-plane me-2"></i>Soumettre la Demande
            </button>
        </div>
    </form>
</div>

<style>
    /* Animation pour le focus des champs */
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        border-color: #86b7fe;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    /* Style pour les labels */
    .form-label {
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    /* Animation des cartes */
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    /* Style pour l'indicateur de solde */
    #solde-indicator {
        transition: all 0.3s ease;
        border-left: 4px solid;
    }

    #solde-indicator.solde-positif {
        background-color: #d1e7dd;
        border-color: #0f5132;
        color: #0f5132;
    }

    #solde-indicator.solde-negatif {
        background-color: #f8d7da;
        border-color: #842029;
        color: #842029;
    }

    /* Animation du bouton submit */
    #submitBtn {
        transition: all 0.3s ease;
    }

    #submitBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
    }

    #submitBtn:active {
        transform: translateY(0);
    }

    /* Style pour les champs readonly */
    .form-control:read-only, .form-select:disabled {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }

        .btn-lg {
            padding: 0.75rem 2rem;
        }
    }
</style>

@section('add-js')
<script>
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('demandeForm');
        const soldeField = document.getElementById('solde');
        const soldeIndicator = document.getElementById('solde-indicator');
        const submitBtn = document.getElementById('submitBtn');

        // Empêcher la soumission par Enter
        form.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
                event.preventDefault();
            }
        });

        // Observer le changement du solde pour mettre à jour l'indicateur
        const observeSolde = new MutationObserver(function() {
            updateSoldeIndicator();
        });

        if (soldeField) {
            observeSolde.observe(soldeField, {
                attributes: true,
                attributeFilter: ['value']
            });

            // Observer aussi via l'événement input
            soldeField.addEventListener('input', updateSoldeIndicator);
        }

        function updateSoldeIndicator() {
            const soldeValue = parseFloat(soldeField.value.replace(/\s/g, '')) || 0;

            if (soldeValue > 0) {
                soldeIndicator.className = 'alert alert-danger solde-negatif';
                soldeIndicator.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Déficit :</strong> Il manque ${formatNumber(soldeValue)} FCFA pour couvrir les salaires. Montant à demander.
                `;
            } else if (soldeValue < 0) {
                soldeIndicator.className = 'alert alert-success solde-positif';
                soldeIndicator.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Surplus :</strong> Il reste ${formatNumber(Math.abs(soldeValue))} FCFA après paiement des salaires.
                `;
            } else {
                soldeIndicator.className = 'alert alert-info';
                soldeIndicator.innerHTML = `
                    <i class="fas fa-equals me-2"></i>
                    <strong>Solde Équilibré :</strong> Les recettes couvrent exactement les besoins en salaires.
                `;
            }
        }

        function formatNumber(number) {
            return new Intl.NumberFormat('fr-FR').format(number);
        }

        // Animation lors de la soumission
        form.addEventListener('submit', function(e) {
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';
            submitBtn.disabled = true;
        });

        // Initialiser l'indicateur
        updateSoldeIndicator();

        console.log('Formulaire de demande de fonds initialisé avec succès');
    });
})();
</script>
@endsection
@endsection
