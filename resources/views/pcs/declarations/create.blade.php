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
        // Mois manquants année courante (dédupliqués)
        $moisManquantsAnnee = [];
        foreach ($moisManquants as $programme => $mois) {
            $moisManquantsAnnee = array_merge($moisManquantsAnnee, $mois);
        }
        $moisManquantsAnnee = array_unique($moisManquantsAnnee);

        // Mois manquants année précédente (dédupliqués) - seulement septembre à décembre
        $moisManquantsPrecedente = [];
        foreach ($moisManquantsAnneePrecedente as $programme => $mois) {
            $moisManquantsPrecedente = array_merge($moisManquantsPrecedente, $mois);
        }
        $moisManquantsPrecedente = array_unique($moisManquantsPrecedente);

        // Tous les mois manquants (pour savoir s'il y a des manquants)
        $tousMoisManquants = array_merge($moisManquantsAnnee, $moisManquantsPrecedente);
        $tousMoisManquants = array_unique($tousMoisManquants);
        $aDesMoisManquants = !empty($tousMoisManquants);

        // Créer un tableau des mois déjà renseignés par année (dédupliqués)
        $moisRenseignesAnneeCourante = [];
        foreach ($moisRenseignes as $programme => $mois) {
            $moisRenseignesAnneeCourante = array_merge($moisRenseignesAnneeCourante, $mois);
        }
        $moisRenseignesAnneeCourante = array_unique($moisRenseignesAnneeCourante);

        $moisRenseignesAnneePrecedente = [];
        foreach ($moisRenseignesAnneePrecedente as $programme => $mois) {
            $moisRenseignesAnneePrecedente = array_merge($moisRenseignesAnneePrecedente, $mois);
        }
        $moisRenseignesAnneePrecedente = array_unique($moisRenseignesAnneePrecedente);
    @endphp

    @if(count($moisManquantsAnnee) > 0)
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Attention !</strong> Vous avez <strong>{{ count($moisManquantsAnnee) }} mois restant (s)</strong> pour {{ $annee }}.
        <br>
        <button type="button" class="btn btn-warning btn-sm mt-2" id="toggleRattrapage">
            <i class="fas fa-history me-1"></i>Utiliser le mode Rattrapage
        </button>
        <button type="button" class="btn btn-secondary btn-sm mt-2 ms-2" id="toggleNormal" style="display:none;">
            <i class="fas fa-calendar me-1"></i>Revenir au mode Normal
        </button>
    </div>
    @endif

    <form action="{{ route('pcs.declarations.store') }}" method="POST" id="declarationForm" enctype="multipart/form-data">
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
                            <div class="row" id="moisContainer">
                                @for($i = 1; $i <= 12; $i++)
                                    @php
                                        $moisLibelle = \Carbon\Carbon::create()->month($i)->locale('fr')->translatedFormat('F');
                                        // Pour l'année courante
                                        $isManquantAnneeCourante = in_array($i, $moisManquantsAnnee);
                                        $isRenseigneAnneeCourante = in_array($i, $moisRenseignesAnneeCourante);
                                        // Pour l'année précédente (seulement septembre à décembre)
                                        $isManquantAnneePrecedente = ($i >= 9 && $i <= 12) && in_array($i, $moisManquantsPrecedente);
                                        $isRenseigneAnneePrecedente = ($i >= 9 && $i <= 12) && in_array($i, $moisRenseignesAnneePrecedente);
                                    @endphp
                                    <div class="col-md-3 mb-2 mois-item"
                                         data-mois="{{ $i }}"
                                         data-manquant-annee-courante="{{ $isManquantAnneeCourante ? '1' : '0' }}"
                                         data-renseigne-annee-courante="{{ $isRenseigneAnneeCourante ? '1' : '0' }}"
                                         data-manquant-annee-precedente="{{ $isManquantAnneePrecedente ? '1' : '0' }}"
                                         data-renseigne-annee-precedente="{{ $isRenseigneAnneePrecedente ? '1' : '0' }}">
                                        <div class="form-check">
                                            <input class="form-check-input mois-checkbox"
                                                   type="checkbox"
                                                   name="mois_selectionnes[]"
                                                   value="{{ $i }}"
                                                   id="mois_{{ $i }}"
                                                   data-mois="{{ $i }}">
                                            <label class="form-check-label"
                                                   for="mois_{{ $i }}"
                                                   id="label_mois_{{ $i }}">
                                                {{ $moisLibelle }}
                                                <span class="badge-container ms-1"></span>
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
                                        <th>UEMOA - Preuve</th>
                                        <th>AES - Recouvré</th>
                                        <th>AES - Reversé</th>
                                        <th>AES - Référence</th>
                                        <th>AES - Preuve</th>
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
                                            <th>UEMOA - Preuve</th>
                                            <th>AES - Recouvré</th>
                                            <th>AES - Reversé</th>
                                            <th>AES - Référence</th>
                                            <th>AES - Preuve</th>
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
                                        <th>UEMOA - Preuve</th>
                                        <th>AES - Recouvré</th>
                                        <th>AES - Reversé</th>
                                        <th>AES - Référence</th>
                                        <th>AES - Preuve</th>
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
                                    <input type="text"
                                           name="rgd_UEMOA_recouvrement"
                                           class="form-control montant-input"
                                           inputmode="decimal"
                                           data-min="0"
                                           value="{{ old('rgd_UEMOA_recouvrement') }}"
                                           placeholder="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Montant Reversé (FCFA)</label>
                                    <input type="text"
                                           name="rgd_UEMOA_reversement"
                                           class="form-control montant-input"
                                           inputmode="decimal"
                                           data-min="0"
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
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-paperclip me-1"></i>Preuve de paiement</label>
                                    <input type="file" name="rgd_UEMOA_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
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
                                <input type="text"
                                       name="rgd_AES_recouvrement"
                                       class="form-control montant-input"
                                       inputmode="decimal"
                                       data-min="0"
                                       value="{{ old('rgd_AES_recouvrement') }}"
                                       placeholder="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Montant Reversé (FCFA)</label>
                                <input type="text"
                                       name="rgd_AES_reversement"
                                       class="form-control montant-input"
                                       inputmode="decimal"
                                       data-min="0"
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
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-paperclip me-1"></i>Preuve de paiement</label>
                                <input type="file" name="rgd_AES_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
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
                                        <input type="text"
                                               name="bureau_{{ $bureau->id }}_UEMOA_recouvrement"
                                               class="form-control form-control-sm montant-input"
                                               inputmode="decimal"
                                               data-min="0"
                                               value="{{ old('bureau_'.$bureau->id.'_UEMOA_recouvrement') }}"
                                               placeholder="0">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Reversé (FCFA)</label>
                                        <input type="text"
                                               name="bureau_{{ $bureau->id }}_UEMOA_reversement"
                                               class="form-control form-control-sm montant-input"
                                               inputmode="decimal"
                                               data-min="0"
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
                                    <div class="mb-2">
                                        <label class="form-label small"><i class="fas fa-paperclip"></i> Preuve</label>
                                        <input type="file" name="bureau_{{ $bureau->id }}_UEMOA_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
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
                                    <input type="text"
                                           name="bureau_{{ $bureau->id }}_AES_recouvrement"
                                           class="form-control form-control-sm montant-input"
                                           inputmode="decimal"
                                           data-min="0"
                                           value="{{ old('bureau_'.$bureau->id.'_AES_recouvrement') }}"
                                           placeholder="0">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Reversé (FCFA)</label>
                                    <input type="text"
                                           name="bureau_{{ $bureau->id }}_AES_reversement"
                                           class="form-control form-control-sm montant-input"
                                           inputmode="decimal"
                                           data-min="0"
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
                                <div class="mb-2">
                                    <label class="form-label small"><i class="fas fa-paperclip"></i> Preuve</label>
                                    <input type="file" name="bureau_{{ $bureau->id }}_AES_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
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
                                    <input type="text"
                                           name="UEMOA_recouvrement"
                                           class="form-control form-control-lg montant-input"
                                           inputmode="decimal"
                                           data-min="0"
                                           value="{{ old('UEMOA_recouvrement') }}"
                                           placeholder="0">
                                    <small class="text-muted">Recettes perçues ce mois</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Montant Reversé (FCFA)</label>
                                    <input type="text"
                                           name="UEMOA_reversement"
                                           class="form-control form-control-lg montant-input"
                                           inputmode="decimal"
                                           data-min="0"
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
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><i class="fas fa-paperclip me-1"></i>Preuve de paiement</label>
                                    <input type="file" name="UEMOA_preuve_paiement" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
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
                                <input type="text"
                                       name="AES_recouvrement"
                                       class="form-control form-control-lg montant-input"
                                       inputmode="decimal"
                                       data-min="0"
                                       value="{{ old('AES_recouvrement') }}"
                                       placeholder="0">
                                <small class="text-muted">Recettes perçues ce mois</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Montant Reversé (FCFA)</label>
                                <input type="text"
                                       name="AES_reversement"
                                       class="form-control form-control-lg montant-input"
                                       inputmode="decimal"
                                       data-min="0"
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
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-paperclip me-1"></i>Preuve de paiement</label>
                                <input type="file" name="AES_preuve_paiement" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
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
                    {{-- <button type="submit" name="action" value="brouillon" class="btn btn-outline-danger btn-lg">
                        <i class="fas fa-save me-1"></i>Enregistrer Brouillon
                    </button> --}}
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
        user-select: none;
        pointer-events: none;
    }

    .form-check-input:disabled {
        cursor: not-allowed;
        opacity: 0.5;
        pointer-events: none;
    }

    /* Empêcher le clic sur le conteneur si la checkbox est désactivée */
    .mois-item:has(.mois-checkbox:disabled) {
        pointer-events: none;
        opacity: 0.6;
    }

    .mois-item:has(.mois-checkbox:disabled) .form-check-label {
        cursor: not-allowed;
        user-select: none;
    }

    .mois-renseigne {
        background-color: #f8f9fa;
        border-left: 3px solid #28a745;
        padding-left: 8px;
    }
</style>
<script>
    // --- Formatage des montants (séparateur de milliers : espace, décimal : virgule) ---
    function parseMontant(str) {
        if (str === '' || str == null) return '';
        const s = String(str).replace(/\s/g, '').replace(',', '.');
        const num = parseFloat(s);
        return isNaN(num) ? '' : String(num);
    }
    function formatMontant(str) {
        const parsed = parseMontant(str);
        if (parsed === '') return '';
        const parts = parsed.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return parts.length > 1 ? parts[0] + ',' + parts[1] : parts[0];
    }
    function initMontantInputs(container) {
        const scope = container || document;
        scope.querySelectorAll('.montant-input').forEach(input => {
            if (input.dataset.montantInit) return;
            input.dataset.montantInit = '1';
            input.addEventListener('blur', function() {
                const v = this.value.trim();
                if (v) this.value = formatMontant(v);
            });
            input.addEventListener('focus', function() {
                const v = this.value.trim();
                if (v) this.value = parseMontant(v).replace('.', ',');
            });
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        initMontantInputs();
        document.querySelectorAll('.montant-input').forEach(input => {
            if (input.value && input.value.trim()) input.value = formatMontant(input.value);
        });
    });

    // Noms des mois en français
    const moisNoms = {
        1: 'Janvier', 2: 'Février', 3: 'Mars', 4: 'Avril',
        5: 'Mai', 6: 'Juin', 7: 'Juillet', 8: 'Août',
        9: 'Septembre', 10: 'Octobre', 11: 'Novembre', 12: 'Décembre'
    };

    let modeRattrapage = false;
    const isRgd = {{ $poste->isRgd() ? 'true' : 'false' }};
    const bureaux = @json($poste->isRgd() ? $bureaux->map(fn($b) => $b->id)->toArray() : []);
    const anneeCourante = {{ $annee }};
    const anneePrecedente = {{ $anneePrecedente }};

    // Fonction pour mettre à jour les mois selon l'année sélectionnée
    function mettreAJourMoisSelonAnnee() {
        const anneeSelect = document.getElementById('anneeRattrapage');
        if (!anneeSelect) return;

        const anneeSelectionnee = parseInt(anneeSelect.value);
        const isAnneeCourante = anneeSelectionnee === anneeCourante;
        const isAnneePrecedente = anneeSelectionnee === anneePrecedente;

        // Parcourir tous les mois
        document.querySelectorAll('.mois-item').forEach(item => {
            const mois = parseInt(item.dataset.mois);
            const checkbox = item.querySelector('.mois-checkbox');
            const label = item.querySelector('label');
            const badgeContainer = item.querySelector('.badge-container');

            let isManquant = false;
            let isRenseigne = false;

            if (isAnneeCourante) {
                // Pour l'année courante, vérifier tous les mois (1-12)
                isManquant = item.dataset.manquantAnneeCourante === '1';
                isRenseigne = item.dataset.renseigneAnneeCourante === '1';
            } else if (isAnneePrecedente) {
                // Pour l'année précédente, seulement septembre à décembre
                if (mois >= 9 && mois <= 12) {
                    isManquant = item.dataset.manquantAnneePrecedente === '1';
                    isRenseigne = item.dataset.renseigneAnneePrecedente === '1';
                } else {
                    // Les mois 1-8 de l'année précédente ne sont pas disponibles
                    isRenseigne = false;
                    isManquant = false;
                }
            } else {
                // Pour une autre année, tous les mois sont disponibles (non renseignés)
                isManquant = true;
                isRenseigne = false;
            }

            // Mettre à jour la checkbox
            checkbox.disabled = isRenseigne;
            checkbox.checked = isManquant && !isRenseigne;

            // Mettre à jour le label
            if (isRenseigne) {
                label.classList.add('text-muted');
                label.style.opacity = '0.6';
                label.style.cursor = 'not-allowed';
            } else {
                label.classList.remove('text-muted');
                label.style.opacity = '';
                label.style.cursor = '';
            }

            // Mettre à jour le badge
            badgeContainer.innerHTML = '';
            if (isManquant && !isRenseigne) {
                badgeContainer.innerHTML = '<span class="badge bg-warning text-dark">Manquant</span>';
            } else if (isRenseigne) {
                badgeContainer.innerHTML = '<span class="badge bg-success">Déjà renseigné</span>';
            }

            // Si c'est l'année précédente et le mois est < 9, désactiver
            if (isAnneePrecedente && mois < 9) {
                checkbox.disabled = true;
                checkbox.checked = false;
                label.classList.add('text-muted');
                label.style.opacity = '0.4';
                label.style.cursor = 'not-allowed';
                badgeContainer.innerHTML = '<span class="badge bg-secondary">Non disponible</span>';
            }
        });

        // Régénérer les lignes du tableau si on est en mode rattrapage
        if (modeRattrapage) {
            genererLignesRattrapage();
        }
    }

    // Empêcher la sélection des mois déjà renseignés
    function prevenirSelectionMoisRenseignes() {
        document.querySelectorAll('.mois-checkbox').forEach(checkbox => {
            // Empêcher le clic sur les checkboxes désactivées
            checkbox.addEventListener('click', function(e) {
                if (this.disabled) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });

            // Empêcher le changement d'état si désactivé
            checkbox.addEventListener('change', function(e) {
                if (this.disabled) {
                    this.checked = false;
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });

            // Empêcher la manipulation via le clavier
            checkbox.addEventListener('keydown', function(e) {
                if (this.disabled) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        });
    }

    // Écouter les changements d'année dans le formulaire de rattrapage
    @if($aDesMoisManquants)
    document.addEventListener('DOMContentLoaded', function() {
        const anneeSelect = document.getElementById('anneeRattrapage');
        if (anneeSelect) {
            anneeSelect.addEventListener('change', function() {
                mettreAJourMoisSelonAnnee();
                // Réappliquer les protections après la mise à jour
                setTimeout(prevenirSelectionMoisRenseignes, 100);
            });
            // Initialiser l'affichage au chargement
            mettreAJourMoisSelonAnnee();
            // Appliquer les protections au chargement
            setTimeout(prevenirSelectionMoisRenseignes, 100);
        }
    });
    @endif

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

        // Désactiver les champs du mode normal pour qu'ils ne soient pas soumis
        document.querySelectorAll('#modeNormal input, #modeNormal select').forEach(el => el.disabled = true);
        document.querySelectorAll('#formulaireNormal input, #formulaireNormal select, #formulaireNormal textarea').forEach(el => el.disabled = true);

        // Activer les champs du mode rattrapage
        document.querySelectorAll('#modeRattrapage input, #modeRattrapage select').forEach(el => el.disabled = false);
        document.querySelectorAll('#formulaireRattrapage input, #formulaireRattrapage select, #formulaireRattrapage textarea').forEach(el => el.disabled = false);

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

        // Activer les champs du mode normal
        document.querySelectorAll('#modeNormal input, #modeNormal select').forEach(el => el.disabled = false);
        document.querySelectorAll('#formulaireNormal input, #formulaireNormal select, #formulaireNormal textarea').forEach(el => el.disabled = false);

        // Désactiver les champs du mode rattrapage
        document.querySelectorAll('#modeRattrapage input, #modeRattrapage select').forEach(el => el.disabled = true);
        document.querySelectorAll('#formulaireRattrapage input, #formulaireRattrapage select, #formulaireRattrapage textarea').forEach(el => el.disabled = true);
    });

    // Au chargement de la page, s'assurer que les champs du mode rattrapage sont désactivés
    document.addEventListener('DOMContentLoaded', function() {
        if (!modeRattrapage) {
            // Désactiver les champs du mode rattrapage par défaut
            document.querySelectorAll('#modeRattrapage input, #modeRattrapage select').forEach(el => el.disabled = true);
            document.querySelectorAll('#formulaireRattrapage input, #formulaireRattrapage select, #formulaireRattrapage textarea').forEach(el => el.disabled = true);
        }
    });
    @endif

    // Générer les lignes du tableau de rattrapage
    function genererLignesRattrapage() {
        // Ne récupérer que les checkboxes cochées ET non désactivées
        const moisSelectionnes = [];
        document.querySelectorAll('.mois-checkbox').forEach(cb => {
            // Vérifier explicitement que la checkbox n'est pas désactivée
            if (cb.checked && !cb.disabled) {
                const mois = parseInt(cb.value);
                if (!isNaN(mois)) {
                    moisSelectionnes.push(mois);
                }
            }
        });

        if (moisSelectionnes.length === 0) {
            // Afficher un message si aucun mois n'est sélectionné
            const tbodyRgd = document.getElementById('tbodyRattrapageRgd');
            const tbodyPoste = document.getElementById('tbodyRattrapagePoste');
            if (tbodyRgd) tbodyRgd.innerHTML = '<tr><td colspan="10" class="text-center text-muted">Aucun mois sélectionné</td></tr>';
            if (tbodyPoste) tbodyPoste.innerHTML = '<tr><td colspan="10" class="text-center text-muted">Aucun mois sélectionné</td></tr>';
            return;
        }

        if (isRgd) {
            genererLignesRgd(moisSelectionnes);
            genererLignesBureaux(moisSelectionnes);
        } else {
            genererLignesPoste(moisSelectionnes);
        }
        initMontantInputs(document.getElementById('formulaireRattrapage'));
    }

    // Générer lignes pour RGD
    function genererLignesRgd(moisSelectionnes) {
        const tbody = document.getElementById('tbodyRattrapageRgd');
        tbody.innerHTML = '';

        moisSelectionnes.forEach((mois, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${moisNoms[mois]}</strong><input type="hidden" name="mois_selectionnes[]" value="${mois}"></td>
                <td><input type="text" name="mois_${mois}_rgd_UEMOA_recouvrement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_rgd_UEMOA_reversement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_rgd_UEMOA_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                <td><input type="file" name="mois_${mois}_rgd_UEMOA_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></td>
                <td><input type="text" name="mois_${mois}_rgd_AES_recouvrement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_rgd_AES_reversement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_rgd_AES_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                <td><input type="file" name="mois_${mois}_rgd_AES_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></td>
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
                    <td><input type="text" name="mois_${mois}_bureau_${bureauId}_UEMOA_recouvrement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                    <td><input type="text" name="mois_${mois}_bureau_${bureauId}_UEMOA_reversement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                    <td><input type="text" name="mois_${mois}_bureau_${bureauId}_UEMOA_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                    <td><input type="file" name="mois_${mois}_bureau_${bureauId}_UEMOA_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></td>
                    <td><input type="text" name="mois_${mois}_bureau_${bureauId}_AES_recouvrement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                    <td><input type="text" name="mois_${mois}_bureau_${bureauId}_AES_reversement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                    <td><input type="text" name="mois_${mois}_bureau_${bureauId}_AES_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                    <td><input type="file" name="mois_${mois}_bureau_${bureauId}_AES_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></td>
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
                <td><input type="text" name="mois_${mois}_UEMOA_recouvrement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_UEMOA_reversement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_UEMOA_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                <td><input type="file" name="mois_${mois}_UEMOA_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></td>
                <td><input type="text" name="mois_${mois}_AES_recouvrement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_AES_reversement" class="form-control form-control-sm montant-input" inputmode="decimal" data-min="0" placeholder="0"></td>
                <td><input type="text" name="mois_${mois}_AES_reference" class="form-control form-control-sm" placeholder="Réf."></td>
                <td><input type="file" name="mois_${mois}_AES_preuve_paiement" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"></td>
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
        const inputs = firstRow.querySelectorAll('input:not([type="hidden"])');
        document.querySelectorAll('#tbodyRattrapageRgd tr:not(:first-child)').forEach(row => {
            row.querySelectorAll('input:not([type="hidden"])').forEach((rowInput, index) => {
                if (inputs[index]) rowInput.value = inputs[index].value;
            });
        });
    });

    // Copier les valeurs du premier mois vers les autres (Poste)
    document.getElementById('copierPremierMoisPoste')?.addEventListener('click', function() {
        const firstRow = document.querySelector('#tbodyRattrapagePoste tr:first-child');
        if (!firstRow) return;
        const inputs = firstRow.querySelectorAll('input:not([type="hidden"])');
        document.querySelectorAll('#tbodyRattrapagePoste tr:not(:first-child)').forEach(row => {
            row.querySelectorAll('input:not([type="hidden"])').forEach((rowInput, index) => {
                if (inputs[index]) rowInput.value = inputs[index].value;
            });
        });
    });

    // Empêcher la soumission du formulaire par la touche Entrée (sauf dans les textarea)
    document.getElementById('declarationForm').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
    });

    // À la soumission : envoyer les montants sans séparateurs (format numérique)
    document.getElementById('declarationForm').addEventListener('submit', function(e) {
        document.querySelectorAll('.montant-input').forEach(input => {
            if (input.value && input.value.trim()) input.value = parseMontant(input.value);
        });
    });

    // Validation du formulaire
    document.getElementById('declarationForm').addEventListener('submit', function(e) {
        const action = e.submitter.value;

        // Vérifier si on est en mode rattrapage
        if (modeRattrapage) {
            // Ne compter que les checkboxes cochées et non désactivées
            const moisSelectionnes = Array.from(document.querySelectorAll('.mois-checkbox:checked:not(:disabled)'));

            // Vérification supplémentaire : s'assurer qu'aucun mois désactivé n'est inclus
            const moisDesactivesCoches = Array.from(document.querySelectorAll('.mois-checkbox:checked:disabled'));
            if (moisDesactivesCoches.length > 0) {
                e.preventDefault();
                alert('Erreur : Certains mois déjà renseignés sont sélectionnés. Veuillez les désélectionner.');
                // Décocher automatiquement les mois désactivés
                moisDesactivesCoches.forEach(cb => cb.checked = false);
                return;
            }

            if (moisSelectionnes.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins un mois à renseigner (les mois déjà renseignés ne peuvent pas être sélectionnés).');
                return;
            }

            // Vérification finale : s'assurer que tous les mois sélectionnés sont valides
            const moisInvalides = [];
            moisSelectionnes.forEach(checkbox => {
                if (checkbox.disabled) {
                    moisInvalides.push(checkbox.value);
                }
            });

            if (moisInvalides.length > 0) {
                e.preventDefault();
                alert('Erreur : Certains mois sélectionnés sont déjà renseignés et ne peuvent pas être inclus.');
                moisInvalides.forEach(mois => {
                    const cb = document.getElementById(`mois_${mois}`);
                    if (cb) cb.checked = false;
                });
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

