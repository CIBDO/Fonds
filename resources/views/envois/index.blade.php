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

<form action="{{ route('envois-fonds.updateStatus', ['id' => $demandeFonds->first()->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
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
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Demandes</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <!-- Boutons d'action -->
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                        <thead class="student-thread">
                            <tr>
                                <!-- Assurez-vous que le nombre de <th> ici correspond au nombre de <td> dans chaque <tr> de <tbody> -->
                                <th>Mois</th>
                                {{-- <th>Date de réception</th> --}}
                                <th>Poste</th>
                                <th>Montant</th>
                                <th>Date de création</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="demandes-table-body">
                            @foreach($demandeFonds as $demande)
                            <tr>
                                <td>{{ $demande->mois }}</td>
                                {{-- <td>{{ $demande->date_reception }}</td> --}}
                                <td>{{ $demande->poste->nom }}</td>
                                <td>{{ number_format($demande->total_courant, 0, ',', ' ') }}</td>
                                <td>{{ $demande->created_at }}</td>
                                <td>{{ $demande->status }}</td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a href="{{ route('demandes-fonds.show', $demande->id) }}" class="btn btn-sm bg-success-light me-2">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('demandes-fonds.edit', $demande->id) }}" class="btn btn-sm bg-danger-light">
                                            <i class="feather-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm bg-primary-light" data-bs-toggle="modal" data-bs-target="#statusModal" data-id="{{ $demande->id }}">
                                            <i class="feather-check"></i>
                                        </a>
                                        <a href="{{ route('demande-fonds.generate.pdf', $demande->id) }}" class="btn btn-sm bg-info-light">
                                            <i class="feather-printer"></i>
                                        </a>
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

    <div class="pagination-wrapper">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Ajout de la classe Bootstrap pour la pagination -->
                {{ $demandeFonds->links('pagination::bootstrap-4') }}
            </ul>
        </nav>
    </div>
</div>
<!-- Modale pour l'approbation/rejet -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Approuver ou Rejeter la Demande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusFor" method="POST">
                @csrf
                <!-- Supprimer la directive -->
                <div class="modal-body">
                    <input type="hidden" name="demande_id" id="demande-id">
                    <div class="form-group">
                        <label for="date_envois">Date :</label>
                        <input type="date" name="date_envois" class="form-control" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label for="status">Statut :</label>
                        <select name="status" class="form-select" required>
                            <option value="approuve">Approuver</option>
                            <option value="rejete">Rejeter</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="montant">Montant :</label>
                        <input type="number" name="montant" class="form-control" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="observation">Observation :</label>
                        <textarea name="observation" class="form-control" rows="4" ></textarea>
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
@push('scripts')
<script>
    var statusModal = document.getElementById('statusModal');
    statusModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var demandeId = button.getAttribute('data-id');

        // Modifier l'action du formulaire pour inclure l'ID dans l'URL
        var form = document.getElementById('statusForm');
        form.action = "/envois-fonds/" + demandeId + "/updateStatus";

        // Mettre à jour le champ caché avec l'ID de la demande
        document.getElementById('demande-id').value = demandeId;
    });
</script>
@endpush

@endsection
