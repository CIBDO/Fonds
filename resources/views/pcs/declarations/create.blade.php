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
                    <p class="text-muted mb-0"> - {{ $poste->nom }} - Programme UEMOA & AES</p>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.declarations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('pcs.declarations.store') }}" method="POST" id="declarationForm">
        @csrf

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
                                    <i class="fas fa-globe"></i> Programme UEMOA
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
                                <i class="fas fa-globe"></i> Programme AES
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
                                        <i class="fas fa-globe"></i> Programme UEMOA
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
                                    <i class="fas fa-globe"></i> Programme AES
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
                                    <i class="fas fa-globe"></i> Programme UEMOA
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
                                    <small class="text-muted">Montant reversé à l'ACCT</small>
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
                                <i class="fas fa-globe"></i> Programme AES
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
                                <small class="text-muted">Montant reversé à l'ACCT</small>
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
                        <i class="fas fa-paper-plane me-1"></i>Soumettre pour Validation
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('declarationForm').addEventListener('submit', function(e) {
        const action = e.submitter.value;
        if (action === 'soumettre') {
            if (!confirm('Êtes-vous sûr de vouloir soumettre cette déclaration ? Elle sera envoyée pour validation et ne pourra plus être modifiée.')) {
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

