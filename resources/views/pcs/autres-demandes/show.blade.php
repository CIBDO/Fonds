@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title fw-bold text-danger">
                    <i class="fas fa-file-alt me-2"></i>Détail de la Demande
                </h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de la Demande</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Désignation</label>
                            <div class="fw-bold">{{ $demande->designation }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Statut</label>
                            <div>
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
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Montant Demandé</label>
                            <div class="fs-4 fw-bold text-primary">
                                {{ number_format($demande->montant, 0, ',', ' ') }} FCFA
                            </div>
                            @if($demande->montant_accord !== null || $demande->montant_verse > 0)
                                <label class="text-muted small mt-2">Montant versé / accordé</label>
                                <div class="fs-4 fw-bold text-success">
                                    {{ number_format($demande->montant_verse, 0, ',', ' ') }}
                                    / {{ number_format($demande->montant_accord ?? $demande->montant, 0, ',', ' ') }} FCFA
                                </div>
                                @if($demande->montant_restant_accord > 0 && $demande->statut !== 'valide')
                                    <div class="small text-warning mt-1">Reste à verser : {{ number_format($demande->montant_restant_accord, 0, ',', ' ') }} FCFA</div>
                                @endif
                                <div class="mt-1">
                                    <span class="badge bg-info">{{ $demande->pourcentage_accorde }}% du montant demandé</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Date de la demande</label>
                            <div class="fw-bold">{{ $demande->date_demande->format('d/m/Y') }}</div>
                            @if($demande->date_validation)
                                <label class="text-muted small mt-2">Date de validation</label>
                                <div class="fw-bold text-success">{{ $demande->date_validation->format('d/m/Y à H:i') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Poste</label>
                            <div><span class="badge bg-primary">{{ $demande->poste->nom }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Année</label>
                            <div class="fw-bold">{{ $demande->annee }}</div>
                        </div>
                    </div>

                    @if($demande->observation)
                    <div>
                        <label class="text-muted small">Observation</label>
                        <div class="alert alert-info mb-0">{{ $demande->observation }}</div>
                    </div>
                    @endif

                    @if($demande->preuve_paiement)
                    <div class="mt-3">
                        <label class="text-muted small">Preuve de paiement</label>
                        <div>
                            <a href="{{ route('pcs.autres-demandes.preuve', $demande) }}"
                               class="btn btn-outline-primary btn-sm"
                               target="_blank">
                                <i class="fas fa-paperclip me-1"></i>Télécharger le fichier
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($demande->motif_rejet)
                    <div class="mt-3">
                        <div class="alert alert-danger">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Motif du rejet :</strong>
                            <p class="mb-0 mt-2">{{ $demande->motif_rejet }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @include('pcs.autres-demandes.partials.echelons-liste', ['demande' => $demande])

            @if((auth()->user()->peut_valider_pcs || auth()->user()->hasRole('acct') || auth()->user()->hasRole('admin')) && ($demande->peutRecevoirVersement() || $demande->statut == 'soumis'))
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Actions de Validation</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        @if($demande->peutRecevoirVersement())
                        <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#validationModal">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ $demande->estPartiellementValidee() ? 'Enregistrer un versement' : 'Valider la demande' }}
                        </button>
                        @endif
                        @if($demande->statut == 'soumis')
                        <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#rejeterModal">
                            <i class="fas fa-times-circle me-1"></i>Rejeter
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Traçabilité</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <small class="text-muted">Saisi par</small>
                            <div class="fw-bold">{{ $demande->saisiPar->name }}</div>
                        </li>
                        @if($demande->validePar)
                        <li class="mb-3">
                            <small class="text-muted">Validé par</small>
                            <div class="fw-bold">{{ $demande->validePar->name }}</div>
                            @if($demande->date_validation)
                            <small class="text-muted">{{ $demande->date_validation->format('d/m/Y à H:i') }}</small>
                            @endif
                        </li>
                        @endif
                        <li>
                            <small class="text-muted">Créé le</small>
                            <div class="fw-bold">{{ $demande->created_at->format('d/m/Y à H:i') }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@if($demande->peutRecevoirVersement())
@include('pcs.autres-demandes.partials.modal-validation', ['demande' => $demande, 'modalId' => 'validationModal'])
@endif

<!-- Modal Rejeter -->
<div class="modal fade" id="rejeterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Rejeter la Demande</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pcs.autres-demandes.rejeter', $demande) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea name="motif_rejet" class="form-control" rows="5" required
                                  placeholder="Expliquez la raison du rejet..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-1"></i>Confirmer le Rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@include('pcs.autres-demandes.partials.scripts-echelons-validation')
@endpush
@endsection


