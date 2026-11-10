@extends('layouts.master')

@section('title', 'Consolidation VIR par banque')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="ti tabler-chart-bar me-2"></i>Consolidation par banque</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('virements.acct.index', request()->query()) }}" class="btn btn-label-secondary btn-sm">Liste VIR</a>
        <a href="{{ route('virements.acct.consolidation.export-pdf', request()->query()) }}" class="btn btn-label-danger btn-sm">
            <i class="ti tabler-file-type-pdf me-1"></i>Exporter PDF
        </a>
        <a href="{{ route('virements.acct.consolidation.export', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="ti tabler-file-spreadsheet me-1"></i>Exporter Excel
        </a>
    </div>
</div>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <select name="poste_id" class="form-select form-select-sm">
                <option value="">Tous les postes</option>
                @foreach($postes as $poste)
                    <option value="{{ $poste->id }}" @selected(request('poste_id') == $poste->id)>{{ $poste->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="mois" class="form-select form-select-sm">
                <option value="">Tous les mois</option>
                @foreach(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'] as $m)
                    <option value="{{ $m }}" @selected(request('mois') == $m)>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="annee" class="form-select form-select-sm">
                <option value="">Année</option>
                @for($y = (int) date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" @selected((string) request('annee') === (string) $y)>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm w-100">Filtrer</button>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Totaux par banque" icon="tabler-building-bank">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Banque</th>
                    <th class="text-end">Nb VIR</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Payé</th>
                    <th class="text-end">Non payé</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $tNb = 0; $tTot = 0; $tPaye = 0; $tNon = 0;
                @endphp
                @forelse($parBanque as $row)
                    @php
                        $tNb += $row['nb'];
                        $tTot += $row['total'];
                        $tPaye += $row['paye'];
                        $tNon += $row['non_paye'];
                    @endphp
                    <tr>
                        <td>{{ $row['banque'] }}</td>
                        <td class="text-end">{{ $row['nb'] }}</td>
                        <td class="text-end">{{ number_format($row['total'], 0, ',', ' ') }}</td>
                        <td class="text-end text-success">{{ number_format($row['paye'], 0, ',', ' ') }}</td>
                        <td class="text-end text-warning">{{ number_format($row['non_paye'], 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Aucune donnée</td></tr>
                @endforelse
            </tbody>
            @if($parBanque->isNotEmpty())
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="text-end">{{ $tNb }}</th>
                    <th class="text-end">{{ number_format($tTot, 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($tPaye, 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($tNon, 0, ',', ' ') }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</x-vuexy.card>
@endsection
