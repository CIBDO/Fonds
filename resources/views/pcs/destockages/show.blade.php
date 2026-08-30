@extends('layouts.master')

@section('title', 'Détail Destockage PCS')

@section('content')
<div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
    <a href="{{ route('pcs.destockages.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
    <a href="{{ route('pcs.destockages.pdf', $destockage) }}" class="btn btn-primary btn-sm" target="_blank">
        <i class="icon-base ti tabler-file-type-pdf me-1"></i>Télécharger PDF
    </a>
</div>

<x-vuexy.card title="Informations Générales" icon="tabler-info-circle" class="mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="text-body-secondary small">Référence</label>
            <div>
                <span class="badge bg-label-primary fs-6">
                    <i class="icon-base ti tabler-hash me-1"></i>{{ $destockage->reference_destockage }}
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <label class="text-body-secondary small">Programme</label>
            <div>
                <span class="badge bg-label-{{ $destockage->programme == 'UEMOA' ? 'primary' : 'warning' }} fs-6">
                    {{ $destockage->programme }}
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <label class="text-body-secondary small">Période</label>
            <div class="fw-medium">{{ $destockage->nom_mois }} {{ $destockage->periode_annee }}</div>
        </div>
        <div class="col-md-3">
            <label class="text-body-secondary small">Date Règlement</label>
            <div class="fw-medium">
                <i class="icon-base ti tabler-calendar-check me-1"></i>
                {{ \Carbon\Carbon::parse($destockage->date_destockage)->format('d/m/Y') }}
            </div>
        </div>
        <div class="col-md-3">
            <label class="text-body-secondary small">Montant Total Règlement</label>
            <div class="fw-bold text-success fs-5">
                {{ number_format($destockage->montant_total_destocke, 0, ',', ' ') }} FCFA
            </div>
        </div>
        <div class="col-md-3">
            <label class="text-body-secondary small">Nombre de Postes</label>
            <div>
                <span class="badge bg-label-info fs-6">{{ $destockage->postes->count() }} postes</span>
            </div>
        </div>
        <div class="col-md-3">
            <label class="text-body-secondary small">Statut</label>
            <div>@include('partials.pcs.status-badge', ['statut' => $destockage->statut])</div>
        </div>
        <div class="col-md-3">
            <label class="text-body-secondary small">Créé Par</label>
            <div class="fw-medium">
                <i class="icon-base ti tabler-user me-1"></i>
                {{ $destockage->creePar->name ?? 'N/A' }}
            </div>
        </div>
        @if($destockage->observation)
        <div class="col-12">
            <label class="text-body-secondary small">Observation</label>
            <div class="alert alert-light border mb-0">
                <i class="icon-base ti tabler-message me-2"></i>{{ $destockage->observation }}
            </div>
        </div>
        @endif
    </div>
</x-vuexy.card>

<x-vuexy.card title="Détail par Poste" icon="tabler-list" class="mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="icon-base ti tabler-building me-1"></i>Entité</th>
                    <th class="text-end"><i class="icon-base ti tabler-arrow-up me-1"></i>Montant Collecté</th>
                    <th class="text-end"><i class="icon-base ti tabler-currency-franc me-1"></i>Montant Déstocké</th>
                    <th class="text-end"><i class="icon-base ti tabler-scale me-1"></i>Solde Avant</th>
                    <th class="text-end"><i class="icon-base ti tabler-wallet me-1"></i>Solde Après</th>
                </tr>
            </thead>
            <tbody>
                @foreach($destockage->postes as $posteDestockage)
                <tr>
                    <td>
                        @if($posteDestockage->poste_id)
                            <span class="badge bg-label-primary me-1">Poste</span>
                            <strong>{{ $posteDestockage->poste->nom ?? 'N/A' }}</strong>
                        @else
                            <span class="badge bg-label-info me-1">Bureau</span>
                            <strong>{{ $posteDestockage->bureauDouane->libelle ?? 'N/A' }}</strong>
                        @endif
                    </td>
                    <td class="text-end fw-medium text-success">
                        {{ number_format($posteDestockage->montant_collecte, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="text-end fw-medium text-danger">
                        {{ number_format($posteDestockage->montant_destocke, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="text-end fw-medium text-warning">
                        {{ number_format($posteDestockage->solde_avant, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="text-end fw-medium {{ $posteDestockage->solde_apres > 0 ? 'text-success' : 'text-body-secondary' }}">
                        {{ number_format($posteDestockage->solde_apres, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th>TOTAUX</th>
                    <th class="text-end text-success">{{ number_format($destockage->postes->sum('montant_collecte'), 0, ',', ' ') }} FCFA</th>
                    <th class="text-end text-danger">{{ number_format($destockage->postes->sum('montant_destocke'), 0, ',', ' ') }} FCFA</th>
                    <th class="text-end text-warning">{{ number_format($destockage->postes->sum('solde_avant'), 0, ',', ' ') }} FCFA</th>
                    <th class="text-end text-success">{{ number_format($destockage->postes->sum('solde_apres'), 0, ',', ' ') }} FCFA</th>
                </tr>
            </tfoot>
        </table>
    </div>
</x-vuexy.card>

<div class="row g-3">
    <div class="col-md-3">
        <x-vuexy.card class="bg-label-success text-center h-100">
            <i class="icon-base ti tabler-arrow-up icon-32px text-success mb-2"></i>
            <h6 class="mb-1">Total Collecté</h6>
            <h5 class="fw-bold text-success mb-0">{{ number_format($destockage->postes->sum('montant_collecte'), 0, ',', ' ') }} FCFA</h5>
        </x-vuexy.card>
    </div>
    <div class="col-md-3">
        <x-vuexy.card class="bg-label-danger text-center h-100">
            <i class="icon-base ti tabler-arrow-down icon-32px text-danger mb-2"></i>
            <h6 class="mb-1">Total Règlement</h6>
            <h5 class="fw-bold text-danger mb-0">{{ number_format($destockage->postes->sum('montant_destocke'), 0, ',', ' ') }} FCFA</h5>
        </x-vuexy.card>
    </div>
    <div class="col-md-3">
        <x-vuexy.card class="bg-label-warning text-center h-100">
            <i class="icon-base ti tabler-scale icon-32px text-warning mb-2"></i>
            <h6 class="mb-1">Taux Règlement</h6>
            <h5 class="fw-bold text-warning mb-0">
                {{ $destockage->postes->sum('montant_collecte') > 0
                   ? number_format(($destockage->postes->sum('montant_destocke') / $destockage->postes->sum('montant_collecte')) * 100, 1)
                   : 0 }}%
            </h5>
        </x-vuexy.card>
    </div>
    <div class="col-md-3">
        <x-vuexy.card class="bg-label-info text-center h-100">
            <i class="icon-base ti tabler-wallet icon-32px text-info mb-2"></i>
            <h6 class="mb-1">Solde Restant</h6>
            <h5 class="fw-bold text-info mb-0">{{ number_format($destockage->postes->sum('solde_apres'), 0, ',', ' ') }} FCFA</h5>
        </x-vuexy.card>
    </div>
</div>
@endsection
