@extends('layouts.master')

@section('title', 'États Consolidés PCS - Interface Unifiée')

@section('content')

<div class="row g-4">
            <div class="col-12 mb-4">
                <x-vuexy.card title="Type d'état à générer" icon="tabler-file-text">
                        <div class="row g-4">
                            <div class="col-lg-4 col-md-6">
                                <div class="form-check card-type-selector" onclick="selectTypeEtat('recouvrements')">
                                    <input class="form-check-input" type="radio" name="type_etat" id="type_recouvrements" value="recouvrements">
                                    <label class="form-check-label" for="type_recouvrements">
                                        <div class="type-card">
                                            <i class="icon-base ti tabler-coins icon-48px text-success mb-3"></i>
                                            <h5>Recouvrements</h5>
                                            <p class="text-body-secondary">État des recouvrements PCS par poste et mois</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-check card-type-selector" onclick="selectTypeEtat('reversements')">
                                    <input class="form-check-input" type="radio" name="type_etat" id="type_reversements" value="reversements">
                                    <label class="form-check-label" for="type_reversements">
                                        <div class="type-card">
                                            <i class="icon-base ti tabler-arrows-exchange icon-48px text-primary mb-3"></i>
                                            <h5>Reversements</h5>
                                            <p class="text-body-secondary">État des reversements PCS par poste et mois</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-check card-type-selector" onclick="selectTypeEtat('uemoa-aes')">
                                    <input class="form-check-input" type="radio" name="type_etat" id="type_uemoa_aes" value="uemoa-aes">
                                    <label class="form-check-label" for="type_uemoa_aes">
                                        <div class="type-card">
                                            <i class="icon-base ti tabler-world icon-48px text-info mb-3"></i>
                                            <h5>États UEMOA/AES</h5>
                                            <p class="text-body-secondary">Situation mensuelle des liquidations UEMOA/AES</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-check card-type-selector" onclick="selectTypeEtat('autres-demandes')">
                                    <input class="form-check-input" type="radio" name="type_etat" id="type_autres_demandes" value="autres-demandes">
                                    <label class="form-check-label" for="type_autres_demandes">
                                        <div class="type-card">
                                            <i class="icon-base ti tabler-folder-open icon-48px text-warning mb-3"></i>
                                            <h5>Autres Demandes</h5>
                                            <p class="text-body-secondary">État des autres demandes financières</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-check card-type-selector" onclick="selectTypeEtat('trie')">
                                    <input class="form-check-input" type="radio" name="type_etat" id="type_trie" value="trie">
                                    <label class="form-check-label" for="type_trie">
                                        <div class="type-card">
                                            <i class="icon-base ti tabler-coins icon-48px text-primary mb-3"></i>
                                            <h5>États TRIE</h5>
                                            <p class="text-body-secondary">États et rapports TRIE/CCIM</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                </x-vuexy.card>
            </div>

            <div class="col-12 mb-4">
                <div id="filtresCard" style="display: none;">
                <x-vuexy.card title="Paramètres de filtrage" icon="tabler-filter">
                        <form id="filtreForm" method="GET" target="_blank">
                            @csrf

                            <!-- Filtres principaux (en haut) -->
                            <div class="row mb-4">
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <label for="annee" class="form-label fw-bold">
                                        <i class="icon-base ti tabler-calendar text-warning me-1"></i>
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
                                <div class="col-xl-4 col-lg-4 col-md-6" id="programmeField">
                                    <label for="programme" class="form-label fw-bold">
                                        <i class="icon-base ti tabler-world text-info me-1"></i>
                                        Programme <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="programme" name="programme">
                                        <option value="">Tous les programmes</option>
                                        <option value="UEMOA">UEMOA</option>
                                        <option value="AES">AES</option>
                                    </select>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <label for="poste_id" class="form-label fw-bold">
                                        <i class="icon-base ti tabler-map-pin text-info me-1"></i>
                                        Filtrer par poste
                                    </label>
                                    <select class="form-select" id="poste_id" name="poste_id">
                                        <option value="">Tous les postes</option>
                                        @foreach($postes as $poste)
                                            <option value="{{ $poste->id }}">{{ $poste->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Filtres supplémentaires -->
                            <div class="row mb-4">
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <label for="mois" class="form-label fw-bold">
                                        <i class="icon-base ti tabler-calendar-event text-primary me-1"></i>
                                        Filtrer par mois <span id="moisRequired" class="text-danger" style="display: none;">*</span>
                                    </label>
                                    <select class="form-select" id="mois" name="mois">
                                        <option value="">Tous les mois</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <label for="format" class="form-label fw-bold">
                                        <i class="icon-base ti tabler-file-export text-primary me-1"></i>
                                        Format d'export
                                    </label>
                                    <select class="form-select" id="format" name="format">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6" id="statutField" style="display: none;">
                                    <label for="statut" class="form-label fw-bold">
                                        <i class="icon-base ti tabler-tags text-secondary me-1"></i>
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
                                <div class="col-xl-4 col-lg-4 col-md-6" id="typeEtatTrieField" style="display: none;">
                                    <label for="type_etat_trie" class="form-label fw-bold">
                                        <i class="icon-base ti tabler-file-text text-primary me-1"></i>
                                        Type d'état TRIE <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="type_etat_trie" name="type_etat_trie">
                                        <option value="mensuel">État Mensuel</option>
                                        <option value="consolide">État Consolidé</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-primary" id="btnGenerer" onclick="genererEtat()">
                                            <i class="icon-base ti tabler-file-type-pdf me-2"></i>
                                            Générer l'état
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="afficherApercu()">
                                            <i class="icon-base ti tabler-eye me-2"></i>
                                            Aperçu
                                        </button>
                                        <button type="button" class="btn btn-label-secondary" onclick="resetForm()">
                                            <i class="icon-base ti tabler-arrow-back-up me-1"></i>
                                            Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                </x-vuexy.card>
                </div>
            </div>
        </div>

        <div class="row" id="uemoaAesSection" style="display: none;">
            <div class="col-12">
                <x-vuexy.card title="États UEMOA et AES - Situation Mensuelle des Liquidations" icon="tabler-world">
                        <div class="row mb-4 g-3">
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
                                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div id="contenuEtatUemoaAes"></div>
                </x-vuexy.card>
            </div>
        </div>

        <div class="row" id="statsRow" style="display: none;">
            <div class="col-12 mb-4">
                <x-vuexy.card title="Statistiques" icon="tabler-chart-bar">
                    <div id="statsContainer">
                        <div class="text-center text-body-secondary py-3">
                            <i class="icon-base ti tabler-info-circle icon-32px mb-2 d-block"></i>
                            <p class="mb-0">Sélectionnez un type d'état et ajustez les filtres pour voir les statistiques</p>
                        </div>
                    </div>
                </x-vuexy.card>
            </div>
        </div>

        <!-- Modal d'aperçu -->
        <div class="modal fade" id="apercuModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="icon-base ti tabler-eye me-2"></i>
                            Aperçu de l'état
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="apercuContent">
                            <div class="text-center">
                                <i class="spinner-border text-primary"></i>
                                <p class="mt-2">Chargement de l'aperçu...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" onclick="genererEtat()">
                            <i class="icon-base ti tabler-file-type-pdf me-1"></i>
                            Générer le PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let typeEtatSelectionne = null;

function toggleStatsRow(type) {
    const statsRow = document.getElementById('statsRow');
    if (!statsRow) return;
    statsRow.style.display = (type && type !== 'trie') ? 'block' : 'none';
}

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

    // Adapter les champs selon le type
    if (type === 'autres-demandes') {
        document.getElementById('programmeField').style.display = 'none';
        document.getElementById('statutField').style.display = 'block';
        document.getElementById('uemoaAesSection').style.display = 'none';
        document.getElementById('filtresCard').style.display = 'block';
        toggleStatsRow(type);
        chargerStatistiques();
    } else if (type === 'uemoa-aes') {
        document.getElementById('programmeField').style.display = 'none';
        document.getElementById('statutField').style.display = 'none';
        document.getElementById('filtresCard').style.display = 'none';
        document.getElementById('uemoaAesSection').style.display = 'block';
        toggleStatsRow(type);
        chargerEtatUemoaAes();
    } else if (type === 'trie') {
        document.getElementById('programmeField').style.display = 'none';
        document.getElementById('statutField').style.display = 'none';
        document.getElementById('typeEtatTrieField').style.display = 'block';
        document.getElementById('uemoaAesSection').style.display = 'none';
        document.getElementById('filtresCard').style.display = 'block';
        toggleStatsRow('trie');
        gererChampMoisTrie();
    } else {
        document.getElementById('programmeField').style.display = 'block';
        document.getElementById('statutField').style.display = 'none';
        document.getElementById('typeEtatTrieField').style.display = 'none';
        document.getElementById('uemoaAesSection').style.display = 'none';
        document.getElementById('filtresCard').style.display = 'block';
        toggleStatsRow(type);
        chargerStatistiques();
    }

    // Scroll vers la section appropriée
    if (type === 'uemoa-aes') {
        document.getElementById('uemoaAesSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        document.getElementById('filtresCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Fonction helper pour afficher des alertes
function showAlert(icon, title, text) {
    if (typeof Swal !== 'undefined' && Swal.fire) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
        });
    } else {
        alert(title + ': ' + text);
    }
}

function genererEtat() {
    if (!typeEtatSelectionne) {
        showAlert('warning', 'Type d\'état non sélectionné', 'Veuillez sélectionner un type d\'état à générer');
        return;
    }

    // Pour les états TRIE, utiliser les routes spécifiques
    if (typeEtatSelectionne === 'trie') {
        const typeEtatTrieEl = document.getElementById('type_etat_trie');
        const anneeEl = document.getElementById('annee');
        const moisEl = document.getElementById('mois');

        if (!typeEtatTrieEl || !anneeEl) {
            showAlert('error', 'Erreur', 'Veuillez remplir tous les champs requis');
            return;
        }

        const typeEtatTrie = typeEtatTrieEl.value;
        const annee = anneeEl.value;
        const mois = moisEl ? moisEl.value : '';

        if (typeEtatTrie === 'mensuel') {
            if (!mois) {
                showAlert('warning', 'Mois requis', 'Veuillez sélectionner un mois pour l\'état mensuel');
                return;
            }
            const url = '{{ route("trie.etats.mensuel") }}?mois=' + mois + '&annee=' + annee;
            window.open(url, '_blank');
        } else if (typeEtatTrie === 'consolide') {
            const url = '{{ route("trie.etats.consolide") }}?annee=' + annee;
            window.open(url, '_blank');
        }
        return;
    }

    const anneeEl = document.getElementById('annee');
    const dateDebutEl = document.getElementById('date_debut');
    const dateFinEl = document.getElementById('date_fin');
    const posteIdEl = document.getElementById('poste_id');
    const moisEl = document.getElementById('mois');
    const formatEl = document.getElementById('format');

    const params = new URLSearchParams({
        type: typeEtatSelectionne,
        annee: anneeEl ? anneeEl.value : '',
        date_debut: dateDebutEl ? dateDebutEl.value : '',
        date_fin: dateFinEl ? dateFinEl.value : '',
        poste_id: posteIdEl ? posteIdEl.value : '',
        mois: moisEl ? moisEl.value : '',
        format: formatEl ? formatEl.value : 'pdf',
        _token: '{{ csrf_token() }}'
    });

    if (typeEtatSelectionne !== 'autres-demandes') {
        const programmeEl = document.getElementById('programme');
        if (programmeEl) {
            params.append('programme', programmeEl.value);
        }
    } else {
        const statutEl = document.getElementById('statut');
        if (statutEl) {
            params.append('statut', statutEl.value);
        }
    }

    const url = '{{ route("pcs.etats-consolides.generer") }}?' + params.toString();
    window.open(url, '_blank');
}

function afficherApercu() {
    if (!typeEtatSelectionne) {
        showAlert('warning', 'Type d\'état non sélectionné', 'Veuillez sélectionner un type d\'état pour l\'aperçu');
        return;
    }

    const modal = new bootstrap.Modal(document.getElementById('apercuModal'));
    modal.show();

    const anneeEl = document.getElementById('annee');
    const dateDebutEl = document.getElementById('date_debut');
    const dateFinEl = document.getElementById('date_fin');
    const posteIdEl = document.getElementById('poste_id');
    const moisEl = document.getElementById('mois');

    const params = new URLSearchParams({
        type: typeEtatSelectionne,
        annee: anneeEl ? anneeEl.value : '',
        date_debut: dateDebutEl ? dateDebutEl.value : '',
        date_fin: dateFinEl ? dateFinEl.value : '',
        poste_id: posteIdEl ? posteIdEl.value : '',
        mois: moisEl ? moisEl.value : '',
        apercu: '1',
        _token: '{{ csrf_token() }}'
    });

    if (typeEtatSelectionne !== 'autres-demandes') {
        const programmeEl = document.getElementById('programme');
        if (programmeEl) {
            params.append('programme', programmeEl.value);
        }
    } else {
        const statutEl = document.getElementById('statut');
        if (statutEl) {
            params.append('statut', statutEl.value);
        }
    }

    fetch('{{ route("pcs.etats-consolides.apercu") }}?' + params.toString())
        .then(response => response.text())
        .then(html => {
            document.getElementById('apercuContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('apercuContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="icon-base ti tabler-alert-triangle me-1"></i>
                    Erreur lors du chargement de l'aperçu
                </div>
            `;
        });
}

function chargerStatistiques() {
    if (!typeEtatSelectionne) return;

    // Ne pas charger les statistiques pour UEMOA/AES et TRIE (elles sont chargées séparément)
    if (typeEtatSelectionne === 'uemoa-aes' || typeEtatSelectionne === 'trie') return;

    const anneeEl = document.getElementById('annee');
    const dateDebutEl = document.getElementById('date_debut');
    const dateFinEl = document.getElementById('date_fin');
    const posteIdEl = document.getElementById('poste_id');
    const moisEl = document.getElementById('mois');

    if (!anneeEl) return; // Si l'élément n'existe pas, ne pas continuer

    const params = new URLSearchParams({
        type: typeEtatSelectionne,
        annee: anneeEl.value,
        date_debut: dateDebutEl ? dateDebutEl.value : '',
        date_fin: dateFinEl ? dateFinEl.value : '',
        poste_id: posteIdEl ? posteIdEl.value : '',
        mois: moisEl ? moisEl.value : '',
        _token: '{{ csrf_token() }}'
    });

    if (typeEtatSelectionne !== 'autres-demandes') {
        const programmeEl = document.getElementById('programme');
        if (programmeEl) {
            params.append('programme', programmeEl.value);
        }
    }

    fetch('{{ route("pcs.etats-consolides.stats") }}?' + params.toString())
        .then(response => response.json())
        .then(data => {
            let statsHTML = '';

            if (typeEtatSelectionne === 'recouvrements' || typeEtatSelectionne === 'reversements') {
                statsHTML = `
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center h-100">
                                <i class="icon-base ti tabler-file-check text-primary icon-lg mb-2"></i>
                                <h4 class="text-primary mb-1">${data.total}</h4>
                                <p class="text-body-secondary small mb-0">Total déclarations</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center h-100">
                                <i class="icon-base ti tabler-cash text-success icon-lg mb-2"></i>
                                <h5 class="text-success mb-1">${data.montant}</h5>
                                <p class="text-body-secondary small mb-0">Montant total (FCFA)</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center h-100">
                                <i class="icon-base ti tabler-map-pin text-info icon-lg mb-2"></i>
                                <h5 class="text-info mb-1">${data.postes}</h5>
                                <p class="text-body-secondary small mb-0">Postes actifs</p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statsHTML = `
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center h-100">
                                <i class="icon-base ti tabler-folder text-primary icon-lg mb-2"></i>
                                <h4 class="text-primary mb-1">${data.total}</h4>
                                <p class="text-body-secondary small mb-0">Total demandes</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center h-100">
                                <i class="icon-base ti tabler-hand-stop text-success icon-lg mb-2"></i>
                                <h5 class="text-success mb-1">${data.montant_demande}</h5>
                                <p class="text-body-secondary small mb-0">Montant demandé</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center h-100">
                                <i class="icon-base ti tabler-circle-check text-warning icon-lg mb-2"></i>
                                <h5 class="text-warning mb-1">${data.montant_accorde}</h5>
                                <p class="text-body-secondary small mb-0">Montant accordé</p>
                            </div>
                        </div>
                    </div>
                `;
            }

            const statsContainer = document.getElementById('statsContainer');
            if (statsContainer) {
                statsContainer.innerHTML = statsHTML;
            } else {
                console.log('statsContainer n\'existe pas dans le DOM, les statistiques ne seront pas affichées');
            }
        })
        .catch(error => {
            console.error('Erreur stats:', error);
        });
}

function resetForm() {
    const filtreForm = document.getElementById('filtreForm');
    if (filtreForm) {
        filtreForm.reset();
    }

    const anneeEl = document.getElementById('annee');
    if (anneeEl) {
        anneeEl.value = '{{ date("Y") }}';
    }

    const dateDebutEl = document.getElementById('date_debut');
    if (dateDebutEl) {
        dateDebutEl.value = '{{ date("Y-01-01") }}';
    }

    const dateFinEl = document.getElementById('date_fin');
    if (dateFinEl) {
        dateFinEl.value = '{{ date("Y-m-d") }}';
    }

    chargerStatistiques();
}

// Fonction pour charger les états UEMOA/AES
function chargerEtatUemoaAes() {
    const programme = document.getElementById('programmeUemoaAes').value;
    const annee = document.getElementById('anneeUemoaAes').value;

    // Afficher un loader
    document.getElementById('contenuEtatUemoaAes').innerHTML = `
        <div class="text-center py-5">
            <i class="spinner-border spinner-border-sm text-primary icon-48px text-primary mb-3"></i>
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
                    <i class="icon-base ti tabler-alert-triangle me-2"></i>
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
            <div class="row">
                <div class="col-12 mb-4">
                    <h5 class="fw-bold text-success">ÉTAT CONSOLIDÉ ${donnees.annee}</h5>
                    ${resultatTableau.html}
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-body-secondary small">*Ces données mensuelles sont provisoires et ne concernent que les déclarations validées</p>
                <button class="btn btn-primary" onclick="genererPDFUemoaAes()">
                    <i class="icon-base ti tabler-file-type-pdf me-2"></i>Générer PDF
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
    const statsContainer = document.getElementById('statsContainer');

    // Vérifier si l'élément existe avant de le modifier
    if (!statsContainer) {
        console.log('statsContainer n\'existe pas dans le DOM, les statistiques ne seront pas affichées');
        return;
    }

    const statsHTML = `
        <div class="row g-3">
            <div class="col-md-4">
                <div class="border rounded p-3 text-center h-100">
                    <i class="icon-base ti tabler-trending-up text-success icon-lg mb-2"></i>
                    <h4 class="text-success mb-1">${totalRecouvrements.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
                    <p class="text-body-secondary small mb-0">Recouvrement (Millions FCFA)</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center h-100">
                    <i class="icon-base ti tabler-trending-down text-primary icon-lg mb-2"></i>
                    <h4 class="text-primary mb-1">${totalReversements.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
                    <p class="text-body-secondary small mb-0">Reversement (Millions FCFA)</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center h-100">
                    <i class="icon-base ti tabler-scale icon-lg mb-2 ${totalResteAReverser > 0 ? 'text-danger' : 'text-success'}"></i>
                    <h4 class="${totalResteAReverser > 0 ? 'text-danger' : 'text-success'} mb-1">${totalResteAReverser.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
                    <p class="text-body-secondary small mb-0">Reste à reverser (Millions FCFA)</p>
                </div>
            </div>
        </div>
    `;

    statsContainer.innerHTML = statsHTML;
}

// Fonction pour générer le PDF UEMOA/AES consolidé
function genererPDFUemoaAes() {
    const programme = document.getElementById('programmeUemoaAes').value;
    const annee = document.getElementById('anneeUemoaAes').value;

    // Préparer l'URL avec tous les paramètres nécessaires
    const params = new URLSearchParams({
        type: 'uemoa-aes',
        programme: programme,
        annee: annee,
        format: 'pdf',
        _token: '{{ csrf_token() }}'
    });

    const url = `{{ route("pcs.etats-consolides.generer") }}?${params.toString()}`;

    // Ouvrir dans un nouvel onglet
    window.open(url, '_blank');
}


// Fonction pour gérer l'affichage du champ mois selon le type d'état TRIE
function gererChampMoisTrie() {
    const typeEtatTrieEl = document.getElementById('type_etat_trie');
    const moisRequiredEl = document.getElementById('moisRequired');
    const moisEl = document.getElementById('mois');

    if (typeEtatTrieEl && moisRequiredEl && moisEl) {
        const updateMoisRequired = () => {
            if (typeEtatTrieEl.value === 'mensuel') {
                moisRequiredEl.style.display = 'inline';
                moisEl.setAttribute('required', 'required');
            } else {
                moisRequiredEl.style.display = 'none';
                moisEl.removeAttribute('required');
            }
        };

        updateMoisRequired();
        typeEtatTrieEl.addEventListener('change', updateMoisRequired);
    }
}

// Charger les stats quand les filtres changent
document.addEventListener('DOMContentLoaded', function() {
    ['annee', 'date_debut', 'date_fin', 'poste_id', 'programme', 'mois', 'statut'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', chargerStatistiques);
        }
    });

    // Gérer le champ mois pour TRIE
    const typeEtatTrieEl = document.getElementById('type_etat_trie');
    if (typeEtatTrieEl) {
        typeEtatTrieEl.addEventListener('change', gererChampMoisTrie);
    }
});
</script>

<style>
.card-type-selector {
    cursor: pointer;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    border: 2px solid transparent;
    border-radius: var(--bs-border-radius-lg, 0.5rem);
    padding: 0.5rem;
    height: 100%;
}

.card-type-selector:hover {
    border-color: var(--bs-primary);
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 1rem rgba(var(--bs-primary-rgb), 0.15);
}

.card-type-selector.active {
    border-color: var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), 0.06);
}

.card-type-selector input[type="radio"] {
    display: none;
}

.type-card {
    text-align: center;
    padding: 1.25rem 1rem;
    border-radius: var(--bs-border-radius, 0.375rem);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.type-card h5 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.type-card p {
    font-size: 0.875rem;
    margin-bottom: 0;
}

#filtresCard,
#uemoaAesSection {
    animation: fadeSlideIn 0.35s ease;
}

@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

.etat-container {
    background: var(--bs-body-bg);
    padding: 1rem;
    border-radius: var(--bs-border-radius-lg, 0.5rem);
    border: 1px solid var(--bs-border-color);
}

.bg-success-light {
    background-color: rgba(var(--bs-success-rgb), 0.15) !important;
    color: var(--bs-success-text-emphasis, #0f5132) !important;
    font-weight: 600 !important;
}

.bg-primary-light {
    background-color: rgba(var(--bs-primary-rgb), 0.15) !important;
    color: var(--bs-primary-text-emphasis, #052c65) !important;
    font-weight: 600 !important;
}

.etat-container .table {
    font-size: 0.85rem;
}

.etat-container .table th {
    vertical-align: middle;
}

.etat-container .text-end {
    font-variant-numeric: tabular-nums;
}

@media (max-width: 768px) {
    .etat-container .table {
        font-size: 0.75rem;
    }
}
</style>
@endpush
