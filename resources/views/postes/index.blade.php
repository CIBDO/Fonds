@extends('layouts.master')

@section('content')
<style>
    /* Animations et styles modernes */
    .fade-in {
        animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card-stats {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    }

    .search-container {
        background: #f8f9ff;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .search-input {
        border-radius: 25px;
        border: 2px solid #e3e6f0;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-search {
        border-radius: 25px;
        padding: 12px 30px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .table-card {
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        border: none;
    }

    .table-modern {
        border-radius: 15px;
        overflow: hidden;
    }

    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table-modern thead th {
        border: none;
        padding: 20px 15px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-modern tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9ff;
        transform: scale(1.01);
    }

    .table-modern tbody td {
        padding: 20px 15px;
        vertical-align: middle;
    }

    .action-btn {
        margin: 0 3px;
        border-radius: 10px;
        padding: 8px 12px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .btn-view {
        background: linear-gradient(45deg, #28a745, #20c997);
        border-color: #28a745;
    }

    .btn-edit {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
        border-color: #ffc107;
    }

    .btn-add {
        background: linear-gradient(45deg, #007bff, #6f42c1);
        border-radius: 50px;
        padding: 15px 30px;
        font-weight: 600;
        box-shadow: 0 8px 25px rgba(0,123,255,0.3);
        transition: all 0.3s ease;
    }

    .btn-add:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(0,123,255,0.4);
    }

    .page-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .breadcrumb-modern {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-modern .breadcrumb-item a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }

    .breadcrumb-modern .breadcrumb-item.active {
        color: white;
        font-weight: 600;
    }

    .stats-icon {
        font-size: 3rem;
        opacity: 0.3;
        position: absolute;
        right: 20px;
        top: 20px;
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .download-buttons .btn {
        margin: 0 5px;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .download-buttons .btn:hover {
        transform: translateY(-2px);
    }
</style>

<div class="fade-in">
    <!-- En-tête moderne avec gradient -->
    {{-- <div class="page-header-modern">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title mb-2">
                    <i class="fas fa-briefcase me-3"></i>
                    Gestion des Postes
                </h2>
                <ul class="breadcrumb breadcrumb-modern">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active">Postes</li>
                </ul>
            </div>
            <div class="col-auto">
                <div class="stats-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Barre de recherche améliorée -->
    
    <!-- Table moderne -->
    <div class="row fade-in">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header" style="background: linear-gradient(135deg, #f8f9ff 0%, #e3e6f0 100%); border-bottom: 2px solid #667eea;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-list-ul me-2" style="color: #667eea;"></i>
                                Liste des Postes
                            </h4>
                        </div>
                        <div class="col-auto download-buttons">
                            <a href="#" class="btn btn-outline-secondary btn-sm me-2" title="Vue Liste">
                                <i class="feather-list"></i>
                            </a>
                            <a href="#" class="btn btn-outline-secondary btn-sm me-2" title="Vue Grille">
                                <i class="feather-grid"></i>
                            </a>
                            <a href="#" class="btn btn-outline-success btn-sm me-3" title="Télécharger">
                                <i class="fas fa-download me-1"></i>Export
                            </a>
                            <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addPosteModal">
                                <i class="fas fa-plus me-2"></i>Nouveau Poste
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="demandes-table" class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <i class="fas fa-hashtag me-2"></i>ID
                                    </th>
                                    <th>
                                        <i class="fas fa-briefcase me-2"></i>Nom du Poste
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-cogs me-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($postes as $poste)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">{{ $poste->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-user-tie text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $poste->nom }}</h6>
                                                <small class="text-muted">Poste #{{ $poste->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{route('postes.show', $poste->id)}}"
                                               class="btn btn-sm btn-view action-btn"
                                               title="Voir les détails">
                                                <i class="feather-eye me-1"></i>Voir
                                            </a>
                                            <button class="btn btn-sm btn-edit action-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editPosteModal{{ $poste->id }}"
                                                    title="Modifier le poste">
                                                <i class="feather-edit me-1"></i>Modifier
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination moderne -->
    <div class="row mt-4 fade-in">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Affichage de {{ $postes->firstItem() }} à {{ $postes->lastItem() }} sur {{ $postes->total() }} résultats
                </div>
                <nav aria-label="Page navigation">
                    <div class="pagination-wrapper">
                        {{ $postes->links('pagination::bootstrap-4') }}
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>

@include('postes.add')
@include('postes.edit')

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
            // DataTable avec configuration améliorée
            $('#demandes-table').DataTable({
                order: [[0, 'desc']],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-outline-primary btn-sm',
                        text: '<i class="fas fa-copy"></i> Copier'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-outline-success btn-sm',
                        text: '<i class="fas fa-file-csv"></i> CSV'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-success btn-sm',
                        text: '<i class="fas fa-file-excel"></i> Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-outline-danger btn-sm',
                        text: '<i class="fas fa-file-pdf"></i> PDF'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-info btn-sm',
                        text: '<i class="fas fa-print"></i> Imprimer'
                    }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json"
                },
                paging: false, // Désactiver la pagination DataTables pour utiliser celle de Laravel
                searching: true,
                ordering: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tous"]],
                pageLength: 10,
                drawCallback: function() {
                    // Animation pour les nouvelles lignes
                    $('.table-modern tbody tr').addClass('fade-in');
                }
            });

            // Fonctionnalité de recherche personnalisée
            $('.btn-search').on('click', function() {
                var searchTerm = $('input[name="nom"]').val();
                $('#demandes-table').DataTable().search(searchTerm).draw();
            });

            // Recherche en temps réel
            $('input[name="nom"]').on('keyup', function() {
                var searchTerm = $(this).val();
                $('#demandes-table').DataTable().search(searchTerm).draw();
            });

            // Animation au survol des cartes
            $('.card-stats').hover(
                function() {
                    $(this).addClass('pulse');
                },
                function() {
                    $(this).removeClass('pulse');
                }
            );

            // Tooltip pour les boutons d'action
            $('[title]').tooltip();

            // Animation d'entrée séquentielle
            $('.fade-in').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });
        });
    </script>

    <style>
        /* Styles pour les boutons DataTables */
        .dt-buttons {
            margin-bottom: 20px;
        }

        .dt-buttons .btn {
            margin: 0 5px 10px 0;
            border-radius: 8px;
        }

        /* Animation pour la pagination */
        .pagination {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .pagination .page-link {
            border: none;
            color: #667eea;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background-color: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-color: #667eea;
        }
    </style>
@stop
@endsection
