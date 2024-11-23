@extends('layouts.master')
@section('content')

<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Demandes de fonds</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Demandes de fonds</li>
            </ul>
        </div>
    </div>
</div>
<form action="{{ route('demandes-fonds.fonctionnaires') }}" method="GET">
    <div class="demande-group-form">
        <div class="row">
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste ..." value="{{ request('poste') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <input type="text" name="mois" class="form-control" placeholder="Rechercher par mois ..." value="{{ request('mois') }}">
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
                    <table id="demandes-table" class="table border-0 table-hover table-center mb-0 datatable table-striped">
                        <thead>
                            <tr>
                                <th>Poste</th>
                                <th>BCS</th>
                                <th>Santé</th>
                                <th>Education</th>
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
                                <td>{{ number_format($demande->saisonnier_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->epn_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->ced_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->ecom_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ number_format($demande->cfp_cpam_total_courant, 0, ',', ' ') }}</td>
                                <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th id="total-bcs"></th>
                                <th id="total-sante"></th>
                                <th id="total-education"></th>
                                <th id="total-saisonnier"></th>
                                <th id="total-epn"></th>
                                <th id="total-ced"></th>
                                <th id="total-ecom"></th>
                                <th id="total-cfp-cpam"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
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
        </div>
    </div>

</div>

@section('add-js')
    <!-- Inclure jQuery en premier -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Inclure les fichiers DataTables CSS et JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

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

            // Calculer les totaux
            table.on('draw', function() {
                var totalBCS = table.column(1, { page: 'current' }).data().reduce(function(a, b) {
                    return a + parseFloat(b.replace(/[\s,]/g, ''));
                }, 0);
                $('#total-bcs').html(totalBCS.toLocaleString('fr-FR'));

                var totalSante = table.column(2, { page: 'current' }).data().reduce(function(a, b) {
                    return a + parseFloat(b.replace(/[\s,]/g, ''));
                }, 0);
                $('#total-sante').html(totalSante.toLocaleString('fr-FR'));

                // Ajoutez des calculs similaires pour les autres colonnes
                var totalEducation = table.column(3, { page: 'current' }).data().reduce(function(a, b) {
                    return a + parseFloat(b.replace(/[\s,]/g, ''));
                }, 0);
                $('#total-education').html(totalEducation.toLocaleString('fr-FR'));

                var totalSaisonnier = table.column(4, { page: 'current' }).data().reduce(function(a, b) {
                    return a + parseFloat(b.replace(/[\s,]/g, ''));
                }, 0);
                $('#total-saisonnier').html(totalSaisonnier.toLocaleString('fr-FR'));

                var totalEPN = table.column(5, { page: 'current' }).data().reduce(function(a, b) {
                    return a + parseFloat(b.replace(/[\s,]/g, ''));
                }, 0);
                $('#total-epn').html(totalEPN.toLocaleString('fr-FR'));

                var totalCED = table.column(6, { page: 'current' }).data().reduce(function(a, b) {
                    return a + parseFloat(b.replace(/[\s,]/g, ''));
                }, 0);
                $('#total-ced').html(totalCED.toLocaleString('fr-FR'));

                var totalECOM = table.column(7, { page: 'current' }).data().reduce(function(a, b) {
                    return a + parseFloat(b.replace(/[\s,]/g, ''));
                }, 0);
                $('#total-ecom').html(totalECOM.toLocaleString('fr-FR'));

                var totalCFPCPAM = table.column(8, { page: 'current' }).data().reduce(function(a, b) {
                    return a + parseFloat(b.replace(/[\s,]/g, ''));
                }, 0);
                $('#total-cfp-cpam').html(totalCFPCPAM.toLocaleString('fr-FR'));
            });

            // Gérer le changement du nombre d'entrées à afficher
            $('#entries-per-page').on('change', function() {
                var entries = $(this).val();
                if (entries == -1) {
                    table.page.len(table.data().length).draw();  // Afficher tous les éléments
                } else {
                    table.page.len(entries).draw();
                }
            });
        });
    </script>
@stop

@endsection
