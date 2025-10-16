@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-edit me-2"></i>Modifier le Bureau de Douane
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.bureaux.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Informations du Bureau</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('pcs.bureaux.update', $bureau) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Code -->
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

                            <!-- Libellé -->
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

                        <!-- Poste RGD -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Poste RGD</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $bureau->posteRgd->nom }}"
                                   disabled>
                        </div>

                        <!-- Actif -->
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

                        <!-- Informations système -->
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

                        <!-- Boutons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('pcs.bureaux.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save me-1"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

