@extends('layouts.master')

@section('content')
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #333;
        }
        .export-form {
            background: #f4f4f4;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .export-form input[type="date"], .export-form button {
            padding: 10px;
            margin-right: 10px;
        }
        .export-form button {
            background: #3d5ee1;
            color: white;
            border: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
        background-color: #f8f9fa !important;
    }
    .total-row td {
        font-weight: bold;
    }
    .text-end {
        text-align: right !important;
    }

    </style>
    <div class="container">
        <h3 style="text-align: center; font-weight: bold; font-family: 'Times New Roman', Times, serif;">Situation des demandes de fonds</h3>
        <div class="export-form">

            <form action="{{ route('demandes-fonds.detail') }}" method="GET">
                @csrf
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
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <input type="date" name="start_date" class="form-control" placeholder="Date de début ..." value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <input type="date" name="end_date" class="form-control" placeholder="Date de fin ..." value="{{ request('end_date') }}">
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
        </div>
        <table id="demandes-table" class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Poste</th>
                    <th>Recette </th>
                    <th>Mois</th>
                    <th>Désignation</th>
                    <th>Salaire </th>
                    <th>Revers</th>
                    <th>mois courant</th>
                    <th>mois antérieur</th>
                    <th>Écart</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                @php
                    $categories = [
                        'fonctionnaires_bcs' => 'Fonctionnaires BCS',
                        'collectivite_sante' => 'Collectivité Santé',
                        'collectivite_education' => 'Collectivité Éducation',
                        'personnels_saisonniers' => 'Personnels Saisonniers',
                        'epn' => 'Personnels EPN',
                        'ced' => 'Personnels CED',
                        'ecom' => 'Personnels ECOM',
                        'cfp_cpam' => 'Personnels CFPCPAM',
                    ];
                @endphp
                    <tr class="data-row">
                        <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                        <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                        <td>Fonctionnaires BCS</td>
                        <td>{{ number_format($demande->fonctionnaires_bcs_net, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->fonctionnaires_bcs_revers, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->fonctionnaires_bcs_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->fonctionnaires_bcs_salaire_ancien, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->fonctionnaires_bcs_total_courant - $demande->fonctionnaires_bcs_salaire_ancien, 0, ',', ' ') }}</td>
                    </tr>
                    {{--  <tr  class="total-row ">
                        <td colspan="4" style="text-align:center;"><strong>Total Fonctionnaires BCS:</strong></td>
                        <td>{{ number_format($totaux['net'] ?? 0, 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['revers'] ?? 0, 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'] ?? 0, 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_ancien'] ?? 0, 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'] - $totaux['total_ancien'] ?? 0, 0, ',', ' ') }}</td>
                    </tr>  --}}
                    <tr class="data-row">
                        <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                        <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                        <td>Collectivité Santé</td>
                        <td>{{ number_format($demande->collectivite_sante_net, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_sante_revers, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_sante_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_sante_salaire_ancien, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_sante_total_courant - $demande->collectivite_sante_salaire_ancien, 0, ',', ' ') }}</td>
                    </tr>
                    {{-- <tr class="total-row">
                        <td colspan="4" style="text-align:center;"><strong>Total Collectivité Santé:</strong></td>
                        <td>{{ number_format($totaux['net'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['revers'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_ancien'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'] - $totaux['total_ancien'], 0, ',', ' ') }}</td>
                    </tr> --}}
                    <tr class="data-row">
                        <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                        <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                        <td>Collectivité Éducation</td>
                        <td>{{ number_format($demande->collectivite_education_net, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_education_revers, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_education_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_education_salaire_ancien, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->collectivite_education_total_courant - $demande->collectivite_education_salaire_ancien, 0, ',', ' ') }}</td>
                    </tr>
                    {{-- <tr class="total-row">
                        <td colspan="4" style="text-align:center;"><strong>Total Collectivité Éducation:</strong></td>
                        <td>{{ number_format($totaux['net'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['revers'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_ancien'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'] - $totaux['total_ancien'], 0, ',', ' ') }}</td>
                    </tr> --}}
                    <tr class="data-row"   >
                        <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                        <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                        <td>Personnels Saisonniers</td>
                        <td>{{ number_format($demande->personnels_saisonniers_net, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->personnels_saisonniers_revers, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->personnels_saisonniers_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->personnels_saisonniers_salaire_ancien, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->personnels_saisonniers_total_courant - $demande->personnels_saisonniers_salaire_ancien, 0, ',', ' ') }}</td>
                    </tr>
                    {{-- <tr class="total-row">
                        <td colspan="4" style="text-align:center;"><strong>Total Personnels Saisonniers:</strong></td>
                        <td>{{ number_format($totaux['net'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['revers'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_ancien'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'] - $totaux['total_ancien'], 0, ',', ' ') }}</td>
                    </tr> --}}
                    <tr class="data-row">
                        <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                        <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                        <td>Personnels EPN</td>
                        <td>{{ number_format($demande->epn_net, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->epn_revers, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->epn_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->epn_salaire_ancien, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->epn_total_courant - $demande->epn_salaire_ancien, 0, ',', ' ') }}</td>
                    </tr>
                    {{-- <tr class="total-row">
                        <td colspan="4" style="text-align:center;"><strong>Total Personnels EPN:</strong></td>
                        <td>{{ number_format($totaux['net'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['revers'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_ancien'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'] - $totaux['total_ancien'], 0, ',', ' ') }}</td>
                    </tr> --}}
                    <tr class="data-row">
                        <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                        <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                        <td>Personnels CED</td>
                        <td>{{ number_format($demande->ced_net, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ced_revers, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ced_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ced_salaire_ancien, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ced_total_courant - $demande->ced_salaire_ancien, 0, ',', ' ') }}</td>
                    </tr>
                    {{-- <tr class="total-row">
                        <td colspan="4" style="text-align:center;"><strong>Total Personnels CED:</strong></td>
                        <td>{{ number_format($totaux['net'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['revers'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_ancien'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'] - $totaux['total_ancien'], 0, ',', ' ') }}</td>
                    </tr> --}}
                    <tr class="data-row">
                        <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                        <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                        <td>Personnels ECOM</td>
                        <td>{{ number_format($demande->ecom_net, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ecom_revers, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ecom_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ecom_salaire_ancien, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->ecom_total_courant - $demande->ecom_salaire_ancien, 0, ',', ' ') }}</td>
                    </tr>
                    {{-- <tr class="total-row">
                        <td colspan="4" style="text-align:center;"><strong>Total Personnels ECOM:</strong></td>
                        <td>{{ number_format($totaux['net'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['revers'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_ancien'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'] - $totaux['total_ancien'], 0, ',', ' ') }}</td>
                    </tr> --}}
                    <tr class="data-row">
                        <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                        <td>{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                        <td>{{ $demande->mois . ' ' . $demande->annee }}</td>
                        <td>Personnels CFPCPAM</td>
                        <td>{{ number_format($demande->cfp_cpam_net, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->cfp_cpam_revers, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->cfp_cpam_total_courant, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->cfp_cpam_salaire_ancien, 0, ',', ' ') }}</td>
                        <td>{{ number_format($demande->cfp_cpam_total_courant - $demande->cfp_cpam_salaire_ancien, 0, ',', ' ') }}</td>
                    </tr>
                    {{-- <tr class="total-row">
                        <td colspan="4" style="text-align: right;"><strong>Total Personnels CFPCPAM:</strong></td>
                        <td>{{ number_format($totaux['net'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['revers'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_ancien'], 0, ',', ' ') }}</td>
                        <td>{{ number_format($totaux['total_courant'] - $totaux['total_ancien'], 0, ',', ' ') }}</td>
                    </tr> --}}
                    <!-- Répétez ce bloc pour chaque catégorie (Collectivité Santé, Collectivité Éducation, etc.) si nécessaire -->
                @endforeach

            </tbody>
        </table>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

 <script>
    $(document).ready(function() {
        var table = $('#demandes-table').DataTable({
            order: [[1, 'desc']],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Exporter en Excel',
                    title: 'Demandes de Fonds',
                    exportOptions: {
                        format: {
                            body: function(data, row, column, node) {
                                // Vérifiez si c'est une ligne de total
                                if ($(node).hasClass('total-row')) {
                                    return $(node).find('td').map(function() {
                                        return $(this).text();
                                    }).get().join(',');
                                }
                                return data;
                            }
                        }
                    }
                },
                'copy', 'csv', 'pdf', 'print'
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json"
            },
            rowGroup: {
                dataSrc: 3 // Groupe par désignation (index de la colonne)
            },
            ordering: true,
            responsive: true,
            pageLength: 8,
            drawCallback: function(settings) {
                var api = this.api();

                // Réinitialisation des totaux
                var total_net = 0;
                var total_revers = 0;
                var total_courant = 0;
                var total_ancien = 0;

                // Parcourir les lignes de la table actuelle
                api.rows({ page: 'current' }).every(function() {
                    var data = this.data();

                    // Vérifiez que les colonnes ont des valeurs valides
                    var net = parseFloat(data[4].replace(/ /g, '') || 0);
                    var revers = parseFloat(data[5].replace(/ /g, '') || 0);
                    var courant = parseFloat(data[6].replace(/ /g, '') || 0);
                    var ancien = parseFloat(data[7].replace(/ /g, '') || 0);

                    // Additionnez les totaux
                    total_net += net;
                    total_revers += revers;
                    total_courant += courant;
                    total_ancien += ancien;
                });

                // Calcul de l'écart
                var total_ecart = total_courant - total_ancien;

                // Mettez à jour la ligne des totaux dans la table
                var totalRow = `
                    <tr class="total-row">
                        <td colspan="4" style="text-align:center;"><strong>Total:</strong></td>
                        <td>${number_format(total_net, 0, ',', ' ')}</td>
                        <td>${number_format(total_revers, 0, ',', ' ')}</td>
                        <td>${number_format(total_courant, 0, ',', ' ')}</td>
                        <td>${number_format(total_ancien, 0, ',', ' ')}</td>
                        <td>${number_format(total_ecart, 0, ',', ' ')}</td>
                    </tr>
                `;

                // Supprimer la ligne précédente des totaux pour éviter les doublons
                $('.total-row').remove();
                // Ajouter la nouvelle ligne des totaux après la dernière ligne de données
                $('#demandes-table tbody').append(totalRow);
            }
        });

        // Fonction de formatage similaire à PHP pour afficher les nombres
        function number_format(number, decimals, decPoint, thousandsSep) {
            decimals = decimals || 0;
            decPoint = decPoint || ',';
            thousandsSep = thousandsSep || ' ';

            var parts = number.toFixed(decimals).split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);
            return parts.join(decPoint);
        }
    });
</script>



@stop

@endsection
