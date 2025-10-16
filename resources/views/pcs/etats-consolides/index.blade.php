@extends('layouts.master')

@section('title', 'États Consolidés PCS - Interface Unifiée')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- En-tête de page -->
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        États Consolidés PCS - Interface Dynamique
                    </h4>
                    <h6 class="text-muted">Générez vos états personnalisés en quelques clics</h6>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9">
                <!-- Carte de sélection du type d'état -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            Type d'état à générer
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check card-type-selector" onclick="selectTypeEtat('recouvrements')">
                                    <input class="form-check-input" type="radio" name="type_etat" id="type_recouvrements" value="recouvrements">
                                    <label class="form-check-label" for="type_recouvrements">
                                        <div class="type-card">
                                            <i class="fas fa-coins fa-3x text-success mb-3"></i>
                                            <h5>Recouvrements</h5>
                                            <p class="text-muted">État des recouvrements PCS par poste et mois</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card-type-selector" onclick="selectTypeEtat('reversements')">
                                    <input class="form-check-input" type="radio" name="type_etat" id="type_reversements" value="reversements">
                                    <label class="form-check-label" for="type_reversements">
                                        <div class="type-card">
                                            <i class="fas fa-exchange-alt fa-3x text-primary mb-3"></i>
                                            <h5>Reversements</h5>
                                            <p class="text-muted">État des reversements PCS par poste et mois</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card-type-selector" onclick="selectTypeEtat('autres-demandes')">
                                    <input class="form-check-input" type="radio" name="type_etat" id="type_autres_demandes" value="autres-demandes">
                                    <label class="form-check-label" for="type_autres_demandes">
                                        <div class="type-card">
                                            <i class="fas fa-folder-open fa-3x text-warning mb-3"></i>
                                            <h5>Autres Demandes</h5>
                                            <p class="text-muted">État des autres demandes financières</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte de filtrage -->
                <div class="card" id="filtresCard" style="display: none;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-filter text-primary me-2"></i>
                            Paramètres de filtrage
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="filtreForm" method="GET" target="_blank">
                            @csrf

                            <!-- Filtres communs -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="annee" class="form-label fw-bold">
                                        <i class="fas fa-calendar-year text-warning me-1"></i>
                                        Année de référence <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           class="form-control"
                                           id="annee"
                                           name="annee"
                                           value="{{ date('Y') }}"
                                           min="2020"
                                           max="{{ date('Y') + 1 }}"
                                           required>
                                </div>
                                <div class="col-md-6" id="programmeField">
                                    <label for="programme" class="form-label fw-bold">
                                        <i class="fas fa-globe text-info me-1"></i>
                                        Programme <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="programme" name="programme">
                                        <option value="">Tous les programmes</option>
                                        <option value="UEMOA">UEMOA</option>
                                        <option value="AES">AES</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Période de filtrage -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="date_debut" class="form-label fw-bold">
                                        <i class="fas fa-calendar-plus text-success me-1"></i>
                                        Date de début
                                    </label>
                                    <input type="date"
                                           class="form-control"
                                           id="date_debut"
                                           name="date_debut"
                                           value="{{ date('Y-01-01') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="date_fin" class="form-label fw-bold">
                                        <i class="fas fa-calendar-minus text-danger me-1"></i>
                                        Date de fin
                                    </label>
                                    <input type="date"
                                           class="form-control"
                                           id="date_fin"
                                           name="date_fin"
                                           value="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            <!-- Filtres supplémentaires -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="poste_id" class="form-label fw-bold">
                                        <i class="fas fa-map-marker-alt text-info me-1"></i>
                                        Filtrer par poste
                                    </label>
                                    <select class="form-select" id="poste_id" name="poste_id">
                                        <option value="">Tous les postes</option>
                                        @foreach($postes as $poste)
                                            <option value="{{ $poste->id }}">{{ $poste->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6" id="statutField">
                                    <label for="statut" class="form-label fw-bold">
                                        <i class="fas fa-tags text-secondary me-1"></i>
                                        Filtrer par statut
                                    </label>
                                    <select class="form-select" id="statut" name="statut">
                                        <option value="">Tous les statuts</option>
                                        <option value="brouillon">Brouillon</option>
                                        <option value="soumis">Soumis</option>
                                        <option value="valide">Validé</option>
                                        <option value="rejete">Rejeté</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Mois -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="mois" class="form-label fw-bold">
                                        <i class="fas fa-calendar-day text-primary me-1"></i>
                                        Filtrer par mois
                                    </label>
                                    <select class="form-select" id="mois" name="mois">
                                        <option value="">Tous les mois</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="format" class="form-label fw-bold">
                                        <i class="fas fa-file-export text-primary me-1"></i>
                                        Format d'export
                                    </label>
                                    <select class="form-select" id="format" name="format">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary btn-lg" id="btnGenerer" onclick="genererEtat()">
                                            <i class="fas fa-file-pdf me-2"></i>
                                            Générer l'état
                                        </button>
                                        <button type="button" class="btn btn-success btn-lg" onclick="afficherApercu()">
                                            <i class="fas fa-eye me-2"></i>
                                            Aperçu
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

            <div class="col-lg-3">
                <!-- Guide d'utilisation -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-question-circle text-info me-2"></i>
                            Guide d'utilisation
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="guide-step">
                            <div class="step-number">1</div>
                            <p>Choisissez le type d'état à générer</p>
                        </div>
                        <div class="guide-step">
                            <div class="step-number">2</div>
                            <p>Configurez vos filtres (année, période, poste...)</p>
                        </div>
                        <div class="guide-step">
                            <div class="step-number">3</div>
                            <p>Cliquez sur "Générer l'état" ou "Aperçu"</p>
                        </div>
                        <div class="guide-step">
                            <div class="step-number">4</div>
                            <p>Le PDF se télécharge automatiquement</p>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar text-success me-2"></i>
                            Statistiques
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="statsContainer">
                            <div class="text-center text-muted">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p>Sélectionnez un type d'état pour voir les statistiques</p>
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
                            Aperçu de l'état
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
                        <button type="button" class="btn btn-primary" onclick="genererEtat()">
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
let typeEtatSelectionne = null;

function selectTypeEtat(type) {
    typeEtatSelectionne = type;

    // Mettre à jour les radio buttons
    document.querySelectorAll('input[name="type_etat"]').forEach(radio => {
        radio.checked = radio.value === type;
    });

    // Ajouter la classe active
    document.querySelectorAll('.card-type-selector').forEach(card => {
        card.classList.remove('active');
    });
    event.currentTarget.classList.add('active');

    // Afficher le formulaire de filtres
    document.getElementById('filtresCard').style.display = 'block';

    // Adapter les champs selon le type
    if (type === 'autres-demandes') {
        document.getElementById('programmeField').style.display = 'none';
        document.getElementById('statutField').style.display = 'block';
    } else {
        document.getElementById('programmeField').style.display = 'block';
        document.getElementById('statutField').style.display = 'none';
    }

    // Charger les statistiques
    chargerStatistiques();

    // Scroll vers le formulaire
    document.getElementById('filtresCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function genererEtat() {
    if (!typeEtatSelectionne) {
        Swal.fire({
            icon: 'warning',
            title: 'Type d\'état non sélectionné',
            text: 'Veuillez sélectionner un type d\'état à générer',
        });
        return;
    }

    const params = new URLSearchParams({
        type: typeEtatSelectionne,
        annee: document.getElementById('annee').value,
        date_debut: document.getElementById('date_debut').value,
        date_fin: document.getElementById('date_fin').value,
        poste_id: document.getElementById('poste_id').value,
        mois: document.getElementById('mois').value,
        format: document.getElementById('format').value,
        _token: '{{ csrf_token() }}'
    });

    if (typeEtatSelectionne !== 'autres-demandes') {
        params.append('programme', document.getElementById('programme').value);
    } else {
        params.append('statut', document.getElementById('statut').value);
    }

    const url = '{{ route("pcs.etats-consolides.generer") }}?' + params.toString();
    window.open(url, '_blank');
}

function afficherApercu() {
    if (!typeEtatSelectionne) {
        Swal.fire({
            icon: 'warning',
            title: 'Type d\'état non sélectionné',
            text: 'Veuillez sélectionner un type d\'état pour l\'aperçu',
        });
        return;
    }

    const modal = new bootstrap.Modal(document.getElementById('apercuModal'));
    modal.show();

    const params = new URLSearchParams({
        type: typeEtatSelectionne,
        annee: document.getElementById('annee').value,
        date_debut: document.getElementById('date_debut').value,
        date_fin: document.getElementById('date_fin').value,
        poste_id: document.getElementById('poste_id').value,
        mois: document.getElementById('mois').value,
        apercu: '1',
        _token: '{{ csrf_token() }}'
    });

    if (typeEtatSelectionne !== 'autres-demandes') {
        params.append('programme', document.getElementById('programme').value);
    } else {
        params.append('statut', document.getElementById('statut').value);
    }

    fetch('{{ route("pcs.etats-consolides.apercu") }}?' + params.toString())
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

function chargerStatistiques() {
    if (!typeEtatSelectionne) return;

    const params = new URLSearchParams({
        type: typeEtatSelectionne,
        annee: document.getElementById('annee').value,
        date_debut: document.getElementById('date_debut').value,
        date_fin: document.getElementById('date_fin').value,
        poste_id: document.getElementById('poste_id').value,
        mois: document.getElementById('mois').value,
        _token: '{{ csrf_token() }}'
    });

    if (typeEtatSelectionne !== 'autres-demandes') {
        params.append('programme', document.getElementById('programme').value);
    }

    fetch('{{ route("pcs.etats-consolides.stats") }}?' + params.toString())
        .then(response => response.json())
        .then(data => {
            let statsHTML = '';

            if (typeEtatSelectionne === 'recouvrements' || typeEtatSelectionne === 'reversements') {
                statsHTML = `
                    <div class="stat-item text-center">
                        <h4 class="text-primary">${data.total}</h4>
                        <p class="text-muted mb-0">Total déclarations</p>
                    </div>
                    <hr>
                    <div class="stat-item text-center">
                        <h5 class="text-success">${data.montant}</h5>
                        <p class="text-muted mb-0">Montant total (FCFA)</p>
                    </div>
                    <hr>
                    <div class="stat-item text-center">
                        <h5 class="text-info">${data.postes}</h5>
                        <p class="text-muted mb-0">Postes actifs</p>
                    </div>
                `;
            } else {
                statsHTML = `
                    <div class="stat-item text-center">
                        <h4 class="text-primary">${data.total}</h4>
                        <p class="text-muted mb-0">Total demandes</p>
                    </div>
                    <hr>
                    <div class="stat-item text-center">
                        <h5 class="text-success">${data.montant_demande}</h5>
                        <p class="text-muted mb-0">Montant demandé</p>
                    </div>
                    <hr>
                    <div class="stat-item text-center">
                        <h5 class="text-warning">${data.montant_accorde}</h5>
                        <p class="text-muted mb-0">Montant accordé</p>
                    </div>
                `;
            }

            document.getElementById('statsContainer').innerHTML = statsHTML;
        })
        .catch(error => {
            console.error('Erreur stats:', error);
        });
}

function resetForm() {
    document.getElementById('filtreForm').reset();
    document.getElementById('annee').value = '{{ date("Y") }}';
    document.getElementById('date_debut').value = '{{ date("Y-01-01") }}';
    document.getElementById('date_fin').value = '{{ date("Y-m-d") }}';
    chargerStatistiques();
}

// Charger les stats quand les filtres changent
document.addEventListener('DOMContentLoaded', function() {
    ['annee', 'date_debut', 'date_fin', 'poste_id', 'programme', 'mois', 'statut'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', chargerStatistiques);
        }
    });
});
</script>

<style>
.card-type-selector {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 10px;
}

.card-type-selector:hover {
    border-color: #667eea;
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
}

.card-type-selector.active {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}

.card-type-selector input[type="radio"] {
    display: none;
}

.type-card {
    text-align: center;
    padding: 20px;
    border-radius: 8px;
    background: #fff;
    transition: all 0.3s ease;
}

.type-card h5 {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.type-card p {
    font-size: 0.9rem;
    margin-bottom: 0;
}

.guide-step {
    display: flex;
    align-items: start;
    margin-bottom: 15px;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 10px;
    flex-shrink: 0;
}

.guide-step p {
    margin: 0;
    line-height: 30px;
}

.stat-item h4, .stat-item h5 {
    margin-bottom: 5px;
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

#filtresCard {
    animation: slideDown 0.5s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
