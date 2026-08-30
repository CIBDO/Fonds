@extends('layouts.master')

@section('title', 'Gestion Bureaux TRIE')

@section('content')
<x-vuexy.page-header title="Bureaux TRIE - {{ $poste->nom }}" subtitle="Gestion des bureaux du poste">
    <x-slot:actions>
        <a href="{{ route('trie.bureaux.index') }}" class="btn btn-label-secondary btn-sm">
            <i class="ti tabler-arrow-left me-1"></i>Retour
        </a>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNouveauBureau">
            <i class="ti tabler-plus me-1"></i>Nouveau Bureau
        </button>
    </x-slot:actions>
</x-vuexy.page-header>

<x-vuexy.card title="Liste des Bureaux ({{ $bureaux->count() }})" icon="tabler-list">
    @if($bureaux->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Nom du Bureau</th>
                    <th>Description</th>
                    <th class="text-center">Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bureaux as $bureau)
                <tr>
                    <td><strong class="text-primary">{{ $bureau->code_bureau }}</strong></td>
                    <td>{{ $bureau->nom_bureau }}</td>
                    <td>
                        <small class="text-muted">
                            {{ $bureau->description ?? '-' }}
                        </small>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('trie.bureaux.toggle-status', $bureau) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-{{ $bureau->actif ? 'success' : 'secondary' }}">
                                <i class="ti tabler-{{ $bureau->actif ? 'circle-check' : 'circle-x' }}"></i>
                                {{ $bureau->actif ? 'Actif' : 'Inactif' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button type="button"
                                    class="btn btn-sm btn-outline-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalModifierBureau{{ $bureau->id }}"
                                    title="Modifier">
                                <i class="ti tabler-edit"></i>
                            </button>
                            <form action="{{ route('trie.bureaux.destroy', $bureau) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bureau ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="ti tabler-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <div class="modal fade" id="modalModifierBureau{{ $bureau->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('trie.bureaux.update', $bureau) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="ti tabler-edit me-2"></i>Modifier le Bureau
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Code Bureau <span class="text-danger">*</span></label>
                                        <input type="text" name="code_bureau" class="form-control" value="{{ $bureau->code_bureau }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nom du Bureau <span class="text-danger">*</span></label>
                                        <input type="text" name="nom_bureau" class="form-control" value="{{ $bureau->nom_bureau }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description</label>
                                        <textarea name="description" class="form-control" rows="2">{{ $bureau->description }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="actif" value="1" class="form-check-input" id="actif{{ $bureau->id }}" {{ $bureau->actif ? 'checked' : '' }}>
                                            <label class="form-check-label" for="actif{{ $bureau->id }}">
                                                Bureau actif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="ti tabler-device-floppy me-1"></i>Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <x-vuexy.alert type="info">
        Aucun bureau enregistré pour ce poste.
        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalNouveauBureau">
            <i class="ti tabler-plus me-1"></i>Créer le premier bureau
        </button>
    </x-vuexy.alert>
    @endif
</x-vuexy.card>

<div class="modal fade" id="modalNouveauBureau" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('trie.bureaux.store') }}" method="POST">
                @csrf
                <input type="hidden" name="poste_id" value="{{ $poste->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti tabler-plus me-2"></i>Nouveau Bureau TRIE
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Poste</label>
                        <input type="text" class="form-control" value="{{ $poste->nom }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Code Bureau <span class="text-danger">*</span></label>
                        <input type="text" name="code_bureau" class="form-control" placeholder="Ex: DIB001" required>
                        <small class="text-muted">Code unique identifiant le bureau</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom du Bureau <span class="text-danger">*</span></label>
                        <input type="text" name="nom_bureau" class="form-control" placeholder="Ex: Diboli" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Description optionnelle du bureau..."></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="actif" value="1" class="form-check-input" id="actifNew" checked>
                            <label class="form-check-label" for="actifNew">
                                Bureau actif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti tabler-device-floppy me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
