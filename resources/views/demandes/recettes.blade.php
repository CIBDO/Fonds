@extends('layouts.master')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Recettes Douanières</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Recettes Douanières</li>
            </ul>
        </div>
    </div>
</div>

<!-- Formulaire de recherche -->
<form action="{{ route('demandes-fonds.recettes') }}" method="GET">
    <div class="demande-group-form">
        <div class="row">
            <!-- Filtre par poste -->
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste ..." value="{{ request('poste') }}">
                </div>
            </div>

            <!-- Filtre par mois -->
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <select name="mois" class="form-select" placeholder="Mois...">
                        <option value="">Sélectionner un mois</option>
                        @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $mois)
                            <option value="{{ $mois }}" {{ request('mois') == $mois ? 'selected' : '' }}>{{ $mois }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <select name="annee" class="form-select" placeholder="Année...">
                        <option value="">Sélectionner une année</option>
                        @for ($year = now()->year; $year >= now()->year - 10; $year--)
                            <option value="{{ $year }}" {{ request('annee') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Bouton "Rechercher" -->
            <div class="col-lg-2">
                <button type="submit" class="btn btn-primary w-100">Rechercher</button>
            </div>
        </div>
    </div>
</form>

<!-- Section des totaux globaux -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Totaux Globaux</h5>
        <div class="row">
            <!-- Total Recettes Douanières -->
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Recettes Douanières</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRecettesDouanieres, 0, ',', ' ') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des demandes -->
<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="demandes-table" class="table border-0 table-hover table-center mb-0 datatable table-striped">
                        <thead>
                            <tr>
                                <th>Recettes Douanières</th>
                                <th>Mois</th>
                                <th>Poste</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandeFonds as $demande)
                            <tr>
                                <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                                <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                                <td>{{ $demande->poste->nom }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            {{-- <tr>
                                <th>Total: </th>
                                <th colspan="2"></th>
                            </tr> --}}
                        </tfoot>
                    </table>
                </div>
                <div class="pagination-wrapper">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            {{ $demandeFonds->links('pagination::bootstrap-4') }}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

@section('add-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#demandes-table').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            language: { url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json" },
            responsive: true,
            paging: false,
            searching: true,
            ordering: true,
            lengthChange: false,
            pageLength: 8,
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                // Convertir en nombre pour le calcul
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        parseFloat(i.replace(/[\s,]/g, '').replace(',', '.')) :
                        typeof i === 'number' ?
                        i : 0;
                };

                // Calculer le total de toutes les pages
                var total = api
                    .column(0)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Mettre à jour le pied de page
                $(api.column(0).footer()).html(
                    'Total: ' + total.toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                );
            }
        });
    });
</script>
@stop

@endsection