@extends('layouts.master')

@section('title', 'Envoi des Demandes de Fonds')

@include('partials.vuexy.datatables-assets')

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="icon-base ti tabler-alert-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<x-vuexy.card title="PDF consolidé mensuel" icon="tabler-file-type-pdf" class="mb-4">
    <form id="monthly-pdf-form" action="" method="GET" target="_blank" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="mois-select" class="form-label">Mois</label>
            <select name="mois" id="mois-select" class="form-select">
                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $m)
                    <option value="{{ $m }}" {{ request('mois', date('F')) == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="annee-select" class="form-label">Année</label>
            <select name="annee" id="annee-select" class="form-select">
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('annee', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-label-secondary w-100">
                <i class="icon-base ti tabler-file-type-pdf me-1"></i>Générer PDF consolidé
            </button>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Liste des demandes" icon="tabler-send">
    <form action="{{ route('demandes-fonds.envois') }}" method="GET" class="row g-3 mb-4">
        <div class="col-lg-2 col-md-6">
            <label class="form-label small">Poste</label>
            <select name="poste" class="form-select form-select-sm">
                <option value="">Tous les postes</option>
                @foreach($postes as $poste)
                    <option value="{{ $poste->nom }}" {{ request('poste') == $poste->nom ? 'selected' : '' }}>
                        {{ $poste->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small">Mois</label>
            <select name="mois" class="form-select form-select-sm">
                <option value="">Tous</option>
                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $m)
                    <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small">Année</label>
            <select name="annee" class="form-select form-select-sm">
                <option value="">Toutes</option>
                @for($y = (int) date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ (string) request('annee') === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small">Date envoi du</label>
            <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}">
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label small">Date envoi au</label>
            <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ request('date_fin') }}">
        </div>
        <div class="col-lg-auto d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-sm btn-primary"><i class="icon-base ti tabler-search"></i></button>
            <a href="{{ route('demandes-fonds.envois') }}" class="btn btn-sm btn-label-secondary">Réinit.</a>
        </div>
    </form>

    <div class="table-responsive">
        <table id="demandes-table" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mois</th>
                    <th>Date Réception</th>
                    <th>Poste</th>
                    <th class="text-end">Fonds Demandés</th>
                    <th>Date Création</th>
                    <th>Statut</th>
                    <th class="text-center" width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                <tr>
                    <td class="fw-medium">{{ $demande->mois }}</td>
                    <td>{{ date('d/m/Y', strtotime($demande->date_reception)) }}</td>
                    <td>{{ $demande->poste->nom }}</td>
                    <td class="text-end">{{ number_format($demande->total_courant, 0, ',', ' ') }}</td>
                    <td>{{ date('d/m/Y', strtotime($demande->created_at)) }}</td>
                    <td>@include('partials.demandes.status-badge', ['status' => $demande->status])</td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <a href="{{ route('demandes-fonds.show', $demande->id) }}"
                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                               data-bs-toggle="tooltip" title="Voir">
                                <i class="icon-base ti tabler-eye icon-22px"></i>
                            </a>
                            <button type="button" class="btn btn-icon btn-sm btn-text-success rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#approveModal-{{ $demande->id }}"
                                    title="Valider">
                                <i class="icon-base ti tabler-check icon-22px"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-sm btn-text-danger rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $demande->id }}"
                                    title="Rejeter">
                                <i class="icon-base ti tabler-x icon-22px"></i>
                            </button>
                            <a href="{{ route('demande-fonds.generate.pdf', $demande->id) }}"
                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                               data-bs-toggle="tooltip" title="PDF" target="_blank">
                                <i class="icon-base ti tabler-file-type-pdf icon-22px"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-vuexy.card>

@foreach($demandeFonds as $demande)
<div class="modal fade" id="approveModal-{{ $demande->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approuver — {{ $demande->poste->nom }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('demandes-fonds.update-status', $demande->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="status" value="approuve">
                    <div class="mb-3">
                        <label class="form-label">Date d'envoi</label>
                        <input type="text" name="date_envois" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant</label>
                        <input type="text" name="montant" class="form-control" oninput="formatNumberInput(this)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recette douanière</label>
                        <input type="text" name="montant_disponible" class="form-control" value="{{ number_format($demande->montant_disponible, 0, ',', ' ') }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Solde du mois</label>
                        <input type="text" name="solde" class="form-control" value="{{ number_format($demande->solde, 0, ',', ' ') }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observation</label>
                        <textarea name="observation" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal-{{ $demande->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeter — {{ $demande->poste->nom }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('demandes-fonds.update-status', $demande->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="status" value="rejete">
                    <div class="mb-3">
                        <label class="form-label">Date d'envoi</label>
                        <input type="date" name="date_envois" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Raison du rejet</label>
                        <textarea name="observation" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Soumettre</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
@include('partials.demandes.datatable-init', ['orderCol' => 4, 'title' => 'Envoi des Demandes'])
<script>
function formatNumberInput(input) {
    input.value = input.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('monthly-pdf-form');
    const moisSelect = document.getElementById('mois-select');
    const anneeSelect = document.getElementById('annee-select');
    function updateFormAction() {
        form.action = "{{ url('demandes-fonds/mois') }}/" + moisSelect.value + "/" + anneeSelect.value + "/pdf";
    }
    updateFormAction();
    moisSelect.addEventListener('change', updateFormAction);
    anneeSelect.addEventListener('change', updateFormAction);

    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('input[name="montant"], input[name="solde"], input[name="montant_disponible"]').forEach(function(input) {
                if (input.value) input.value = input.value.replace(/\s+/g, '');
            });
        });
    });
});
</script>
@endpush
