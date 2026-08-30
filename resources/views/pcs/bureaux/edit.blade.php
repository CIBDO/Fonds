@extends('layouts.master')

@section('title', 'Modifier le Bureau de Douane')

@section('content')
<x-vuexy.page-header title="Modifier le Bureau de Douane" subtitle="{{ $bureau->libelle }}">
    <x-slot:actions>
        <a href="{{ route('pcs.bureaux.index') }}" class="btn btn-label-secondary btn-sm">
            <i class="ti tabler-arrow-left me-1"></i>Retour
        </a>
    </x-slot:actions>
</x-vuexy.page-header>

<x-vuexy.card title="Informations du Bureau" icon="tabler-building">
    <form action="{{ route('pcs.bureaux.update', $bureau) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="code" class="form-label fw-bold">
                    Code Bureau <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control @error('code') is-invalid @enderror"
                       id="code"
                       name="code"
                       value="{{ old('code', $bureau->code) }}"
                       placeholder="Ex: 200"
                       required>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-8 mb-3">
                <label for="libelle" class="form-label fw-bold">
                    Libellé <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control @error('libelle') is-invalid @enderror"
                       id="libelle"
                       name="libelle"
                       value="{{ old('libelle', $bureau->libelle) }}"
                       placeholder="Ex: BUREAU 200"
                       required>
                @error('libelle')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Poste RGD</label>
            <input type="text"
                   class="form-control"
                   value="{{ $bureau->posteRgd->nom }}"
                   disabled>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input"
                       type="checkbox"
                       id="actif"
                       name="actif"
                       {{ old('actif', $bureau->actif) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="actif">
                    Bureau actif
                </label>
            </div>
        </div>

        <div class="alert alert-light border">
            <div class="row small">
                <div class="col-md-6">
                    <strong>Créé le :</strong> {{ $bureau->created_at->format('d/m/Y à H:i') }}
                </div>
                <div class="col-md-6">
                    <strong>Modifié le :</strong> {{ $bureau->updated_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('pcs.bureaux.index') }}" class="btn btn-label-secondary">
                <i class="ti tabler-x me-1"></i>Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ti tabler-device-floppy me-1"></i>Mettre à jour
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection
