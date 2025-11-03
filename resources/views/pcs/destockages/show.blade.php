@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-file-invoice me-2"></i>Détail du Déstockage
                    </h3>
                    {{-- <p class="text-muted mb-0">{{ $destockage->reference_destockage }}</p> --}}
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.destockages.index') }}" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
                <a href="{{ route('pcs.destockages.pdf', $destockage) }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>Télécharger PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="text-muted small">Référence</label>
                    <div>
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-hashtag me-1"></i>{{ $destockage->reference_destockage }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-muted small">Programme</label>
                    <div>
                        <span class="badge bg-{{ $destockage->programme == 'UEMOA' ? 'primary' : 'warning' }} fs-6">
                            {{ $destockage->programme }}
                        </span>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-muted small">Période</label>
                    <div class="fw-bold">{{ $destockage->nom_mois }} {{ $destockage->periode_annee }}</div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-muted small">Date Déstockage</label>
                    <div class="fw-bold">
                        <i class="fas fa-calendar-check text-danger me-1"></i>
                        {{ \Carbon\Carbon::parse($destockage->date_destockage)->format('d/m/Y') }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="text-muted small">Montant Total Déstocké</label>
                    <div class="fw-bold text-success fs-5">
                        {{ number_format($destockage->montant_total_destocke, 0, ',', ' ') }} FCFA
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-muted small">Nombre de Postes</label>
                    <div>
                        <span class="badge bg-info text-dark fs-6">
                            {{ $destockage->postes->count() }} postes
                        </span>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-muted small">Statut</label>
                    <div>
                        @if($destockage->statut == 'valide')
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle"></i> Validé
                            </span>
                        @elseif($destockage->statut == 'brouillon')
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-edit"></i> Brouillon
                            </span>
                        @else
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-times-circle"></i> Annulé
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-muted small">Créé Par</label>
                    <div class="fw-bold">
                        <i class="fas fa-user text-primary me-1"></i>
                        {{ $destockage->creePar->name ?? 'N/A' }}
                    </div>
                </div>
            </div>
            @if($destockage->observation)
            <div class="row">
                <div class="col-md-12">
                    <label class="text-muted small">Observation</label>
                    <div class="alert alert-light mb-0">
                        <i class="fas fa-comment-alt text-muted me-2"></i>{{ $destockage->observation }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Détail par poste -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i>Détail par Poste</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-building"></i> Entité</th>
                            <th class="text-end"><i class="fas fa-arrow-up"></i> Montant Collecté</th>
                            <th class="text-end"><i class="fas fa-money-bill-wave"></i> Montant Déstocké</th>
                            <th class="text-end"><i class="fas fa-balance-scale"></i> Solde Avant</th>
                            <th class="text-end"><i class="fas fa-wallet"></i> Solde Après</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($destockage->postes as $posteDestockage)
                        <tr>
                            <td>
                                @if($posteDestockage->poste_id)
                                    <span class="badge bg-primary">Poste</span>
                                    <strong>{{ $posteDestockage->poste->nom ?? 'N/A' }}</strong>
                                @else
                                    <span class="badge bg-info">Bureau</span>
                                    <strong>{{ $posteDestockage->bureauDouane->libelle ?? 'N/A' }}</strong>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ number_format($posteDestockage->montant_collecte, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end fw-bold text-danger">
                                {{ number_format($posteDestockage->montant_destocke, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end fw-bold text-warning">
                                {{ number_format($posteDestockage->solde_avant, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end fw-bold {{ $posteDestockage->solde_apres > 0 ? 'text-success' : 'text-muted' }}">
                                {{ number_format($posteDestockage->solde_apres, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>TOTAUX</th>
                            <th class="text-end text-success">
                                {{ number_format($destockage->postes->sum('montant_collecte'), 0, ',', ' ') }} FCFA
                            </th>
                            <th class="text-end text-danger">
                                {{ number_format($destockage->postes->sum('montant_destocke'), 0, ',', ' ') }} FCFA
                            </th>
                            <th class="text-end text-warning">
                                {{ number_format($destockage->postes->sum('solde_avant'), 0, ',', ' ') }} FCFA
                            </th>
                            <th class="text-end text-success">
                                {{ number_format($destockage->postes->sum('solde_apres'), 0, ',', ' ') }} FCFA
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-up fa-2x mb-2"></i>
                    <h6 class="mb-1">Total Collecté</h6>
                    <h5 class="fw-bold">{{ number_format($destockage->postes->sum('montant_collecte'), 0, ',', ' ') }} FCFA</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-arrow-down fa-2x mb-2"></i>
                    <h6 class="mb-1">Total Déstocké</h6>
                    <h5 class="fw-bold">{{ number_format($destockage->postes->sum('montant_destocke'), 0, ',', ' ') }} FCFA</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-balance-scale fa-2x mb-2"></i>
                    <h6 class="mb-1">Taux Déstockage</h6>
                    <h5 class="fw-bold">
                        {{ $destockage->postes->sum('montant_collecte') > 0
                           ? number_format(($destockage->postes->sum('montant_destocke') / $destockage->postes->sum('montant_collecte')) * 100, 1)
                           : 0 }}%
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-wallet fa-2x mb-2"></i>
                    <h6 class="mb-1">Solde Restant</h6>
                    <h5 class="fw-bold">{{ number_format($destockage->postes->sum('solde_apres'), 0, ',', ' ') }} FCFA</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

