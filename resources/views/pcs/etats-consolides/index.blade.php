@extends('layouts.master')

@section('title', 'États Consolidés PCS - Interface Unifiée')

@section('content')

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
            <!-- Carte de sélection du type d'état -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            Type d'état à générer
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
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
                            <div class="col-lg-4 col-md-6">
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
                            <div class="col-lg-4 col-md-6">
                                <div class="form-check card-type-selector" onclick="selectTypeEtat('uemoa-aes')">
                                    <input class="form-check-input" type="radio" name="type_etat" id="type_uemoa_aes" value="uemoa-aes">
                                    <label class="form-check-label" for="type_uemoa_aes">
                                        <div class="type-card">
                                            <i class="fas fa-globe-africa fa-3x text-info mb-3"></i>
                                            <h5>États UEMOA/AES</h5>
                                            <p class="text-muted">Situation mensuelle des liquidations UEMOA/AES</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
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
            </div>

            <!-- Carte de filtrage -->
            <div class="col-12 mb-4">
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
                                <div class="col-xl-3 col-lg-4 col-md-6">
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
                                <div class="col-xl-3 col-lg-4 col-md-6" id="programmeField">
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
                                <div class="col-xl-3 col-lg-4 col-md-6">
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
                                <div class="col-xl-3 col-lg-4 col-md-6">
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
                                <div class="col-xl-3 col-lg-4 col-md-6">
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
                                <div class="col-xl-3 col-lg-4 col-md-6" id="statutField">
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
                                <div class="col-xl-3 col-lg-4 col-md-6">
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
                                <div class="col-xl-3 col-lg-4 col-md-6">
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
        </div>

        <!-- Section des états UEMOA/AES -->
        <div class="row" id="uemoaAesSection" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-globe-africa text-info me-2"></i>
                            États UEMOA et AES - Situation Mensuelle des Liquidations
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Sélecteur de programme -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="programmeUemoaAes" class="form-label fw-bold">Programme</label>
                                <select class="form-select" id="programmeUemoaAes" onchange="chargerEtatUemoaAes()">
                                    <option value="UEMOA">UEMOA</option>
                                    <option value="AES">AES</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="anneeUemoaAes" class="form-label fw-bold">Année</label>
                                <select class="form-select" id="anneeUemoaAes" onchange="chargerEtatUemoaAes()">
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                </select>
                            </div>
                        </div>

                        <!-- Contenu des états -->
                        <div id="contenuEtatUemoaAes">
                            <!-- Les états seront chargés ici dynamiquement -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section des statistiques et guide en bas -->
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <!-- Guide d'utilisation -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-question-circle text-info me-2"></i>
                            Guide d'utilisation
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="guide-step">
                                    <div class="step-number">1</div>
                                    <p>Choisissez le type d'état à générer</p>
                                </div>
                                <div class="guide-step">
                                    <div class="step-number">2</div>
                                    <p>Configurez vos filtres (année, période, poste...)</p>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
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
        document.getElementById('uemoaAesSection').style.display = 'none';
        document.getElementById('filtresCard').style.display = 'block';
        // Charger les statistiques normales
        chargerStatistiques();
    } else if (type === 'uemoa-aes') {
        document.getElementById('programmeField').style.display = 'none';
        document.getElementById('statutField').style.display = 'none';
        document.getElementById('filtresCard').style.display = 'none';
        document.getElementById('uemoaAesSection').style.display = 'block';
        // Charger l'état UEMOA/AES (les statistiques seront chargées automatiquement)
        chargerEtatUemoaAes();
    } else {
        document.getElementById('programmeField').style.display = 'block';
        document.getElementById('statutField').style.display = 'none';
        document.getElementById('uemoaAesSection').style.display = 'none';
        document.getElementById('filtresCard').style.display = 'block';
        // Charger les statistiques normales
        chargerStatistiques();
    }

    // Scroll vers la section appropriée
    if (type === 'uemoa-aes') {
        document.getElementById('uemoaAesSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        document.getElementById('filtresCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
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

    // Ne pas charger les statistiques pour UEMOA/AES (elles sont chargées séparément)
    if (typeEtatSelectionne === 'uemoa-aes') return;

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

// Fonction pour charger les états UEMOA/AES
function chargerEtatUemoaAes() {
    const programme = document.getElementById('programmeUemoaAes').value;
    const annee = document.getElementById('anneeUemoaAes').value;

    // Afficher un loader
    document.getElementById('contenuEtatUemoaAes').innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <p>Chargement des données...</p>
        </div>
    `;

    // Récupérer les données du backend
    fetch(`{{ route('pcs.etats-consolides.donnees-uemoa-aes') }}?programme=${programme}&annee=${annee}`)
        .then(response => response.json())
        .then(donnees => {
            genererAffichageEtat(donnees);
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('contenuEtatUemoaAes').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Erreur lors du chargement des données. Veuillez réessayer.
                </div>
            `;
        });
}

// Fonction pour générer l'affichage avec les données reçues
function genererAffichageEtat(donnees) {
    // Données de test basées sur les images (gardé en commentaire pour référence)
    /*const donneesUemoa = {
        titre: "SITUATION MENSUELLE DES LIQUIDATIONS DES RECOUVREMENTS ET DES REVERSEMENTS DU PCS-UEMOA AU TITRE DE L'EXERCICE " + annee + " (REGIONS)",
        recouvrements: {
            "KAYES": [105.6, 102.6, 148.4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 356.6],
            "KOULIKORO": [316.6, 267.9, 278.7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 863.2],
            "SIKASSO": [31.3, 32.5, 29.7, 33.4, 38.2, 41.5, 56.2, 54.7, 0, 0, 0, 0, 317.4],
            "SEGOU": [13.9, 21.6, 21.1, 15.2, 26.3, 15.4, 26.3, 22.9, 0, 0, 0, 0, 162.7],
            "MOPTI": [7.2, 6.3, 4.9, 2.5, 4.9, 5.4, 0, 0, 0, 0, 0, 0, 31.3],
            "TOMBOUCTOU": [0.2, 0.2, 0.3, 0.3, 0, 0, 0, 0, 0, 0, 0, 0, 1.1],
            "GAO": [1.7, 1.8, 1.5, 0, 0, 0, 1.8, 0, 0, 0, 0, 0, 6.8],
            "KIDAL": [0.02, 0.006, 0.006, 0.006, 0.008, 0.006, 0, 0.08, 0, 0, 0, 0, 0.13],
            "MENAKA": [0.007, 0.008, 0.007, 0.003, 0, 0, 0, 0, 0, 0, 0, 0, 0.025],
            "BOUGOUNI": [2.1, 4.0, 3.5, 8.2, 0, 0, 0, 0, 0, 0, 0, 0, 17.7],
            "NIORO": [8.1, 8.8, 6.2, 4.5, 0, 0, 0, 0, 0, 0, 0, 0, 27.5],
            "KOUTIALA": [13.8, 16.1, 16.3, 5.9, 21.0, 14.8, 19.7, 0, 0, 0, 0, 0, 107.6],
            "KITA": [10.3, 13.3, 20.7, 17.8, 0, 0, 0, 0, 0, 0, 0, 0, 62.2],
            "SAN": [1.2, 1.2, 1.9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4.4],
            "NARA": [0.02, 0.02, 0, 0.02, 0.02, 0.02, 0.02, 0.02, 0, 0, 0, 0, 0.13],
            "Bandiagara": [0.2, 0.3, 0, 0, 0.002, 0.003, 0, 0, 0, 0, 0, 0, 0.47]
        },
        reversements: {
            "KAYES": [105.6, 102.6, 148.4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 356.6],
            "KOULIKORO": [316.6, 267.9, 278.7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 863.2],
            "SIKASSO": [31.3, 32.5, 29.7, 33.4, 38.2, 41.5, 56.2, 22.9, 0, 0, 0, 0, 262.7],
            "SEGOU": [13.9, 21.6, 21.1, 15.2, 26.3, 15.4, 26.3, 22.9, 0, 0, 0, 0, 162.7],
            "MOPTI": [7.2, 6.3, 4.9, 2.5, 4.9, 5.4, 0, 0, 0, 0, 0, 0, 31.3],
            "TOMBOUCTOU": [0.2, 0.2, 0.3, 0.3, 0, 0, 0, 0, 0, 0, 0, 0, 1.1],
            "GAO": [1.7, 1.7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3.5],
            "KIDAL": [0.02, 0.006, 0.006, 0.006, 0.008, 0.006, 0, 0.08, 0, 0, 0, 0, 0.13],
            "MENAKA": [0.007, 0.008, 0.007, 0.003, 0, 0, 0, 0, 0, 0, 0, 0, 0.025],
            "BOUGOUNI": [2.1, 4.0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 6.1],
            "NIORO": [137.0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 137.0],
            "KOUTIALA": [13.8, 16.1, 16.3, 5.9, 21.0, 14.8, 19.7, 0, 0, 0, 0, 0, 107.6],
            "KITA": [10.3, 13.3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 23.7],
            "SAN": [1.2, 1.2, 1.9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4.4],
            "NARA": [0.02, 0.02, 0, 0.02, 0.02, 0.02, 0.02, 0.02, 0, 0, 0, 0, 0.13],
            "Bandiagara": [0.2, 0.3, 0, 0, 0.002, 0.003, 0, 0, 0, 0, 0, 0, 0.47]
        }
    };

    */

    // Générer le tableau et récupérer les totaux (une seule fois)
    const resultatTableau = genererTableauComplet(donnees.recouvrements, donnees.reversements);

    // Générer le HTML des états avec les données reçues du backend
    let html = `
        <div class="etat-container">
            <div class="etat-header text-center mb-4">
                <h4 class="fw-bold">MINISTÈRE DE L'ÉCONOMIE ET DES FINANCES</h4>
                <h5 class="fw-bold">DIRECTION GÉNÉRALE DU TRÉSOR ET DE LA COMPTABILITÉ PUBLIQUE</h5>
                <h6 class="fw-bold">AGENCE CENTRALE DE COMPTABILITÉ DU TRÉSOR</h6>
                <hr>
                <h4 class="fw-bold text-primary">${donnees.titre}</h4>
                <p class="text-muted">Période du 1er Janvier ${donnees.annee} au ${new Date().toLocaleDateString('fr-FR')}</p>
                <p class="text-muted">Bamako, le ${new Date().toLocaleDateString('fr-FR')}</p>
                <hr>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> Les montants sont exprimés en <strong>millions de francs CFA</strong>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <h5 class="fw-bold text-success">ÉTAT CONSOLIDÉ ${donnees.annee}</h5>
                    ${resultatTableau.html}
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small">*Ces données mensuelles sont provisoires et ne concernent que les déclarations validées</p>
                <button class="btn btn-primary" onclick="genererPDFUemoaAes()">
                    <i class="fas fa-file-pdf me-2"></i>Générer PDF
                </button>
            </div>
        </div>
    `;

    document.getElementById('contenuEtatUemoaAes').innerHTML = html;

    // Mettre à jour les statistiques avec les totaux déjà calculés
    afficherStatistiquesUemoaAes(resultatTableau.totalRecouvrements, resultatTableau.totalReversements, resultatTableau.totalResteAReverser);
}

// Fonction pour générer le tableau complet avec recouvrements, reversements et reste à reverser
function genererTableauComplet(recouvrements, reversements) {
    const mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    // Obtenir tous les postes uniques
    const tousLesPostes = new Set([...Object.keys(recouvrements), ...Object.keys(reversements)]);
    const postesTriés = Array.from(tousLesPostes).sort();

    let html = `
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th rowspan="2" class="text-center align-middle" style="vertical-align: middle;">POSTES COMPTABLES</th>
                        <th colspan="13" class="text-center" style="background-color: #28a745 !important; color: white; font-weight: bold; font-size: 14px;">RECOUVREMENTS</th>
                        <th colspan="13" class="text-center" style="background-color: #007bff !important; color: white; font-weight: bold; font-size: 14px;">REVERSEMENTS</th>
                        <th rowspan="2" class="text-center align-middle" style="background-color: #ffc107 !important; color: #000; font-weight: bold; vertical-align: middle;">RESTE À REVERSER</th>
                    </tr>
                    <tr>
                        <th class="text-center bg-success-light">EX. ANT.</th>
                        ${mois.map(m => `<th class="text-center bg-success-light">${m.substr(0, 3).toUpperCase()}</th>`).join('')}
                        <th class="text-center bg-success-light">TOTAL</th>
                        <th class="text-center bg-primary-light">EX. ANT.</th>
                        ${mois.map(m => `<th class="text-center bg-primary-light">${m.substr(0, 3).toUpperCase()}</th>`).join('')}
                        <th class="text-center bg-primary-light">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Calculer les totaux
    let totauxRecouvrements = Array(14).fill(0); // Ex ant + 12 mois + total
    let totauxReversements = Array(14).fill(0);
    let totalResteAReverser = 0;

    postesTriés.forEach(poste => {
        const valeursRecouvrement = recouvrements[poste] || Array(13).fill(0);
        const valeursReversement = reversements[poste] || Array(13).fill(0);

        html += `<tr>`;
        html += `<td class="fw-bold">${poste}</td>`;

        // Recouvrements - Exercice antérieur
        html += `<td class="text-end">0</td>`;

        // Recouvrements - Mois
        for (let i = 0; i < 12; i++) {
            const valeur = valeursRecouvrement[i] || 0;
            html += `<td class="text-end">${valeur.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>`;
            totauxRecouvrements[i + 1] += valeur;
        }

        // Recouvrements - Total
        const totalRecouvrement = valeursRecouvrement[12] || 0;
        html += `<td class="text-end fw-bold bg-light">${totalRecouvrement.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>`;
        totauxRecouvrements[13] += totalRecouvrement;

        // Reversements - Exercice antérieur
        html += `<td class="text-end">0</td>`;

        // Reversements - Mois
        for (let i = 0; i < 12; i++) {
            const valeur = valeursReversement[i] || 0;
            html += `<td class="text-end">${valeur.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>`;
            totauxReversements[i + 1] += valeur;
        }

        // Reversements - Total
        const totalReversement = valeursReversement[12] || 0;
        html += `<td class="text-end fw-bold bg-light">${totalReversement.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>`;
        totauxReversements[13] += totalReversement;

        // Reste à reverser
        const resteAReverser = totalRecouvrement - totalReversement;
        html += `<td class="text-end fw-bold ${resteAReverser > 0 ? 'text-danger' : 'text-success'}">${resteAReverser.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>`;
        totalResteAReverser += resteAReverser;

        html += `</tr>`;
    });

    // Ligne des totaux
    html += `
        <tr class="table-success fw-bold">
            <td>TOTAUX</td>
            <td class="text-end">0</td>
            ${totauxRecouvrements.slice(1, 13).map(t => `<td class="text-end">${t.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>`).join('')}
            <td class="text-end">${totauxRecouvrements[13].toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end">0</td>
            ${totauxReversements.slice(1, 13).map(t => `<td class="text-end">${t.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>`).join('')}
            <td class="text-end">${totauxReversements[13].toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end ${totalResteAReverser > 0 ? 'text-danger' : 'text-success'}">${totalResteAReverser.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
        </tr>
    `;

    html += `
                </tbody>
            </table>
        </div>
    `;

    // Retourner le HTML et les totaux pour les statistiques
    return {
        html: html,
        totalRecouvrements: totauxRecouvrements[13],
        totalReversements: totauxReversements[13],
        totalResteAReverser: totalResteAReverser
    };
}

// Fonction pour afficher les statistiques UEMOA/AES
function afficherStatistiquesUemoaAes(totalRecouvrements, totalReversements, totalResteAReverser) {
    const statsHTML = `
        <div class="stat-item text-center">
            <h4 class="text-success">${totalRecouvrements.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
            <p class="text-muted mb-0">Recouvrement (Millions FCFA)</p>
        </div>
        <hr>
        <div class="stat-item text-center">
            <h4 class="text-primary">${totalReversements.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
            <p class="text-muted mb-0">Reversement (Millions FCFA)</p>
        </div>
        <hr>
        <div class="stat-item text-center">
            <h4 class="${totalResteAReverser > 0 ? 'text-danger' : 'text-success'}">${totalResteAReverser.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
            <p class="text-muted mb-0">Reste à reverser (Millions FCFA)</p>
        </div>
    `;

    document.getElementById('statsContainer').innerHTML = statsHTML;
}

// Fonction pour générer le PDF
function genererPDFUemoaAes() {
    const programme = document.getElementById('programmeUemoaAes').value;
    const annee = document.getElementById('anneeUemoaAes').value;

    // Préparer l'URL avec tous les paramètres nécessaires
    const params = new URLSearchParams({
        type: programme === 'UEMOA' ? 'recouvrements' : 'reversements',
        programme: programme,
        annee: annee,
        format: 'pdf',
        _token: '{{ csrf_token() }}'
    });

    const url = `{{ route("pcs.etats-consolides.generer") }}?${params.toString()}`;

    // Ouvrir dans un nouvel onglet
    window.open(url, '_blank');
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
/* Optimisation pour l'utilisation de toute la largeur */
.page-wrapper {
    max-width: 100%;
    padding: 0 15px;
}

.content {
    max-width: 100%;
}

/* Cards de sélection de type d'état */
.card-type-selector {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 10px;
    height: 100%;
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
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
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

/* Formulaires de filtrage optimisés */
.form-label {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.form-control, .form-select {
    font-size: 0.9rem;
}

/* Guide d'utilisation */
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
    font-size: 0.9rem;
}

.stat-item h4, .stat-item h5 {
    margin-bottom: 5px;
}

/* Cards générales */
.card {
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0 !important;
}

.card-header .card-title {
    font-size: 1.1rem;
}

/* Animation pour les filtres */
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

/* Responsive optimisé */
@media (min-width: 1200px) {
    .col-xl-3 {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

@media (min-width: 992px) {
    .col-lg-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
    .col-lg-5 {
        flex: 0 0 41.666667%;
        max-width: 41.666667%;
    }
    .col-lg-7 {
        flex: 0 0 58.333333%;
        max-width: 58.333333%;
    }
}

/* Boutons d'action */
.btn-lg {
    padding: 12px 24px;
    font-size: 1rem;
}

/* Optimisation des statistiques */
#statsContainer .stat-item {
    text-align: center;
    padding: 10px 0;
}

#statsContainer .stat-item h4 {
    font-size: 1.5rem;
    font-weight: bold;
}

#statsContainer .stat-item h5 {
    font-size: 1.2rem;
    font-weight: 600;
}

/* Styles spécifiques pour les états UEMOA/AES */
.etat-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Styles pour les en-têtes des colonnes */
.bg-success-light {
    background-color: rgba(40, 167, 69, 0.3) !important;
    color: #155724 !important;
    font-weight: bold !important;
}

.bg-primary-light {
    background-color: rgba(0, 123, 255, 0.3) !important;
    color: #004085 !important;
    font-weight: bold !important;
}

.etat-header {
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.etat-header h4, .etat-header h5, .etat-header h6 {
    margin-bottom: 5px;
    color: #2c3e50;
}

.etat-header .text-primary {
    color: #007bff !important;
}

/* Styles des tableaux */
.etat-container .table {
    font-size: 0.85rem;
    margin-bottom: 30px;
}

.etat-container .table th {
    background-color: #343a40;
    color: white;
    font-weight: bold;
    text-align: center;
    vertical-align: middle;
    padding: 8px 4px;
    border: 1px solid #dee2e6;
}

.etat-container .table td {
    padding: 6px 4px;
    border: 1px solid #dee2e6;
    vertical-align: middle;
}

.etat-container .table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,.02);
}

.etat-container .table-success {
    background-color: #d4edda !important;
    font-weight: bold;
}

.etat-container .text-end {
    text-align: right;
    font-family: 'Courier New', monospace;
}

/* Responsive pour les tableaux */
@media (max-width: 768px) {
    .etat-container .table {
        font-size: 0.75rem;
    }

    .etat-container .table th,
    .etat-container .table td {
        padding: 4px 2px;
    }
}

/* Animation pour l'affichage des états */
#uemoaAesSection {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
