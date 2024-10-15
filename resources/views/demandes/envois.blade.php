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

<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border-0 table-hover table-center mb-0 datatable table-striped">
                        <thead>
                            <tr>
                                <th>Mois</th>
                                <th>Date de réception</th>
                                <th>Poste</th>
                                <th>Montant</th>
                                <th>Date de création</th>
                                <th>Statut</th>
                                <th>Actions</th>
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
                                <td>{{ $demande->status }}</td>
                                <td>
                                    <div class="actions">
                                        <button type="button" class="btn btn-sm bg-primary-light" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $demande->id }}">
                                            <i class="feather-check"></i> Approuver
                                        </button>
                                        <button type="button" class="btn btn-sm bg-danger-light" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $demande->id }}">
                                            <i class="feather-x"></i> Rejeter
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal pour approuver la demande -->
                            <div class="modal fade" id="approveModal-{{ $demande->id }}" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approuver la demande</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('demandes-fonds.update-status', $demande->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <input type="hidden" name="status" value="approuve">
                                                <div class="form-group">
                                                    <label for="date_envois">Date d'envoi :</label>
                                                    <input type="date" name="date_envois" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="montant">Montant :</label>
                                                    <input type="number" name="montant" class="form-control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="observation">Observation :</label>
                                                    <textarea name="observation" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Soumettre</button>
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
                                            <h5 class="modal-title">Rejeter la demande</h5>
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
            </div>
        </div>
    </div>
</div>
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

@endsection
