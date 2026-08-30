@extends('layouts.master')

@section('title', 'Totaux par Mois')

@include('partials.vuexy.datatables-assets')

@section('content')

<x-vuexy.card title="Sélection de l'année" icon="tabler-calendar" class="mb-4">
    <form action="{{ route('demandes-fonds.totaux-par-mois') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="annee" class="form-label">Année</label>
            <input type="number" name="annee" id="annee" class="form-control"
                   value="{{ $annee ?? date('Y') }}" min="2000" max="{{ date('Y') }}">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i>Afficher</button>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Détail des montants" icon="tabler-chart-bar">
    <div class="table-responsive">
        <table id="totaux-table" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mois</th>
                    <th class="text-end">Total Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($montantsParMois as $montant)
                <tr>
                    <td class="fw-medium">{{ $montant->mois }}</td>
                    <td class="text-end">{{ number_format($montant->total_mois, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th>Total général</th>
                    <th class="text-end" id="total-sum"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#totaux-table').DataTable({
        language: window.DGTCP_DATATABLES_FR,
        dom: 'tr<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
        responsive: true,
        pageLength: 12,
        paging: false,
        drawCallback: function () {
            const total = this.api().column(1, { page: 'all' }).data().reduce((a, b) => {
                return parseFloat(a) + parseFloat(String(b).replace(/\s/g, '').replace('F CFA', ''));
            }, 0);
            $('#total-sum').html(new Intl.NumberFormat('fr-FR').format(total) + ' FCFA');
        }
    });
});
</script>
@endpush
