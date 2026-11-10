<div class="modal fade" id="addPosteModal" tabindex="-1" aria-labelledby="addPosteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPosteModalLabel">
                    <i class="icon-base ti tabler-plus me-2"></i>Ajouter un Poste / RP
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('postes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select" data-poste-type="#addParentWrap" required>
                            <option value="superieur" {{ old('type', 'superieur') === 'superieur' ? 'selected' : '' }}>Poste supérieur (TR) — menu complet</option>
                            <option value="rp" {{ old('type') === 'rp' ? 'selected' : '' }}>Recette-Perception (RP) — menu FNL uniquement</option>
                        </select>
                        <small class="text-body-secondary">Une RP doit être de type « Recette-Perception » et rattachée à son poste supérieur.</small>
                    </div>
                    <div class="mb-3" id="addParentWrap" style="display:none;">
                        <label for="parent_id" class="form-label">Poste supérieur <span class="text-danger">*</span></label>
                        <select name="parent_id" id="parent_id" class="form-select">
                            <option value="">Sélectionner…</option>
                            @foreach($postesSuperieurs as $superieur)
                                <option value="{{ $superieur->id }}" {{ (string) old('parent_id') === (string) $superieur->id ? 'selected' : '' }}>
                                    {{ $superieur->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="intitule_officiel" class="form-label">Intitulé officiel (en-tête)</label>
                        <input type="text" name="intitule_officiel" id="intitule_officiel" class="form-control"
                               value="{{ old('intitule_officiel') }}"
                               placeholder="Ex. TRESORERIE REGIONALE DE SEGOU">
                    </div>
                    <div class="mb-3">
                        <label for="region" class="form-label">Région (sous-titre)</label>
                        <input type="text" name="region" id="region" class="form-control"
                               value="{{ old('region') }}"
                               placeholder="Ex. Région de Ségou">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-plus me-1"></i>Ajouter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
