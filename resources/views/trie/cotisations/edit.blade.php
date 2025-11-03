@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-warning">
                        <i class="fas fa-edit me-2"></i>Modifier la Cotisation TRIE
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('trie.cotisations.show', $cotisation) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('trie.cotisations.update', $cotisation) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Informations générales (Non modifiables) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de la Cotisation</h5>
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-3">
                        <label class="fw-bold text-muted">Période:</label>
                        <p class="fs-5"><strong>{{ $cotisation->nom_mois }} {{ $cotisation->annee }}</strong></p>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold text-muted">Poste:</label>
                        <p class="fs-5"><strong>{{ $cotisation->poste->nom }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-muted">Bureau:</label>
                        <p class="fs-5">
                            <span class="text-primary">{{ $cotisation->bureauTrie->code_bureau }}</span> - 
                            <span class="text-muted">{{ $cotisation->bureauTrie->nom_bureau }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Montants (Modifiables) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-coins me-2"></i>Montants de la Cotisation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Montant Cotisation Courante <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" 
                                   name="montant_cotisation_courante" 
                                   class="form-control form-control-lg" 
                                   step="0.01" 
                                   min="0"
                                   value="{{ old('montant_cotisation_courante', $cotisation->montant_cotisation_courante) }}"
                                   required>
                            <span class="input-group-text">FCFA</span>
                        </div>
                        <small class="text-muted">Montant de la cotisation du mois courant</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Montant Apurement (Rattrapage)</label>
                        <div class="input-group">
                            <input type="number" 
                                   name="montant_apurement" 
                                   class="form-control form-control-lg" 
                                   step="0.01" 
                                   min="0"
                                   value="{{ old('montant_apurement', $cotisation->montant_apurement) }}">
                            <span class="input-group-text">FCFA</span>
                        </div>
                        <small class="text-muted">Montant de rattrapage des périodes antérieures</small>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Détail Apurement</label>
                        <input type="text" 
                               name="detail_apurement" 
                               class="form-control" 
                               placeholder="Ex: Rattrapage janvier-mars 2024"
                               value="{{ old('detail_apurement', $cotisation->detail_apurement) }}">
                        <small class="text-muted">Précisez les périodes ou détails du rattrapage</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations de Paiement (Modifiables) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Informations de Paiement</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Mode de Paiement</label>
                        <select name="mode_paiement" class="form-select">
                            <option value="">-- Sélectionnez --</option>
                            <option value="cheque" {{ old('mode_paiement', $cotisation->mode_paiement) == 'cheque' ? 'selected' : '' }}>Chèque</option>
                            <option value="virement" {{ old('mode_paiement', $cotisation->mode_paiement) == 'virement' ? 'selected' : '' }}>Virement</option>
                            <option value="especes" {{ old('mode_paiement', $cotisation->mode_paiement) == 'especes' ? 'selected' : '' }}>Espèces</option>
                            <option value="autre" {{ old('mode_paiement', $cotisation->mode_paiement) == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Référence de Paiement</label>
                        <input type="text" 
                               name="reference_paiement" 
                               class="form-control" 
                               placeholder="Ex: CHQ BDM n°8903232"
                               value="{{ old('reference_paiement', $cotisation->reference_paiement) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date de Paiement</label>
                        <input type="date" 
                               name="date_paiement" 
                               class="form-control"
                               value="{{ old('date_paiement', $cotisation->date_paiement?->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Observation</label>
                        <textarea name="observation" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Observations éventuelles...">{{ old('observation', $cotisation->observation) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('trie.cotisations.show', $cotisation) }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-1"></i>Enregistrer les Modifications
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

