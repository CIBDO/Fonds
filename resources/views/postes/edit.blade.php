@foreach($postes as $poste)
<div class="modal fade" id="editPosteModal{{ $poste->id }}" tabindex="-1" aria-labelledby="editPosteModalLabel{{ $poste->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPosteModalLabel{{ $poste->id }}">
                    <i class="icon-base ti tabler-edit me-2"></i>Modifier — {{ $poste->nom }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('postes.update', $poste) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nom{{ $poste->id }}" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" id="nom{{ $poste->id }}" name="nom" class="form-control" value="{{ old('nom', $poste->nom) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="type{{ $poste->id }}" class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="type{{ $poste->id }}" class="form-select"
                                data-poste-type="#editParentWrap{{ $poste->id }}" required>
                            <option value="superieur" {{ old('type', $poste->type) === 'superieur' ? 'selected' : '' }}>Poste supérieur</option>
                            <option value="rp" {{ old('type', $poste->type) === 'rp' ? 'selected' : '' }}>Recette-Perception (RP)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="editParentWrap{{ $poste->id }}" @if(($poste->type ?? 'superieur') !== 'rp') style="display:none;" @endif>
                        <label for="parent_id{{ $poste->id }}" class="form-label">Poste supérieur <span class="text-danger">*</span></label>
                        <select name="parent_id" id="parent_id{{ $poste->id }}" class="form-select">
                            <option value="">Sélectionner…</option>
                            @foreach($postesSuperieurs as $superieur)
                                @if((int) $superieur->id !== (int) $poste->id)
                                    <option value="{{ $superieur->id }}" {{ (string) old('parent_id', $poste->parent_id) === (string) $superieur->id ? 'selected' : '' }}>
                                        {{ $superieur->nom }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="intitule_officiel{{ $poste->id }}" class="form-label">Intitulé officiel (en-tête)</label>
                        <input type="text" name="intitule_officiel" id="intitule_officiel{{ $poste->id }}" class="form-control"
                               value="{{ old('intitule_officiel', $poste->intitule_officiel) }}">
                    </div>
                    <div class="mb-3">
                        <label for="region{{ $poste->id }}" class="form-label">Région (sous-titre)</label>
                        <input type="text" name="region" id="region{{ $poste->id }}" class="form-control"
                               value="{{ old('region', $poste->region) }}">
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-device-floppy me-1"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
