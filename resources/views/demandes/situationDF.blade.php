@extends('layouts.master')

@section('title', 'Situation DF - Demandes de Fonds')

@include('partials.vuexy.datatables-assets')

@section('content')

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form action="{{ route('demandes-fonds.situationDF') }}" method="GET" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Poste</label>
            <input type="text" name="poste" class="form-control" placeholder="Rechercher..." value="{{ request('poste') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Mois</label>
            <select name="mois" class="form-select">
                <option value="">Tous</option>
                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $mois)
                    <option value="{{ $mois }}" {{ request('mois') == $mois ? 'selected' : '' }}>{{ $mois }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Année</label>
            <input type="number" name="annee" class="form-control" value="{{ request('annee', date('Y')) }}" min="2000" max="{{ date('Y') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i>Rechercher</button>
        </div>
    </form>
</x-vuexy.card>

<div class="row mb-4">
    @include('partials.demandes.stat-box', ['label' => 'Montant demandé', 'value' => number_format($totalDemande, 0, ',', ' ') . ' FCFA'])
    @include('partials.demandes.stat-box', ['label' => 'Recettes douanières', 'value' => number_format($totalRecettes, 0, ',', ' ') . ' FCFA'])
    @include('partials.demandes.stat-box', ['label' => 'Montant à envoyer', 'value' => number_format($totalSolde, 0, ',', ' ') . ' FCFA'])
</div>

<x-vuexy.card title="Détail par poste" icon="tabler-table">
    <x-slot:actions><span class="badge bg-label-secondary">{{ $demandeFonds->count() }} ligne(s)</span></x-slot:actions>
    <div class="table-responsive">
        <table id="demandes-table" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Poste</th>
                    <th class="text-end">Montant Demandé</th>
                    <th class="text-end">Recettes Douanières</th>
                    <th class="text-end">Montant à Envoyer</th>
                    <th>Mois</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                <tr>
                    <td class="fw-medium">{{ $demande->poste->nom }}</td>
                    <td class="text-end">{{ number_format($demande->total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                    <td class="text-end fw-medium">{{ number_format($demande->solde, 0, ',', ' ') }}</td>
                    <td>{{ $demande->mois }} {{ $demande->annee }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
@include('partials.demandes.datatable-init', ['excludeLastCol' => false, 'title' => 'Situation DF'])
@endpush
