@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-edit me-2"></i>Modifier la Déclaration PCS
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.declarations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('pcs.declarations.update', $declaration) }}" method="POST" id="declarationForm">
        @csrf
        @method('PUT')

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
                                <option value="{{ $i }}" {{ old('mois', $declaration->mois) == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->locale('fr')->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                        <select name="annee" class="form-select" required>
                            @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                <option value="{{ $i }}" {{ old('annee', $declaration->annee) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programme et Montants -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2"></i>
                    @if($declaration->poste_id)
                        {{ $declaration->poste->nom }}
                    @else
                        {{ $declaration->bureauDouane->libelle }}
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <input type="hidden" name="programme" value="{{ $declaration->programme }}">

                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Programme :</strong> {{ $declaration->programme }}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Montant Recouvré (FCFA)</label>
                            <input type="number"
                                   name="montant_recouvrement"
                                   class="form-control form-control-lg"
                                   step="0.01"
                                   min="0"
                                   value="{{ old('montant_recouvrement', $declaration->montant_recouvrement) }}"
                                   placeholder="0">
                            <small class="text-muted">Recettes perçues ce mois</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Montant Reversé (FCFA)</label>
                            <input type="number"
                                   name="montant_reversement"
                                   class="form-control form-control-lg"
                                   step="0.01"
                                   min="0"
                                   value="{{ old('montant_reversement', $declaration->montant_reversement) }}"
                                   placeholder="0">
                            <small class="text-muted">Montant reversé à l'ACCT</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Référence</label>
                    <input type="text"
                           name="reference"
                           class="form-control form-control-lg"
                           value="{{ old('reference', $declaration->reference) }}"
                           placeholder="Référence...">
                </div>

                <div>
                    <label class="form-label fw-bold">Observation</label>
                    <textarea name="observation"
                              class="form-control"
                              rows="3"
                              placeholder="Observations éventuelles...">{{ old('observation', $declaration->observation) }}</textarea>
                </div>
            </div>
        </div>

        @if($declaration->statut == 'rejete' && $declaration->motif_rejet)
        <!-- Motif du rejet -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Motif du Rejet</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-0">
                    <strong>Raison du rejet :</strong><br>
                    {{ $declaration->motif_rejet }}
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
                        <i class="fas fa-check-circle me-1"></i>Valider et Envoyer
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
            if (!confirm('Êtes-vous sûr de vouloir valider cette déclaration ? Elle sera automatiquement validée et envoyée à l\'ACCT.')) {
                e.preventDefault();
            }
        }
    });
</script>
@endpush
@endsection

