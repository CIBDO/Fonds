@extends('layouts.master')

@section('title', 'Paiement FNL')

@section('content')
@php
    $statutBadge = match ($paiement->statut) {
        'valide' => ['bg-label-success', 'Validé'],
        'rejete' => ['bg-label-danger', 'Rejeté'],
        default => ['bg-label-warning', 'Soumis'],
    };
@endphp

<div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
    <a href="{{ route('fnl.paiements.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
    @if($peutModifier)
        <a href="{{ route('fnl.paiements.edit', $paiement) }}" class="btn btn-primary btn-sm">
            <i class="icon-base ti tabler-edit me-1"></i>Corriger
        </a>
    @endif
    <a href="{{ route('fnl.paiements.situation-mensuelle', ['mois' => $paiement->mois, 'annee' => $paiement->annee, 'poste_id' => $paiement->poste_id]) }}"
       class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-printer me-1"></i>Imprimer l'état
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <x-vuexy.card title="Paiement FNL — {{ $paiement->poste->nom ?? 'Poste' }}" icon="tabler-home" class="mb-4">
            <x-slot:header>
                <span class="badge {{ $statutBadge[0] }}">{{ $statutBadge[1] }}</span>
            </x-slot:header>

            <div class="rounded-3 bg-label-primary p-4 mb-4">
                <div class="text-body-secondary small mb-1">Retenue FNL</div>
                <div class="fs-2 fw-semibold lh-1 mb-0">
                    {{ number_format($paiement->retenue_fnl, 0, ',', ' ') }}
                    <span class="fs-6 fw-normal text-body-secondary">FCFA</span>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-body-secondary small mb-1">Période</div>
                        <div class="fw-medium">{{ $paiement->nom_mois }} {{ $paiement->annee }}</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-body-secondary small mb-1">Date de paiement</div>
                        <div class="fw-medium">{{ $paiement->date_paiement?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-body-secondary small mb-1">Référence</div>
                        <div class="fw-medium">{{ $paiement->reference_paiement ?: '—' }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3">
                        <div class="text-body-secondary small mb-1">Observation</div>
                        <div class="fw-medium mb-0">{{ $paiement->observation ?: '—' }}</div>
                    </div>
                </div>
            </div>

            @if($paiement->statut === 'rejete' && $paiement->motif_rejet)
                <div class="alert alert-warning mt-4 mb-0">
                    <strong>Motif du rejet :</strong> {{ $paiement->motif_rejet }}
                </div>
            @endif
        </x-vuexy.card>

        @if($paiement->preuve_paiement)
        <x-vuexy.card title="Preuve de paiement" icon="tabler-paperclip">
            @include('partials.pcs.preuve-paiement', [
                'model' => $paiement,
                'downloadRoute' => 'fnl.paiements.preuve',
            ])
        </x-vuexy.card>
        @endif
    </div>

    <div class="col-lg-4">
        <x-vuexy.card title="Suivi" icon="tabler-history" class="mb-4">
            <ul class="list-unstyled mb-0">
                <li class="d-flex justify-content-between gap-3 py-2 border-bottom">
                    <span class="text-body-secondary">Saisi par</span>
                    <span class="fw-medium text-end">{{ $paiement->saisiPar->name ?? '—' }}</span>
                </li>
                <li class="d-flex justify-content-between gap-3 py-2 border-bottom">
                    <span class="text-body-secondary">Date de saisie</span>
                    <span class="fw-medium text-end">{{ $paiement->date_saisie?->format('d/m/Y H:i') ?? '—' }}</span>
                </li>
                <li class="d-flex justify-content-between gap-3 py-2 {{ $paiement->date_validation ? 'border-bottom' : '' }}">
                    <span class="text-body-secondary">Décision ACCD</span>
                    <span class="fw-medium text-end">{{ $paiement->validePar->name ?? 'En attente' }}</span>
                </li>
                @if($paiement->date_validation)
                <li class="d-flex justify-content-between gap-3 py-2">
                    <span class="text-body-secondary">Date de décision</span>
                    <span class="fw-medium text-end">{{ $paiement->date_validation->format('d/m/Y H:i') }}</span>
                </li>
                @endif
            </ul>
        </x-vuexy.card>

        @if($peutValider)
        <x-vuexy.card title="Validation ACCD" icon="tabler-checkbox">
            <form method="POST" action="{{ route('fnl.paiements.valider', $paiement) }}" class="mb-3">
                @csrf
                <button type="submit" class="btn btn-success w-100">
                    <i class="icon-base ti tabler-check me-1"></i>Valider
                </button>
            </form>
            <form method="POST" action="{{ route('fnl.paiements.rejeter', $paiement) }}">
                @csrf
                <label class="form-label">Motif du rejet</label>
                <textarea name="motif_rejet" class="form-control mb-2" rows="3" required>{{ old('motif_rejet') }}</textarea>
                @error('motif_rejet')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror
                <button type="submit" class="btn btn-label-danger w-100">
                    <i class="icon-base ti tabler-x me-1"></i>Rejeter
                </button>
            </form>
        </x-vuexy.card>
        @endif
    </div>
</div>
@endsection
