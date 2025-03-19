@extends('layouts.master')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Demandes de Fonds - Fonctionnaires</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Demandes de Fonds</li>
            </ul>
        </div>
    </div>
</div>

<!-- Formulaire de recherche -->
<form action="{{ route('demandes-fonds.fonctionnaires') }}" method="GET">
    <div class="demande-group-form">
        <div class="row g-3">
            <!-- Filtre par poste -->
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste..." value="{{ request('poste') }}">
                </div>
            </div>

            <!-- Filtre par mois -->
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

            <!-- Filtre par plage de dates -->
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="date" name="date_debut" class="form-control" placeholder="Date de début" value="{{ request('date_debut') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="date" name="date_fin" class="form-control" placeholder="Date de fin" value="{{ request('date_fin') }}">
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
<!-- Section des totaux globaux -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Totaux Globaux</h5>
        <div class="row">
            <!-- Total BCS -->
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">BCS</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalBCS, 0, ',', ' ') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Santé -->
            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Santé</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSante, 0, ',', ' ') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hospital fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Éducation -->
            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Éducation</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEducation, 0, ',', ' ') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-school fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Saisonnier -->
            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Saisonnier</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSaisonnier, 0, ',', ' ') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total EPN -->
            <div class="col-md-3">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">EPN</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEPN, 0, ',', ' ') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-network-wired fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total CED -->
            <div class="col-md-3">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">CED</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalCED, 0, ',', ' ') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total ECOM -->
            <div class="col-md-3">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">ECOM</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalECOM, 0, ',', ' ') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total CFP-CPAM -->
            <div class="col-md-3">
                <div class="card border-left-light shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">CFP-CPAM</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalCFPCPAM, 0, ',', ' ') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-tie fa-2x text-gray-300"></i>
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
                                <th>Poste</th>
                                <th>BCS</th>
                                <th>Santé</th>
                                <th>Éducation</th>
                                <th>Saisonnier</th>
                                <th>EPN</th>
                                <th>CED</th>
                                <th>ECOM</th>
                                <th>CFP-CPAM</th>
                                <th>Mois</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandeFonds as $demande)
                            <tr>
                                <td>{{ $demande->poste->nom }}</td>
                                <td>{{ number_format($demande->fonctionnaires_bcs_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->collectivite_sante_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->collectivite_education_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->personnels_saisonniers_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->epn_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->ced_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->ecom_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->cfp_cpam_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
            pageLength: 8
        });
    });
</script>
@stop

@endsection