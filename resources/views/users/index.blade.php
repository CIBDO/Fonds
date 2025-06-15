@extends('layouts.master')

@section('content')
    <div class="content container-fluid">
        <!-- En-tête de page moderne -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <div class="page-sub-header">
                        <h3 class="page-title fw-bold text-primary">
                            <i class="fas fa-users me-2"></i>Gestion des Utilisateurs
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb bg-transparent p-0 mb-0">
                                <li class="breadcrumb-item">
                                    <a href="#" class="text-decoration-none">
                                        <i class="fas fa-home me-1"></i>Accueil
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">Utilisateurs</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group">
                        <a href="{{ route('users.edit', auth()->user()->id) }}"
                           class="btn btn-outline-info btn-sm me-2"
                           data-bs-toggle="tooltip"
                           title="Mon Profil">
                            <i class="fas fa-user-circle me-1"></i>Mon Profil
                        </a>
                        <a href="{{route('users.create')}}"
                           class="btn btn-primary btn-sm"
                           data-bs-toggle="tooltip"
                           title="Ajouter un utilisateur">
                            <i class="fas fa-plus me-1"></i>Nouvel Utilisateur
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        {{-- <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Total Utilisateurs</h6>
                                <h3 class="mb-0">{{ $users->total() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Utilisateurs Actifs</h6>
                                <h3 class="mb-0">{{ $users->where('active', true)->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user-check fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Administrateurs</h6>
                                <h3 class="mb-0">{{ $users->where('role', 'admin')->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user-shield fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Employés</h6>
                                <h3 class="mb-0">{{ $users->where('role', 'tresorier')->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user-tie fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Tableau principal avec DataTables -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-table me-2 text-primary"></i>Liste des Utilisateurs
                                </h5>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="tableView">
                                        <i class="fas fa-list"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="cardView">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success" id="exportBtn">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filtres avancés -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body py-3">
                                        <form method="GET" action="{{ route('users.index') }}" id="filterForm">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small text-muted">Nom & Prénom</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                           name="name" placeholder="Rechercher par nom..."
                                                           value="{{ request('name') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small text-muted">Email</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                           name="email" placeholder="Rechercher par email..."
                                                           value="{{ request('email') }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small text-muted">Rôle</label>
                                                    <select class="form-select form-select-sm" name="role">
                                                        <option value="">Tous les rôles</option>
                                                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                                        <option value="tresorier" {{ request('role') == 'tresorier' ? 'selected' : '' }}>Tresorier</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small text-muted">Statut</label>
                                                    <select class="form-select form-select-sm" name="status">
                                                        <option value="">Tous</option>
                                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <div class="btn-group w-100">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-search"></i> Filtrer
                                                        </button>
                                                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau avec DataTables -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="usersDataTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th width="25%">Utilisateur</th>
                                        <th width="20%">Contact</th>
                                        <th width="15%">Rôle</th>
                                        <th width="15%">Poste</th>
                                        <th width="10%">Statut</th>
                                        <th width="10%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        @if (auth()->user()->role === 'admin' || auth()->user()->id === $user->id)
                                        <tr data-user-id="{{ $user->id }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <span class="text-white fw-bold">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                                        <small class="text-muted">
                                                            Inscrit le {{ $user->created_at->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="text-dark">{{ $user->email }}</div>
                                                    @if($user->phone)
                                                        <small class="text-muted">
                                                            <i class="fas fa-phone fa-xs me-1"></i>{{ $user->phone }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($user->role === 'admin')
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-shield-alt me-1"></i>Administrateur
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-user me-1"></i>Employé
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $user->poste->nom ?? 'Non défini' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->isActive())
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Actif
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Inactif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @if (auth()->user()->id === $user->id || auth()->user()->role === 'admin')
                                                        <a href="{{ route('users.edit', $user->id) }}"
                                                           class="btn btn-outline-warning"
                                                           data-bs-toggle="tooltip"
                                                           title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    {{-- @if(auth()->user()->role === 'admin' && auth()->user()->id !== $user->id)
                                                        @if($user->isActive())
                                                            <button type="button"
                                                                    class="btn btn-outline-danger deactivate-user"
                                                                    data-user-id="{{ $user->id }}"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Désactiver">
                                                                <i class="fas fa-user-slash"></i>
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                    class="btn btn-outline-success activate-user"
                                                                    data-user-id="{{ $user->id }}"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Activer">
                                                                <i class="fas fa-user-check"></i>
                                                            </button>
                                                        @endif
                                                    @endif

                                                    <button type="button"
                                                            class="btn btn-outline-info view-user"
                                                            data-user-id="{{ $user->id }}"
                                                            data-bs-toggle="tooltip"
                                                            title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </button> --}}
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination personnalisée -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                Affichage de {{ $users->firstItem() ?? 0 }} à {{ $users->lastItem() ?? 0 }}
                                sur {{ $users->total() }} utilisateurs
                            </div>
                            <div>
                                {{ $users->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de détails utilisateur -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user-circle me-2"></i>Détails de l'utilisateur
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="confirmMessage">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <style>
        .avatar-sm {
            width: 40px;
            height: 40px;
        }

        .card {
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .btn-group-sm > .btn {
            border-radius: 4px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            color: white !important;
            border: 1px solid #007bff;
            background-color: #007bff;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            color: white !important;
            border: 1px solid #007bff;
            background-color: #007bff;
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 4s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .page-title {
            color: white !important;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item.active {
            color: white;
        }

        .animate-card {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            border-left: 4px solid;
        }

        .table-responsive {
            border-radius: 8px;
        }

        .loading-spinner {
            display: none;
        }

        .btn {
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }
    </style>
@endsection

@section('scripts')
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialisation des tooltips Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Configuration DataTables
            var table = $('#usersDataTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
                order: [[1, 'asc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimer',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        }
                    }
                ],
                columnDefs: [
                    { orderable: false, targets: [0, 6] },
                    { searchable: false, targets: [0, 6] }
                ]
            });

            // Sélection multiple
            $('#selectAll').on('click', function() {
                var isChecked = this.checked;
                $('.user-checkbox').prop('checked', isChecked);
            });

            $('.user-checkbox').on('click', function() {
                var totalCheckboxes = $('.user-checkbox').length;
                var checkedCheckboxes = $('.user-checkbox:checked').length;
                $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
            });

            // Actions sur les utilisateurs
            $(document).on('click', '.deactivate-user', function() {
                var userId = $(this).data('user-id');
                showConfirmModal(
                    'Êtes-vous sûr de vouloir désactiver cet utilisateur ?',
                    function() {
                        toggleUserStatus(userId, 'deactivate');
                    }
                );
            });

            $(document).on('click', '.activate-user', function() {
                var userId = $(this).data('user-id');
                showConfirmModal(
                    'Êtes-vous sûr de vouloir activer cet utilisateur ?',
                    function() {
                        toggleUserStatus(userId, 'activate');
                    }
                );
            });

            // Voir les détails de l'utilisateur
            $(document).on('click', '.view-user', function() {
                var userId = $(this).data('user-id');
                loadUserDetails(userId);
            });

            // Export personnalisé
            $('#exportBtn').on('click', function() {
                $('.dt-buttons .buttons-excel').click();
            });

            // Fonctions utilitaires
            function showConfirmModal(message, callback) {
                $('#confirmMessage').text(message);
                $('#confirmModal').modal('show');

                $('#confirmAction').off('click').on('click', function() {
                    callback();
                    $('#confirmModal').modal('hide');
                });
            }

                        function toggleUserStatus(userId, action) {
                // Construire l'URL correcte selon les routes définies
                var url = action === 'activate' ?
                    `/users/${userId}/activate` :
                    `/users/${userId}/deactivate`;

                $.ajax({
                    url: url,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        showLoadingSpinner();
                    },
                                        success: function(response) {
                        hideLoadingSpinner();

                        // Utiliser le message de la réponse ou un message par défaut
                        var message = response.message || 'Action effectuée avec succès';
                        showNotification(message, 'success');

                        // Recharger la page après un court délai pour voir la notification
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        hideLoadingSpinner();
                        var message = 'Une erreur est survenue';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            message = 'Route non trouvée. Vérifiez les routes dans web.php';
                        } else if (xhr.status === 403) {
                            message = 'Vous n\'avez pas l\'autorisation pour cette action';
                        } else if (xhr.status === 500) {
                            message = 'Erreur serveur interne';
                        }

                        showNotification(message, 'error');
                        console.error('Erreur AJAX:', xhr);
                    }
                });
            }

            function loadUserDetails(userId) {
                $.ajax({
                    url: `/users/${userId}/details`,
                    method: 'GET',
                    beforeSend: function() {
                        $('#userDetailsContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Chargement...</div>');
                        $('#userDetailsModal').modal('show');
                    },
                    success: function(response) {
                        $('#userDetailsContent').html(response);
                    },
                    error: function() {
                        $('#userDetailsContent').html('<div class="alert alert-danger">Erreur lors du chargement des détails</div>');
                    }
                });
            }

            function showNotification(message, type) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

                var notification = `
                    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                        <i class="fas ${icon} me-2"></i>${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                $('body').append(notification);

                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }

            function showLoadingSpinner() {
                $('.loading-spinner').show();
            }

            function hideLoadingSpinner() {
                $('.loading-spinner').hide();
            }

            // Animation des cartes
            $('.card').addClass('animate-card');

            // Recherche en temps réel
            $('#filterForm input, #filterForm select').on('change keyup', function() {
                var formData = $('#filterForm').serialize();
                // Vous pouvez implémenter une recherche AJAX ici si nécessaire
            });
        });
    </script>
@endsection
