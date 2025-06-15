@extends('layouts.master')

@section('content')
<!-- Section principale DGTCP -->
<div class="dgtcp-section">
    <!-- Formulaire de recherche DGTCP -->
    <div class="dgtcp-card mb-4">
        <div class="dgtcp-card-header">
            <div class="dgtcp-card-title">
                <i class="fas fa-search"></i>
                <span>Recherche et Filtres</span>
            </div>
        </div>
        <div class="dgtcp-card-body">
            <form action="{{ route('demandes-fonds.solde') }}" method="GET" class="dgtcp-filter-form">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <label class="dgtcp-form-label">
                            <i class="fas fa-building"></i>
                            Poste
                        </label>
                        <input type="text" name="poste" class="dgtcp-form-control"
                               placeholder="Rechercher par poste..." value="{{ request('poste') }}">
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="dgtcp-form-label">
                            <i class="fas fa-calendar"></i>
                            Mois
                        </label>
                        <select name="mois" class="dgtcp-form-control">
                            <option value="">Sélectionner un mois</option>
                            @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $mois)
                                <option value="{{ $mois }}" {{ request('mois') == $mois ? 'selected' : '' }}>{{ $mois }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-12 d-flex align-items-end">
                        <button type="submit" class="dgtcp-btn primary w-100">
                            <i class="fas fa-search"></i>
                            <span>Rechercher</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Carte statistique du total DGTCP -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mx-auto">
            <div class="dgtcp-stats-card success">
                <div class="dgtcp-stats-content">
                    <div class="dgtcp-stats-info">
                        <div class="dgtcp-stats-title">Total des Soldes</div>
                        <div class="dgtcp-stats-value">{{ number_format($demandeFonds->sum('solde'), 0, ',', ' ') }}</div>
                        <div class="dgtcp-stats-unit">F CFA</div>
                    </div>
                    <div class="dgtcp-stats-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="dgtcp-stats-footer">
                    <i class="fas fa-arrow-up"></i>
                    <span>Somme de tous les soldes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des soldes DGTCP -->
    <div class="dgtcp-card">
        <div class="dgtcp-card-header">
            <div class="dgtcp-card-title">
                <i class="fas fa-table"></i>
                <span>Soldes des Demandes de Fonds</span>
            </div>
            <div class="dgtcp-card-actions">
                <div class="dgtcp-stats-mini">
                    <span class="dgtcp-stats-value">{{ $demandeFonds->count() }}</span>
                    <span class="dgtcp-stats-label">Soldes</span>
                </div>
            </div>
        </div>
        <div class="dgtcp-card-body">
            <div class="dgtcp-table-container">
                <table id="demandes-table" class="dgtcp-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-wallet"></i> Solde</th>
                            <th><i class="fas fa-calendar"></i> Mois</th>
                            <th><i class="fas fa-building"></i> Poste</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demandeFonds as $demande)
                        <tr>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount success">{{ number_format($demande->solde, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ $demande->mois . ' ' . $demande->annee }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ $demande->poste->nom }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="dgtcp-total-row">
                            <th class="dgtcp-total-cell">
                                <span class="dgtcp-total-label">Total Solde:</span>
                                <span class="dgtcp-total-value" id="total-solde">0</span>
                            </th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@section('add-js')
<!-- DataTables CSS moderne -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<style>
/* Styles DGTCP pour la vue solde */
.dgtcp-section {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
}

.dgtcp-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    overflow: hidden;
    transition: all 0.3s ease;
}

.dgtcp-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
}

.dgtcp-card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dgtcp-card-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
}

.dgtcp-card-title i {
    color: #22c55e;
    font-size: 1.5rem;
}

.dgtcp-card-body {
    padding: 2rem;
}

.dgtcp-card-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Formulaire de recherche */
.dgtcp-filter-form {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

/* Carte statistique */
.dgtcp-stats-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    height: 100%;
}

.dgtcp-stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #22c55e 0%, #f59e0b 100%);
}

.dgtcp-stats-card.success::before {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
}

.dgtcp-stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

.dgtcp-stats-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.dgtcp-stats-info {
    flex: 1;
}

.dgtcp-stats-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.dgtcp-stats-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.dgtcp-stats-unit {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
}

.dgtcp-stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(22, 163, 74, 0.1) 100%);
    color: #22c55e;
    font-size: 1.5rem;
}

.dgtcp-stats-footer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
}

.dgtcp-stats-footer i {
    color: #22c55e;
}

.dgtcp-form-label {
    color: #374151;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.dgtcp-form-label i {
    color: #22c55e;
    font-size: 0.875rem;
}

.dgtcp-form-control {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    color: #1f2937;
    background: white;
    transition: all 0.3s ease;
    width: 100%;
}

.dgtcp-form-control:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.dgtcp-btn.primary {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.dgtcp-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(34, 197, 94, 0.3);
}

/* Styles tableau */
.dgtcp-table-container {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.dgtcp-table {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #1e293b !important;
}

.dgtcp-table thead th {
    background: linear-gradient(135deg, #22c55e 0%, #f59e0b 100%) !important;
    color: white !important;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 16px 12px;
    text-align: left;
    border: none;
    position: relative;
    white-space: nowrap;
}

.dgtcp-table thead th i {
    margin-right: 6px;
    opacity: 0.9;
    color: white !important;
}

.dgtcp-table tbody td {
    padding: 14px 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: 0.875rem;
    color: #1e293b !important;
    background: white !important;
}

.dgtcp-table tbody tr {
    transition: all 0.3s ease;
    background: white !important;
}

.dgtcp-table tbody tr:hover {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(245, 158, 11, 0.05) 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.dgtcp-table tfoot th {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
    color: #1e293b !important;
    font-weight: 700;
    font-size: 0.9rem;
    padding: 16px 12px;
    border-top: 2px solid #22c55e;
}

.dgtcp-total-row {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
}

.dgtcp-total-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dgtcp-total-label {
    font-weight: 600;
    color: #64748b;
}

.dgtcp-total-value {
    font-weight: 800;
    color: #22c55e;
    font-size: 1rem;
}

.dgtcp-cell-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.dgtcp-cell-primary {
    font-weight: 600;
    color: #1e293b !important;
}

.dgtcp-amount {
    font-weight: 700;
    font-size: 0.95rem;
}

.dgtcp-amount.success {
    color: #22c55e !important;
}

.dgtcp-stats-mini {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.dgtcp-stats-mini .dgtcp-stats-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #22c55e;
}

.dgtcp-stats-mini .dgtcp-stats-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Personnalisation DataTables */
.dataTables_wrapper {
    padding: 0;
    color: #1e293b !important;
}

.dataTables_length,
.dataTables_filter,
.dataTables_info,
.dataTables_paginate {
    margin: 16px 0;
    color: #1e293b !important;
}

.dataTables_length label,
.dataTables_filter label,
.dataTables_info {
    color: #1e293b !important;
    font-weight: 500;
}

.dataTables_length select,
.dataTables_filter input {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    color: #1e293b !important;
    background: white !important;
}

.dataTables_length select:focus,
.dataTables_filter input:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.dataTables_paginate .paginate_button {
    padding: 8px 16px !important;
    margin: 0 2px !important;
    border-radius: 8px !important;
    border: 2px solid #e2e8f0 !important;
    background: white !important;
    color: #64748b !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
}

.dataTables_paginate .paginate_button:hover {
    background: #22c55e !important;
    color: white !important;
    border-color: #22c55e !important;
    transform: translateY(-1px);
}

.dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #22c55e 0%, #f59e0b 100%) !important;
    color: white !important;
    border-color: #22c55e !important;
}

.dt-buttons {
    margin-bottom: 16px;
}

.dt-button {
    background: linear-gradient(135deg, #22c55e 0%, #f59e0b 100%) !important;
    color: white !important;
    border: none !important;
    padding: 10px 20px !important;
    border-radius: 8px !important;
    margin-right: 8px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
}

.dt-button:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 16px rgba(34, 197, 94, 0.3) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .dgtcp-section {
        padding: 1rem 0;
    }

    .dgtcp-card-body {
        padding: 1rem;
    }

    .dgtcp-table thead th {
        padding: 12px 8px;
        font-size: 0.75rem;
    }

    .dgtcp-table tbody td {
        padding: 10px 8px;
        font-size: 0.8rem;
    }
}
</style>

<script>
$(document).ready(function() {
    // Configuration DataTable DGTCP pour les soldes
    const table = $('#demandes-table').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"B>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copier',
                className: 'dt-button',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'dt-button',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'dt-button',
                title: 'Soldes Demandes de Fonds DGTCP',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'dt-button',
                title: 'Soldes Demandes de Fonds DGTCP',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimer',
                className: 'dt-button',
                title: 'Soldes Demandes de Fonds DGTCP',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json",
            buttons: {
                copy: 'Copier',
                csv: 'CSV',
                excel: 'Excel',
                pdf: 'PDF',
                print: 'Imprimer'
            }
        },
        responsive: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Tout"]],
        order: [[1, 'desc']], // Trier par mois décroissant
        columnDefs: [
            {
                targets: [0], // Colonne solde
                type: 'num-fmt',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'type') {
                        return data;
                    }
                    return data.replace(/[^\d]/g, '');
                }
            }
        ],
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

            // Mettre à jour le pied de page avec formatage français
            $('#total-solde').html(
                total.toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
            );
        },
        initComplete: function() {
            // Animation d'entrée pour les lignes
            $('.dgtcp-table tbody tr').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                }).delay(index * 50).animate({
                    'opacity': '1'
                }, 300).css('transform', 'translateY(0px)');
            });
        }
    });

    // Fonction pour réinitialiser les filtres
    window.resetFilters = function() {
        $('.dgtcp-filter-form')[0].reset();
        table.search('').columns().search('').draw();
    };
});
</script>
@stop
@endsection
