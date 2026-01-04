@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-folder-open me-2"></i>Autres Demandes Financières
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('pcs.autres-demandes.create') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-plus me-1"></i>Nouvelle Demande
                    </a>
                    @if(auth()->user()->poste_id && !in_array(auth()->user()->role, ['acct', 'admin']))
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEtatConsolideAutresDemandes">
                        <i class="fas fa-file-export me-1"></i>État Consolidé
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Année</label>
                    <select name="annee" class="form-select">
                        <option value="">Toutes</option>
                        @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                            <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                        <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                        <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-danger d-block w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des demandes -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Demandes</h5>
                <span class="badge bg-white text-danger">{{ $demandes->total() }} demandes</span>
            </div>
        </div>

        <div class="card-body">
            @if($demandes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-building"></i> Poste</th>
                            <th><i class="fas fa-tag"></i> Désignation</th>
                            <th class="text-end"><i class="fas fa-money-bill-wave"></i> Montant</th>
                            <th class="text-center"><i class="fas fa-flag"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demandes as $demande)
                        <tr>
                            <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                            <td><span class="badge bg-primary poste-badge">{{ $demande->poste->nom }}</span></td>
                            <td class="fw-bold">{{ Str::limit($demande->designation, 50) }}</td>
                            <td class="text-end">
                                <div class="fw-bold text-primary">{{ number_format($demande->montant, 0, ',', ' ') }} FCFA</div>
                                @if($demande->montant_accord !== null)
                                    <div class="small text-success">Accordé: {{ number_format($demande->montant_accord, 0, ',', ' ') }} FCFA</div>
                                    @if($demande->montant_accord != $demande->montant)
                                        <div class="small">
                                            <span class="badge {{ $demande->montant_accord > $demande->montant ? 'bg-warning' : 'bg-info' }} montant-badge">
                                                {{ $demande->montant_accord > $demande->montant ? '+' : '' }}{{ number_format($demande->montant_accord - $demande->montant, 0, ',', ' ') }}
                                                ({{ $demande->montant > 0 ? round(($demande->montant_accord / $demande->montant) * 100, 1) : 0 }}%)
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                @switch($demande->statut)
                                    @case('brouillon')
                                        <span class="badge bg-secondary">Brouillon</span>
                                        @break
                                    @case('soumis')
                                        <span class="badge bg-primary">Soumis</span>
                                        @break
                                    @case('valide')
                                        <span class="badge bg-success">Validé</span>
                                        @break
                                    @case('rejete')
                                        <span class="badge bg-danger">Rejeté</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('pcs.autres-demandes.show', $demande) }}"
                                       class="btn btn-outline-info"
                                       data-bs-toggle="tooltip"
                                       title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(in_array($demande->statut, ['brouillon', 'soumis', 'rejete']) && $demande->saisi_par == auth()->id())
                                        <a href="{{ route('pcs.autres-demandes.edit', $demande) }}"
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->peut_valider_pcs && $demande->statut == 'soumis')
                                        <button type="button"
                                                class="btn btn-outline-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#validationModal{{ $demande->id }}"
                                                data-bs-toggle="tooltip"
                                                title="Valider avec montant">
                                            <i class="fas fa-check"></i>
                                        </button>
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
                    Affichage de <strong>{{ $demandes->firstItem() ?? 0 }}</strong> à <strong>{{ $demandes->lastItem() ?? 0 }}</strong>
                    sur <strong>{{ $demandes->total() }}</strong> demande(s)
                </div>
                <div>
                    @if ($demandes->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                {{-- Bouton Précédent --}}
                                @if ($demandes->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $demandes->previousPageUrl() }}" rel="prev">« Précédent</a>
                                    </li>
                                @endif

                                {{-- Numéros de page --}}
                                @foreach ($demandes->getUrlRange(1, $demandes->lastPage()) as $page => $url)
                                    @if ($page == $demandes->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Bouton Suivant --}}
                                @if ($demandes->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $demandes->nextPageUrl() }}" rel="next">Suivant »</a>
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
                <p class="mb-0">Aucune demande trouvée. Cliquez sur "Nouvelle Demande" pour commencer.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modales de Validation -->
@foreach($demandes as $demande)
@if($demande->statut == 'soumis')
<div class="modal fade" id="validationModal{{ $demande->id }}" tabindex="-1" aria-labelledby="validationModalLabel{{ $demande->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="validationModalLabel{{ $demande->id }}">
                    <i class="fas fa-check-circle me-2"></i>Valider la Demande
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('pcs.autres-demandes.valider', $demande) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Poste :</strong> {{ $demande->poste->nom }}
                        </div>
                        <div class="col-md-6">
                            <strong>Date :</strong> {{ $demande->date_demande->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Désignation :</strong>
                        <p class="text-muted">{{ $demande->designation }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-primary">Montant Demandé</h6>
                                    <h4 class="text-primary">{{ number_format($demande->montant, 0, ',', ' ') }} FCFA</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Montant à Accorder</h6>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control form-control-lg text-center"
                                               name="montant_accord"
                                               id="montant_accord{{ $demande->id }}"
                                               value="{{ $demande->montant }}"
                                               step="0.01"
                                               min="0"
                                               max="{{ $demande->montant * 2 }}"
                                               required>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($demande->observation)
                    <div class="mb-3">
                        <strong>Observation :</strong>
                        <p class="text-muted">{{ $demande->observation }}</p>
                    </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note :</strong> Vous pouvez accorder un montant différent de celui demandé selon les disponibilités budgétaires.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Valider avec ce Montant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@if(auth()->user()->poste_id && !in_array(auth()->user()->role, ['acct','admin']))
<!-- Modal État Consolidé Poste Émetteur -->
<div class="modal fade" id="modalEtatConsolideAutresDemandes" tabindex="-1" aria-labelledby="modalEtatConsolideAutresDemandesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEtatConsolideAutresDemandesLabel">
                    <i class="fas fa-file-export me-2"></i>Générer État Consolidé
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="GET" action="{{ route('pcs.autres-demandes.etat-consolide.poste-emetteur') }}" target="_blank">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Poste émetteur :</strong> {{ auth()->user()->poste->nom }}
                    </div>
                    <div class="mb-3">
                        <label for="annee_etat_ad" class="form-label fw-bold">
                            Année <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="annee_etat_ad" name="annee" required>
                            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-file-pdf me-1"></i>Générer le PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endpush
@endsection

