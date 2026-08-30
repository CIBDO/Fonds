@extends('layouts.master')

@section('title', 'Nouveau Destockage PCS')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('pcs.destockages.collecte', ['programme' => $programme, 'mois' => $mois, 'annee' => $annee]) }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<form action="{{ route('pcs.destockages.store') }}" method="POST" id="destockageForm">
    @csrf

    <x-vuexy.card title="Informations Générales" icon="tabler-info-circle" class="mb-4">
        <div class="row g-3">
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
                <input type="date" name="date_destockage" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Observation</label>
                <textarea name="observation" class="form-control" rows="2" placeholder="Observations éventuelles..."></textarea>
            </div>
        </div>
    </x-vuexy.card>

    <x-vuexy.card title="Sélection des Postes" icon="tabler-list-check" class="mb-4">
        <p class="text-body-secondary small mb-3">Cochez les postes à inclure dans ce règlement</p>

        @if(count($collectesParPoste) > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th><i class="icon-base ti tabler-building me-1"></i>Entité</th>
                        <th class="text-end"><i class="icon-base ti tabler-arrow-up me-1"></i>Collecté</th>
                        <th class="text-end"><i class="icon-base ti tabler-arrow-down me-1"></i>Déjà Règlement</th>
                        <th class="text-end"><i class="icon-base ti tabler-scale me-1"></i>Disponible</th>
                        <th class="text-end"><i class="icon-base ti tabler-currency-franc me-1"></i>Montant à Règlement</th>
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
                                <span class="badge bg-label-{{ $collecte['type'] == 'poste' ? 'primary' : 'info' }} me-1">
                                    {{ $collecte['type'] == 'poste' ? 'Poste' : 'Bureau' }}
                                </span>
                                <strong>{{ $collecte['nom'] }}</strong>
                            </td>
                            <td class="text-end text-success fw-medium">
                                {{ number_format($collecte['montant_collecte'], 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end text-warning fw-medium">
                                {{ number_format($collecte['montant_deja_destocke'], 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end text-success fw-medium">
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
                                <span class="badge bg-label-secondary me-1">{{ $collecte['type'] == 'poste' ? 'Poste' : 'Bureau' }}</span>
                                <strong class="text-body-secondary">{{ $collecte['nom'] }}</strong>
                            </td>
                            <td class="text-end text-body-secondary">
                                {{ number_format($collecte['montant_collecte'], 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end text-body-secondary">
                                {{ number_format($collecte['montant_deja_destocke'], 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end text-body-secondary">
                                {{ number_format($collecte['solde_disponible'], 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-secondary">Solde épuisé</span>
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
        <x-vuexy.alert type="warning">
            Aucun fonds disponible pour cette période.
        </x-vuexy.alert>
        @endif
    </x-vuexy.card>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
        <a href="{{ route('pcs.destockages.collecte', ['programme' => $programme, 'mois' => $mois, 'annee' => $annee]) }}" class="btn btn-label-secondary btn-lg">
            <i class="icon-base ti tabler-x me-1"></i>Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
            <i class="icon-base ti tabler-circle-check me-1"></i>Enregistrer le Règlement
        </button>
    </div>
</form>

@push('scripts')
<script>
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

    document.getElementById('programmeSelect')?.addEventListener('change', function() {
        window.location.href = '{{ route("pcs.destockages.create") }}?programme=' + this.value + '&mois=' + document.getElementById('moisSelect').value + '&annee=' + document.getElementById('anneeSelect').value;
    });

    document.getElementById('moisSelect')?.addEventListener('change', function() {
        window.location.href = '{{ route("pcs.destockages.create") }}?programme=' + document.getElementById('programmeSelect').value + '&mois=' + this.value + '&annee=' + document.getElementById('anneeSelect').value;
    });

    document.getElementById('anneeSelect')?.addEventListener('change', function() {
        window.location.href = '{{ route("pcs.destockages.create") }}?programme=' + document.getElementById('programmeSelect').value + '&mois=' + document.getElementById('moisSelect').value + '&annee=' + this.value;
    });

    document.getElementById('destockageForm')?.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.poste-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un poste.');
            return false;
        }

        document.querySelectorAll('.poste-id-input').forEach(input => {
            const index = input.name.match(/\[(\d+)\]/)[1];
            const checkbox = document.querySelector(`.poste-checkbox[data-index="${index}"]`);
            if (!checkbox || !checkbox.checked) {
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
