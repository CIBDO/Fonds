@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-file-alt me-2"></i>Déclarations PCS
                    </h3>
                    {{-- <p class="text-muted mb-0">Programme de Consolidation des Statistiques UEMOA/AES</p> --}}
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('pcs.declarations.create') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-plus me-1"></i>Nouvelle Déclaration
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('pcs.declarations.pdf.recettes') }}?programme=UEMOA&annee={{ date('Y') }}">
                            <i class="fas fa-file-pdf text-danger"></i> État UEMOA
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('pcs.declarations.pdf.recettes') }}?programme=AES&annee={{ date('Y') }}">
                            <i class="fas fa-file-pdf text-danger"></i> État AES
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pcs.declarations.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Programme</label>
                    <select name="programme" class="form-select">
                        <option value="">Tous</option>
                        <option value="UEMOA" {{ request('programme') == 'UEMOA' ? 'selected' : '' }}>UEMOA</option>
                        <option value="AES" {{ request('programme') == 'AES' ? 'selected' : '' }}>AES</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Mois</label>
                    <select name="mois" class="form-select">
                        <option value="">Tous</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('mois') == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('fr')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Année</label>
                    <select name="annee" class="form-select">
                        <option value="">Toutes</option>
                        @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                            <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                        <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                        <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table des déclarations -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Déclarations</h5>
                <span class="badge bg-white text-danger">{{ $declarations->total() }} déclarations</span>
            </div>
        </div>

        <div class="card-body">
            @if($declarations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-calendar"></i> Période</th>
                            <th><i class="fas fa-building"></i> Entité</th>
                            <th><i class="fas fa-globe"></i> Programme</th>
                            <th class="text-end"><i class="fas fa-arrow-up"></i> Recouvrement</th>
                            <th class="text-end"><i class="fas fa-arrow-down"></i> Reversement</th>
                            <th class="text-center"><i class="fas fa-flag"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-user"></i> Saisi par</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($declarations as $decl)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::create()->month($decl->mois)->locale('fr')->translatedFormat('F') }}</strong> {{ $decl->annee }}
                            </td>
                            <td>
                                @if($decl->poste_id)
                                    <span class="badge bg-primary poste-badge">{{ $decl->poste->nom }}</span>
                                @else
                                    <span class="badge bg-info bureau-badge">{{ $decl->bureauDouane->libelle }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $decl->programme == 'UEMOA' ? 'success' : 'warning' }} programme-badge">
                                    {{ $decl->programme }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">{{ number_format($decl->montant_recouvrement, 0, ',', ' ') }} FCFA</td>
                            <td class="text-end fw-bold">{{ number_format($decl->montant_reversement, 0, ',', ' ') }} FCFA</td>
                            <td class="text-center">
                                @switch($decl->statut)
                                    @case('brouillon')
                                        <span class="badge bg-secondary"><i class="fas fa-pencil-alt"></i> Brouillon</span>
                                        @break
                                    @case('soumis')
                                        <span class="badge bg-primary"><i class="fas fa-paper-plane"></i> Soumis</span>
                                        @break
                                    @case('valide')
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> Validé</span>
                                        @break
                                    @case('rejete')
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Rejeté</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-center">
                                <small class="text-muted">{{ $decl->saisiPar->name }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('pcs.declarations.show', $decl) }}"
                                       class="btn btn-outline-info"
                                       data-bs-toggle="tooltip"
                                       title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(in_array($decl->statut, ['brouillon', 'rejete']) && $decl->saisi_par == auth()->id())
                                        <a href="{{ route('pcs.declarations.edit', $decl) }}"
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->peut_valider_pcs && $decl->statut == 'soumis')
                                        <button type="button"
                                                class="btn btn-outline-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#validerModal{{ $decl->id }}"
                                                title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejeterModal{{ $decl->id }}"
                                                title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Valider -->
                        @if(auth()->user()->peut_valider_pcs && $decl->statut == 'soumis')
                        <div class="modal fade" id="validerModal{{ $decl->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">Valider la Déclaration</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('pcs.declarations.valider', $decl) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <p>Êtes-vous sûr de vouloir valider cette déclaration ?</p>
                                            <ul class="list-unstyled">
                                                <li><strong>Période :</strong> {{ \Carbon\Carbon::create()->month($decl->mois)->translatedFormat('F') }} {{ $decl->annee }}</li>
                                                <li><strong>Programme :</strong> {{ $decl->programme }}</li>
                                                <li><strong>Montant :</strong> {{ number_format($decl->montant_recouvrement, 0, ',', ' ') }} FCFA</li>
                                            </ul>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-success">Valider</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Rejeter -->
                        <div class="modal fade" id="rejeterModal{{ $decl->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Rejeter la Déclaration</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('pcs.declarations.rejeter', $decl) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Motif du rejet <span class="text-danger">*</span></label>
                                                <textarea name="motif_rejet" class="form-control" rows="4" required
                                                          placeholder="Expliquez la raison du rejet..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-danger">Rejeter</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $declarations->links() }}
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Aucune déclaration trouvée. Cliquez sur "Nouvelle Déclaration" pour commencer.</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endpush
@endsection

