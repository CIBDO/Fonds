@extends('layouts.master')
@section('content')
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

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
<form action="{{ route('demandes-fonds.envois') }}" method="GET">
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
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <input type="date" name="date_debut" class="form-control" placeholder="Date de début" value="{{ request('date_debut') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <input type="date" name="date_fin" class="form-control" placeholder="Date de fin" value="{{ request('date_fin') }}">
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
                                <th>Mois</th>
                                <th>Date de réception</th>
                                <th>Poste</th>
                                <th>Fonds Demandés</th>
                                <th>Date de création</th>
                                <th>Statut</th>
                                <th style="text-align: center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandeFonds as $demande)
                            <tr>
                                <td>{{ $demande->mois }}</td>
                                <td>{{ $demande->date_reception }}</td>
                                <td>{{ $demande->poste->nom }}</td>
                                <td>{{ number_format($demande->total_courant, 0, ',', ' ') }}</td>
                                <td>{{ $demande->created_at }}</td>
                                <td>
                                    @if($demande->status === 'en_attente')
                                    <span class="status-en-attente">En attente</span>
                                @elseif($demande->status === 'approuve')
                                    <span class="status-approuve">Approuvé</span>
                                @elseif($demande->status === 'rejete')
                                    <span class="status-rejete">Rejeté</span>
                                @else
                                    <span>{{ $demande->status }}</span>
                                @endif
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('demandes-fonds.show', $demande->id) }}" class="btn btn-sm bg-success-light me-2">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm bg-primary-light" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $demande->id }}">
                                            <i class="feather-check"></i>Valider
                                        </button>
                                        <button type="button" class="btn btn-sm bg-danger-light " data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $demande->id }}">
                                            <i class="feather-X"></i>Rejeter
                                        </button>
                                        <a href="{{ route('demande-fonds.generate.pdf', $demande->id) }}" class="btn btn-sm bg-info-light">
                                            <i class="feather-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal pour approuver la demande -->
                            <div class="modal fade" id="approveModal-{{ $demande->id }}" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approuver la demande pour {{ $demande->poste->nom }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('demandes-fonds.update-status', $demande->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <input type="hidden" name="status" value="approuve">
                                                <div class="form-group">
                                                    <label for="date_envois">Date d'envoi :</label>
                                                    <input type="text" name="date_envois" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="montant">Montant :</label>
                                                    <input type="text" name="montant" class="form-control" oninput="formatNumberInput(this)" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="montant_disponible">Recette Douanière :</label>
                                                    <input type="text" name="montant_disponible" class="form-control" value="{{ number_format($demande->montant_disponible, 0, ',', ' ') }}" readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label for="solde">Solde du mois :</label>
                                                    <input type="text" name="solde" class="form-control" value="{{ number_format($demande->solde, 0, ',', ' ') }}" readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label for="observation">Observation :</label>
                                                    <textarea name="observation" class="form-control" rows="1"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Envoyer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal pour rejeter la demande -->
                            <div class="modal fade" id="rejectModal-{{ $demande->id }}" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Rejeter la demande pour {{ $demande->poste->nom }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('demandes-fonds.update-status', $demande->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <input type="hidden" name="status" value="rejete">
                                                <div class="form-group">
                                                    <label for="date_envois">Date d'envoi :</label>
                                                    <input type="date" name="date_envois" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="observation">Raison du rejet :</label>
                                                    <textarea name="observation" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-danger">Soumettre</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
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
<script>
    // Fonction pour formater les nombres avec un séparateur de milliers (espace)
    function formatNumberInput(input) {
        // Supprime tout caractère non numérique, puis applique le formatage
        input.value = input.value.replace(/\D/g, '') // Supprimer tout caractère non numérique
                                .replace(/\B(?=(\d{3})+(?!\d))/g, ' '); // Ajouter des espaces comme séparateurs
    }

    // Fonction pour nettoyer les champs avant soumission du formulaire
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            form.addEventListener('submit', function () {
                // Sélectionner les champs de montant
                const inputs = form.querySelectorAll('input[name="montant"], input[name="solde"], input[name="montant_disponible"]');

                inputs.forEach(input => {
                    if (input.value) {
                        input.value = input.value.replace(/\s+/g, ''); // Supprimer tous les espaces pour la soumission
                    }
                });
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fonction pour gérer l'affichage des champs
        function toggleFields(status) {
            const montantField = document.getElementById('montant-field');
            const observationField = document.getElementById('observation-field');

            if (status === 'approuve') {
                montantField.style.display = 'block';
                observationField.style.display = 'block';
            } else {
                montantField.style.display = 'none';
                observationField.style.display = 'none';
            }
        }

        // Écouter les changements sur le select du statut
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function () {
                const selectedStatus = this.value;
                toggleFields(selectedStatus);
            });
        });

        // Initialiser l'état des champs lors du chargement de la page
        statusSelects.forEach(select => {
            toggleFields(select.value);
        });
    });
</script>
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
