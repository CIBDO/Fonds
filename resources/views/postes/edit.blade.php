@foreach($postes as $poste)
<div class="modal fade" id="editPosteModal{{ $poste->id }}" tabindex="-1" aria-labelledby="editPosteModalLabel{{ $poste->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPosteModalLabel{{ $poste->id }}">
                    <i class="icon-base ti tabler-edit me-2"></i>Modifier le Poste
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('postes.update', $poste) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nom{{ $poste->id }}" class="form-label">Libellé</label>
                        <input type="text" id="nom{{ $poste->id }}" name="nom" class="form-control" value="{{ $poste->nom }}" required>
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
