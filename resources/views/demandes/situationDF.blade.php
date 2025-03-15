@extends('layouts.master')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Demandes</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Demandes</li>
            </ul>
        </div>
    </div>
</div>

<!-- Formulaire de recherche -->
<form action="{{ route('demandes-fonds.situationDF') }}" method="GET">
    <div class="demande-group-form">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste..." value="{{ request('poste') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <select name="mois" class="form-select" placeholder="Mois...">
                        <option value="">Sélectionner un mois</option>
                        @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $mois)
                            <option value="{{ $mois }}" {{ request('mois') == $mois ? 'selected' : '' }}>{{ $mois }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="number" name="annee" class="form-control" placeholder="Année..." value="{{ request('annee', date('Y')) }}" min="2000" max="{{ date('Y') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
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
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Montant total demandé </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalDemande, 0, ',', ' ') }} F CFA</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Recettes douanières</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRecettes, 0, ',', ' ') }} F CFA</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-coins fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Montant à envoyer</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSolde, 0, ',', ' ') }} F CFA</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des demandes -->
<div class="card card-table">
    <div class="card-body">
        <div class="table-responsive">
            <table id="demandes-table" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Postes</th>
                        <th>Montant demandé</th>
                        <th>Recettes Douanières</th>
                        <th>Montant à Envoyer</th>
                        <th>Mois</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandeFonds as $demande)
                    <tr>
                        <td>{{ $demande->poste->nom }}</td>
                        <td>{{ number_format($demande->total_courant, 0, ',', ' ') }} F CFA</td>
                        <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }} F CFA</td>
                        <td>{{ number_format($demande->solde, 0, ',', ' ') }} F CFA</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="pagination-wrapper">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{ $demandeFonds->links('pagination::bootstrap-4') }}
        </ul>
    </nav>
</div>

@section('add-js')
<!-- DataTables CSS et JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        $('#demandes-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/French.json"
            },
            responsive: true,
            pageLength: 8
        });
    });
</script>
@stop
@endsection