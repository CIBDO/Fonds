@extends('layouts.master')

@section('title', 'États Destockages PCS')

@section('content')
<x-vuexy.card title="Type d'état à générer" icon="tabler-file-text" class="mb-4">
    <div class="row g-4">
        <div class="col-lg-6">
            <x-vuexy.card class="border border-success h-100 text-center">
                <i class="icon-base ti tabler-coins icon-48px text-success mb-3"></i>
                <h5 class="fw-bold">État de Collecte</h5>
                <p class="text-body-secondary">
                    Affiche les fonds collectés par poste pour un programme et une année donnés,
                    avec les montants déstockés et soldes disponibles.
                </p>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEtatCollecte">
                    <i class="icon-base ti tabler-download me-1"></i>Générer l'État de Collecte
                </button>
            </x-vuexy.card>
        </div>
        <div class="col-lg-6">
            <x-vuexy.card class="border border-primary h-100 text-center">
                <i class="icon-base ti tabler-chart-bar icon-48px text-primary mb-3"></i>
                <h5 class="fw-bold">État Consolidé des Règlements</h5>
                <p class="text-body-secondary">
                    Affiche tous les règlements effectués par poste et par mois pour un programme et une année donnés.
                </p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEtatConsolide">
                    <i class="icon-base ti tabler-download me-1"></i>Générer l'État Consolidé
                </button>
            </x-vuexy.card>
        </div>
    </div>
</x-vuexy.card>

<x-vuexy.card title="Statistiques Rapides" icon="tabler-chart-pie">
    @php
        $collecteUemoa = \App\Models\DeclarationPcs::where('statut', 'valide')
            ->where('programme', 'UEMOA')
            ->where('annee', date('Y'))
            ->sum('montant_recouvrement');
        $collecteAes = \App\Models\DeclarationPcs::where('statut', 'valide')
            ->where('programme', 'AES')
            ->where('annee', date('Y'))
            ->sum('montant_recouvrement');
        $destockeUemoa = \App\Models\DestockagePcs::where('statut', 'valide')
            ->where('programme', 'UEMOA')
            ->where('periode_annee', date('Y'))
            ->sum('montant_total_destocke');
        $destockeAes = \App\Models\DestockagePcs::where('statut', 'valide')
            ->where('programme', 'AES')
            ->where('periode_annee', date('Y'))
            ->sum('montant_total_destocke');
    @endphp

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <x-vuexy.card class="bg-label-success text-center h-100">
                <i class="icon-base ti tabler-coins icon-32px text-success mb-2"></i>
                <h6>Collecte UEMOA {{ date('Y') }}</h6>
                <h4 class="fw-bold text-success mb-0">{{ number_format($collecteUemoa, 0, ',', ' ') }} FCFA</h4>
            </x-vuexy.card>
        </div>
        <div class="col-md-3">
            <x-vuexy.card class="bg-label-warning text-center h-100">
                <i class="icon-base ti tabler-coins icon-32px text-warning mb-2"></i>
                <h6>Collecte AES {{ date('Y') }}</h6>
                <h4 class="fw-bold text-warning mb-0">{{ number_format($collecteAes, 0, ',', ' ') }} FCFA</h4>
            </x-vuexy.card>
        </div>
        <div class="col-md-3">
            <x-vuexy.card class="bg-label-danger text-center h-100">
                <i class="icon-base ti tabler-arrow-down icon-32px text-danger mb-2"></i>
                <h6>Déstocké UEMOA {{ date('Y') }}</h6>
                <h4 class="fw-bold text-danger mb-0">{{ number_format($destockeUemoa, 0, ',', ' ') }} FCFA</h4>
            </x-vuexy.card>
        </div>
        <div class="col-md-3">
            <x-vuexy.card class="bg-label-info text-center h-100">
                <i class="icon-base ti tabler-arrow-down icon-32px text-info mb-2"></i>
                <h6>Déstocké AES {{ date('Y') }}</h6>
                <h4 class="fw-bold text-info mb-0">{{ number_format($destockeAes, 0, ',', ' ') }} FCFA</h4>
            </x-vuexy.card>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <x-vuexy.card class="border border-success text-center h-100">
                <h6 class="text-success">Solde Disponible UEMOA {{ date('Y') }}</h6>
                <h3 class="fw-bold text-success">{{ number_format($collecteUemoa - $destockeUemoa, 0, ',', ' ') }} FCFA</h3>
                <small class="text-body-secondary">
                    Taux règlement : {{ $collecteUemoa > 0 ? number_format(($destockeUemoa / $collecteUemoa) * 100, 1) : 0 }}%
                </small>
            </x-vuexy.card>
        </div>
        <div class="col-md-6">
            <x-vuexy.card class="border border-warning text-center h-100">
                <h6 class="text-warning">Solde Disponible AES {{ date('Y') }}</h6>
                <h3 class="fw-bold text-warning">{{ number_format($collecteAes - $destockeAes, 0, ',', ' ') }} FCFA</h3>
                <small class="text-body-secondary">
                    Taux règlement : {{ $collecteAes > 0 ? number_format(($destockeAes / $collecteAes) * 100, 1) : 0 }}%
                </small>
            </x-vuexy.card>
        </div>
    </div>
</x-vuexy.card>

<div class="modal fade" id="modalEtatCollecte" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icon-base ti tabler-coins me-2 text-success"></i>Générer l'État de Collecte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <i class="icon-base ti tabler-download me-1"></i>Télécharger l'État
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEtatConsolide" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icon-base ti tabler-chart-bar me-2 text-primary"></i>Générer l'État Consolidé
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <i class="icon-base ti tabler-download me-1"></i>Télécharger l'État
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
