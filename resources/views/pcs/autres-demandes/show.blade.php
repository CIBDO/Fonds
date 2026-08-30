@extends('layouts.master')

@php
    $montantAccorde = $demande->montant_accord ?? $demande->montant;
    $aDesVersements = $demande->montant_accord !== null || $demande->montant_verse > 0;
    $surplusAccorde = $demande->montant_accord !== null && $demande->montant_accord > $demande->montant;
    $peutValider = auth()->user()->peut_valider_pcs || auth()->user()->hasRole('acct') || auth()->user()->hasRole('admin');
    $peutModifier = in_array($demande->statut, ['brouillon', 'soumis', 'rejete']) && $demande->saisi_par == auth()->id();
@endphp

@section('title', 'Détail Autre Demande')

@section('content')
<div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
    <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
    @if($peutModifier)
    <a href="{{ route('pcs.autres-demandes.edit', $demande) }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-edit me-1"></i>Modifier
    </a>
    @endif
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <x-vuexy.card>
            <div class="text-body-secondary small mb-1"><i class="icon-base ti tabler-hand-stop me-1"></i>Montant demandé</div>
            <div class="fs-5 fw-bold text-primary">{{ number_format($demande->montant, 0, ',', ' ') }} <small class="fs-6 fw-normal">FCFA</small></div>
        </x-vuexy.card>
    </div>
    <div class="col-md-6 col-xl-3">
        <x-vuexy.card>
            <div class="text-body-secondary small mb-1"><i class="icon-base ti tabler-checks me-1"></i>Montant versé</div>
            <div class="fs-5 fw-bold text-success">
                {{ number_format($demande->montant_verse_cumule, 0, ',', ' ') }}
                <small class="fs-6 fw-normal">FCFA</small>
            </div>
            @if($aDesVersements)
                <span class="badge bg-label-info mt-2">{{ $demande->pourcentage_accorde }}% du demandé</span>
            @endif
        </x-vuexy.card>
    </div>
    <div class="col-md-6 col-xl-3">
        <x-vuexy.card>
            <div class="text-body-secondary small mb-1"><i class="icon-base ti tabler-file-certificate me-1"></i>Montant accordé</div>
            <div class="fs-5 fw-bold">
                {{ number_format($montantAccorde, 0, ',', ' ') }}
                <small class="fs-6 fw-normal">FCFA</small>
            </div>
            @if($surplusAccorde)
                <span class="badge bg-label-info mt-2">+{{ number_format($demande->montant_accord - $demande->montant, 0, ',', ' ') }} au-delà</span>
            @endif
        </x-vuexy.card>
    </div>
    <div class="col-md-6 col-xl-3">
        <x-vuexy.card>
            <div class="text-body-secondary small mb-1"><i class="icon-base ti tabler-flag me-1"></i>Statut</div>
            <div class="mb-1">
                @include('partials.pcs.status-badge', [
                    'statut' => $demande->statut,
                    'partiel' => $demande->estPartiellementValidee(),
                ])
            </div>
            @if($demande->montant_restant_accord > 0 && $demande->statut !== 'valide')
                <div class="small text-warning fw-semibold">
                    Reste : {{ number_format($demande->montant_restant_accord, 0, ',', ' ') }} FCFA
                </div>
            @elseif($demande->statut === 'valide' && $demande->montant_restant_accord <= 0)
                <div class="small text-body-secondary">Versements complémentaires possibles</div>
            @endif
        </x-vuexy.card>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <x-vuexy.card title="Informations générales" icon="tabler-info-circle" class="mb-4">
            <div class="row g-3">
                <div class="col-12">
                    <label class="text-body-secondary small d-block">Désignation</label>
                    <div class="fw-medium fs-6">{{ $demande->designation }}</div>
                </div>
                <div class="col-md-4">
                    <label class="text-body-secondary small d-block">Poste</label>
                    <span class="badge bg-label-primary">{{ $demande->poste->nom }}</span>
                </div>
                <div class="col-md-4">
                    <label class="text-body-secondary small d-block">Date de la demande</label>
                    <div class="fw-medium">
                        <i class="icon-base ti tabler-calendar me-1"></i>{{ $demande->date_demande->format('d/m/Y') }}
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-body-secondary small d-block">Année budgétaire</label>
                    <div class="fw-medium">{{ $demande->annee }}</div>
                </div>
                @if($demande->date_validation)
                <div class="col-md-4">
                    <label class="text-body-secondary small d-block">Dernière validation</label>
                    <div class="fw-medium text-success">
                        <i class="icon-base ti tabler-circle-check me-1"></i>{{ $demande->date_validation->format('d/m/Y à H:i') }}
                    </div>
                </div>
                @endif
            </div>
        </x-vuexy.card>

        @if($demande->observation || $demande->preuve_paiement)
        <x-vuexy.card title="Observations et pièces" icon="tabler-paperclip" class="mb-4">
            @if($demande->observation)
            <div class="mb-3 {{ $demande->preuve_paiement ? 'pb-3 border-bottom' : '' }}">
                <label class="text-body-secondary small d-block">Observation</label>
            <x-vuexy.alert type="info" class="mb-0 py-2 border-0">
                <span><i class="icon-base ti tabler-message me-2"></i>{{ $demande->observation }}</span>
            </x-vuexy.alert>
            </div>
            @endif
            @if($demande->preuve_paiement)
            <div>
                <label class="text-body-secondary small d-block mb-2">Preuve de paiement</label>
                @include('partials.pcs.preuve-paiement', [
                    'model' => $demande,
                    'downloadRoute' => 'pcs.autres-demandes.preuve',
                    'showPreview' => true,
                ])
            </div>
            @endif
        </x-vuexy.card>
        @endif

        @if($demande->motif_rejet)
        <x-vuexy.card class="mb-4 border-start border-danger border-3">
            <h6 class="text-danger mb-2">
                <i class="icon-base ti tabler-alert-triangle me-2"></i>Motif du rejet
            </h6>
            <p class="mb-0 text-body-secondary">{{ $demande->motif_rejet }}</p>
        </x-vuexy.card>
        @endif

        @include('pcs.autres-demandes.partials.echelons-liste', ['demande' => $demande])
    </div>

    <div class="col-lg-4">
        <x-vuexy.card title="Traçabilité" icon="tabler-history" class="mb-4">
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-0">
                    <div class="d-flex align-items-start">
                        <span class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded bg-label-secondary"><i class="icon-base ti tabler-user-edit"></i></span>
                        </span>
                        <div>
                            <small class="text-body-secondary d-block">Saisi par</small>
                            <div class="fw-medium">{{ $demande->saisiPar->name }}</div>
                            <small class="text-body-secondary">{{ $demande->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    </div>
                </li>
                @if($demande->validePar)
                <li class="list-group-item px-0">
                    <div class="d-flex align-items-start">
                        <span class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-user-check"></i></span>
                        </span>
                        <div>
                            <small class="text-body-secondary d-block">Dernier traitement par</small>
                            <div class="fw-medium">{{ $demande->validePar->name }}</div>
                            @if($demande->date_validation)
                            <small class="text-body-secondary">{{ $demande->date_validation->format('d/m/Y à H:i') }}</small>
                            @endif
                        </div>
                    </div>
                </li>
                @endif
                @if($demande->echelons->isNotEmpty())
                <li class="list-group-item px-0">
                    <div class="d-flex align-items-start">
                        <span class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-receipt"></i></span>
                        </span>
                        <div>
                            <small class="text-body-secondary d-block">Versements</small>
                            <div class="fw-medium">{{ $demande->echelons->count() }} enregistrement(s)</div>
                        </div>
                    </div>
                </li>
                @endif
            </ul>
        </x-vuexy.card>

        @if($peutValider && in_array($demande->statut, ['soumis', 'valide']))
        <x-vuexy.card title="Actions" icon="tabler-list-check">
            <div class="d-grid gap-2">
                @if($demande->peutRecevoirVersement())
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#validationModal">
                    <i class="icon-base ti tabler-circle-check me-1"></i>
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
                    <i class="icon-base ti tabler-circle-x me-1"></i>Rejeter la demande
                </button>
                @endif
            </div>
        </x-vuexy.card>
        @endif
    </div>
</div>

@if($demande->peutRecevoirVersement())
    @include('pcs.autres-demandes.partials.modal-validation', ['demande' => $demande, 'modalId' => 'validationModal'])
@endif

@if($peutValider && $demande->statut === 'soumis')
<div class="modal fade" id="rejeterModal" tabindex="-1" aria-labelledby="rejeterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejeterModalLabel">
                    <i class="icon-base ti tabler-circle-x me-2 text-danger"></i>Rejeter la demande
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
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
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="icon-base ti tabler-circle-x me-1"></i>Confirmer le rejet
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
