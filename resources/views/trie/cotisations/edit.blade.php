@extends('layouts.master')

@section('title', 'Modifier Cotisation TRIE')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('trie.cotisations.show', $cotisation) }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<form action="{{ route('trie.cotisations.update', $cotisation) }}" method="POST">
    @csrf
    @method('PUT')

    <x-vuexy.card title="Informations de la cotisation" icon="tabler-info-circle" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="text-body-secondary small">Période</label>
                <div class="fw-medium">{{ $cotisation->nom_mois }} {{ $cotisation->annee }}</div>
            </div>
            <div class="col-md-4">
                <label class="text-body-secondary small">Poste</label>
                <div class="fw-medium">{{ $cotisation->poste->nom }}</div>
            </div>
            <div class="col-md-4">
                <label class="text-body-secondary small">Bureau</label>
                <div class="fw-medium">
                    <span class="text-primary">{{ $cotisation->bureauTrie->code_bureau }}</span>
                    <span class="text-body-secondary"> — {{ $cotisation->bureauTrie->nom_bureau }}</span>
                </div>
            </div>
        </div>
    </x-vuexy.card>

    <x-vuexy.card title="Montants de la cotisation" icon="tabler-coins" class="mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Montant cotisation courante <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number"
                           name="montant_cotisation_courante"
                           class="form-control"
                           step="0.01"
                           min="0"
                           value="{{ old('montant_cotisation_courante', $cotisation->montant_cotisation_courante) }}"
                           required>
                    <span class="input-group-text">FCFA</span>
                </div>
                <small class="text-body-secondary">Montant de la cotisation du mois courant</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Montant apurement (rattrapage)</label>
                <div class="input-group">
                    <input type="number"
                           name="montant_apurement"
                           class="form-control"
                           step="0.01"
                           min="0"
                           value="{{ old('montant_apurement', $cotisation->montant_apurement) }}">
                    <span class="input-group-text">FCFA</span>
                </div>
                <small class="text-body-secondary">Montant de rattrapage des périodes antérieures</small>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Détail apurement</label>
                <input type="text"
                       name="detail_apurement"
                       class="form-control"
                       placeholder="Ex: Rattrapage janvier-mars 2024"
                       value="{{ old('detail_apurement', $cotisation->detail_apurement) }}">
            </div>
        </div>
    </x-vuexy.card>

    <x-vuexy.card title="Informations de paiement" icon="tabler-credit-card" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Mode de paiement</label>
                <select name="mode_paiement" class="form-select">
                    <option value="">-- Sélectionnez --</option>
                    <option value="cheque" {{ old('mode_paiement', $cotisation->mode_paiement) == 'cheque' ? 'selected' : '' }}>Chèque</option>
                    <option value="virement" {{ old('mode_paiement', $cotisation->mode_paiement) == 'virement' ? 'selected' : '' }}>Virement</option>
                    <option value="especes" {{ old('mode_paiement', $cotisation->mode_paiement) == 'especes' ? 'selected' : '' }}>Espèces</option>
                    <option value="autre" {{ old('mode_paiement', $cotisation->mode_paiement) == 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Référence de paiement</label>
                <input type="text"
                       name="reference_paiement"
                       class="form-control"
                       placeholder="Ex: CHQ BDM n°8903232"
                       value="{{ old('reference_paiement', $cotisation->reference_paiement) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Date de paiement</label>
                <input type="date"
                       name="date_paiement"
                       class="form-control"
                       value="{{ old('date_paiement', $cotisation->date_paiement?->format('Y-m-d')) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Observation</label>
                <textarea name="observation"
                          class="form-control"
                          rows="3"
                          placeholder="Observations éventuelles...">{{ old('observation', $cotisation->observation) }}</textarea>
            </div>
        </div>
    </x-vuexy.card>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('trie.cotisations.show', $cotisation) }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-x me-1"></i>Annuler
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-device-floppy me-1"></i>Enregistrer
        </button>
    </div>
</form>
@endsection
