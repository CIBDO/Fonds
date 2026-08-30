@extends('layouts.master')

@section('title', 'Destockages PCS')

@section('content')
<div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
    <div class="dropdown">
        <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
            <i class="icon-base ti tabler-file-type-pdf me-1"></i>États PDF
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-collecte', ['programme' => 'UEMOA', 'annee' => date('Y')]) }}">
                    <i class="icon-base ti tabler-coins text-success me-2"></i>État Collecte UEMOA {{ date('Y') }}
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-collecte', ['programme' => 'AES', 'annee' => date('Y')]) }}">
                    <i class="icon-base ti tabler-coins text-warning me-2"></i>État Collecte AES {{ date('Y') }}
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-consolide', ['programme' => 'UEMOA', 'annee' => date('Y')]) }}">
                    <i class="icon-base ti tabler-chart-bar text-primary me-2"></i>Règlements UEMOA {{ date('Y') }}
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-consolide', ['programme' => 'AES', 'annee' => date('Y')]) }}">
                    <i class="icon-base ti tabler-chart-bar text-info me-2"></i>Règlements AES {{ date('Y') }}
                </a>
            </li>
        </ul>
    </div>
    <a href="{{ route('pcs.destockages.collecte') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-coins me-1"></i>Vue de Collecte
    </a>
    <a href="{{ route('pcs.destockages.create') }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-plus me-1"></i>Nouveau Règlement
    </a>
</div>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
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
            <button type="submit" class="btn btn-primary d-block w-100">
                <i class="icon-base ti tabler-filter me-1"></i>Filtrer
            </button>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Historique des Règlements" icon="tabler-list">
    <x-slot:actions>
        <span class="badge bg-label-secondary">{{ $destockages->total() }} règlements</span>
    </x-slot:actions>

    @if($destockages->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="icon-base ti tabler-hash me-1"></i>Référence</th>
                    <th><i class="icon-base ti tabler-flag me-1"></i>Programme</th>
                    <th><i class="icon-base ti tabler-calendar me-1"></i>Période</th>
                    <th><i class="icon-base ti tabler-calendar-check me-1"></i>Date Règlement</th>
                    <th class="text-end"><i class="icon-base ti tabler-currency-franc me-1"></i>Montant Total</th>
                    <th class="text-center"><i class="icon-base ti tabler-list-numbers me-1"></i>Nb Postes</th>
                    <th class="text-center"><i class="icon-base ti tabler-info-circle me-1"></i>Statut</th>
                    <th class="text-center" width="100">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($destockages as $destockage)
                <tr>
                    <td>
                        <strong class="text-primary">{{ $destockage->reference_destockage }}</strong>
                        <br>
                        <small class="text-body-secondary">
                            <i class="icon-base ti tabler-user me-1"></i>{{ $destockage->creePar->name ?? 'N/A' }}
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-label-{{ $destockage->programme == 'UEMOA' ? 'primary' : 'warning' }}">
                            {{ $destockage->programme }}
                        </span>
                    </td>
                    <td class="fw-medium">{{ $destockage->nom_mois }} {{ $destockage->periode_annee }}</td>
                    <td>{{ \Carbon\Carbon::parse($destockage->date_destockage)->format('d/m/Y') }}</td>
                    <td class="text-end fw-medium text-success">
                        {{ number_format($destockage->montant_total_destocke, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="text-center">
                        <span class="badge bg-label-info">{{ $destockage->postes->count() }} postes</span>
                    </td>
                    <td class="text-center">
                        @include('partials.pcs.status-badge', ['statut' => $destockage->statut])
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <a href="{{ route('pcs.destockages.show', $destockage) }}"
                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                               data-bs-toggle="tooltip"
                               title="Voir le détail">
                                <i class="icon-base ti tabler-eye icon-22px"></i>
                            </a>
                            <a href="{{ route('pcs.destockages.pdf', $destockage) }}"
                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                               data-bs-toggle="tooltip"
                               title="Télécharger le bordereau"
                               target="_blank">
                                <i class="icon-base ti tabler-file-type-pdf icon-22px"></i>
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

    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <div class="text-body-secondary small">
            Affichage de <strong>{{ $destockages->firstItem() ?? 0 }}</strong> à <strong>{{ $destockages->lastItem() ?? 0 }}</strong>
            sur <strong>{{ $destockages->total() }}</strong> règlement(s)
        </div>
        <div>
            @if ($destockages->hasPages())
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        @if ($destockages->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">« Précédent</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $destockages->appends(request()->except('page'))->previousPageUrl() }}" rel="prev">« Précédent</a></li>
                        @endif
                        @foreach ($destockages->appends(request()->except('page'))->getUrlRange(1, $destockages->lastPage()) as $page => $url)
                            @if ($page == $destockages->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                        @if ($destockages->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $destockages->appends(request()->except('page'))->nextPageUrl() }}" rel="next">Suivant »</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Suivant »</span></li>
                        @endif
                    </ul>
                </nav>
            @endif
        </div>
    </div>
    @else
    <x-vuexy.alert type="info" class="text-center">
        <p class="mb-3">Aucun règlement trouvé.</p>
        <a href="{{ route('pcs.destockages.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus me-1"></i>Créer un Règlement
        </a>
    </x-vuexy.alert>
    @endif
</x-vuexy.card>

@push('scripts')
<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
</script>
@endpush
@endsection
