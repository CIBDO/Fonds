@extends('layouts.master')

@section('title', 'Modifier Déclaration PCS')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('pcs.declarations.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<form action="{{ route('pcs.declarations.update', $declaration) }}" method="POST" id="declarationForm">
    @csrf
    @method('PUT')

    <x-vuexy.card title="Période de Déclaration" icon="tabler-calendar" class="mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Mois <span class="text-danger">*</span></label>
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
                <label class="form-label">Année <span class="text-danger">*</span></label>
                <select name="annee" class="form-select" required>
                    @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                        <option value="{{ $i }}" {{ old('annee', $declaration->annee) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </x-vuexy.card>

    <x-vuexy.card title="{{ $declaration->poste_id ? $declaration->poste->nom : $declaration->bureauDouane->libelle }}" icon="tabler-building" class="mb-4">
        <input type="hidden" name="programme" value="{{ $declaration->programme }}">

        <div class="alert alert-info mb-4">
            <i class="icon-base ti tabler-info-circle me-2"></i>
            <strong>Programme :</strong>
            <span class="badge bg-label-{{ $declaration->programme === 'UEMOA' ? 'success' : 'warning' }} ms-1">{{ $declaration->programme }}</span>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Montant Recouvré (FCFA)</label>
                <input type="number"
                       name="montant_recouvrement"
                       class="form-control"
                       step="0.01"
                       min="0"
                       value="{{ old('montant_recouvrement', $declaration->montant_recouvrement) }}"
                       placeholder="0">
                <small class="text-body-secondary">Recettes perçues ce mois</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Montant Reversé (FCFA)</label>
                <input type="number"
                       name="montant_reversement"
                       class="form-control"
                       step="0.01"
                       min="0"
                       value="{{ old('montant_reversement', $declaration->montant_reversement) }}"
                       placeholder="0">
                <small class="text-body-secondary">Montant reversé à l'ACCT</small>
            </div>
            <div class="col-12">
                <label class="form-label">Référence</label>
                <input type="text"
                       name="reference"
                       class="form-control"
                       value="{{ old('reference', $declaration->reference) }}"
                       placeholder="Référence...">
            </div>
            <div class="col-12">
                <label class="form-label">Observation</label>
                <textarea name="observation"
                          class="form-control"
                          rows="3"
                          placeholder="Observations éventuelles...">{{ old('observation', $declaration->observation) }}</textarea>
            </div>
        </div>
    </x-vuexy.card>

    @if($declaration->statut == 'rejete' && $declaration->motif_rejet)
    <x-vuexy.card title="Motif du Rejet" icon="tabler-alert-triangle" class="mb-4">
        <div class="alert alert-warning mb-0">
            <strong>Raison du rejet :</strong><br>
            {{ $declaration->motif_rejet }}
        </div>
    </x-vuexy.card>
    @endif

    <div class="d-flex flex-wrap justify-content-end gap-2 mb-5">
        <a href="{{ route('pcs.declarations.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-x me-1"></i>Annuler
        </a>
        <button type="submit" name="action" value="soumettre" class="btn btn-primary">
            <i class="icon-base ti tabler-circle-check me-1"></i>Valider et Envoyer
        </button>
    </div>
</form>

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
