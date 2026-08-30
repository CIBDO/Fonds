@extends('layouts.master')

@section('title', 'Détail Cotisation TRIE')

@section('content')
@php
    $user = Auth::user();
    $peutModifier = in_array($user->role, ['admin', 'acct']) || $user->poste_id == $cotisation->poste_id;
@endphp

<div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
    <a href="{{ route('trie.cotisations.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
    @if($peutModifier)
    <a href="{{ route('trie.cotisations.edit', $cotisation) }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-edit me-1"></i>Modifier
    </a>
    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalSuppression">
        <i class="icon-base ti tabler-trash me-1"></i>Supprimer
    </button>
    @endif
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <x-vuexy.card title="Informations générales" icon="tabler-info-circle" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-body-secondary small">Période</label>
                    <div class="fw-medium">{{ $cotisation->nom_mois }} {{ $cotisation->annee }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-body-secondary small">Statut</label>
                    <div><span class="badge bg-label-success">Validé</span></div>
                </div>
                <div class="col-md-6">
                    <label class="text-body-secondary small">Poste</label>
                    <div><span class="badge bg-label-primary">{{ $cotisation->poste->nom }}</span></div>
                </div>
                <div class="col-md-6">
                    <label class="text-body-secondary small">Bureau</label>
                    <div class="fw-medium">
                        <span class="text-primary">{{ $cotisation->bureauTrie->code_bureau }}</span>
                        <span class="text-body-secondary"> — {{ $cotisation->bureauTrie->nom_bureau }}</span>
                    </div>
                </div>
            </div>
        </x-vuexy.card>

        <x-vuexy.card title="Détail des montants" icon="tabler-coins" class="mb-4">
            <div class="row g-3 text-center">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <i class="icon-base ti tabler-cash text-primary icon-lg mb-2"></i>
                        <p class="text-body-secondary small mb-1">Cotisation courante</p>
                        <p class="fw-semibold mb-0">{{ number_format($cotisation->montant_cotisation_courante, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <i class="icon-base ti tabler-refresh text-warning icon-lg mb-2"></i>
                        <p class="text-body-secondary small mb-1">Apurement</p>
                        <p class="fw-semibold mb-0">{{ number_format($cotisation->montant_apurement, 0, ',', ' ') }} FCFA</p>
                        @if($cotisation->detail_apurement)
                        <small class="text-body-secondary">{{ $cotisation->detail_apurement }}</small>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <i class="icon-base ti tabler-coins text-success icon-lg mb-2"></i>
                        <p class="text-body-secondary small mb-1">Montant total</p>
                        <p class="fw-semibold mb-0">{{ number_format($cotisation->montant_total, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
        </x-vuexy.card>

        <x-vuexy.card title="Informations de paiement" icon="tabler-credit-card" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="text-body-secondary small">Mode de paiement</label>
                    <div>
                        @if($cotisation->mode_paiement)
                            <span class="badge bg-label-secondary">{{ ucfirst($cotisation->mode_paiement) }}</span>
                        @else
                            <span class="text-body-secondary">Non renseigné</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-body-secondary small">Référence</label>
                    <div class="fw-medium">{{ $cotisation->reference_paiement ?? '—' }}</div>
                </div>
                <div class="col-md-4">
                    <label class="text-body-secondary small">Date de paiement</label>
                    <div class="fw-medium">{{ $cotisation->date_paiement?->format('d/m/Y') ?? '—' }}</div>
                </div>
            </div>
            @if($cotisation->observation)
            <div class="alert alert-info mt-3 mb-0">
                <i class="icon-base ti tabler-message me-2"></i>{{ $cotisation->observation }}
            </div>
            @endif
        </x-vuexy.card>

        @if($cotisation->preuve_paiement)
        <x-vuexy.card title="Preuve de paiement" icon="tabler-paperclip">
            @include('partials.pcs.preuve-paiement', [
                'model' => $cotisation,
                'downloadRoute' => 'trie.cotisations.preuve',
            ])
        </x-vuexy.card>
        @endif
    </div>

    <div class="col-lg-4">
        <x-vuexy.card title="Traçabilité" icon="tabler-history">
            <ul class="list-unstyled mb-0">
                <li class="mb-3">
                    <small class="text-body-secondary">Saisi par</small>
                    <div class="fw-medium">{{ $cotisation->saisiPar->name ?? 'N/A' }}</div>
                    <small class="text-body-secondary">{{ $cotisation->date_saisie->format('d/m/Y à H:i') }}</small>
                </li>
                @if($cotisation->validePar)
                <li>
                    <small class="text-body-secondary">Validé par</small>
                    <div class="fw-medium">{{ $cotisation->validePar->name }}</div>
                    @if($cotisation->date_validation)
                    <small class="text-body-secondary">{{ $cotisation->date_validation->format('d/m/Y à H:i') }}</small>
                    @endif
                </li>
                @endif
            </ul>
        </x-vuexy.card>
    </div>
</div>

<div class="modal fade" id="modalSuppression" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('trie.cotisations.destroy', $cotisation) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="icon-base ti tabler-alert-triangle me-2"></i>Confirmer la suppression
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <i class="icon-base ti tabler-alert-triangle me-2"></i>
                        <strong>Attention !</strong> Cette action est irréversible.
                    </div>
                    <p>Êtes-vous sûr de vouloir supprimer cette cotisation ?</p>
                    <div class="border rounded p-3">
                        <p class="mb-1"><strong>Période :</strong> {{ $cotisation->nom_mois }} {{ $cotisation->annee }}</p>
                        <p class="mb-1"><strong>Bureau :</strong> {{ $cotisation->bureauTrie->nom_complet }}</p>
                        <p class="mb-0"><strong>Montant total :</strong> <span class="text-danger">{{ number_format($cotisation->montant_total, 0, ',', ' ') }} FCFA</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="icon-base ti tabler-trash me-1"></i>Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
