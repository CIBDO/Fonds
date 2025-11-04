@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-list me-2"></i>Liste des Règlements
                    </h3>
                    {{-- <p class="text-muted mb-0">Historique des règlements de fonds PCS</p> --}}
                </div>
            </div>
            <div class="col-auto">
                <div class="btn-group me-2" role="group">
                    <button type="button" class="btn btn-outline-danger btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-file-pdf me-1"></i>États PDF
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-collecte', ['programme' => 'UEMOA', 'annee' => date('Y')]) }}">
                                <i class="fas fa-coins text-success"></i> État Collecte UEMOA {{ date('Y') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-collecte', ['programme' => 'AES', 'annee' => date('Y')]) }}">
                                <i class="fas fa-coins text-warning"></i> État Collecte AES {{ date('Y') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-consolide', ['programme' => 'UEMOA', 'annee' => date('Y')]) }}">
                                <i class="fas fa-chart-bar text-primary"></i> Règlements UEMOA {{ date('Y') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-consolide', ['programme' => 'AES', 'annee' => date('Y')]) }}">
                                <i class="fas fa-chart-bar text-info"></i> Règlements AES {{ date('Y') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('pcs.destockages.collecte') }}" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-coins me-1"></i>Vue de Collecte
                </a>
                <a href="{{ route('pcs.destockages.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus me-1"></i>Nouveau Règlement
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pcs.destockages.index') }}" class="row g-3">
                <div class="col-md-3">
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
                        @foreach($moisList as $moisNum => $moisNom)
                            <option value="{{ $moisNum }}" {{ request('mois') == $moisNum ? 'selected' : '' }}>
                                {{ $moisNom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Année</label>
                    <select name="annee" class="form-select">
                        <option value="">Toutes</option>
                        @foreach($annees as $anneeOption)
                            <option value="{{ $anneeOption }}" {{ request('annee') == $anneeOption ? 'selected' : '' }}>
                                {{ $anneeOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                        <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
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

    <!-- Table des déstockages -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Historique des Règlements</h5>
                <span class="badge bg-white text-danger">{{ $destockages->total() }} règlements</span>
            </div>
        </div>

        <div class="card-body">
            @if($destockages->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-hashtag"></i> Référence</th>
                            <th><i class="fas fa-flag"></i> Programme</th>
                            <th><i class="fas fa-calendar"></i> Période</th>
                            <th><i class="fas fa-calendar-check"></i> Date Règlement</th>
                            <th class="text-end"><i class="fas fa-money-bill-wave"></i> Montant Total</th>
                            <th class="text-center"><i class="fas fa-list-ol"></i> Nb Postes</th>
                            <th class="text-center"><i class="fas fa-info-circle"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($destockages as $destockage)
                        <tr>
                            <td>
                                <strong class="text-danger">{{ $destockage->reference_destockage }}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $destockage->creePar->name ?? 'N/A' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $destockage->programme == 'UEMOA' ? 'primary' : 'warning' }} fs-6">
                                    {{ $destockage->programme }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $destockage->nom_mois }} {{ $destockage->periode_annee }}</strong>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($destockage->date_destockage)->format('d/m/Y') }}
                            </td>
                            <td class="text-end">
                                <strong class="text-success">
                                    {{ number_format($destockage->montant_total_destocke, 0, ',', ' ') }} FCFA
                                </strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">
                                    {{ $destockage->postes->count() }} postes
                                </span>
                            </td>
                            <td class="text-center">
                                @if($destockage->statut == 'valide')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Validé
                                    </span>
                                @elseif($destockage->statut == 'brouillon')
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-edit"></i> Brouillon
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle"></i> Annulé
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pcs.destockages.show', $destockage) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="Voir le détail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pcs.destockages.pdf', $destockage) }}"
                                       class="btn btn-sm btn-outline-danger"
                                       data-bs-toggle="tooltip"
                                       title="Télécharger le bordereau">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">TOTAL GÉNÉRAL:</th>
                            <th class="text-end text-success">
                                {{ number_format($destockages->sum('montant_total_destocke'), 0, ',', ' ') }} FCFA
                            </th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de <strong>{{ $destockages->firstItem() ?? 0 }}</strong> à <strong>{{ $destockages->lastItem() ?? 0 }}</strong>
                    sur <strong>{{ $destockages->total() }}</strong> règlement(s)
                </div>
                <div>
                    @if ($destockages->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                {{-- Bouton Précédent --}}
                                @if ($destockages->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $destockages->appends(request()->except('page'))->previousPageUrl() }}" rel="prev">« Précédent</a>
                                    </li>
                                @endif

                                {{-- Numéros de page --}}
                                @foreach ($destockages->appends(request()->except('page'))->getUrlRange(1, $destockages->lastPage()) as $page => $url)
                                    @if ($page == $destockages->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Bouton Suivant --}}
                                @if ($destockages->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $destockages->appends(request()->except('page'))->nextPageUrl() }}" rel="next">Suivant »</a>
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
                <p class="mb-0">Aucun règlement trouvé.</p>
                <a href="{{ route('pcs.destockages.create') }}" class="btn btn-danger mt-3">
                    <i class="fas fa-plus me-1"></i>Créer un Règlement
                </a>
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

