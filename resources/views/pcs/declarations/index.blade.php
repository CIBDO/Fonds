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
                <span class="badge bg-white text-danger">{{ $declarations->total() }} période(s) · {{ $totalDeclarations ?? 0 }} déclarations</span>
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
                            <th colspan="2" class="text-center bg-success text-white"><i class="fas fa-globe"></i> UEMOA</th>
                            <th colspan="2" class="text-center bg-warning"><i class="fas fa-globe"></i> AES</th>
                            <th class="text-center"><i class="fas fa-flag"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-user"></i> Saisi par</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th class="text-end small bg-success-subtle">Recouvrement</th>
                            <th class="text-end small bg-success-subtle">Reversement</th>
                            <th class="text-end small bg-warning-subtle">Recouvrement</th>
                            <th class="text-end small bg-warning-subtle">Reversement</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($declarations as $groupe)
                            @php
                                $declUemoa = $groupe->firstWhere('programme', 'UEMOA');
                                $declAes = $groupe->firstWhere('programme', 'AES');
                                $premierDecl = $groupe->first();
                            @endphp
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::create()->month($premierDecl->mois)->locale('fr')->translatedFormat('F') }}</strong> {{ $premierDecl->annee }}
                            </td>
                            <td>
                                @if($premierDecl->poste_id)
                                    <span class="badge bg-primary">{{ $premierDecl->poste->nom }}</span>
                                @else
                                    <span class="badge bg-info">{{ $premierDecl->bureauDouane->libelle }}</span>
                                @endif
                            </td>

                            {{-- UEMOA --}}
                            <td class="text-end">
                                @if($declUemoa)
                                    <strong>{{ number_format($declUemoa->montant_recouvrement, 0, ',', ' ') }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($declUemoa)
                                    <strong>{{ number_format($declUemoa->montant_reversement, 0, ',', ' ') }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- AES --}}
                            <td class="text-end">
                                @if($declAes)
                                    <strong>{{ number_format($declAes->montant_recouvrement, 0, ',', ' ') }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($declAes)
                                    <strong>{{ number_format($declAes->montant_reversement, 0, ',', ' ') }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @php
                                    $statuts = $groupe->pluck('statut')->unique();
                                    $statutPrincipal = $statuts->contains('valide') ? 'valide' :
                                                      ($statuts->contains('soumis') ? 'soumis' :
                                                      ($statuts->contains('rejete') ? 'rejete' : 'brouillon'));
                                @endphp
                                @switch($statutPrincipal)
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
                                <small class="text-muted">{{ $premierDecl->saisiPar->name }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    @if($declUemoa)
                                        <a href="{{ route('pcs.declarations.show', $declUemoa) }}"
                                           class="btn btn-outline-success btn-sm"
                                           data-bs-toggle="tooltip"
                                           title="UEMOA">
                                            <i class="fas fa-eye"></i> U
                                        </a>
                                    @endif
                                    @if($declAes)
                                        <a href="{{ route('pcs.declarations.show', $declAes) }}"
                                           class="btn btn-outline-warning btn-sm"
                                           data-bs-toggle="tooltip"
                                           title="AES">
                                            <i class="fas fa-eye"></i> A
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Affichage de <strong>{{ $declarations->firstItem() ?? 0 }}</strong> à <strong>{{ $declarations->lastItem() ?? 0 }}</strong>
                    sur <strong>{{ $declarations->total() }}</strong> période(s)
                    <span class="text-muted-light">({{ $totalDeclarations ?? 0 }} déclarations individuelles)</span>
                </div>
                <div>
                    @if ($declarations->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                {{-- Bouton Précédent --}}
                                @if ($declarations->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $declarations->previousPageUrl() }}" rel="prev">« Précédent</a>
                                    </li>
                                @endif

                                {{-- Numéros de page --}}
                                @foreach ($declarations->getUrlRange(1, $declarations->lastPage()) as $page => $url)
                                    @if ($page == $declarations->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Bouton Suivant --}}
                                @if ($declarations->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $declarations->nextPageUrl() }}" rel="next">Suivant »</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Suivant »</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
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

