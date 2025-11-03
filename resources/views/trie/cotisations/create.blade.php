@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-primary">
                        <i class="fas fa-plus-circle me-2"></i>Nouvelle Cotisation TRIE
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('trie.cotisations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Sélection Poste et Mode -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Sélection du Poste et de la Période</h5>
            @if($poste && count($bureaux) > 0)
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-light btn-sm" id="toggleNormal">
                    <i class="fas fa-calendar-day me-1"></i>Mois Unique
                </button>
                <button type="button" class="btn btn-warning btn-sm" id="toggleRattrapage">
                    <i class="fas fa-history me-1"></i>Rattrapage
                </button>
            </div>
            @endif
        </div>
        <div class="card-body">
            <!-- MODE NORMAL : Un seul mois -->
            <div id="modeNormal">
                <form method="GET" action="{{ route('trie.cotisations.create') }}" id="selectionForm">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Poste <span class="text-danger">*</span></label>
                            <select name="poste_id" class="form-select" required>
                                <option value="">-- Sélectionnez un poste --</option>
                                @foreach($postes as $posteOption)
                                    <option value="{{ $posteOption->id }}" {{ $posteId == $posteOption->id ? 'selected' : '' }}>
                                        {{ $posteOption->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Mois <span class="text-danger">*</span></label>
                            <select name="mois" class="form-select" required>
                                @foreach($moisList as $moisNum => $moisNom)
                                    <option value="{{ $moisNum }}" {{ $mois == $moisNum ? 'selected' : '' }}>
                                        {{ $moisNom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                            <select name="annee" class="form-select" required>
                                @foreach($annees as $anneeOption)
                                    <option value="{{ $anneeOption }}" {{ $annee == $anneeOption ? 'selected' : '' }}>
                                        {{ $anneeOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check"></i> Valider
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- MODE RATTRAPAGE : Plusieurs mois -->
            @if($poste && count($bureaux) > 0)
            <div id="modeRattrapage" style="display:none;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Mode Rattrapage :</strong> Sélectionnez tous les mois que vous souhaitez saisir en une seule fois.
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Sélection des Mois à Rattraper</label>
                        <div class="row">
                            @for($i = 1; $i <= 12; $i++)
                                @php
                                    $moisLibelle = $moisList[$i];
                                    // Vérifier si le mois est déjà renseigné pour au moins un bureau
                                    $moisRenseigne = false;
                                    foreach($bureaux as $bureau) {
                                        $existe = \App\Models\CotisationTrie::where('bureau_trie_id', $bureau->id)
                                            ->where('mois', $i)
                                            ->where('annee', $annee)
                                            ->exists();
                                        if($existe) {
                                            $moisRenseigne = true;
                                            break;
                                        }
                                    }
                                @endphp
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input mois-checkbox"
                                               type="checkbox"
                                               name="mois_rattrapage[]"
                                               value="{{ $i }}"
                                               id="mois_{{ $i }}"
                                               {{ $moisRenseigne ? 'disabled' : '' }}>
                                        <label class="form-check-label {{ $moisRenseigne ? 'text-muted' : '' }}" for="mois_{{ $i }}">
                                            {{ $moisLibelle }}
                                            @if($moisRenseigne)
                                                <span class="badge bg-success ms-1">Déjà saisi</span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Les mois déjà saisis sont grisés et ne peuvent pas être sélectionnés.
                        </small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($poste && count($bureaux) > 0)

    <!-- FORMULAIRE MODE NORMAL -->
    <form action="{{ route('trie.cotisations.store') }}" method="POST" id="cotisationFormNormal">
        @csrf
        <input type="hidden" name="mode" value="normal">
        <input type="hidden" name="poste_id" value="{{ $posteId }}">
        <input type="hidden" name="mois" value="{{ $mois }}">
        <input type="hidden" name="annee" value="{{ $annee }}">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-money-check-alt me-2"></i>Saisie des Cotisations - {{ $poste->nom }}
                    <small class="ms-2">({{ $moisList[$mois] }} {{ $annee }})</small>
                </h5>
            </div>
            <div class="card-body">
                @php
                    $bureauxACompleter = $bureaux->filter(fn($b) => !$b->cotisation_existante);
                    $bureauxDejaSaisis = $bureaux->filter(fn($b) => $b->cotisation_existante);
                @endphp

                @if($bureauxDejaSaisis->count() > 0)
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>{{ $bureauxDejaSaisis->count() }} bureau(x)</strong> a/ont déjà une cotisation enregistrée pour cette période.
                    Les données de ces bureaux sont affichées ci-dessous en <strong class="text-success">vert</strong> (lecture seule).
                    @if($bureauxACompleter->count() > 0)
                        <br>Il reste <strong>{{ $bureauxACompleter->count() }} bureau(x)</strong> à saisir.
                    @endif
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th><i class="fas fa-building"></i> Bureau</th>
                                <th class="text-end"><i class="fas fa-money-bill-wave"></i> Cotisation Courante</th>
                                <th class="text-end"><i class="fas fa-undo"></i> Apurement</th>
                                <th><i class="fas fa-comment"></i> Détail Apurement</th>
                                <th><i class="fas fa-credit-card"></i> Mode Paiement</th>
                                <th><i class="fas fa-receipt"></i> Référence</th>
                                <th><i class="fas fa-calendar-day"></i> Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bureaux as $index => $bureau)
                            @if(!$bureau->cotisation_existante)
                            <!-- Bureau sans cotisation : Formulaire de saisie -->
                            <tr class="bureau-row">
                                <td>
                                    <input type="checkbox"
                                           class="form-check-input bureau-checkbox"
                                           data-index="{{ $index }}"
                                           {{ old('bureaux.' . $index . '.bureau_trie_id') ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ $bureau->code_bureau }}</strong>
                                    <br><small class="text-muted">{{ $bureau->nom_bureau }}</small>
                                </td>
                                <td>
                                    <input type="hidden" name="bureaux[{{ $index }}][bureau_trie_id]" value="{{ $bureau->id }}" class="bureau-id-input" disabled>
                                    <input type="number"
                                           name="bureaux[{{ $index }}][montant_cotisation_courante]"
                                           class="form-control form-control-sm text-end montant-cotisation-input"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('bureaux.' . $index . '.montant_cotisation_courante', 0) }}"
                                           data-index="{{ $index }}"
                                           placeholder="0"
                                           disabled>
                                </td>
                                <td>
                                    <input type="number"
                                           name="bureaux[{{ $index }}][montant_apurement]"
                                           class="form-control form-control-sm text-end montant-apurement-input"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('bureaux.' . $index . '.montant_apurement', 0) }}"
                                           data-index="{{ $index }}"
                                           placeholder="0"
                                           disabled>
                                </td>
                                <td>
                                    <input type="text"
                                           name="bureaux[{{ $index }}][detail_apurement]"
                                           class="form-control form-control-sm detail-apurement-input"
                                           placeholder="Ex: janv-mars 2024"
                                           value="{{ old('bureaux.' . $index . '.detail_apurement') }}"
                                           disabled>
                                </td>
                                <td>
                                    <select name="bureaux[{{ $index }}][mode_paiement]"
                                            class="form-select form-select-sm mode-paiement-input"
                                            disabled>
                                        <option value="">-- Mode --</option>
                                        <option value="cheque" {{ old('bureaux.' . $index . '.mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                        <option value="virement" {{ old('bureaux.' . $index . '.mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                                        <option value="especes" {{ old('bureaux.' . $index . '.mode_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
                                        <option value="autre" {{ old('bureaux.' . $index . '.mode_paiement') == 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text"
                                           name="bureaux[{{ $index }}][reference_paiement]"
                                           class="form-control form-control-sm reference-input"
                                           placeholder="Ex: CHQ n°123"
                                           value="{{ old('bureaux.' . $index . '.reference_paiement') }}"
                                           disabled>
                                </td>
                                <td>
                                    <input type="date"
                                           name="bureaux[{{ $index }}][date_paiement]"
                                           class="form-control form-control-sm date-input"
                                           value="{{ old('bureaux.' . $index . '.date_paiement', date('Y-m-d')) }}"
                                           disabled>
                                </td>
                            </tr>
                            @else
                            <!-- Bureau avec cotisation existante : Affichage des données -->
                            <tr class="table-success">
                                <td class="text-center">
                                    <i class="fas fa-check-circle text-success fs-5" title="Cotisation validée"></i>
                                </td>
                                <td>
                                    <strong class="text-success">{{ $bureau->code_bureau }}</strong>
                                    <br><small class="text-muted">{{ $bureau->nom_bureau }}</small>
                                    <br><small class="text-success"><i class="fas fa-check-circle"></i> Cotisation enregistrée</small>
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">{{ number_format($bureau->cotisation_existante->montant_cotisation_courante, 0, ',', ' ') }}</strong>
                                    <br><small class="text-muted">FCFA</small>
                                </td>
                                <td class="text-end">
                                    @if($bureau->cotisation_existante->montant_apurement > 0)
                                        <strong class="text-warning">{{ number_format($bureau->cotisation_existante->montant_apurement, 0, ',', ' ') }}</strong>
                                        <br><small class="text-muted">FCFA</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $bureau->cotisation_existante->detail_apurement ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    @if($bureau->cotisation_existante->mode_paiement)
                                        <span class="badge bg-info">{{ ucfirst($bureau->cotisation_existante->mode_paiement) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $bureau->cotisation_existante->reference_paiement ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <small>{{ $bureau->cotisation_existante->date_paiement?->format('d/m/Y') ?? '-' }}</small>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            @php
                                $totalCotisationExistante = $bureauxDejaSaisis->sum(fn($b) => $b->cotisation_existante->montant_cotisation_courante);
                                $totalApurementExistant = $bureauxDejaSaisis->sum(fn($b) => $b->cotisation_existante->montant_apurement);
                                $totalExistant = $totalCotisationExistante + $totalApurementExistant;
                            @endphp
                            @if($bureauxDejaSaisis->count() > 0)
                            <tr class="table-success">
                                <th colspan="2" class="text-end">TOTAL DÉJÀ ENREGISTRÉ:</th>
                                <th class="text-end">
                                    <span class="fw-bold text-success">{{ number_format($totalCotisationExistante, 0, ',', ' ') }} FCFA</span>
                                </th>
                                <th class="text-end">
                                    <span class="fw-bold text-success">{{ number_format($totalApurementExistant, 0, ',', ' ') }} FCFA</span>
                                </th>
                                <th colspan="4" class="text-end">
                                    <strong>TOTAL: </strong>
                                    <span class="fw-bold text-success fs-5">{{ number_format($totalExistant, 0, ',', ' ') }} FCFA</span>
                                </th>
                            </tr>
                            @endif
                            @if($bureauxACompleter->count() > 0)
                            <tr>
                                <th colspan="2" class="text-end">TOTAL À SAISIR:</th>
                                <th class="text-end">
                                    <span id="totalCotisation" class="fw-bold text-primary">0 FCFA</span>
                                </th>
                                <th class="text-end">
                                    <span id="totalApurement" class="fw-bold text-warning">0 FCFA</span>
                                </th>
                                <th colspan="4" class="text-end">
                                    <strong>TOTAL: </strong>
                                    <span id="totalGeneral" class="fw-bold text-primary fs-5">0 FCFA</span>
                                </th>
                            </tr>
                            <tr class="table-info">
                                <th colspan="2" class="text-end">TOTAL GLOBAL (Enregistré + À saisir):</th>
                                <th class="text-end">
                                    <span id="totalCotisationGlobal" class="fw-bold text-dark">{{ number_format($totalCotisationExistante, 0, ',', ' ') }} FCFA</span>
                                </th>
                                <th class="text-end">
                                    <span id="totalApurementGlobal" class="fw-bold text-dark">{{ number_format($totalApurementExistant, 0, ',', ' ') }} FCFA</span>
                                </th>
                                <th colspan="4" class="text-end">
                                    <strong>TOTAL: </strong>
                                    <span id="grandTotal" class="fw-bold text-dark fs-4">{{ number_format($totalExistant, 0, ',', ' ') }} FCFA</span>
                                </th>
                            </tr>
                            @else
                            <tr class="table-success">
                                <th colspan="2" class="text-end">TOTAL PÉRIODE ({{ $moisList[$mois] }} {{ $annee }}):</th>
                                <th class="text-end">
                                    <span class="fw-bold text-success">{{ number_format($totalCotisationExistante, 0, ',', ' ') }} FCFA</span>
                                </th>
                                <th class="text-end">
                                    <span class="fw-bold text-success">{{ number_format($totalApurementExistant, 0, ',', ' ') }} FCFA</span>
                                </th>
                                <th colspan="4" class="text-end">
                                    <strong>TOTAL: </strong>
                                    <span class="fw-bold text-success fs-5">{{ number_format($totalExistant, 0, ',', ' ') }} FCFA</span>
                                </th>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Boutons d'action Mode Normal -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('trie.cotisations.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-save me-1"></i>Enregistrer les Cotisations
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- FORMULAIRE MODE RATTRAPAGE -->
    <form action="{{ route('trie.cotisations.store') }}" method="POST" id="cotisationFormRattrapage" style="display:none;">
        @csrf
        <input type="hidden" name="mode" value="rattrapage">
        <input type="hidden" name="poste_id" value="{{ $posteId }}">
        <input type="hidden" name="annee" value="{{ $annee }}">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Rattrapage Multi-Mois - {{ $poste->nom }}
                    <small class="ms-2">({{ $annee }})</small>
                </h5>
            </div>
            <div class="card-body">
                <div id="tableauRattrapageContainer">
                    <!-- Le tableau sera généré dynamiquement par JavaScript -->
                </div>
            </div>
        </div>

        <!-- Boutons d'action Mode Rattrapage -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('trie.cotisations.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-warning btn-lg" id="submitBtnRattrapage" disabled>
                        <i class="fas fa-save me-1"></i>Enregistrer le Rattrapage
                    </button>
                </div>
            </div>
        </div>
    </form>
    @elseif($poste && count($bureaux) == 0)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
        <p class="mb-0">Aucun bureau actif trouvé pour ce poste. Veuillez d'abord créer des bureaux.</p>
        @if($posteId)
        <a href="{{ route('trie.bureaux.manage', $posteId) }}" class="btn btn-primary mt-3">
            <i class="fas fa-building me-1"></i>Gérer les Bureaux
        </a>
        @else
        <a href="{{ route('trie.bureaux.index') }}" class="btn btn-primary mt-3">
            <i class="fas fa-building me-1"></i>Voir les Bureaux
        </a>
        @endif
    </div>
    @endif
</div>

@push('scripts')
<script>
    const moisNoms = {
        1: 'Janvier', 2: 'Février', 3: 'Mars', 4: 'Avril',
        5: 'Mai', 6: 'Juin', 7: 'Juillet', 8: 'Août',
        9: 'Septembre', 10: 'Octobre', 11: 'Novembre', 12: 'Décembre'
    };

    const bureaux = @json($bureaux->map(function($b) {
        return [
            'id' => $b->id,
            'code' => $b->code_bureau,
            'nom' => $b->nom_bureau
        ];
    }));

    let modeRattrapage = false;

    // =============== GESTION DES MODES ===============
    document.getElementById('toggleRattrapage')?.addEventListener('click', function() {
        modeRattrapage = true;
        document.getElementById('modeNormal').style.display = 'none';
        document.getElementById('modeRattrapage').style.display = 'block';
        document.getElementById('cotisationFormNormal').style.display = 'none';
        document.getElementById('cotisationFormRattrapage').style.display = 'block';
        document.getElementById('toggleRattrapage').classList.remove('btn-warning');
        document.getElementById('toggleRattrapage').classList.add('btn-light');
        document.getElementById('toggleNormal').classList.remove('btn-light');
        document.getElementById('toggleNormal').classList.add('btn-warning');

        genererTableauRattrapage();
    });

    document.getElementById('toggleNormal')?.addEventListener('click', function() {
        modeRattrapage = false;
        document.getElementById('modeNormal').style.display = 'block';
        document.getElementById('modeRattrapage').style.display = 'none';
        document.getElementById('cotisationFormNormal').style.display = 'block';
        document.getElementById('cotisationFormRattrapage').style.display = 'none';
        document.getElementById('toggleNormal').classList.remove('btn-warning');
        document.getElementById('toggleNormal').classList.add('btn-light');
        document.getElementById('toggleRattrapage').classList.remove('btn-light');
        document.getElementById('toggleRattrapage').classList.add('btn-warning');
    });

    // =============== GÉNÉRATION DU TABLEAU RATTRAPAGE ===============
    function genererTableauRattrapage() {
        const moisSelectionnes = [];
        document.querySelectorAll('.mois-checkbox:checked:not(:disabled)').forEach(cb => {
            moisSelectionnes.push(parseInt(cb.value));
        });

        if (moisSelectionnes.length === 0) {
            document.getElementById('tableauRattrapageContainer').innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Veuillez sélectionner au moins un mois pour le rattrapage.
                </div>
            `;
            document.getElementById('submitBtnRattrapage').disabled = true;
            return;
        }

        moisSelectionnes.sort((a, b) => a - b);

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="align-middle"><i class="fas fa-calendar"></i> Mois</th>
        `;

        // En-têtes des bureaux
        bureaux.forEach(bureau => {
            html += `<th colspan="4" class="text-center bg-primary text-white">${bureau.code} - ${bureau.nom}</th>`;
        });

        html += `</tr><tr>`;

        // Sous-en-têtes pour chaque bureau
        bureaux.forEach(() => {
            html += `
                <th class="text-end" style="min-width:120px;">Cotisation</th>
                <th class="text-end" style="min-width:120px;">Apurement</th>
                <th style="min-width:150px;">Réf. Paiement</th>
                <th style="min-width:130px;">Date</th>
            `;
        });

        html += `</tr></thead><tbody>`;

        // Lignes pour chaque mois sélectionné
        moisSelectionnes.forEach(mois => {
            html += `
                <tr>
                    <td><strong>${moisNoms[mois]}</strong><input type="hidden" name="mois_selectionnes[]" value="${mois}"></td>
            `;

            bureaux.forEach(bureau => {
                html += `
                    <td><input type="number"
                              name="cotisation_${mois}_${bureau.id}_montant_cotisation"
                              class="form-control form-control-sm text-end montant-rattrapage-input"
                              step="0.01" min="0" value="0" placeholder="0"></td>
                    <td><input type="number"
                              name="cotisation_${mois}_${bureau.id}_montant_apurement"
                              class="form-control form-control-sm text-end"
                              step="0.01" min="0" value="0" placeholder="0"></td>
                    <td><input type="text"
                              name="cotisation_${mois}_${bureau.id}_reference"
                              class="form-control form-control-sm"
                              placeholder="Ex: CHQ n°123"></td>
                    <td><input type="date"
                              name="cotisation_${mois}_${bureau.id}_date_paiement"
                              class="form-control form-control-sm"
                              value="{{ date('Y-m-d') }}"></td>
                `;
            });

            html += `</tr>`;
        });

        html += `</tbody></table></div>`;

        document.getElementById('tableauRattrapageContainer').innerHTML = html;

        // Activer le bouton submit
        document.getElementById('submitBtnRattrapage').disabled = false;

        // Ajouter les écouteurs pour validation
        document.querySelectorAll('.montant-rattrapage-input').forEach(input => {
            input.addEventListener('input', validateRattrapageForm);
        });
    }

    // Écouter les changements de sélection des mois
    document.querySelectorAll('.mois-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (modeRattrapage) {
                genererTableauRattrapage();
            }
        });
    });

    function validateRattrapageForm() {
        let hasValue = false;
        document.querySelectorAll('.montant-rattrapage-input').forEach(input => {
            if (parseFloat(input.value) > 0) {
                hasValue = true;
            }
        });
        document.getElementById('submitBtnRattrapage').disabled = !hasValue;
    }

    // =============== MODE NORMAL (Code existant) ===============
    // Sélectionner/Désélectionner tous les bureaux
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.bureau-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            toggleBureauInputs(cb.dataset.index, this.checked);
        });
        updateTotals();
        checkFormValidity();
    });

    // Gérer la sélection individuelle
    document.querySelectorAll('.bureau-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleBureauInputs(this.dataset.index, this.checked);
            updateTotals();
            checkFormValidity();
        });
    });

    // Activer/Désactiver les inputs d'un bureau
    function toggleBureauInputs(index, enabled) {
        const row = document.querySelector(`tr.bureau-row:nth-child(${parseInt(index) + 1})`);
        if (!row) return;

        const inputs = row.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (!input.classList.contains('bureau-checkbox')) {
                input.disabled = !enabled;
            }
        });
    }

    // Calculer les totaux
    document.querySelectorAll('.montant-cotisation-input, .montant-apurement-input').forEach(input => {
        input.addEventListener('input', function() {
            updateTotals();
            checkFormValidity();
        });
    });

    function updateTotals() {
        // Totaux des bureaux à saisir (sélectionnés)
        let totalCotisation = 0;
        let totalApurement = 0;

        document.querySelectorAll('.bureau-checkbox:checked').forEach(checkbox => {
            const index = checkbox.dataset.index;
            const cotisationInput = document.querySelector(`.montant-cotisation-input[data-index="${index}"]`);
            const apurementInput = document.querySelector(`.montant-apurement-input[data-index="${index}"]`);

            if (cotisationInput && !cotisationInput.disabled) {
                totalCotisation += parseFloat(cotisationInput.value) || 0;
            }
            if (apurementInput && !apurementInput.disabled) {
                totalApurement += parseFloat(apurementInput.value) || 0;
            }
        });

        const totalGeneral = totalCotisation + totalApurement;

        // Mettre à jour les totaux à saisir
        const totalCotisationElement = document.getElementById('totalCotisation');
        const totalApurementElement = document.getElementById('totalApurement');
        const totalGeneralElement = document.getElementById('totalGeneral');

        if (totalCotisationElement) {
            totalCotisationElement.textContent = new Intl.NumberFormat('fr-FR').format(totalCotisation) + ' FCFA';
        }
        if (totalApurementElement) {
            totalApurementElement.textContent = new Intl.NumberFormat('fr-FR').format(totalApurement) + ' FCFA';
        }
        if (totalGeneralElement) {
            totalGeneralElement.textContent = new Intl.NumberFormat('fr-FR').format(totalGeneral) + ' FCFA';
        }

        // Calculer et mettre à jour les totaux globaux (existant + à saisir)
        const totalCotisationGlobalElement = document.getElementById('totalCotisationGlobal');
        const totalApurementGlobalElement = document.getElementById('totalApurementGlobal');
        const grandTotalElement = document.getElementById('grandTotal');

        if (totalCotisationGlobalElement && totalApurementGlobalElement && grandTotalElement) {
            // Récupérer les totaux existants depuis les valeurs initiales
            const totalExistantCotisation = {{ $totalCotisationExistante }};
            const totalExistantApurement = {{ $totalApurementExistant }};

            const totalGlobalCotisation = totalExistantCotisation + totalCotisation;
            const totalGlobalApurement = totalExistantApurement + totalApurement;
            const grandTotal = totalGlobalCotisation + totalGlobalApurement;

            totalCotisationGlobalElement.textContent = new Intl.NumberFormat('fr-FR').format(totalGlobalCotisation) + ' FCFA';
            totalApurementGlobalElement.textContent = new Intl.NumberFormat('fr-FR').format(totalGlobalApurement) + ' FCFA';
            grandTotalElement.textContent = new Intl.NumberFormat('fr-FR').format(grandTotal) + ' FCFA';
        }
    }

    // Vérifier la validité du formulaire
    function checkFormValidity() {
        const checkedBoxes = document.querySelectorAll('.bureau-checkbox:checked');
        let isValid = checkedBoxes.length > 0;

        if (isValid) {
            checkedBoxes.forEach(checkbox => {
                const index = checkbox.dataset.index;
                const cotisationInput = document.querySelector(`.montant-cotisation-input[data-index="${index}"]`);
                const value = parseFloat(cotisationInput.value) || 0;
                if (value <= 0) {
                    isValid = false;
                }
            });
        }

        document.getElementById('submitBtn').disabled = !isValid;
    }

    // Validation du formulaire mode normal
    document.getElementById('cotisationFormNormal')?.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.bureau-checkbox:checked');

        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un bureau.');
            return false;
        }

        let totalGeneral = 0;
        checkedBoxes.forEach(checkbox => {
            const index = checkbox.dataset.index;
            const cotisationInput = document.querySelector(`.montant-cotisation-input[data-index="${index}"]`);
            const apurementInput = document.querySelector(`.montant-apurement-input[data-index="${index}"]`);

            const cotisation = parseFloat(cotisationInput.value) || 0;
            const apurement = parseFloat(apurementInput.value) || 0;
            totalGeneral += cotisation + apurement;

            if (cotisation <= 0) {
                e.preventDefault();
                alert('Le montant de la cotisation doit être supérieur à zéro pour tous les bureaux sélectionnés.');
                return false;
            }
        });

        if (!confirm(`Êtes-vous sûr de vouloir enregistrer ces cotisations pour un montant total de ${new Intl.NumberFormat('fr-FR').format(totalGeneral)} FCFA ?`)) {
            e.preventDefault();
            return false;
        }
    });

    // Initialiser les totaux au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        updateTotals();
    });
</script>
@endpush
@endsection

