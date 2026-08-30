@extends('layouts.master')

@section('title', 'Recettes Douanières')

@include('partials.vuexy.datatables-assets')

@section('content')

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form action="{{ route('demandes-fonds.recettes') }}" method="GET" class="row g-3">
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
            <select name="annee" class="form-select">
                <option value="">Toutes</option>
                @for ($year = now()->year; $year >= now()->year - 10; $year--)
                    <option value="{{ $year }}" {{ request('annee') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i>Rechercher</button>
        </div>
    </form>
</x-vuexy.card>

<div class="row mb-4">
    @include('partials.demandes.stat-box', [
        'label' => 'Total recettes douanières',
        'value' => number_format($totalRecettesDouanieres, 0, ',', ' ') . ' FCFA',
        'col' => 'col-md-4 mx-auto',
    ])
</div>

<x-vuexy.card title="Détail des recettes" icon="tabler-cash">
    <div class="table-responsive">
        <table id="demandes-table" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-end">Recettes</th>
                    <th>Mois</th>
                    <th>Poste</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                <tr>
                    <td class="text-end fw-medium">{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                    <td>{{ $demande->mois }} {{ $demande->annee }}</td>
                    <td>{{ $demande->poste->nom }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th class="text-end">{{ number_format($totalRecettesDouanieres, 0, ',', ' ') }}</th>
                    <th colspan="2">Total</th>
                </tr>
            </tfoot>
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
@include('partials.demandes.datatable-init', ['excludeLastCol' => false, 'title' => 'Recettes Douanières'])
@endpush
