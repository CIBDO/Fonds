@extends('layouts.master')

@php
    $montantAccorde = $demande->montant_accord ?? $demande->montant;
    $aDesVersements = $demande->montant_accord !== null || $demande->montant_verse > 0;
    $surplusAccorde = $demande->montant_accord !== null && $demande->montant_accord > $demande->montant;
    $peutValider = auth()->user()->peut_valider_pcs || auth()->user()->hasRole('acct') || auth()->user()->hasRole('admin');
    $peutModifier = in_array($demande->statut, ['brouillon', 'soumis', 'rejete']) && $demande->saisi_par == auth()->id();
@endphp

@section('content')
<div class="content container-fluid">

    {{-- En-tête --}}
    <div class="page-header mb-4">
        <div class="row align-items-center g-3">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger mb-1">
                        <i class="fas fa-file-alt me-2"></i>Détail de la Demande
                    </h3>
                    <p class="text-muted mb-0">
                        <span class="badge bg-primary me-1">{{ $demande->poste->nom }}</span>
                        <span class="me-1">·</span>
                        {{ $demande->date_demande->format('d/m/Y') }}
                        <span class="me-1">·</span>
                        Année {{ $demande->annee }}
                    </p>
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                    @if($peutModifier)
                    <a href="{{ route('pcs.autres-demandes.edit', $demande) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>Modifier
                    </a>
                    @endif
                    @if($demande->preuve_paiement)
                    <a href="{{ route('pcs.autres-demandes.preuve', $demande) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-paperclip me-1"></i>Preuve
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Synthèse financière --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1"><i class="fas fa-hand-holding-usd me-1"></i>Montant demandé</div>
                    <div class="fs-5 fw-bold text-primary">{{ number_format($demande->montant, 0, ',', ' ') }} <small class="fs-6 fw-normal">FCFA</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1"><i class="fas fa-check-double me-1"></i>Montant versé</div>
                    <div class="fs-5 fw-bold text-success">
                        {{ number_format($demande->montant_verse_cumule, 0, ',', ' ') }}
                        <small class="fs-6 fw-normal">FCFA</small>
                    </div>
                    @if($aDesVersements)
                        <span class="badge bg-info mt-1">{{ $demande->pourcentage_accorde }}% du demandé</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1"><i class="fas fa-file-signature me-1"></i>Montant accordé</div>
                    <div class="fs-5 fw-bold text-dark">
                        {{ number_format($montantAccorde, 0, ',', ' ') }}
                        <small class="fs-6 fw-normal">FCFA</small>
                    </div>
                    @if($surplusAccorde)
                        <span class="badge bg-info mt-1">+{{ number_format($demande->montant_accord - $demande->montant, 0, ',', ' ') }} au-delà</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1"><i class="fas fa-flag me-1"></i>Statut</div>
                    <div class="mb-1">
                        @switch($demande->statut)
                            @case('brouillon')
                                <span class="badge bg-secondary fs-6">Brouillon</span>
                                @break
                            @case('soumis')
                                @if($demande->estPartiellementValidee())
                                    <span class="badge bg-warning text-dark fs-6">Partiellement validé</span>
                                @else
                                    <span class="badge bg-primary fs-6">Soumis</span>
                                @endif
                                @break
                            @case('valide')
                                <span class="badge bg-success fs-6">Validé</span>
                                @break
                            @case('rejete')
                                <span class="badge bg-danger fs-6">Rejeté</span>
                                @break
                        @endswitch
                    </div>
                    @if($demande->montant_restant_accord > 0 && $demande->statut !== 'valide')
                        <div class="small text-warning fw-semibold">
                            Reste : {{ number_format($demande->montant_restant_accord, 0, ',', ' ') }} FCFA
                        </div>
                    @elseif($demande->statut === 'valide' && $demande->montant_restant_accord <= 0)
                        <div class="small text-muted">Versements complémentaires possibles</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Colonne principale --}}
        <div class="col-lg-8">

            {{-- Informations générales --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations générales</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="text-muted small d-block">Désignation</label>
                            <div class="fw-bold fs-6">{{ $demande->designation }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small d-block">Poste</label>
                            <span class="badge bg-primary">{{ $demande->poste->nom }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small d-block">Date de la demande</label>
                            <div class="fw-bold">
                                <i class="fas fa-calendar text-danger me-1"></i>{{ $demande->date_demande->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small d-block">Année budgétaire</label>
                            <div class="fw-bold">{{ $demande->annee }}</div>
                        </div>
                        @if($demande->date_validation)
                        <div class="col-md-4">
                            <label class="text-muted small d-block">Dernière validation</label>
                            <div class="fw-bold text-success">
                                <i class="fas fa-check-circle me-1"></i>{{ $demande->date_validation->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Observation & pièces --}}
            @if($demande->observation || $demande->preuve_paiement)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fas fa-paperclip me-2"></i>Observations et pièces</h6>
                </div>
                <div class="card-body">
                    @if($demande->observation)
                    <div class="mb-3 {{ $demande->preuve_paiement ? 'pb-3 border-bottom' : '' }}">
                        <label class="text-muted small d-block">Observation</label>
                        <div class="alert alert-info mb-0 py-2">
                            <i class="fas fa-comment-alt me-2"></i>{{ $demande->observation }}
                        </div>
                    </div>
                    @endif
                    @if($demande->preuve_paiement)
                    <div>
                        <label class="text-muted small d-block">Preuve de paiement</label>
                        <a href="{{ route('pcs.autres-demandes.preuve', $demande) }}"
                           class="btn btn-outline-primary btn-sm"
                           target="_blank">
                            <i class="fas fa-download me-1"></i>Télécharger le fichier
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Motif de rejet --}}
            @if($demande->motif_rejet)
            <div class="card shadow-sm border-0 border-start border-danger border-4 mb-4">
                <div class="card-body">
                    <h6 class="text-danger mb-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>Motif du rejet
                    </h6>
                    <p class="mb-0 text-muted">{{ $demande->motif_rejet }}</p>
                </div>
            </div>
            @endif

            {{-- Historique des versements --}}
            @include('pcs.autres-demandes.partials.echelons-liste', ['demande' => $demande])
        </div>

        {{-- Colonne latérale --}}
        <div class="col-lg-4">

            {{-- Traçabilité --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Traçabilité</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-light text-secondary me-3 mt-1"><i class="fas fa-user-edit"></i></span>
                                <div>
                                    <small class="text-muted d-block">Saisi par</small>
                                    <div class="fw-bold">{{ $demande->saisiPar->name }}</div>
                                    <small class="text-muted">{{ $demande->created_at->format('d/m/Y à H:i') }}</small>
                                </div>
                            </div>
                        </li>
                        @if($demande->validePar)
                        <li class="list-group-item">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-light text-success me-3 mt-1"><i class="fas fa-user-check"></i></span>
                                <div>
                                    <small class="text-muted d-block">Dernier traitement par</small>
                                    <div class="fw-bold">{{ $demande->validePar->name }}</div>
                                    @if($demande->date_validation)
                                    <small class="text-muted">{{ $demande->date_validation->format('d/m/Y à H:i') }}</small>
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endif
                        @if($demande->echelons->isNotEmpty())
                        <li class="list-group-item">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-light text-primary me-3 mt-1"><i class="fas fa-receipt"></i></span>
                                <div>
                                    <small class="text-muted d-block">Versements</small>
                                    <div class="fw-bold">{{ $demande->echelons->count() }} enregistrement(s)</div>
                                </div>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Actions validateur --}}
            @if($peutValider && in_array($demande->statut, ['soumis', 'valide']))
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    @if($demande->peutRecevoirVersement())
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#validationModal">
                        <i class="fas fa-check-circle me-1"></i>
                        @if($demande->statut === 'valide')
                            Versement supplémentaire
                        @elseif($demande->estPartiellementValidee())
                            Enregistrer un versement
                        @else
                            Valider la demande
                        @endif
                    </button>
                    @endif
                    @if($demande->statut === 'soumis')
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejeterModal">
                        <i class="fas fa-times-circle me-1"></i>Rejeter la demande
                    </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modales (hors flux principal) --}}
@if($demande->peutRecevoirVersement())
    @include('pcs.autres-demandes.partials.modal-validation', ['demande' => $demande, 'modalId' => 'validationModal'])
@endif

@if($peutValider && $demande->statut === 'soumis')
<div class="modal fade" id="rejeterModal" tabindex="-1" aria-labelledby="rejeterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejeterModalLabel">
                    <i class="fas fa-times-circle me-2"></i>Rejeter la demande
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('pcs.autres-demandes.rejeter', $demande) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-light border mb-3 small">
                        <strong>{{ $demande->poste->nom }}</strong> —
                        {{ number_format($demande->montant, 0, ',', ' ') }} FCFA
                    </div>
                    <label class="form-label fw-bold">Motif du rejet <span class="text-danger">*</span></label>
                    <textarea name="motif_rejet" class="form-control" rows="4" required
                              placeholder="Expliquez la raison du rejet (minimum 10 caractères)..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-1"></i>Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
@include('pcs.autres-demandes.partials.scripts-echelons-validation')
@endpush
@endsection
