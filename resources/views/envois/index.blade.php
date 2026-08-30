@extends('layouts.master')

@section('title', 'Demandes — Envois')

@include('partials.vuexy.datatables-assets')

@section('content')
<x-vuexy.page-header title="Demandes" subtitle="Gestion des envois de fonds">
    <x-slot:actions>
        <a href="{{ route('dashboard') }}" class="btn btn-label-secondary btn-sm">
            <i class="icon-base ti tabler-arrow-left me-1"></i>Dashboard
        </a>
    </x-slot:actions>
</x-vuexy.page-header>

<x-vuexy.card title="Filtres de recherche" icon="tabler-filter" class="mb-6">
    <form action="{{ url()->current() }}" method="GET">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Poste</label>
                <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste..." value="{{ request('poste') }}">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Mois</label>
                <input type="text" name="mois" class="form-control" placeholder="Rechercher par mois..." value="{{ request('mois') }}">
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label">Montant</label>
                <input type="text" name="total_courant" class="form-control" placeholder="Rechercher par montant..." value="{{ request('total_courant') }}">
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="icon-base ti tabler-search me-1"></i>Rechercher
                </button>
            </div>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Liste des Demandes" icon="tabler-list">
    <div class="table-responsive">
        <table class="table table-bordered align-middle" id="envois-table">
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Poste</th>
                    <th>Montant</th>
                    <th>Date de création</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                <tr>
                    <td>{{ $demande->mois }}</td>
                    <td>{{ $demande->poste->nom }}</td>
                    <td>{{ number_format($demande->total_courant, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $demande->created_at }}</td>
                    <td>
                        <span class="badge bg-label-secondary">{{ $demande->status }}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('demandes-fonds.show', $demande->id) }}" class="btn btn-outline-primary" title="Voir">
                                <i class="icon-base ti tabler-eye"></i>
                            </a>
                            <a href="{{ route('demandes-fonds.edit', $demande->id) }}" class="btn btn-outline-warning" title="Modifier">
                                <i class="icon-base ti tabler-edit"></i>
                            </a>
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#statusModal" data-id="{{ $demande->id }}" title="Statut">
                                <i class="icon-base ti tabler-check"></i>
                            </button>
                            <a href="{{ route('demande-fonds.generate.pdf', $demande->id) }}" class="btn btn-outline-info" title="PDF">
                                <i class="icon-base ti tabler-printer"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $demandeFonds->links('pagination::bootstrap-4') }}
    </div>
</x-vuexy.card>

<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Approuver ou Rejeter la Demande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="demande_id" id="demande-id">
                    <div class="mb-3">
                        <label for="date_envois" class="form-label">Date</label>
                        <input type="date" name="date_envois" id="date_envois" class="form-control" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="approuve">Approuver</option>
                            <option value="rejete">Rejeter</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant</label>
                        <input type="number" name="montant" id="montant" class="form-control" required>
                    </div>
                    <div class="mb-0">
                        <label for="observation" class="form-label">Observation</label>
                        <textarea name="observation" id="observation" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Soumettre</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#envois-table').DataTable({
        language: typeof datatablesFrench !== 'undefined' ? datatablesFrench : window.DGTCP_DATATABLES_FR,
        paging: false,
        searching: true,
        ordering: true,
        responsive: true
    });

    var statusModal = document.getElementById('statusModal');
    statusModal?.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var demandeId = button.getAttribute('data-id');
        var form = document.getElementById('statusForm');
        form.action = '/envois-fonds/' + demandeId + '/updateStatus';
        document.getElementById('demande-id').value = demandeId;
    });
});
</script>
@endpush
