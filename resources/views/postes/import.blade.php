<div class="modal fade" id="importRpModal" tabindex="-1" aria-labelledby="importRpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importRpModalLabel">
                    <i class="icon-base ti tabler-file-spreadsheet me-2"></i>Importer des recettes-perceptions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="text-body-secondary small mb-3">
                   
                    <a href="{{ route('postes.import-rp.template') }}">Télécharger le modèle Excel</a>
                </p>
                <form action="{{ route('postes.import-rp') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="fichier_rp" class="form-label">Fichier Excel ou CSV <span class="text-danger">*</span></label>
                        <input type="file" name="fichier" id="fichier_rp" class="form-control @error('fichier') is-invalid @enderror"
                               accept=".xlsx,.xls,.csv" required>
                        @error('fichier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-body-secondary">Colonnes : Nom RP, Poste supérieur, Intitulé officiel, Région.</small>
                    </div>
                    <div class="mb-3">
                        <label for="import_parent_id" class="form-label">Poste supérieur par défaut</label>
                        <select name="parent_id" id="import_parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">Aucun — utiliser la colonne du fichier</option>
                            @foreach($postesSuperieurs as $superieur)
                                <option value="{{ $superieur->id }}" {{ (string) old('parent_id') === (string) $superieur->id ? 'selected' : '' }}>
                                    {{ $superieur->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-body-secondary">Utilisé seulement pour les lignes sans poste supérieur.</small>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-upload me-1"></i>Importer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
