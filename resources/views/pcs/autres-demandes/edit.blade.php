@extends('layouts.master')

@section('title', 'Modifier Autre Demande')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<x-vuexy.card title="Informations de la Demande" icon="tabler-file">
    @if($demande->statut == 'soumis')
    <x-vuexy.alert type="warning" class="mb-4">
        <strong>Attention :</strong> Cette demande a déjà été soumise. Vous pouvez la modifier avant sa validation.
        Les modifications réinitialiseront le statut de la demande selon votre action.
    </x-vuexy.alert>
    @endif

    <form action="{{ route('pcs.autres-demandes.update', $demande) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="designation" class="form-label fw-bold">Désignation <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control @error('designation') is-invalid @enderror"
                   name="designation"
                   value="{{ old('designation', $demande->designation) }}"
                   required>
            @error('designation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Montant (FCFA) <span class="text-danger">*</span></label>
                <input type="number"
                       class="form-control form-control-lg"
                       name="montant"
                       value="{{ old('montant', $demande->montant) }}"
                       step="0.01"
                       min="0"
                       required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Date de la demande <span class="text-danger">*</span></label>
                <input type="date"
                       class="form-control"
                       name="date_demande"
                       value="{{ old('date_demande', $demande->date_demande->format('Y-m-d')) }}"
                       required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
            <select name="annee" class="form-select" required>
                @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                    <option value="{{ $i }}" {{ old('annee', $demande->annee) == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Observation</label>
            <textarea name="observation" class="form-control" rows="4">{{ old('observation', $demande->observation) }}</textarea>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-label-secondary btn-lg">
                <i class="icon-base ti tabler-x me-1"></i>Annuler
            </a>
            <button type="submit" name="action" value="soumettre" class="btn btn-primary btn-lg">
                <i class="icon-base ti tabler-send me-1"></i>Soumettre
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection
