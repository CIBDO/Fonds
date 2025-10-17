@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-plus-circle me-2"></i>Nouvelle(s) Demande(s) Financière(s)
                    </h3>
                    {{-- <p class="text-muted mb-0">{{ $poste->nom }}</p> --}}
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Informations des Demandes</h5>
                        <button type="button" class="btn btn-light btn-sm" id="ajouterLigne">
                            <i class="fas fa-plus me-1"></i>Ajouter une ligne
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('pcs.autres-demandes.store') }}" method="POST" id="formDemandes">
                        @csrf

                        <!-- Paramètres globaux -->
                        <div class="row mb-4">
                            <!-- Année commune -->
                            <div class="col-md-6 mb-3">
                                <label for="annee_globale" class="form-label fw-bold">
                                    Année (pour toutes les demandes) <span class="text-danger">*</span>
                                </label>
                                <select name="annee_globale"
                                        id="annee_globale"
                                        class="form-select @error('annee_globale') is-invalid @enderror"
                                        required>
                                    @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                        <option value="{{ $i }}" {{ old('annee_globale', date('Y')) == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('annee_globale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date commune -->
                            <div class="col-md-6 mb-3">
                                <label for="date_globale" class="form-label fw-bold">
                                    Date (pour toutes les demandes) <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       class="form-control @error('date_globale') is-invalid @enderror"
                                       id="date_globale"
                                       name="date_globale"
                                       value="{{ old('date_globale', date('Y-m-d')) }}"
                                       required>
                                @error('date_globale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Conteneur des lignes de demandes -->
                        <div id="lignes-demandes">
                            <!-- Ligne de demande template (sera dupliquée) -->
                            <div class="ligne-demande border rounded p-3 mb-3 bg-light position-relative" data-index="0">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <button type="button" class="btn btn-danger btn-sm supprimer-ligne" style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <h6 class="text-danger fw-bold mb-3">
                                    <i class="fas fa-file-alt me-2"></i>Demande <span class="numero-ligne">1</span>
                                </h6>

                                <div class="row">
                                    <!-- Désignation -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">
                                            Désignation <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control"
                                               name="demandes[0][designation]"
                                               placeholder="Ex: Demande d'avance pour mission, Achat matériel, etc."
                                               required>
                                        <small class="text-muted">Nature ou objet de la demande</small>
                                    </div>

                                    <!-- Montant -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">
                                            Montant (FCFA) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                               class="form-control form-control-lg montant-ligne"
                                               name="demandes[0][montant]"
                                               step="0.01"
                                               min="0"
                                               placeholder="0"
                                               required>
                                    </div>

                                    <!-- Observation -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Observation</label>
                                        <textarea name="demandes[0][observation]"
                                                  class="form-control"
                                                  rows="3"
                                                  placeholder="Détails complémentaires..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Résumé -->
                        <div class="card border-danger mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Nombre de demandes</h6>
                                        <h4 class="text-danger" id="nombre-demandes">1</h4>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h6 class="text-muted">Montant total</h6>
                                        <h4 class="text-danger" id="montant-total">0 FCFA</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Poste (affichage) -->
                        <div class="alert alert-light border">
                            <strong>Poste :</strong> {{ $poste->nom }}
                        </div>

                        <!-- Messages d'erreur globaux -->
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">Erreurs de validation :</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Boutons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" name="action" value="brouillon" class="btn btn-outline-danger btn-lg">
                                <i class="fas fa-save me-1"></i>Enregistrer en Brouillon
                            </button>
                            <button type="submit" name="action" value="soumettre" class="btn btn-danger btn-lg">
                                <i class="fas fa-paper-plane me-1"></i>Soumettre Tout
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('add-js')
<script>
(function() {
    'use strict';

    console.log('Script chargé - initialisation...');

    // Attendre que le DOM soit complètement chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        console.log('DOM prêt - démarrage de l\'initialisation');

        let ligneIndex = 1; // Commence à 1 car on a déjà la ligne 0

        // Bouton d'ajout de ligne
        const btnAjouter = document.getElementById('ajouterLigne');
        if (!btnAjouter) {
            console.error('Bouton ajouterLigne non trouvé !');
            return;
        }

        console.log('Bouton trouvé, ajout du listener...');

        btnAjouter.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Clic sur ajouter ligne, index actuel:', ligneIndex);

            const container = document.getElementById('lignes-demandes');
            if (!container) {
                console.error('Container lignes-demandes non trouvé !');
                return;
            }

            const template = container.querySelector('.ligne-demande');
            if (!template) {
                console.error('Template ligne-demande non trouvé !');
                return;
            }

            const nouvelleLigne = template.cloneNode(true);

            // Mettre à jour l'index
            nouvelleLigne.setAttribute('data-index', ligneIndex);

            const numeroLigne = nouvelleLigne.querySelector('.numero-ligne');
            if (numeroLigne) {
                numeroLigne.textContent = ligneIndex + 1;
            }

            // Mettre à jour les noms des champs
            nouvelleLigne.querySelectorAll('input, textarea').forEach(function(input) {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, '[' + ligneIndex + ']');
                    input.setAttribute('name', newName);
                    input.value = ''; // Vider les valeurs
                    console.log('Champ renommé:', name, '->', newName);
                }
            });

            // Afficher le bouton supprimer
            const btnSupprimer = nouvelleLigne.querySelector('.supprimer-ligne');
            if (btnSupprimer) {
                btnSupprimer.style.display = 'inline-block';
            }

            // Ajouter au container
            container.appendChild(nouvelleLigne);

            console.log('Nouvelle ligne ajoutée avec index:', ligneIndex);

            ligneIndex++;
            mettreAJourBoutonSupprimer();
            calculerTotal();
        });

        // Délégation d'événement pour la suppression
        const container = document.getElementById('lignes-demandes');
        if (container) {
            container.addEventListener('click', function(e) {
                if (e.target.closest('.supprimer-ligne')) {
                    e.preventDefault();
                    const ligne = e.target.closest('.ligne-demande');
                    if (ligne) {
                        ligne.remove();
                        console.log('Ligne supprimée');
                        mettreAJourNumeros();
                        mettreAJourBoutonSupprimer();
                        calculerTotal();
                    }
                }
            });

            // Calculer le total à chaque modification de montant
            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('montant-ligne')) {
                    calculerTotal();
                }
            });
        }

        // Fonction pour mettre à jour les numéros de ligne
        function mettreAJourNumeros() {
            document.querySelectorAll('.ligne-demande').forEach(function(ligne, index) {
                const numeroLigne = ligne.querySelector('.numero-ligne');
                if (numeroLigne) {
                    numeroLigne.textContent = index + 1;
                }
            });
        }

        // Fonction pour gérer l'affichage du bouton supprimer
        function mettreAJourBoutonSupprimer() {
            const lignes = document.querySelectorAll('.ligne-demande');
            lignes.forEach(function(ligne) {
                const btnSupprimer = ligne.querySelector('.supprimer-ligne');
                if (btnSupprimer) {
                    if (lignes.length === 1) {
                        btnSupprimer.style.display = 'none';
                    } else {
                        btnSupprimer.style.display = 'inline-block';
                    }
                }
            });
        }

        // Fonction pour calculer le total
        function calculerTotal() {
            let total = 0;

            document.querySelectorAll('.montant-ligne').forEach(function(input) {
                const valeur = parseFloat(input.value) || 0;
                total += valeur;
            });

            const nombreDemandes = document.getElementById('nombre-demandes');
            const montantTotal = document.getElementById('montant-total');

            if (nombreDemandes) {
                nombreDemandes.textContent = document.querySelectorAll('.ligne-demande').length;
            }

            if (montantTotal) {
                montantTotal.textContent = new Intl.NumberFormat('fr-FR', {
                    style: 'decimal',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(total) + ' FCFA';
            }
        }

        // Initialiser le calcul
        calculerTotal();
        console.log('Initialisation terminée avec succès');
    }
})();
</script>
@endsection

