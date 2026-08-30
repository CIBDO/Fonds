@extends('layouts.master')

@section('title', 'Détail Déclaration PCS')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('pcs.declarations.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <x-vuexy.card title="Informations générales" icon="tabler-info-circle" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-body-secondary small">Statut</label>
                    <div>@include('partials.pcs.status-badge', ['statut' => $declaration->statut])</div>
                </div>
                <div class="col-md-6">
                    <label class="text-body-secondary small">Programme</label>
                    <div>
                        <span class="badge bg-label-{{ $declaration->programme === 'UEMOA' ? 'success' : 'warning' }}">
                            {{ $declaration->programme }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-body-secondary small">Référence</label>
                    <div class="fw-medium">
                        @if($declaration->reference)
                            <i class="icon-base ti tabler-hash me-1 text-body-secondary"></i>{{ $declaration->reference }}
                        @else
                            <span class="text-body-secondary">Non renseignée</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-body-secondary small">Période</label>
                    <div class="fw-medium">
                        {{ \Carbon\Carbon::create()->month((int)$declaration->mois)->locale('fr')->translatedFormat('F') }} {{ $declaration->annee }}
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="text-body-secondary small">Type d'entité</label>
                    <div>
                        @if($declaration->poste_id)
                            <span class="badge bg-label-primary">{{ $declaration->poste->nom }}</span>
                        @else
                            <span class="badge bg-label-info">{{ $declaration->bureauDouane->libelle }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-vuexy.card>

        <x-vuexy.card title="Montants déclarés" icon="tabler-cash" class="mb-4">
            <div class="row g-3 text-center">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <i class="icon-base ti tabler-trending-up text-success icon-lg mb-2"></i>
                        <p class="text-body-secondary small mb-1">Recouvrement</p>
                        <p class="fw-semibold mb-0">{{ number_format($declaration->montant_recouvrement, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <i class="icon-base ti tabler-trending-down text-primary icon-lg mb-2"></i>
                        <p class="text-body-secondary small mb-1">Reversement</p>
                        <p class="fw-semibold mb-0">{{ number_format($declaration->montant_reversement, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <i class="icon-base ti tabler-scale text-warning icon-lg mb-2"></i>
                        <p class="text-body-secondary small mb-1">Reste à reverser</p>
                        <p class="fw-semibold mb-0">{{ number_format($declaration->reste_a_reverser, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>

            @if($declaration->observation)
            <div class="alert alert-info mt-3 mb-0">
                <i class="icon-base ti tabler-message me-2"></i>{{ $declaration->observation }}
            </div>
            @endif

            @if($declaration->motif_rejet)
            <div class="alert alert-danger mt-3 mb-0">
                <strong><i class="icon-base ti tabler-alert-triangle me-1"></i>Motif du rejet</strong>
                <p class="mb-0 mt-2">{{ $declaration->motif_rejet }}</p>
            </div>
            @endif
        </x-vuexy.card>

        @if($declaration->preuve_paiement)
        <x-vuexy.card title="Preuve de paiement" icon="tabler-paperclip" class="mb-4">
            @include('partials.pcs.preuve-paiement', [
                'model' => $declaration,
                'downloadRoute' => 'pcs.declarations.preuve',
            ])
        </x-vuexy.card>
        @endif

        @if($declaration->piecesJointes->count() > 0)
        <x-vuexy.card title="Pièces jointes ({{ $declaration->piecesJointes->count() }})" icon="tabler-files">
            <div class="list-group list-group-flush">
                @foreach($declaration->piecesJointes as $piece)
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <i class="icon-base ti tabler-file-type-pdf text-danger me-2"></i>
                        <span class="fw-medium">{{ $piece->nom_original }}</span>
                        <small class="text-body-secondary">({{ $piece->taille_formatee }})</small>
                    </div>
                </div>
                @endforeach
            </div>
        </x-vuexy.card>
        @endif
    </div>

    <div class="col-lg-4">
        <x-vuexy.card title="Traçabilité" icon="tabler-history" class="mb-4">
            <ul class="list-unstyled mb-0">
                <li class="mb-3">
                    <small class="text-body-secondary">Saisi par</small>
                    <div class="fw-medium">{{ $declaration->saisiPar->name }}</div>
                    <small class="text-body-secondary">{{ $declaration->date_saisie->format('d/m/Y à H:i') }}</small>
                </li>
                @if($declaration->date_soumission)
                <li class="mb-3">
                    <small class="text-body-secondary">Soumis le</small>
                    <div class="fw-medium">{{ $declaration->date_soumission->format('d/m/Y à H:i') }}</div>
                </li>
                @endif
                @if($declaration->validePar)
                <li>
                    <small class="text-body-secondary">Validé par</small>
                    <div class="fw-medium">{{ $declaration->validePar->name }}</div>
                    @if($declaration->date_validation)
                    <small class="text-body-secondary">{{ $declaration->date_validation->format('d/m/Y à H:i') }}</small>
                    @endif
                </li>
                @endif
            </ul>
        </x-vuexy.card>

        @if($declaration->historiqueStatuts->count() > 0)
        <x-vuexy.card title="Historique" icon="tabler-timeline">
            @foreach($declaration->historiqueStatuts->sortByDesc('date_changement') as $historique)
            <div class="mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                <div class="fw-medium">{{ ucfirst($historique->nouveau_statut) }}</div>
                <small class="text-body-secondary d-block">
                    {{ $historique->utilisateur->name }} · {{ $historique->date_changement->format('d/m/Y à H:i') }}
                </small>
                @if($historique->commentaire)
                <em class="small text-body-secondary">{{ $historique->commentaire }}</em>
                @endif
            </div>
            @endforeach
        </x-vuexy.card>
        @endif
    </div>
</div>
@endsection
