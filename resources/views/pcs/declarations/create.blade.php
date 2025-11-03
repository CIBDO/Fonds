@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-plus-circle me-2"></i>Nouvelle Déclaration PCS
                    </h3>
                    {{-- <p class="text-muted mb-0"> - {{ $poste->nom }} - Programme UEMOA & AES</p> --}}
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.declarations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Alerte mois manquants -->
    @php
        $tousMoisManquants = [];
        foreach ($moisManquants as $programme => $mois) {
            $tousMoisManquants = array_merge($tousMoisManquants, $mois);
        }
        foreach ($moisManquantsAnneePrecedente as $programme => $mois) {
            $tousMoisManquants = array_merge($tousMoisManquants, $mois);
        }
        $tousMoisManquants = array_unique($tousMoisManquants);
        $aDesMoisManquants = !empty($tousMoisManquants);

        // Créer un tableau des mois déjà renseignés (pour toutes les années et programmes)
        $tousMoisRenseignes = [];
        foreach ($moisRenseignes as $programme => $mois) {
            $tousMoisRenseignes = array_merge($tousMoisRenseignes, $mois);
        }
        foreach ($moisRenseignesAnneePrecedente as $programme => $mois) {
            $tousMoisRenseignes = array_merge($tousMoisRenseignes, $mois);
        }
        $tousMoisRenseignes = array_unique($tousMoisRenseignes);
    @endphp

    @if($aDesMoisManquants)
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Attention !</strong> Vous avez <strong>{{ count($tousMoisManquants) }} mois non renseigné(s)</strong> pour {{ $annee }} ou {{ $anneePrecedente }}.
        <br>
        <button type="button" class="btn btn-warning btn-sm mt-2" id="toggleRattrapage">
            <i class="fas fa-history me-1"></i>Utiliser le mode Rattrapage
        </button>
        <button type="button" class="btn btn-secondary btn-sm mt-2 ms-2" id="toggleNormal" style="display:none;">
            <i class="fas fa-calendar me-1"></i>Revenir au mode Normal
        </button>
    </div>
    @endif

    <form action="{{ route('pcs.declarations.store') }}" method="POST" id="declarationForm">
        @csrf

        <!-- Mode Normal -->
        <div id="modeNormal" class="{{ $aDesMoisManquants ? '' : '' }}">
            <!-- Période -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Période de Déclaration</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mois <span class="text-danger">*</span></label>
                            <select name="mois" class="form-select" required>
                                <option value="">Sélectionner un mois</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('mois', date('n')) == $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->locale('fr')->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                            <select name="annee" class="form-select" required>
                                @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                    <option value="{{ $i }}" {{ old('annee', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mode Rattrapage -->
        @if($aDesMoisManquants)
        <div id="modeRattrapage" style="display:none;">
            <!-- Sélection des mois -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Rattrapage - Sélection des Mois</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                            <select name="annee" class="form-select" id="anneeRattrapage" required>
                                @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                    <option value="{{ $i }}" {{ old('annee', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold">Mois à renseigner <span class="text-danger">*</span></label>
                            <div class="row">
                                @for($i = 1; $i <= 12; $i++)
                                    @php
                                        $moisLibelle = \Carbon\Carbon::create()->month($i)->locale('fr')->translatedFormat('F');
                                        $isManquant = in_array($i, $tousMoisManquants);
                                        $isRenseigne = in_array($i, $tousMoisRenseignes);
                                    @endphp
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input mois-checkbox"
                                                   type="checkbox"
                                                   name="mois_selectionnes[]"
                                                   value="{{ $i }}"
                                                   id="mois_{{ $i }}"
                                                   {{ $isManquant ? 'checked' : '' }}
                                                   {{ $isRenseigne ? 'disabled' : '' }}>
                                            <label class="form-check-label {{ $isRenseigne ? 'text-muted' : '' }}"
                                                   for="mois_{{ $i }}"
                                                   style="{{ $isRenseigne ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                                {{ $moisLibelle }}
                                                @if($isManquant && !$isRenseigne)
                                                    <span class="badge bg-warning text-dark ms-1">Manquant</span>
                                                @elseif($isRenseigne)
                                                    <span class="badge bg-success ms-1">Déjà renseigné</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Sélectionnez tous les mois que vous souhaitez renseigner en une seule fois.
                                Les mois déjà renseignés sont grisés et ne peuvent pas être sélectionnés.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Formulaire Rattrapage (Tableau) -->
        @if($aDesMoisManquants)
        <div id="formulaireRattrapage" style="display:none;">
            @if($poste->isRgd())
                <!-- Tableau RGD -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>RGD - Opérations Propres</h5>
                        <small class="text-white-50">Renseignez les données pour chaque mois sélectionné</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="tableRattrapageRgd">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mois</th>
                                        <th>UEMOA - Recouvré</th>
                                        <th>UEMOA - Reversé</th>
                                        <th>UEMOA - Référence</th>
                                        <th>AES - Recouvré</th>
                                        <th>AES - Reversé</th>
                                        <th>AES - Référence</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyRattrapageRgd">
                                    <!-- Les lignes seront générées dynamiquement par JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="copierPremierMoisRgd">
                                <i class="fas fa-copy me-1"></i>Copier les valeurs du premier mois vers les autres
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tableau Bureaux -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Bureaux de Douanes</h5>
                    </div>
                    <div class="card-body">
                        @foreach($bureaux as $bureau)
                        <div class="mb-4 border-bottom pb-4">
                            <h6 class="text-primary fw-bold mb-3">
                                <span class="badge bg-primary">{{ $bureau->code }}</span> {{ $bureau->libelle }}
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" data-bureau="{{ $bureau->id }}">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mois</th>
                                            <th>UEMOA - Recouvré</th>
                                            <th>UEMOA - Reversé</th>
                                            <th>UEMOA - Référence</th>
                                            <th>AES - Recouvré</th>
                                            <th>AES - Reversé</th>
                                            <th>AES - Référence</th>
                                        </tr>
                                    </thead>
                                    <tbody data-bureau="{{ $bureau->id }}">
                                        <!-- Les lignes seront générées dynamiquement -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Tableau Poste Normal -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>{{ $poste->nom }}</h5>
                        <small class="text-white-50">Renseignez les données pour chaque mois sélectionné</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="tableRattrapagePoste">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mois</th>
                                        <th>UEMOA - Recouvré</th>
                                        <th>UEMOA - Reversé</th>
                                        <th>UEMOA - Référence</th>
                                        <th>AES - Recouvré</th>
                                        <th>AES - Reversé</th>
                                        <th>AES - Référence</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyRattrapagePoste">
                                    <!-- Les lignes seront générées dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="copierPremierMoisPoste">
                                <i class="fas fa-copy me-1"></i>Copier les valeurs du premier mois vers les autres
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
            {{-- FORMULAIRE RGD AVEC BUREAUX --}}
        @endif

        <!-- Formulaire Normal -->
        <div id="formulaireNormal">
        @if($poste->isRgd())
            {{-- FORMULAIRE RGD AVEC BUREAUX --}}

            <!-- RGD Propre -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>RGD - Opérations Propres</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- UEMOA -->
                        <div class="col-md-6">
                            <div class="border-end pe-3">
                                <h6 class="text-success fw-bold mb-3">
                                    <i class="fas fa-globe"></i> Prélèvement UEMOA
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">Montant Recouvré (FCFA)</label>
                                    <input type="number"
                                           name="rgd_UEMOA_recouvrement"
                                           class="form-control"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('rgd_UEMOA_recouvrement') }}"
                                           placeholder="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Montant Reversé (FCFA)</label>
                                    <input type="number"
                                           name="rgd_UEMOA_reversement"
                                           class="form-control"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('rgd_UEMOA_reversement') }}"
                                           placeholder="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Référence</label>
                                    <input type="text"
                                           name="rgd_UEMOA_reference"
                                           class="form-control"
                                           value="{{ old('rgd_UEMOA_reference') }}"
                                           placeholder="Référence...">
                                </div>
                                <div>
                                    <label class="form-label">Observation</label>
                                    <textarea name="rgd_UEMOA_observation"
                                              class="form-control"
                                              rows="2"
                                              placeholder="Observations éventuelles...">{{ old('rgd_UEMOA_observation') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- AES -->
                        <div class="col-md-6">
                            <h6 class="text-warning fw-bold mb-3">
                                <i class="fas fa-globe"></i> Prélèvement AES (PC)
                            </h6>
                            <div class="mb-3">
                                <label class="form-label">Montant Recouvré (FCFA)</label>
                                <input type="number"
                                       name="rgd_AES_recouvrement"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('rgd_AES_recouvrement') }}"
                                       placeholder="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Montant Reversé (FCFA)</label>
                                <input type="number"
                                       name="rgd_AES_reversement"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('rgd_AES_reversement') }}"
                                       placeholder="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Référence</label>
                                <input type="text"
                                       name="rgd_AES_reference"
                                       class="form-control"
                                       value="{{ old('rgd_AES_reference') }}"
                                       placeholder="Référence...">
                            </div>
                            <div>
                                <label class="form-label">Observation</label>
                                <textarea name="rgd_AES_observation"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Observations éventuelles...">{{ old('rgd_AES_observation') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bureaux de Douanes -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Bureaux de Douanes ({{ $bureaux->count() }} bureaux)</h5>
                </div>
                <div class="card-body">
                    @foreach($bureaux as $bureau)
                    <div class="bureau-section mb-4 pb-4 border-bottom">
                        <h6 class="text-primary fw-bold mb-3">
                            <span class="badge bg-primary">{{ $bureau->code }}</span> {{ $bureau->libelle }}
                        </h6>

                        <div class="row">
                            <!-- UEMOA -->
                            <div class="col-md-6">
                                <div class="border-end pe-3">
                                    <p class="text-success fw-bold mb-2">
                                        <i class="fas fa-globe"></i> Prélèvement UEMOA
                                    </p>
                                    <div class="mb-2">
                                        <label class="form-label small">Recouvré (FCFA)</label>
                                        <input type="number"
                                               name="bureau_{{ $bureau->id }}_UEMOA_recouvrement"
                                               class="form-control form-control-sm"
                                               step="0.01"
                                               min="0"
                                               value="{{ old('bureau_'.$bureau->id.'_UEMOA_recouvrement') }}"
                                               placeholder="0">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Reversé (FCFA)</label>
                                        <input type="number"
                                               name="bureau_{{ $bureau->id }}_UEMOA_reversement"
                                               class="form-control form-control-sm"
                                               step="0.01"
                                               min="0"
                                               value="{{ old('bureau_'.$bureau->id.'_UEMOA_reversement') }}"
                                               placeholder="0">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Référence</label>
                                        <input type="text"
                                               name="bureau_{{ $bureau->id }}_UEMOA_reference"
                                               class="form-control form-control-sm"
                                               value="{{ old('bureau_'.$bureau->id.'_UEMOA_reference') }}"
                                               placeholder="Référence...">
                                    </div>
                                    <div>
                                        <label class="form-label small">Observation</label>
                                        <textarea name="bureau_{{ $bureau->id }}_UEMOA_observation"
                                                  class="form-control form-control-sm"
                                                  rows="2"
                                                  placeholder="Observations...">{{ old('bureau_'.$bureau->id.'_UEMOA_observation') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- AES -->
                            <div class="col-md-6">
                                <p class="text-warning fw-bold mb-2">
                                    <i class="fas fa-globe"></i> Prélèvement AES
                                </p>
                                <div class="mb-2">
                                    <label class="form-label small">Recouvré (FCFA)</label>
                                    <input type="number"
                                           name="bureau_{{ $bureau->id }}_AES_recouvrement"
                                           class="form-control form-control-sm"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('bureau_'.$bureau->id.'_AES_recouvrement') }}"
                                           placeholder="0">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Reversé (FCFA)</label>
                                    <input type="number"
                                           name="bureau_{{ $bureau->id }}_AES_reversement"
                                           class="form-control form-control-sm"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('bureau_'.$bureau->id.'_AES_reversement') }}"
                                           placeholder="0">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Référence</label>
                                    <input type="text"
                                           name="bureau_{{ $bureau->id }}_AES_reference"
                                           class="form-control form-control-sm"
                                           value="{{ old('bureau_'.$bureau->id.'_AES_reference') }}"
                                           placeholder="Référence...">
                                </div>
                                <div>
                                    <label class="form-label small">Observation</label>
                                    <textarea name="bureau_{{ $bureau->id }}_AES_observation"
                                              class="form-control form-control-sm"
                                              rows="2"
                                              placeholder="Observations...">{{ old('bureau_'.$bureau->id.'_AES_observation') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        @else
            {{-- FORMULAIRE POSTE NORMAL --}}

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>{{ $poste->nom }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- UEMOA -->
                        <div class="col-md-6">
                            <div class="border-end pe-4">
                                <h5 class="text-success fw-bold mb-3">
                                    <i class="fas fa-globe"></i> Prélèvement UEMOA
                                </h5>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Montant Recouvré (FCFA)</label>
                                    <input type="number"
                                           name="UEMOA_recouvrement"
                                           class="form-control form-control-lg"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('UEMOA_recouvrement') }}"
                                           placeholder="0">
                                    <small class="text-muted">Recettes perçues ce mois</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Montant Reversé (FCFA)</label>
                                    <input type="number"
                                           name="UEMOA_reversement"
                                           class="form-control form-control-lg"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('UEMOA_reversement') }}"
                                           placeholder="0">
                                    <small class="text-muted">Montant reversé </small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Référence</label>
                                    <input type="text"
                                           name="UEMOA_reference"
                                           class="form-control form-control-lg"
                                           value="{{ old('UEMOA_reference') }}"
                                           placeholder="Référence...">
                                </div>
                                <div>
                                    <label class="form-label fw-bold">Observation</label>
                                    <textarea name="UEMOA_observation"
                                              class="form-control"
                                              rows="3"
                                              placeholder="Observations éventuelles...">{{ old('UEMOA_observation') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- AES -->
                        <div class="col-md-6">
                            <h5 class="text-warning fw-bold mb-3">
                                <i class="fas fa-globe"></i> Prélèvement AES
                            </h5>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Montant Recouvré (FCFA)</label>
                                <input type="number"
                                       name="AES_recouvrement"
                                       class="form-control form-control-lg"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('AES_recouvrement') }}"
                                       placeholder="0">
                                <small class="text-muted">Recettes perçues ce mois</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Montant Reversé (FCFA)</label>
                                <input type="number"
                                       name="AES_reversement"
                                       class="form-control form-control-lg"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('AES_reversement') }}"
                                       placeholder="0">
                                <small class="text-muted">Montant reversé </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Référence</label>
                                <input type="text"
                                       name="AES_reference"
                                       class="form-control form-control-lg"
                                       value="{{ old('AES_reference') }}"
                                       placeholder="Référence...">
                            </div>
                            <div>
                                <label class="form-label fw-bold">Observation</label>
                                <textarea name="AES_observation"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Observations éventuelles...">{{ old('AES_observation') }}</textarea>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        @endif
        </div>
        <!-- Fin Formulaire Normal -->

        <!-- Boutons d'action -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('pcs.declarations.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    <button type="submit" name="action" value="brouillon" class="btn btn-outline-danger btn-lg">
                        <i class="fas fa-save me-1"></i>Enregistrer Brouillon
                    </button>
                    <button type="submit" name="action" value="soumettre" class="btn btn-danger btn-lg">
                        <i class="fas fa-check-circle me-1"></i>Valider et Envoyer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<style>
    /* Style pour les mois déjà renseignés */
    .form-check-input:disabled + .form-check-label {
        opacity: 0.6;
        cursor: not-allowed;
        text-decoration: none;
    }

    .form-check-input:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    .mois-renseigne {
        background-color: #f8f9fa;
        border-left: 3px solid #28a745;
        padding-left: 8px;
    }
</style>
<script>
    // Noms des mois en français
    const moisNoms = {
        1: 'Janvier', 2: 'Février', 3: 'Mars', 4: 'Avril',
        5: 'Mai', 6: 'Juin', 7: 'Juillet', 8: 'Août',
        9: 'Septembre', 10: 'Octobre', 11: 'Novembre', 12: 'Décembre'
    };

    let modeRattrapage = false;
    const isRgd = {{ $poste->isRgd() ? 'true' : 'false' }};
    const bureaux = @json($poste->isRgd() ? $bureaux->map(fn($b) => $b->id)->toArray() : []);

    // Toggle entre modes normal et rattrapage
    @if($aDesMoisManquants)
    document.getElementById('toggleRattrapage')?.addEventListener('click', function() {
        modeRattrapage = true;
        document.getElementById('modeNormal').style.display = 'none';
        document.getElementById('modeRattrapage').style.display = 'block';
        document.getElementById('formulaireNormal').style.display = 'none';
        document.getElementById('formulaireRattrapage').style.display = 'block';
        document.getElementById('toggleRattrapage').style.display = 'none';
        document.getElementById('toggleNormal').style.display = 'inline-block';

        // Rendre le champ mois non requis en mode rattrapage
        document.querySelector('#modeNormal select[name="mois"]').removeAttribute('required');
        document.querySelector('#modeRattrapage select[name="annee"]').setAttribute('required', 'required');

        // Générer les lignes du tableau
        genererLignesRattrapage();
    });

    document.getElementById('toggleNormal')?.addEventListener('click', function() {
        modeRattrapage = false;
        document.getElementById('modeNormal').style.display = 'block';
        document.getElementById('modeRattrapage').style.display = 'none';
        document.getElementById('formulaireNormal').style.display = 'block';
        document.getElementById('formulaireRattrapage').style.display = 'none';
        document.getElementById('toggleRattrapage').style.display = 'inline-block';
        document.getElementById('toggleNormal').style.display = 'none';

        // Rétablir les requis
        document.querySelector('#modeNormal select[name="mois"]').setAttribute('required', 'required');
        document.querySelector('#modeRattrapage select[name="annee"]').removeAttribute('required');
    });
    @endif

    // Générer les lignes du tableau de rattrapage
    function genererLignesRattrapage() {
        // Ne récupérer que les checkboxes cochées et non désactivées
        const moisSelectionnes = Array.from(document.querySelectorAll('.mois-checkbox:checked:not(:disabled)')).map(cb => parseInt(cb.value));

        if (moisSelectionnes.length === 0) {
            // Afficher un message si aucun mois n'est sélectionné
            const tbodyRgd = document.getElementById('tbodyRattrapageRgd');
            const tbodyPoste = document.getElementById('tbodyRattrapagePoste');
            if (tbodyRgd) tbodyRgd.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Aucun mois sélectionné</td></tr>';
            if (tbodyPoste) tbodyPoste.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Aucun mois sélectionné</td></tr>';
            return;
        }

        if (isRgd) {
            genererLignesRgd(moisSelectionnes);
            genererLignesBureaux(moisSelectionnes);
        } else {
            genererLignesPoste(moisSelectionnes);
        }
    }

    // Générer lignes pour RGD
    function genererLignesRgd(moisSelectionnes) {
        const tbody = document.getElementById('tbodyRattrapageRgd');
        tbody.innerHTML = '';

        moisSelectionnes.forEach((mois, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${moisNoms[mois]}</strong><input type="hidden" name="mois_selectionnes[]" value="${mois}"></td>
                <td><input type="number" name="mois_${mois}_rgd_UEMOA_recouvrement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                <td><input type="number" name="mois_${mois}_rgd_UEMOA_reversement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_rgd_UEMOA_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                <td><input type="number" name="mois_${mois}_rgd_AES_recouvrement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                <td><input type="number" name="mois_${mois}_rgd_AES_reversement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_rgd_AES_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                <td>
                    ${index > 0 ? `<button type="button" class="btn btn-sm btn-outline-secondary copier-ligne" data-index="${index}">
                        <i class="fas fa-copy"></i>
                    </button>` : ''}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Générer lignes pour bureaux
    function genererLignesBureaux(moisSelectionnes) {
        bureaux.forEach(bureauId => {
            const tbody = document.querySelector(`tbody[data-bureau="${bureauId}"]`);
            if (!tbody) return;

            tbody.innerHTML = '';
            moisSelectionnes.forEach((mois, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${moisNoms[mois]}</strong></td>
                    <td><input type="number" name="mois_${mois}_bureau_${bureauId}_UEMOA_recouvrement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                    <td><input type="number" name="mois_${mois}_bureau_${bureauId}_UEMOA_reversement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                    <td><input type="text" name="mois_${mois}_bureau_${bureauId}_UEMOA_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                    <td><input type="number" name="mois_${mois}_bureau_${bureauId}_AES_recouvrement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                    <td><input type="number" name="mois_${mois}_bureau_${bureauId}_AES_reversement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                    <td><input type="text" name="mois_${mois}_bureau_${bureauId}_AES_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    // Générer lignes pour poste normal
    function genererLignesPoste(moisSelectionnes) {
        const tbody = document.getElementById('tbodyRattrapagePoste');
        tbody.innerHTML = '';

        moisSelectionnes.forEach((mois, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${moisNoms[mois]}</strong><input type="hidden" name="mois_selectionnes[]" value="${mois}"></td>
                <td><input type="number" name="mois_${mois}_UEMOA_recouvrement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                <td><input type="number" name="mois_${mois}_UEMOA_reversement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_UEMOA_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                <td><input type="number" name="mois_${mois}_AES_recouvrement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                <td><input type="number" name="mois_${mois}_AES_reversement" class="form-control form-control-sm" step="0.01" min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_AES_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                <td>
                    ${index > 0 ? `<button type="button" class="btn btn-sm btn-outline-secondary copier-ligne" data-index="${index}">
                        <i class="fas fa-copy"></i>
                    </button>` : ''}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Écouter les changements de sélection des mois
    @if($aDesMoisManquants)
    document.querySelectorAll('.mois-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (modeRattrapage) {
                genererLignesRattrapage();
            }
        });
    });
    @endif

    // Copier les valeurs du premier mois vers les autres (RGD)
    document.getElementById('copierPremierMoisRgd')?.addEventListener('click', function() {
        const firstRow = document.querySelector('#tbodyRattrapageRgd tr:first-child');
        if (!firstRow) return;

        const inputs = firstRow.querySelectorAll('input[type="number"], input[type="text"]');
        const rows = document.querySelectorAll('#tbodyRattrapageRgd tr:not(:first-child)');

        rows.forEach(row => {
            const rowInputs = row.querySelectorAll('input[type="number"], input[type="text"]');
            inputs.forEach((input, index) => {
                if (rowInputs[index]) {
                    rowInputs[index].value = input.value;
                }
            });
        });
    });

    // Copier les valeurs du premier mois vers les autres (Poste)
    document.getElementById('copierPremierMoisPoste')?.addEventListener('click', function() {
        const firstRow = document.querySelector('#tbodyRattrapagePoste tr:first-child');
        if (!firstRow) return;

        const inputs = firstRow.querySelectorAll('input[type="number"], input[type="text"]');
        const rows = document.querySelectorAll('#tbodyRattrapagePoste tr:not(:first-child)');

        rows.forEach(row => {
            const rowInputs = row.querySelectorAll('input[type="number"], input[type="text"]');
            inputs.forEach((input, index) => {
                if (rowInputs[index]) {
                    rowInputs[index].value = input.value;
                }
            });
        });
    });

    // Validation du formulaire
    document.getElementById('declarationForm').addEventListener('submit', function(e) {
        const action = e.submitter.value;

        // Vérifier si on est en mode rattrapage
        if (modeRattrapage) {
            // Ne compter que les checkboxes cochées et non désactivées
            const moisSelectionnes = Array.from(document.querySelectorAll('.mois-checkbox:checked:not(:disabled)'));
            if (moisSelectionnes.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins un mois à renseigner (les mois déjà renseignés ne peuvent pas être sélectionnés).');
                return;
            }
        }

        if (action === 'soumettre') {
            const nbMois = modeRattrapage ? document.querySelectorAll('.mois-checkbox:checked:not(:disabled)').length : 1;
            if (!confirm(`Êtes-vous sûr de vouloir valider ${nbMois} déclaration(s) ? Elles seront automatiquement validées et envoyées à l'ACCT.`)) {
                e.preventDefault();
            }
        }
    });

    // Auto-calcul du reste à reverser (optionnel)
    document.querySelectorAll('input[name$="_recouvrement"]').forEach(input => {
        input.addEventListener('input', function() {
            const baseName = this.name.replace('_recouvrement', '');
            const reversementInput = document.querySelector(`input[name="${baseName}_reversement"]`);

            if (reversementInput && this.value) {
                // Vous pouvez ajouter un calcul automatique ou des validations ici
            }
        });
    });
</script>
@endpush
@endsection

