@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-cash-register me-2"></i>Nouveau Règlement
                    </h3>
                    {{-- <p class="text-muted mb-0">Sélectionnez les postes et saisissez les montants à déstocker</p> --}}
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.destockages.collecte', ['programme' => $programme, 'mois' => $mois, 'annee' => $annee]) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('pcs.destockages.store') }}" method="POST" id="destockageForm">
        @csrf

        <!-- Informations générales -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Programme <span class="text-danger">*</span></label>
                        <select name="programme" class="form-select" required id="programmeSelect">
                            <option value="UEMOA" {{ $programme == 'UEMOA' ? 'selected' : '' }}>UEMOA</option>
                            <option value="AES" {{ $programme == 'AES' ? 'selected' : '' }}>AES</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Mois <span class="text-danger">*</span></label>
                        <select name="periode_mois" class="form-select" required id="moisSelect">
                            @foreach($moisList as $moisNum => $moisNom)
                                <option value="{{ $moisNum }}" {{ $mois == $moisNum ? 'selected' : '' }}>
                                    {{ $moisNom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                        <select name="periode_annee" class="form-select" required id="anneeSelect">
                            @foreach($annees as $anneeOption)
                                <option value="{{ $anneeOption }}" {{ $annee == $anneeOption ? 'selected' : '' }}>
                                    {{ $anneeOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date Déstockage <span class="text-danger">*</span></label>
                        <input type="date" name="date_destockage" class="form-select" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Observation</label>
                        <textarea name="observation" class="form-control" rows="2" placeholder="Observations éventuelles..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sélection des postes -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-list-check me-2"></i>Sélection des Postes
                    <small class="text-muted">Cochez les postes à inclure dans ce règlement</small>
                </h5>
            </div>
            <div class="card-body">
                @if(count($collectesParPoste) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th><i class="fas fa-building"></i> Entité</th>
                                <th class="text-end"><i class="fas fa-arrow-up"></i> Collecté</th>
                                <th class="text-end"><i class="fas fa-arrow-down"></i> Déjà Règlement</th>
                                <th class="text-end"><i class="fas fa-balance-scale"></i> Disponible</th>
                                <th class="text-end"><i class="fas fa-money-bill-wave"></i> Montant à Règlement</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collectesParPoste as $index => $collecte)
                                @if($collecte['solde_disponible'] > 0)
                                <tr class="poste-row" data-disponible="{{ $collecte['solde_disponible'] }}">
                                    <td>
                                        <input type="checkbox"
                                               class="form-check-input poste-checkbox"
                                               data-post-id="{{ $collecte['id'] }}"
                                               data-disponible="{{ $collecte['solde_disponible'] }}"
                                               data-index="{{ $index }}">
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $collecte['type'] == 'poste' ? 'primary' : 'info' }}">
                                            {{ $collecte['type'] == 'poste' ? 'Poste' : 'Bureau' }}
                                        </span>
                                        <strong>{{ $collecte['nom'] }}</strong>
                                    </td>
                                    <td class="text-end text-success fw-bold">
                                        {{ number_format($collecte['montant_collecte'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-end text-warning fw-bold">
                                        {{ number_format($collecte['montant_deja_destocke'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-end text-success fw-bold">
                                        {{ number_format($collecte['solde_disponible'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>
                                        <input type="hidden" name="postes[{{ $index }}][id]" value="{{ $collecte['id'] }}" class="poste-id-input">
                                        <input type="number"
                                               name="postes[{{ $index }}][montant_destocke]"
                                               class="form-control form-control-sm montant-input"
                                               step="0.01"
                                               min="0"
                                               max="{{ $collecte['solde_disponible'] }}"
                                               value="0"
                                               data-post-id="{{ $collecte['id'] }}"
                                               data-index="{{ $index }}"
                                               disabled
                                               placeholder="0">
                                        <small class="text-danger d-none montant-error" data-post-id="{{ $collecte['id'] }}">
                                            Montant invalide
                                        </small>
                                    </td>
                                </tr>
                                @else
                                <tr class="table-secondary">
                                    <td></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $collecte['type'] == 'poste' ? 'Poste' : 'Bureau' }}</span>
                                        <strong class="text-muted">{{ $collecte['nom'] }}</strong>
                                    </td>
                                    <td class="text-end text-muted">
                                        {{ number_format($collecte['montant_collecte'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-end text-muted">
                                        {{ number_format($collecte['montant_deja_destocke'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-end text-muted">
                                        {{ number_format($collecte['solde_disponible'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">Solde épuisé</span>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">TOTAL À RÈGLEMENT:</th>
                                <th class="text-end">
                                    <span id="totalDestockage" class="fw-bold text-danger fs-5">0 FCFA</span>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p class="mb-0">Aucun fonds disponible pour cette période.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('pcs.destockages.collecte', ['programme' => $programme, 'mois' => $mois, 'annee' => $annee]) }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-danger btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-check-circle me-1"></i>Enregistrer le Règlement
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Gérer la sélection de tous les postes
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.poste-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            const index = cb.dataset.index;
            const montantInput = document.querySelector(`input[name*="[montant_destocke]"][data-index="${index}"]`);
            if (montantInput) {
                montantInput.disabled = !this.checked;
                if (this.checked && parseFloat(montantInput.value) == 0) {
                    montantInput.value = montantInput.max;
                    montantInput.dispatchEvent(new Event('input'));
                } else if (!this.checked) {
                    montantInput.value = 0;
                    montantInput.dispatchEvent(new Event('input'));
                }
            }
        });
        updateTotal();
        checkFormValidity();
    });

    // Gérer la sélection individuelle
    document.querySelectorAll('.poste-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const index = this.dataset.index;
            const montantInput = document.querySelector(`input[name*="[montant_destocke]"][data-index="${index}"]`);
            if (montantInput) {
                montantInput.disabled = !this.checked;
                if (this.checked && parseFloat(montantInput.value) == 0) {
                    montantInput.value = montantInput.max;
                    montantInput.dispatchEvent(new Event('input'));
                } else if (!this.checked) {
                    montantInput.value = 0;
                    montantInput.dispatchEvent(new Event('input'));
                }
            }
            updateTotal();
            checkFormValidity();
        });
    });

    // Gérer la saisie des montants
    document.querySelectorAll('.montant-input').forEach(input => {
        input.addEventListener('input', function() {
            const max = parseFloat(this.max);
            const value = parseFloat(this.value) || 0;

            if (value > max) {
                this.value = max;
                this.classList.add('is-invalid');
                const errorMsg = document.querySelector(`.montant-error[data-post-id="${this.dataset.postId}"]`);
                if (errorMsg) errorMsg.classList.remove('d-none');
            } else {
                this.classList.remove('is-invalid');
                const errorMsg = document.querySelector(`.montant-error[data-post-id="${this.dataset.postId}"]`);
                if (errorMsg) errorMsg.classList.add('d-none');
            }

            updateTotal();
            checkFormValidity();
        });
    });

    // Calculer le total
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.poste-checkbox:checked').forEach(checkbox => {
            const index = checkbox.dataset.index;
            const montantInput = document.querySelector(`input[name*="[montant_destocke]"][data-index="${index}"]`);
            if (montantInput && !montantInput.disabled) {
                total += parseFloat(montantInput.value) || 0;
            }
        });

        const totalElement = document.getElementById('totalDestockage');
        if (totalElement) {
            totalElement.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
        }
    }

    // Vérifier la validité du formulaire
    function checkFormValidity() {
        const checkedBoxes = document.querySelectorAll('.poste-checkbox:checked');
        let isValid = false;

        if (checkedBoxes.length > 0) {
            isValid = true;
            checkedBoxes.forEach(checkbox => {
                const index = checkbox.dataset.index;
                const montantInput = document.querySelector(`input[name*="[montant_destocke]"][data-index="${index}"]`);
                if (montantInput) {
                    const value = parseFloat(montantInput.value) || 0;
                    if (value <= 0 || value > parseFloat(montantInput.max)) {
                        isValid = false;
                        montantInput.classList.add('is-invalid');
                    }
                }
            });
        }

        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = !isValid;
        }
    }

    // Recalculer les totaux lors du changement de période
    document.getElementById('programmeSelect')?.addEventListener('change', function() {
        window.location.href = '{{ route("pcs.destockages.create") }}?programme=' + this.value + '&mois=' + document.getElementById('moisSelect').value + '&annee=' + document.getElementById('anneeSelect').value;
    });

    document.getElementById('moisSelect')?.addEventListener('change', function() {
        window.location.href = '{{ route("pcs.destockages.create") }}?programme=' + document.getElementById('programmeSelect').value + '&mois=' + this.value + '&annee=' + document.getElementById('anneeSelect').value;
    });

    document.getElementById('anneeSelect')?.addEventListener('change', function() {
        window.location.href = '{{ route("pcs.destockages.create") }}?programme=' + document.getElementById('programmeSelect').value + '&mois=' + document.getElementById('moisSelect').value + '&annee=' + this.value;
    });

    // Validation du formulaire
    document.getElementById('destockageForm')?.addEventListener('submit', function(e) {
        // Ne garder que les postes sélectionnés avec leurs montants
        const checkedBoxes = document.querySelectorAll('.poste-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un poste.');
            return false;
        }

        // Supprimer les inputs cachés des postes non sélectionnés
        document.querySelectorAll('.poste-id-input').forEach(input => {
            const index = input.name.match(/\[(\d+)\]/)[1];
            const checkbox = document.querySelector(`.poste-checkbox[data-index="${index}"]`);
            if (!checkbox || !checkbox.checked) {
                // Supprimer les inputs de ce poste non sélectionné
                const row = input.closest('tr');
                if (row) {
                    row.querySelectorAll('input').forEach(inp => {
                        if (inp.name.includes(`[${index}]`)) {
                            inp.remove();
                        }
                    });
                }
            }
        });

        let isValid = true;
        let total = 0;
        checkedBoxes.forEach(checkbox => {
            const index = checkbox.dataset.index;
            const montantInput = document.querySelector(`input[name*="[montant_destocke]"][data-index="${index}"]`);
            if (montantInput) {
                const value = parseFloat(montantInput.value) || 0;
                const max = parseFloat(montantInput.max);
                if (value <= 0 || value > max) {
                    isValid = false;
                    montantInput.classList.add('is-invalid');
                } else {
                    total += value;
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez vérifier les montants saisis. Certains montants sont invalides.');
            return false;
        }

        if (total <= 0) {
            e.preventDefault();
            alert('Le montant total à règlement doit être supérieur à zéro.');
            return false;
        }

        if (!confirm(`Êtes-vous sûr de vouloir créer ce règlement pour un montant total de ${new Intl.NumberFormat('fr-FR').format(total)} FCFA ?`)) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush
@endsection

