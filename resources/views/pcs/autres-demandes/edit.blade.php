@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title fw-bold text-danger">
                    <i class="fas fa-edit me-2"></i>Modifier la Demande
                </h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Informations de la Demande</h5>
                </div>

                <div class="card-body">
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
                            <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" name="action" value="brouillon" class="btn btn-outline-danger btn-lg">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                            <button type="submit" name="action" value="soumettre" class="btn btn-danger btn-lg">
                                <i class="fas fa-paper-plane me-1"></i>Soumettre
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

