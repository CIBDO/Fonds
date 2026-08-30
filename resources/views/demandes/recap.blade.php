@extends('layouts.master')

@section('title', 'Récapitulatif des Demandes')

@include('partials.vuexy.datatables-assets')

@section('content')

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form action="{{ route('demandes-fonds.recap') }}" method="GET" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Poste</label>
            <input type="text" name="poste" class="form-control" value="{{ request('poste') }}">
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
            <input type="number" name="annee" class="form-control" value="{{ request('annee', date('Y')) }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i>Rechercher</button>
        </div>
    </form>
</x-vuexy.card>

<div class="row mb-4">
    @include('partials.demandes.stat-box', ['label' => 'Salaire net', 'value' => number_format($totalNet, 0, ',', ' ') . ' FCFA'])
    @include('partials.demandes.stat-box', ['label' => 'Reversement', 'value' => number_format($totalRevers, 0, ',', ' ') . ' FCFA'])
    @include('partials.demandes.stat-box', ['label' => 'Total mois courant', 'value' => number_format($totalCourant, 0, ',', ' ') . ' FCFA'])
    @include('partials.demandes.stat-box', ['label' => 'Écart total', 'value' => number_format($totalEcart, 0, ',', ' ') . ' FCFA'])
</div>

<x-vuexy.card title="Détail" icon="tabler-report">
    <div class="table-responsive">
        <table id="demandes-table" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Poste</th>
                    <th class="text-end">Salaire Net</th>
                    <th class="text-end">Reversement</th>
                    <th class="text-end">Total Courant</th>
                    <th class="text-end">Mois Antérieur</th>
                    <th class="text-end">Écart</th>
                    <th class="text-end">Recettes</th>
                    <th>Période</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                <tr>
                    <td class="fw-medium">{{ $demande->poste->nom ?? 'N/A' }}</td>
                    <td class="text-end">{{ number_format($demande->total_net, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->total_revers, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->total_ancien, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->total_courant - $demande->total_ancien, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                    <td>{{ $demande->mois }} {{ $demande->annee }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
@include('partials.demandes.datatable-init', ['excludeLastCol' => false, 'title' => 'Récapitulatif Demandes'])
@endpush
