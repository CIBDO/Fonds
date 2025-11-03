@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-primary">
                        <i class="fas fa-building me-2"></i>Bureaux TRIE - {{ $poste->nom }}
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('trie.bureaux.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNouveauBureau">
                    <i class="fas fa-plus me-1"></i>Nouveau Bureau
                </button>
            </div>
        </div>
    </div>

    <!-- Liste des bureaux -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Liste des Bureaux ({{ $bureaux->count() }})
            </h5>
        </div>
        <div class="card-body">
            @if($bureaux->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-hashtag"></i> Code</th>
                            <th><i class="fas fa-building"></i> Nom du Bureau</th>
                            <th><i class="fas fa-info-circle"></i> Description</th>
                            <th class="text-center"><i class="fas fa-toggle-on"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
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
                                        <i class="fas fa-{{ $bureau->actif ? 'check' : 'times' }}-circle"></i>
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
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('trie.bureaux.destroy', $bureau) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bureau ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Modifier Bureau -->
                        <div class="modal fade" id="modalModifierBureau{{ $bureau->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('trie.bureaux.update', $bureau) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title">
                                                <i class="fas fa-edit me-2"></i>Modifier le Bureau
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
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save me-1"></i>Enregistrer
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
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Aucun bureau enregistré pour ce poste.</p>
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalNouveauBureau">
                    <i class="fas fa-plus me-1"></i>Créer le premier bureau
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Nouveau Bureau -->
<div class="modal fade" id="modalNouveauBureau" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('trie.bureaux.store') }}" method="POST">
                @csrf
                <input type="hidden" name="poste_id" value="{{ $poste->id }}">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Nouveau Bureau TRIE
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

