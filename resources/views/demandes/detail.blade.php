@extends('layouts.master')

@section('title', 'Détail des Demandes de Fonds')

@include('partials.vuexy.datatables-assets')

@section('content')

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form action="{{ route('demandes-fonds.detail') }}" method="GET">
        <div class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label for="poste" class="form-label">Poste</label>
                <input type="text" name="poste" id="poste" class="form-control" placeholder="Rechercher par poste…" value="{{ request('poste') }}">
            </div>
            <div class="col-lg-3 col-md-6">
                <label for="mois" class="form-label">Mois</label>
                <input type="text" name="mois" id="mois" class="form-control" placeholder="Rechercher par mois…" value="{{ request('mois') }}">
            </div>
            <div class="col-lg-2 col-md-6">
                <label for="start_date" class="form-label">Date début</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-lg-2 col-md-6">
                <label for="end_date" class="form-label">Date fin</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-lg-2 col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="icon-base ti tabler-search me-1"></i>Rechercher
                </button>
                <a href="{{ route('demandes-fonds.detail') }}" class="btn btn-label-secondary">
                    <i class="icon-base ti tabler-x"></i>
                </a>
            </div>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Détail des montants" icon="tabler-table">
    <div class="table-responsive">
        <table id="demandes-table" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Poste</th>
                    <th class="text-end">Recette</th>
                    <th>Mois</th>
                    <th>Désignation</th>
                    <th class="text-end">Salaire</th>
                    <th class="text-end">Revers</th>
                    <th class="text-end">Mois courant</th>
                    <th class="text-end">Mois antérieur</th>
                    <th class="text-end">Écart</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                    @php
                        $rows = [
                            ['label' => 'Fonctionnaires BCS', 'prefix' => 'fonctionnaires_bcs'],
                            ['label' => 'Collectivité Santé', 'prefix' => 'collectivite_sante'],
                            ['label' => 'Collectivité Éducation', 'prefix' => 'collectivite_education'],
                            ['label' => 'Personnels Saisonniers', 'prefix' => 'personnels_saisonniers'],
                            ['label' => 'Personnels EPN', 'prefix' => 'epn'],
                            ['label' => 'Personnels CED', 'prefix' => 'ced'],
                            ['label' => 'Personnels ECOM', 'prefix' => 'ecom'],
                            ['label' => 'Personnels CFPCPAM', 'prefix' => 'cfp_cpam'],
                        ];
                    @endphp
                    @foreach($rows as $row)
                    <tr class="data-row">
                        <td class="fw-medium">{{ $demande->poste->nom ?? 'N/A' }}</td>
                        <td class="text-end">{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois }} {{ $demande->annee }}</td>
                        <td>{{ $row['label'] }}</td>
                        <td class="text-end">{{ number_format($demande->{$row['prefix'] . '_net'}, 0, ',', ' ') }}</td>
                        <td class="text-end">{{ number_format($demande->{$row['prefix'] . '_revers'}, 0, ',', ' ') }}</td>
                        <td class="text-end">{{ number_format($demande->{$row['prefix'] . '_total_courant'}, 0, ',', ' ') }}</td>
                        <td class="text-end">{{ number_format($demande->{$row['prefix'] . '_salaire_ancien'}, 0, ',', ' ') }}</td>
                        <td class="text-end">{{ number_format($demande->{$row['prefix'] . '_total_courant'} - $demande->{$row['prefix'] . '_salaire_ancien'}, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function numberFormat(number) {
        return new Intl.NumberFormat('fr-FR').format(Math.round(number));
    }

    const table = $('#demandes-table').DataTable({
        dom: '<"row align-items-center mb-3"<"col-md-6"l><"col-md-6"f>>' +
             '<"row"<"col-12"tr>>' +
             '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
        language: window.DGTCP_DATATABLES_FR,
        order: [[2, 'desc']],
        responsive: true,
        pageLength: 16,
        lengthMenu: [[8, 16, 32, 64, -1], [8, 16, 32, 64, 'Tout']],
        drawCallback: function() {
            const api = this.api();
            let totalNet = 0, totalRevers = 0, totalCourant = 0, totalAncien = 0;

            api.rows({ page: 'current' }).every(function() {
                const data = this.data();
                totalNet += parseFloat(String(data[4]).replace(/\s/g, '') || 0);
                totalRevers += parseFloat(String(data[5]).replace(/\s/g, '') || 0);
                totalCourant += parseFloat(String(data[6]).replace(/\s/g, '') || 0);
                totalAncien += parseFloat(String(data[7]).replace(/\s/g, '') || 0);
            });

            $('#demandes-table tbody tr.total-row').remove();
            $('#demandes-table tbody').append(`
                <tr class="total-row table-light">
                    <td colspan="4" class="fw-semibold">Total (page courante)</td>
                    <td class="text-end fw-semibold">${numberFormat(totalNet)}</td>
                    <td class="text-end fw-semibold">${numberFormat(totalRevers)}</td>
                    <td class="text-end fw-semibold">${numberFormat(totalCourant)}</td>
                    <td class="text-end fw-semibold">${numberFormat(totalAncien)}</td>
                    <td class="text-end fw-semibold">${numberFormat(totalCourant - totalAncien)}</td>
                </tr>
            `);
        }
    });
});
</script>
@endpush
