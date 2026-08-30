@extends('layouts.master')

@section('title', 'Nouveau Bureau de Douane')

@section('content')
<x-vuexy.page-header title="Nouveau Bureau de Douane" subtitle="Création d'un bureau de douane PCS">
    <x-slot:actions>
        <a href="{{ route('pcs.bureaux.index') }}" class="btn btn-label-secondary btn-sm">
            <i class="ti tabler-arrow-left me-1"></i>Retour
        </a>
    </x-slot:actions>
</x-vuexy.page-header>

<x-vuexy.card title="Informations du Bureau" icon="tabler-building">
    <form action="{{ route('pcs.bureaux.store') }}" method="POST">
        @csrf

        <input type="hidden" name="poste_rgd_id" value="{{ $posteRgd->id }}">

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="code" class="form-label fw-bold">
                    Code Bureau <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control @error('code') is-invalid @enderror"
                       id="code"
                       name="code"
                       value="{{ old('code') }}"
                       placeholder="Ex: 200"
                       required>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Code unique du bureau</small>
            </div>

            <div class="col-md-8 mb-3">
                <label for="libelle" class="form-label fw-bold">
                    Libellé <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control @error('libelle') is-invalid @enderror"
                       id="libelle"
                       name="libelle"
                       value="{{ old('libelle') }}"
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
                   value="{{ $posteRgd->nom }}"
                   disabled>
            <small class="text-muted">Tous les bureaux sont rattachés à la RGD</small>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input"
                       type="checkbox"
                       id="actif"
                       name="actif"
                       checked>
                <label class="form-check-label fw-bold" for="actif">
                    Bureau actif
                </label>
                <small class="d-block text-muted">Cochez pour activer le bureau immédiatement</small>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('pcs.bureaux.index') }}" class="btn btn-label-secondary">
                <i class="ti tabler-x me-1"></i>Annuler
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ti tabler-device-floppy me-1"></i>Enregistrer
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection
