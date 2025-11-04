@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-chart-line me-2"></i>États et Rapports - Règlements PCS
                    </h3>
                     {{-- <p class="text-muted mb-0">Générez vos états de collecte et règlements</p> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Carte de sélection du type d'état -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Type d'état à générer
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="card border-success h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-coins fa-4x text-success mb-3"></i>
                                    <h5 class="fw-bold">État de Collecte</h5>
                                    <p class="text-muted">
                                        Affiche les fonds collectés par poste pour un programme et une année donnés,
                                        avec les montants déstockés et soldes disponibles.
                                    </p>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEtatCollecte">
                                        <i class="fas fa-download me-1"></i>Générer l'État de Collecte
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card border-primary h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-4x text-primary mb-3"></i>
                                    <h5 class="fw-bold">État Consolidé des Règlements</h5>
                                    <p class="text-muted">
                                        Affiche tous les règlements effectués par poste et par mois pour un programme et une année donnés.
                                    </p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEtatConsolide">
                                        <i class="fas fa-download me-1"></i>Générer l'État Consolidé
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Statistiques Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-coins fa-2x mb-2"></i>
                                    <h6>Collecte UEMOA {{ date('Y') }}</h6>
                                    <h4 class="fw-bold">
                                        @php
                                            $collecteUemoa = \App\Models\DeclarationPcs::where('statut', 'valide')
                                                ->where('programme', 'UEMOA')
                                                ->where('annee', date('Y'))
                                                ->sum('montant_recouvrement');
                                        @endphp
                                        {{ number_format($collecteUemoa, 0, ',', ' ') }} FCFA
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <i class="fas fa-coins fa-2x mb-2"></i>
                                    <h6>Collecte AES {{ date('Y') }}</h6>
                                    <h4 class="fw-bold">
                                        @php
                                            $collecteAes = \App\Models\DeclarationPcs::where('statut', 'valide')
                                                ->where('programme', 'AES')
                                                ->where('annee', date('Y'))
                                                ->sum('montant_recouvrement');
                                        @endphp
                                        {{ number_format($collecteAes, 0, ',', ' ') }} FCFA
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                    <h6>Déstocké UEMOA {{ date('Y') }}</h6>
                                    <h4 class="fw-bold">
                                        @php
                                            $destockeUemoa = \App\Models\DestockagePcs::where('statut', 'valide')
                                                ->where('programme', 'UEMOA')
                                                ->where('periode_annee', date('Y'))
                                                ->sum('montant_total_destocke');
                                        @endphp
                                        {{ number_format($destockeUemoa, 0, ',', ' ') }} FCFA
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                    <h6>Déstocké AES {{ date('Y') }}</h6>
                                    <h4 class="fw-bold">
                                        @php
                                            $destockeAes = \App\Models\DestockagePcs::where('statut', 'valide')
                                                ->where('programme', 'AES')
                                                ->where('periode_annee', date('Y'))
                                                ->sum('montant_total_destocke');
                                        @endphp
                                        {{ number_format($destockeAes, 0, ',', ' ') }} FCFA
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h6 class="text-success">Solde Disponible UEMOA {{ date('Y') }}</h6>
                                    <h3 class="fw-bold text-success">
                                        {{ number_format($collecteUemoa - $destockeUemoa, 0, ',', ' ') }} FCFA
                                    </h3>
                                    <small class="text-muted">
                                        Taux règlement : {{ $collecteUemoa > 0 ? number_format(($destockeUemoa / $collecteUemoa) * 100, 1) : 0 }}%
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h6 class="text-warning">Solde Disponible AES {{ date('Y') }}</h6>
                                    <h3 class="fw-bold text-warning">
                                        {{ number_format($collecteAes - $destockeAes, 0, ',', ' ') }} FCFA
                                    </h3>
                                    <small class="text-muted">
                                        Taux règlement : {{ $collecteAes > 0 ? number_format(($destockeAes / $collecteAes) * 100, 1) : 0 }}%
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal État de Collecte -->
<div class="modal fade" id="modalEtatCollecte" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-coins me-2"></i>Générer l'État de Collecte
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEtatCollecte" method="GET" action="{{ route('pcs.destockages.pdf.etat-collecte') }}" target="_blank">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Programme <span class="text-danger">*</span></label>
                        <select name="programme" class="form-select" required>
                            <option value="UEMOA">UEMOA</option>
                            <option value="AES">AES</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                        <select name="annee" class="form-select" required>
                            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-download me-1"></i>Télécharger l'État
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal État Consolidé -->
<div class="modal fade" id="modalEtatConsolide" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-chart-bar me-2"></i>Générer l'État Consolidé
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEtatConsolide" method="GET" action="{{ route('pcs.destockages.pdf.etat-consolide') }}" target="_blank">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Programme <span class="text-danger">*</span></label>
                        <select name="programme" class="form-select" required>
                            <option value="UEMOA">UEMOA</option>
                            <option value="AES">AES</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                        <select name="annee" class="form-select" required>
                            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Télécharger l'État
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

