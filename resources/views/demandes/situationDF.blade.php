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

<form action="{{ route('demandes-fonds.situationDF') }}" method="GET">
    <div class="demande-group-form">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste ..." value="{{ request('poste') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="text" name="mois" class="form-control" placeholder="Rechercher par mois ..." value="{{ request('mois') }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <input type="text" name="total_courant" class="form-control" placeholder="Rechercher par montant ..." value="{{ request('total_courant') }}">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="search-student-btn">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="demandes-table" class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                        <thead class="student-thread">
                            <tr>
                                <th>Postes</th>
                                <th>Montant demandé</th>
                                <th>Recettes Douanières</th>
                                <th>Montant à Envoyer</th>
                                <th>Mois</th>
                            </tr>
                        </thead>
                        <tbody id="demandes-table-body">
                            @foreach($demandeFonds as $demande)
                            <tr>
                                <td>{{ $demande->poste->nom }}</td>
                                <td>{{ number_format($demande->total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->solde, 0, ',', ' ') }}</td>
                                <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="pagination-wrapper">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Ajout de la classe Bootstrap pour la pagination -->
                {{ $demandeFonds->links('pagination::bootstrap-4') }}
            </ul>
        </nav>
    </div>
</div>
@section('add-js')
    <!-- Inclure les fichiers DataTables CSS et JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#demandes-table').DataTable({
                order: [[1, 'desc']],  // Classe par date en ordre décroissant
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "/js/i18n/fr-FR.json",  // Chemin local vers le fichier de traduction
                    info: "",  // Désactiver le texte "showing x to y of z entries"
                    infoEmpty: "",  // Désactiver le texte quand il n'y a pas d'entrées
                    infoFiltered: ""  // Désactiver le texte de filtrage
                },
                paging: false,
                searching: true,
                ordering: true,
                responsive: true,
                lengthChange: false,
                pageLength: 8,
                footerCallback: function ( row, data, start, end, display ) {
                    // Custom footer logic here
                }
            });
    </script>
@stop
@endsection
