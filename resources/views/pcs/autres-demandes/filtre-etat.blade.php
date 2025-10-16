@extends('layouts.master')

@section('title', 'Filtrage État Autres Demandes PCS')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- En-tête de page -->
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>
                        <i class="fas fa-filter text-primary me-2"></i>
                        Filtrage État Autres Demandes PCS
                    </h4>
                    <h6 class="text-muted">Génération d'états consolidés avec filtres personnalisés</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <li>
                    <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Carte de filtrage -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            Paramètres de filtrage
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="filtreForm" method="GET" action="{{ route('pcs.autres-demandes.etat-consolide.autres-demandes') }}" target="_blank">
                            <!-- Période de filtrage -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="date_debut" class="form-label fw-bold">
                                        <i class="fas fa-calendar-plus text-success me-1"></i>
                                        Date de début <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('date_debut') is-invalid @enderror"
                                           id="date_debut"
                                           name="date_debut"
                                           value="{{ old('date_debut', request('date_debut', date('Y-01-01'))) }}"
                                           required>
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="date_fin" class="form-label fw-bold">
                                        <i class="fas fa-calendar-minus text-danger me-1"></i>
                                        Date de fin <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('date_fin') is-invalid @enderror"
                                           id="date_fin"
                                           name="date_fin"
                                           value="{{ old('date_fin', request('date_fin', date('Y-m-d'))) }}"
                                           required>
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Année de référence -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="annee" class="form-label fw-bold">
                                        <i class="fas fa-calendar-year text-warning me-1"></i>
                                        Année de référence <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           class="form-control @error('annee') is-invalid @enderror"
                                           id="annee"
                                           name="annee"
                                           value="{{ old('annee', request('annee', date('Y'))) }}"
                                           min="2020"
                                           max="{{ date('Y') + 1 }}"
                                           required>
                                    @error('annee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="poste_id" class="form-label fw-bold">
                                        <i class="fas fa-map-marker-alt text-info me-1"></i>
                                        Filtrer par poste
                                    </label>
                                    <select class="form-select @error('poste_id') is-invalid @enderror"
                                            id="poste_id"
                                            name="poste_id">
                                        <option value="">Tous les postes</option>
                                        @foreach($postes as $poste)
                                            <option value="{{ $poste->id }}"
                                                    {{ old('poste_id', request('poste_id')) == $poste->id ? 'selected' : '' }}>
                                                {{ $poste->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('poste_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Statut -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="statut" class="form-label fw-bold">
                                        <i class="fas fa-tags text-secondary me-1"></i>
                                        Filtrer par statut
                                    </label>
                                    <select class="form-select @error('statut') is-invalid @enderror"
                                            id="statut"
                                            name="statut">
                                        <option value="">Tous les statuts</option>
                                        <option value="brouillon" {{ old('statut', request('statut')) == 'brouillon' ? 'selected' : '' }}>
                                            Brouillon
                                        </option>
                                        <option value="soumis" {{ old('statut', request('statut')) == 'soumis' ? 'selected' : '' }}>
                                            Soumis
                                        </option>
                                        <option value="valide" {{ old('statut', request('statut')) == 'valide' ? 'selected' : '' }}>
                                            Validé
                                        </option>
                                        <option value="rejete" {{ old('statut', request('statut')) == 'rejete' ? 'selected' : '' }}>
                                            Rejeté
                                        </option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="format" class="form-label fw-bold">
                                        <i class="fas fa-file-export text-primary me-1"></i>
                                        Format d'export
                                    </label>
                                    <select class="form-select" id="format" name="format">
                                        <option value="pdf" selected>PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg" id="btnGenerer">
                                            <i class="fas fa-file-pdf me-2"></i>
                                            Générer l'état PDF
                                        </button>
                                        <button type="button" class="btn btn-success btn-lg" id="btnApercu" onclick="afficherApercu()">
                                            <i class="fas fa-eye me-2"></i>
                                            Aperçu des données
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo me-1"></i>
                                            Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Aide et informations -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle text-info me-2"></i>
                            Informations
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb me-1"></i> Comment utiliser :</h6>
                            <ul class="mb-0">
                                <li>Sélectionnez la période de filtrage</li>
                                <li>Choisissez l'année de référence</li>
                                <li>Optionnel : filtrez par poste ou statut</li>
                                <li>Cliquez sur "Générer l'état PDF"</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-1"></i> Note importante :</h6>
                            <p class="mb-0">L'état PDF sera généré avec les données correspondant aux critères sélectionnés et téléchargé automatiquement.</p>
                        </div>
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar text-success me-2"></i>
                            Statistiques rapides
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="statsContainer">
                            <div class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">Chargement des statistiques...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal d'aperçu -->
        <div class="modal fade" id="apercuModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-eye me-2"></i>
                            Aperçu des données filtrées
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="apercuContent">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">Chargement de l'aperçu...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" onclick="genererAvecParametres()">
                            <i class="fas fa-file-pdf me-1"></i>
                            Générer le PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('add-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation des dates
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const btnGenerer = document.getElementById('btnGenerer');

    function validateDates() {
        const debut = new Date(dateDebut.value);
        const fin = new Date(dateFin.value);

        if (debut > fin) {
            dateFin.setCustomValidity('La date de fin doit être postérieure à la date de début');
            btnGenerer.disabled = true;
        } else {
            dateFin.setCustomValidity('');
            btnGenerer.disabled = false;
        }
    }

    dateDebut.addEventListener('change', validateDates);
    dateFin.addEventListener('change', validateDates);

    // Charger les statistiques au chargement de la page
    chargerStatistiques();

    // Recharger les statistiques quand les filtres changent
    [dateDebut, dateFin, document.getElementById('poste_id'), document.getElementById('statut')].forEach(element => {
        element.addEventListener('change', chargerStatistiques);
    });
});

function resetForm() {
    document.getElementById('filtreForm').reset();
    document.getElementById('date_debut').value = '{{ date("Y-01-01") }}';
    document.getElementById('date_fin').value = '{{ date("Y-m-d") }}';
    document.getElementById('annee').value = '{{ date("Y") }}';
    document.getElementById('poste_id').value = '';
    document.getElementById('statut').value = '';
    document.getElementById('format').value = 'pdf';
    chargerStatistiques();
}

function chargerStatistiques() {
    const params = new URLSearchParams({
        date_debut: document.getElementById('date_debut').value,
        date_fin: document.getElementById('date_fin').value,
        annee: document.getElementById('annee').value,
        poste_id: document.getElementById('poste_id').value,
        statut: document.getElementById('statut').value,
        _token: '{{ csrf_token() }}'
    });

    fetch('{{ route("pcs.autres-demandes.stats-rapides") }}?' + params)
        .then(response => response.json())
        .then(data => {
            document.getElementById('statsContainer').innerHTML = `
                <div class="row text-center">
                    <div class="col-6">
                        <div class="stat-item">
                            <h3 class="text-primary">${data.total_demandes}</h3>
                            <p class="text-muted mb-0">Total demandes</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-item">
                            <h3 class="text-success">${data.montant_total}</h3>
                            <p class="text-muted mb-0">Montant total</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="stat-item">
                            <h3 class="text-warning">${data.demandes_soumises}</h3>
                            <p class="text-muted mb-0">Soumises</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-item">
                            <h3 class="text-info">${data.demandes_validees}</h3>
                            <p class="text-muted mb-0">Validées</p>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('statsContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Erreur lors du chargement des statistiques
                </div>
            `;
        });
}

function afficherApercu() {
    const modal = new bootstrap.Modal(document.getElementById('apercuModal'));
    modal.show();

    // Charger l'aperçu
    const params = new URLSearchParams({
        date_debut: document.getElementById('date_debut').value,
        date_fin: document.getElementById('date_fin').value,
        annee: document.getElementById('annee').value,
        poste_id: document.getElementById('poste_id').value,
        statut: document.getElementById('statut').value,
        apercu: '1',
        _token: '{{ csrf_token() }}'
    });

    fetch('{{ route("pcs.autres-demandes.apercu") }}?' + params)
        .then(response => response.text())
        .then(html => {
            document.getElementById('apercuContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('apercuContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Erreur lors du chargement de l'aperçu
                </div>
            `;
        });
}

function genererAvecParametres() {
    document.getElementById('filtreForm').submit();
}
</script>

<style>
.stat-item {
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
    margin-bottom: 1rem;
}

.stat-item h3 {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.form-label {
    margin-bottom: 0.5rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.alert {
    border-radius: 8px;
}

.card {
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0 !important;
}

.page-title h4 {
    color: #2c3e50;
    font-weight: 600;
}

.page-title h6 {
    color: #6c757d;
    font-size: 0.9rem;
}
</style>
@endsection
