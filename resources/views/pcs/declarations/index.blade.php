@extends('layouts.master')

@section('title', 'Déclarations PCS')

@section('content')
@if(!auth()->user()->hasRole('acct'))
<div class="d-flex justify-content-end mb-4">
    <div class="btn-group" role="group">
        <a href="{{ route('pcs.declarations.create') }}" class="btn btn-primary btn-sm">
            <i class="icon-base ti tabler-plus me-1"></i>Nouvelle Déclaration
        </a>
        <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
            <span class="visually-hidden">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @if(auth()->user()->poste_id && !in_array(auth()->user()->role, ['acct', 'admin']))
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalEtatConsolidePosteEmetteur">
                <i class="icon-base ti tabler-file-export text-success"></i> État Consolidé (Poste Émetteur)
            </a></li>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalEtatReferences">
                <i class="icon-base ti tabler-hash text-info"></i> État des références (Déclarations et Cotisations)
            </a></li>
            @endif
        </ul>
    </div>
</div>
@endif

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
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
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-search me-1"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
</x-vuexy.card>

<x-vuexy.card title="Liste des Déclarations" icon="tabler-list">
    <x-slot:header>
        <span class="badge bg-label-primary">{{ $declarations->total() }} période(s) · {{ $totalDeclarations ?? 0 }} déclarations</span>
    </x-slot:header>
            @if($declarations->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="icon-base ti tabler-calendar me-1"></i>Période</th>
                            <th><i class="icon-base ti tabler-building me-1"></i>Entité</th>
                            <th colspan="2" class="text-center bg-label-success">UEMOA</th>
                            <th colspan="2" class="text-center bg-label-warning">AES</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center" style="min-width: 140px;">Actions</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th class="text-end small bg-label-success">Recouvrement</th>
                            <th class="text-end small bg-label-success">Reversement</th>
                            <th class="text-end small bg-label-warning">Recouvrement</th>
                            <th class="text-end small bg-label-warning">Reversement</th>
                            <th></th>
                            <th class="text-center" style="min-width: 140px;">Actions</th>
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
                                <strong>{{ \Carbon\Carbon::create()->month((int)$premierDecl->mois)->locale('fr')->translatedFormat('F') }}</strong> {{ $premierDecl->annee }}
                                @if($groupe->count() > 1)
                                    <br><small class="text-muted">
                                        <i class="icon-base ti tabler-list"></i> {{ $groupe->count() }} déclaration(s)
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($premierDecl->poste_id)
                                    <span class="badge bg-label-primary">{{ $premierDecl->poste->nom }}</span>
                                @else
                                    <span class="badge bg-label-info">{{ $premierDecl->bureauDouane->libelle }}</span>
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
                                        <span class="badge bg-label-secondary"><i class="icon-base ti tabler-pencil me-1"></i>Brouillon</span>
                                        @break
                                    @case('soumis')
                                        <span class="badge bg-label-primary"><i class="icon-base ti tabler-send me-1"></i>Soumis</span>
                                        @break
                                    @case('valide')
                                        <span class="badge bg-label-success"><i class="icon-base ti tabler-circle-check me-1"></i>Validé</span>
                                        @break
                                    @case('rejete')
                                        <span class="badge bg-label-danger"><i class="icon-base ti tabler-circle-x me-1"></i>Rejeté</span>
                                        @break
                                @endswitch
                            </td>
                           {{--  <td class="text-center">
                                <small class="text-muted">{{ $premierDecl->saisiPar->name }}</small>
                            </td> --}}
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                    @if($declUemoa)
                                        <a href="{{ route('pcs.declarations.show', $declUemoa) }}"
                                           class="btn btn-icon btn-sm btn-label-success rounded-pill"
                                           data-bs-toggle="tooltip"
                                           title="Voir UEMOA">
                                            <i class="icon-base ti tabler-eye icon-22px"></i>
                                        </a>
                                        @if($declUemoa->preuve_paiement)
                                            <a href="{{ route('pcs.declarations.preuve', $declUemoa) }}"
                                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                               data-bs-toggle="tooltip"
                                               title="Preuve de paiement UEMOA"
                                               target="_blank">
                                                <i class="icon-base ti tabler-paperclip icon-22px"></i>
                                            </a>
                                        @endif
                                        @if($declUemoa->saisi_par == auth()->id())
                                            <a href="{{ route('pcs.declarations.edit', $declUemoa) }}"
                                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                               data-bs-toggle="tooltip"
                                               title="Modifier UEMOA">
                                                <i class="icon-base ti tabler-edit icon-22px"></i>
                                            </a>
                                        @endif
                                    @endif
                                    @if($declAes)
                                        <a href="{{ route('pcs.declarations.show', $declAes) }}"
                                           class="btn btn-icon btn-sm btn-label-warning rounded-pill"
                                           data-bs-toggle="tooltip"
                                           title="Voir AES">
                                            <i class="icon-base ti tabler-eye icon-22px"></i>
                                        </a>
                                        @if($declAes->preuve_paiement)
                                            <a href="{{ route('pcs.declarations.preuve', $declAes) }}"
                                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                               data-bs-toggle="tooltip"
                                               title="Preuve de paiement AES"
                                               target="_blank">
                                                <i class="icon-base ti tabler-paperclip icon-22px"></i>
                                            </a>
                                        @endif
                                        @if($declAes->saisi_par == auth()->id())
                                            <a href="{{ route('pcs.declarations.edit', $declAes) }}"
                                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                               data-bs-toggle="tooltip"
                                               title="Modifier AES">
                                                <i class="icon-base ti tabler-edit icon-22px"></i>
                                            </a>
                                        @endif
                                    @endif
                                </div>

                                {{-- Modal pour afficher toutes les déclarations du groupe --}}
                                @if($groupe->count() > 1)
                                <div class="modal fade" id="detailModal{{ $loop->index }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $loop->index }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="detailModalLabel{{ $loop->index }}">
                                                    <i class="icon-base ti tabler-list me-1"></i>Détail des déclarations
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-3">
                                                    <strong>Période :</strong> {{ \Carbon\Carbon::create()->month((int)$premierDecl->mois)->locale('fr')->translatedFormat('F') }} {{ $premierDecl->annee }}<br>
                                                    <strong>Entité :</strong>
                                                    @if($premierDecl->poste_id)
                                                        {{ $premierDecl->poste->nom }}
                                                    @else
                                                        {{ $premierDecl->bureauDouane->libelle }}
                                                    @endif
                                                    <br>
                                                    <strong>Total :</strong> {{ $groupe->count() }} déclaration(s)
                                                </p>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Programme</th>
                                                                <th>Entité</th>
                                                                <th class="text-end">Recouvrement</th>
                                                                <th class="text-end">Reversement</th>
                                                                <th class="text-center">Statut</th>
                                                                <th class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($groupe as $decl)
                                                            <tr>
                                                                <td>
                                                                    @if($decl->programme == 'UEMOA')
                                                                        <span class="badge bg-label-success">{{ $decl->programme }}</span>
                                                                    @else
                                                                        <span class="badge bg-label-warning">{{ $decl->programme }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($decl->poste_id)
                                                                        <span class="badge bg-label-primary">{{ $decl->poste->nom }}</span>
                                                                    @else
                                                                        <span class="badge bg-label-info">{{ $decl->bureauDouane->libelle }}</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-end">{{ number_format($decl->montant_recouvrement, 0, ',', ' ') }}</td>
                                                                <td class="text-end">{{ number_format($decl->montant_reversement, 0, ',', ' ') }}</td>
                                                                <td class="text-center">
                                                                    @switch($decl->statut)
                                                                        @case('brouillon')
                                                                            <span class="badge bg-label-secondary">Brouillon</span>
                                                                            @break
                                                                        @case('soumis')
                                                                            <span class="badge bg-label-primary">Soumis</span>
                                                                            @break
                                                                        @case('valide')
                                                                            <span class="badge bg-label-success">Validé</span>
                                                                            @break
                                                                        @case('rejete')
                                                                            <span class="badge bg-label-danger">Rejeté</span>
                                                                            @break
                                                                    @endswitch
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                                                        <a href="{{ route('pcs.declarations.show', $decl) }}"
                                                                           class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                                                           data-bs-toggle="tooltip"
                                                                           title="Voir">
                                                                            <i class="icon-base ti tabler-eye icon-22px"></i>
                                                                        </a>
                                                                        @if($decl->preuve_paiement)
                                                                            <a href="{{ route('pcs.declarations.preuve', $decl) }}"
                                                                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                                                               data-bs-toggle="tooltip"
                                                                               title="Preuve de paiement"
                                                                               target="_blank">
                                                                                <i class="icon-base ti tabler-paperclip icon-22px"></i>
                                                                            </a>
                                                                        @endif
                                                                        @if($decl->saisi_par == auth()->id())
                                                                            <a href="{{ route('pcs.declarations.edit', $decl) }}"
                                                                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                                                               data-bs-toggle="tooltip"
                                                                               title="Modifier">
                                                                                <i class="icon-base ti tabler-edit icon-22px"></i>
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="text-muted">
                    Affichage de <strong>{{ $declarations->firstItem() ?? 0 }}</strong> à <strong>{{ $declarations->lastItem() ?? 0 }}</strong>
                    sur <strong>{{ $declarations->total() }}</strong> période(s)
                    <span class="text-muted-light">({{ $totalDeclarations ?? 0 }} déclarations individuelles)</span>
                </div>
                <div>
                    @if ($declarations->hasPages())
                        <nav aria-label="Navigation des pages">
                            <ul class="pagination mb-0">
                                {{-- Bouton Première page --}}
                                @if ($declarations->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-label="Première page">
                                            <i class="icon-base ti tabler-chevrons-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $declarations->url(1) }}" aria-label="Première page">
                                            <i class="icon-base ti tabler-chevrons-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Bouton Précédent --}}
                                @if ($declarations->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-label="Précédent">
                                            <i class="icon-base ti tabler-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $declarations->previousPageUrl() }}" rel="prev" aria-label="Précédent">
                                            <i class="icon-base ti tabler-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Numéros de page avec pagination intelligente --}}
                                @php
                                    $currentPage = $declarations->currentPage();
                                    $lastPage = $declarations->lastPage();
                                    $delta = 2; // Nombre de pages à afficher de chaque côté
                                    $range = [];

                                    // Calculer la plage de pages à afficher
                                    $start = max(1, $currentPage - $delta);
                                    $end = min($lastPage, $currentPage + $delta);

                                    // Ajuster si on est près du début
                                    if ($currentPage <= $delta + 1) {
                                        $end = min($lastPage, 1 + ($delta * 2));
                                    }

                                    // Ajuster si on est près de la fin
                                    if ($currentPage >= $lastPage - $delta) {
                                        $start = max(1, $lastPage - ($delta * 2));
                                    }

                                    for ($i = $start; $i <= $end; $i++) {
                                        $range[] = $i;
                                    }
                                @endphp

                                @foreach ($range as $page)
                                    @if ($page == $currentPage)
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $declarations->url($page) }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Bouton Suivant --}}
                                @if ($declarations->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $declarations->nextPageUrl() }}" rel="next" aria-label="Suivant">
                                            <i class="icon-base ti tabler-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-label="Suivant">
                                            <i class="icon-base ti tabler-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif

                                {{-- Bouton Dernière page --}}
                                @if ($declarations->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $declarations->url($lastPage) }}" aria-label="Dernière page">
                                            <i class="icon-base ti tabler-chevrons-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-label="Dernière page">
                                            <i class="icon-base ti tabler-chevrons-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @else
                        <span class="text-muted small">Page 1 sur 1</span>
                    @endif
                </div>
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="icon-base ti tabler-info-circle icon-lg mb-2 d-block"></i>
                <p class="mb-0">Aucune déclaration trouvée. Cliquez sur "Nouvelle Déclaration" pour commencer.</p>
            </div>
            @endif
</x-vuexy.card>

@push('scripts')
<script>
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endpush
@if(auth()->user()->poste_id && !in_array(auth()->user()->role, ['acct','admin']))
<!-- Modal État des références (Déclarations + Cotisations) -->
<div class="modal fade" id="modalEtatReferences" tabindex="-1" aria-labelledby="modalEtatReferencesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEtatReferencesLabel">
                    <i class="icon-base ti tabler-hash me-2"></i>Générer État des références
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="GET" action="{{ route('pcs.etat-references.poste-emetteur') }}" target="_blank">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        Affiche les <strong>références</strong> des déclarations PCS et des cotisations TRIE pour votre poste.
                    </div>
                    <div class="mb-3">
                        <label for="programme_ref" class="form-label fw-bold">Programme (déclarations)</label>
                        <select class="form-select" id="programme_ref" name="programme">
                            <option value="">Tous (UEMOA + AES)</option>
                            <option value="UEMOA">UEMOA</option>
                            <option value="AES">AES</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="annee_ref" class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                        <select class="form-select" id="annee_ref" name="annee" required>
                            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        <i class="icon-base ti tabler-x me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-file-type-pdf me-1"></i>Générer le PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal État Consolidé Poste Émetteur -->
<div class="modal fade" id="modalEtatConsolidePosteEmetteur" tabindex="-1" aria-labelledby="modalEtatConsolidePosteEmetteurLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEtatConsolidePosteEmetteurLabel">
                    <i class="icon-base ti tabler-file-export me-2"></i>Générer État Consolidé
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="GET" action="{{ route('pcs.declarations.etat-consolide.poste-emetteur') }}" target="_blank">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="icon-base ti tabler-info-circle me-2"></i>
                        <strong>Poste émetteur :</strong> {{ auth()->user()->poste->nom }}
                    </div>
                    <div class="mb-3">
                        <label for="programme_etat" class="form-label fw-bold">
                            Programme <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="programme_etat" name="programme" required>
                            <option value="">Sélectionner un programme</option>
                            <option value="UEMOA" {{ old('programme', 'UEMOA') == 'UEMOA' ? 'selected' : '' }}>UEMOA</option>
                            <option value="AES" {{ old('programme') == 'AES' ? 'selected' : '' }}>AES</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="annee_etat" class="form-label fw-bold">
                            Année <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="annee_etat" name="annee" required>
                            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        <i class="icon-base ti tabler-x me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-file-type-pdf me-1"></i>Générer le PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

