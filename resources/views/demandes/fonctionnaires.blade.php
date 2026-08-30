@extends('layouts.master')

@section('title', 'Fonctionnaires par Catégorie')

@include('partials.vuexy.datatables-assets')

@section('content')

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form action="{{ route('demandes-fonds.fonctionnaires') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Poste</label>
            <input type="text" name="poste" class="form-control" value="{{ request('poste') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Mois</label>
            <select name="mois" class="form-select">
                <option value="">Tous</option>
                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $mois)
                    <option value="{{ $mois }}" {{ request('mois') == $mois ? 'selected' : '' }}>{{ $mois }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i>Rechercher</button>
        </div>
    </form>
</x-vuexy.card>

<div class="row mb-4">
    @include('partials.demandes.stat-box', ['label' => 'BCS', 'value' => number_format($totalBCS, 0, ',', ' ') . ' FCFA', 'col' => 'col-md-3 col-sm-6'])
    @include('partials.demandes.stat-box', ['label' => 'Santé', 'value' => number_format($totalSante, 0, ',', ' ') . ' FCFA', 'col' => 'col-md-3 col-sm-6'])
    @include('partials.demandes.stat-box', ['label' => 'Éducation', 'value' => number_format($totalEducation, 0, ',', ' ') . ' FCFA', 'col' => 'col-md-3 col-sm-6'])
    @include('partials.demandes.stat-box', ['label' => 'Saisonnier', 'value' => number_format($totalSaisonnier, 0, ',', ' ') . ' FCFA', 'col' => 'col-md-3 col-sm-6'])
    @include('partials.demandes.stat-box', ['label' => 'EPN', 'value' => number_format($totalEPN, 0, ',', ' ') . ' FCFA', 'col' => 'col-md-3 col-sm-6'])
    @include('partials.demandes.stat-box', ['label' => 'CED', 'value' => number_format($totalCED, 0, ',', ' ') . ' FCFA', 'col' => 'col-md-3 col-sm-6'])
    @include('partials.demandes.stat-box', ['label' => 'ECOM', 'value' => number_format($totalECOM, 0, ',', ' ') . ' FCFA', 'col' => 'col-md-3 col-sm-6'])
    @include('partials.demandes.stat-box', ['label' => 'CFP-CPAM', 'value' => number_format($totalCFPCPAM, 0, ',', ' ') . ' FCFA', 'col' => 'col-md-3 col-sm-6'])
</div>

<x-vuexy.card title="Détail par poste" icon="tabler-users">
    <div class="table-responsive">
        <table id="demandes-table" class="table table-hover align-middle table-sm">
            <thead class="table-light">
                <tr>
                    <th>Poste</th>
                    <th class="text-end">BCS</th>
                    <th class="text-end">Santé</th>
                    <th class="text-end">Éducation</th>
                    <th class="text-end">Saisonnier</th>
                    <th class="text-end">EPN</th>
                    <th class="text-end">CED</th>
                    <th class="text-end">ECOM</th>
                    <th class="text-end">CFP-CPAM</th>
                    <th>Mois</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                <tr>
                    <td class="fw-medium">{{ $demande->poste->nom }}</td>
                    <td class="text-end">{{ number_format($demande->fonctionnaires_bcs_total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->collectivite_sante_total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->collectivite_education_total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->personnels_saisonniers_total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->epn_total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->ced_total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->ecom_total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->cfp_cpam_total_courant, 0, ',', ' ') }}</td>
                    <td>{{ $demande->mois }} {{ $demande->annee }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
@include('partials.demandes.datatable-init', ['excludeLastCol' => false, 'title' => 'Fonctionnaires'])
@endpush
