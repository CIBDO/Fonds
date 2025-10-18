@extends('layouts.master')

@section('title', 'Vue Consolidée - Demandes de Fonds')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Vue Consolidée - Demandes de Fonds
                    </h4>
                    <div>
                        <button type="button" class="btn btn-light btn-sm" onclick="window.location.href='{{ route('demandes-fonds.consolide') }}'">
                            <i class="fas fa-redo me-1"></i>Réinitialiser
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Formulaire de filtres -->
                    <form method="GET" action="{{ route('demandes-fonds.consolide') }}" id="filterForm">
                        <div class="row g-3 mb-4">
                            <!-- Filtre Poste -->
                            <div class="col-md-3">
                                <label for="poste" class="form-label">Poste</label>
                                <select name="poste" id="poste" class="form-select">
                                    <option value="">Tous les postes</option>
                                    @foreach($postes as $poste)
                                        <option value="{{ $poste->nom }}" {{ request('poste') == $poste->nom ? 'selected' : '' }}>
                                            {{ $poste->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre Mois -->
                            <div class="col-md-2">
                                <label for="mois" class="form-label">Mois</label>
                                <select name="mois" id="mois" class="form-select">
                                    <option value="">Tous les mois</option>
                                    @foreach($mois as $m)
                                        <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>
                                            {{ $m }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre Année -->
                            <div class="col-md-2">
                                <label for="annee" class="form-label">Année</label>
                                <select name="annee" id="annee" class="form-select">
                                    <option value="">Toutes les années</option>
                                    @foreach($annees as $a)
                                        <option value="{{ $a }}" {{ request('annee') == $a ? 'selected' : '' }}>
                                            {{ $a }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre Statut -->
                            <div class="col-md-2">
                                <label for="status" class="form-label">Statut</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="approuve" {{ request('status') == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                                    <option value="rejete" {{ request('status') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                </select>
                            </div>

                            <!-- Filtre Utilisateur/Trésorier -->
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">Trésorier</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">Tous les trésoriers</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Type de date -->
                            <div class="col-md-2">
                                <label for="date_type" class="form-label">Type de date</label>
                                <select name="date_type" id="date_type" class="form-select">
                                    <option value="date_envois" {{ request('date_type') == 'date_envois' ? 'selected' : '' }}>Date d'envoi</option>
                                    <option value="date_reception" {{ request('date_type') == 'date_reception' ? 'selected' : '' }}>Date de réception</option>
                                    <option value="created_at" {{ request('date_type') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                                </select>
                            </div>

                            <!-- Date début -->
                            <div class="col-md-2">
                                <label for="date_debut" class="form-label">Date début</label>
                                <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut') }}">
                            </div>

                            <!-- Date fin -->
                            <div class="col-md-2">
                                <label for="date_fin" class="form-label">Date fin</label>
                                <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin') }}">
                            </div>

                            <!-- Boutons d'action -->
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter me-1"></i>Filtrer
                                </button>
                                <button type="button" class="btn btn-success me-2" onclick="exportCSV()">
                                    <i class="fas fa-file-csv me-1"></i>Exporter CSV
                                </button>
                                <button type="button" class="btn btn-danger" onclick="exportPDF()">
                                    <i class="fas fa-file-pdf me-1"></i>Exporter PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Cartes de totaux -->
                    {{-- <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Salaires Bruts</h6>
                                    <h4>{{ number_format($totaux['total_courant'], 0, ',', ' ') }} FCFA</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Recettes Douanières</h6>
                                    <h4>{{ number_format($totaux['montant_disponible'], 0, ',', ' ') }} FCFA</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Soldes</h6>
                                    <h4>{{ number_format($totaux['solde'], 0, ',', ' ') }} FCFA</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Montants Envoyés</h6>
                                    <h4>{{ number_format($totaux['montant_envoye'], 0, ',', ' ') }} FCFA</h4>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Tableau des demandes -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Poste</th>
                                    <th>Mois/Année</th>
                                    <th class="text-end">Total Courant</th>
                                    <th class="text-end">Montant Disponible</th>
                                    <th class="text-end">Solde</th>
                                    <th class="text-end">Montant Envoyé</th>
                                    <th>Date Envoi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demandeFonds as $demande)
                                    <tr>
                                        <td>{{ $demande->poste->nom ?? 'N/A' }}</td>
                                        <td>{{ $demande->mois }} {{ $demande->annee }}</td>
                                        <td class="text-end">{{ number_format($demande->total_courant, 0, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($demande->solde, 0, ',', ' ') }}</td>
                                        <td class="text-end">
                                            @if($demande->status === 'approuve' && $demande->montant)
                                                {{ number_format($demande->montant, 0, ',', ' ') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($demande->date_envois)
                                                {{ \Carbon\Carbon::parse($demande->date_envois)->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Aucune demande de fonds trouvée avec les filtres sélectionnés.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($demandeFonds->count() > 0)
                                <tfoot class="table-secondary fw-bold">
                                    <tr>
                                        <td colspan="2" class="text-end">TOTAUX :</td>
                                        <td class="text-end">{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($totaux['montant_disponible'], 0, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($totaux['solde'], 0, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($totaux['montant_envoye'], 0, ',', ' ') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Pagination améliorée -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de {{ $demandeFonds->firstItem() ?? 0 }} à {{ $demandeFonds->lastItem() ?? 0 }}
                            sur {{ $demandeFonds->total() }} résultats
                        </div>
                        <div>
                            {{ $demandeFonds->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fonction pour exporter en CSV
    function exportCSV() {
        const form = document.getElementById('filterForm');
        const url = new URL('{{ route("demandes-fonds.consolide.export-csv") }}', window.location.origin);

        // Ajouter tous les paramètres du formulaire à l'URL
        const formData = new FormData(form);
        formData.forEach((value, key) => {
            if (value) {
                url.searchParams.append(key, value);
            }
        });

        window.location.href = url.toString();
    }

    // Fonction pour exporter en PDF
    function exportPDF() {
        const form = document.getElementById('filterForm');
        const url = new URL('{{ route("demandes-fonds.consolide.export-pdf") }}', window.location.origin);

        // Ajouter tous les paramètres du formulaire à l'URL
        const formData = new FormData(form);
        formData.forEach((value, key) => {
            if (value) {
                url.searchParams.append(key, value);
            }
        });

        window.location.href = url.toString();
    }

    // Initialiser les tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

<style>
    .card {
        border-radius: 8px;
    }

    .table th {
        font-weight: 600;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.8em;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection

