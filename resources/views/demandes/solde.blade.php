@extends('layouts.master')

@section('title', 'Soldes des Demandes de Fonds')

@include('partials.vuexy.datatables-assets')

@section('content')

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form action="{{ route('demandes-fonds.solde') }}" method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Poste</label>
            <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste..." value="{{ request('poste') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Mois</label>
            <select name="mois" class="form-select">
                <option value="">Tous les mois</option>
                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $mois)
                    <option value="{{ $mois }}" {{ request('mois') == $mois ? 'selected' : '' }}>{{ $mois }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-search me-1"></i>Rechercher
            </button>
        </div>
    </form>
</x-vuexy.card>

<div class="row mb-4">
    @include('partials.demandes.stat-box', [
        'label' => 'Total des soldes',
        'value' => number_format($demandeFonds->sum('solde'), 0, ',', ' ') . ' FCFA',
        'col' => 'col-md-4 mx-auto',
    ])
</div>

<x-vuexy.card title="Soldes par demande" icon="tabler-wallet">
    <x-slot:actions>
        <span class="badge bg-label-secondary">{{ $demandeFonds->count() }} ligne(s)</span>
    </x-slot:actions>
    <div class="table-responsive">
        <table id="demandes-table" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-end">Solde</th>
                    <th>Mois</th>
                    <th>Poste</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                <tr>
                    <td class="text-end fw-medium">{{ number_format($demande->solde, 0, ',', ' ') }}</td>
                    <td>{{ $demande->mois }} {{ $demande->annee }}</td>
                    <td>{{ $demande->poste->nom }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th class="text-end">{{ number_format($demandeFonds->sum('solde'), 0, ',', ' ') }}</th>
                    <th colspan="2">Total</th>
                </tr>
            </tfoot>
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
@include('partials.demandes.datatable-init', ['tableId' => 'demandes-table', 'orderCol' => 0, 'orderDir' => 'desc', 'excludeLastCol' => false, 'title' => 'Soldes des Demandes'])
@endpush
