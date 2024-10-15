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
                                        <button type="button" class="btn btn-sm bg-primary-light" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $demande->id }}">
                                            <i class="feather-check"></i> Changer le statut
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal pour approuver ou rejeter la demande -->
                            <div class="modal fade" id="statusModal-{{ $demande->id }}" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approuvé ou Rejeté la demande </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('demandes-fonds.update-status', $demande->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="date_envois">Date d'envoi :</label>
                                                    <input type="date" name="date_envois" class="form-control" value="{{ now()->format('Y-m-d') }}">
                                                </div>

                                                <div class="form-group">
                                                    <label for="status">Statut :</label>
                                                    <select name="status" class="form-select" required id="status-{{ $demande->id }}">
                                                        <option value="approuve" {{ $demande->status == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                                                        <option value="rejete" {{ $demande->status == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                                    </select>
                                                </div>

                                                <div id="montantFields-{{ $demande->id }}" style="display: {{ $demande->status == 'approuve' ? 'block' : 'none' }};">
                                                    <div class="form-group mt-3">
                                                        <label for="montant">Montant :</label>
                                                        <input type="number" name="montant" class="form-control" value="{{ old('montant', $demande->montant) }}">
                                                    </div>

                                                    <div class="form-group mt-3">
                                                        <label for="observation">Observation :</label>
                                                        <textarea name="observation" class="form-control" rows="3">{{ old('observation', $demande->observation) }}</textarea>
                                                    </div>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script pour afficher/cacher les champs du montant -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fonction pour afficher ou cacher les champs en fonction du statut sélectionné
        function toggleMontantFields(statusSelectId, montantFieldsId) {
            const statusSelect = document.getElementById(statusSelectId);
            const montantFields = document.getElementById(montantFieldsId);

            // Vérifiez si le statut sélectionné est "approuve"
            if (statusSelect.value === 'approuve') {
                montantFields.style.display = 'block'; // Affichez les champs
            } else {
                montantFields.style.display = 'none'; // Cachez les champs
            }
        }

        @foreach($demandeFonds as $demande)
            const statusSelectId = 'status-{{ $demande->id }}';
            const montantFieldsId = 'montantFields-{{ $demande->id }}';

            // Écouteur d'événement pour le changement de statut
            document.getElementById(statusSelectId).addEventListener('change', function() {
                toggleMontantFields(statusSelectId, montantFieldsId);
            });

            // Appeler la fonction lors du chargement de la page pour définir l'état initial
            toggleMontantFields(statusSelectId, montantFieldsId);
        @endforeach
    });
</script>
@endpush

@endsection
