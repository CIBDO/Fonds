@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-primary">
                        <i class="fas fa-coins me-2"></i>Cotisations TRIE - CCIM
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('trie.bureaux.index') }}" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-building me-1"></i>Bureaux
                </a>
                <a href="{{ route('trie.etats.index') }}" class="btn btn-info btn-sm me-2">
                    <i class="fas fa-file-pdf me-1"></i>États
                </a>
                <a href="{{ route('trie.cotisations.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Nouvelle Cotisation
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('trie.cotisations.index') }}" class="row g-3">
                @if(in_array(Auth::user()->role, ['admin', 'acct']) && $postes->count() > 1)
                <div class="col-md-3">
                    <label class="form-label fw-bold">Poste</label>
                    <select name="poste_id" class="form-select">
                        <option value="">Tous les postes</option>
                        @foreach($postes as $poste)
                            <option value="{{ $poste->id }}" {{ request('poste_id') == $poste->id ? 'selected' : '' }}>
                                {{ $poste->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="col-md-3">
                    <label class="form-label fw-bold">Poste</label>
                    <input type="text" class="form-control" value="{{ $postes->first()->nom ?? 'N/A' }}" disabled>
                </div>
                @endif
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
                        @foreach($annees as $annee)
                            <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>
                                {{ $annee }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des cotisations -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Cotisations</h5>
                <span class="badge bg-white text-primary">{{ $cotisations->total() }} cotisations</span>
            </div>
        </div>
        <div class="card-body">
            @if($cotisations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-calendar"></i> Période</th>
                            <th><i class="fas fa-map-marker-alt"></i> Poste</th>
                            <th><i class="fas fa-building"></i> Bureau</th>
                            <th class="text-end"><i class="fas fa-money-bill"></i> Cotisation</th>
                            <th class="text-end"><i class="fas fa-undo"></i> Apurement</th>
                            <th class="text-end"><i class="fas fa-coins"></i> Total</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cotisations as $cotisation)
                        <tr>
                            <td><strong>{{ $cotisation->nom_mois }} {{ $cotisation->annee }}</strong></td>
                            <td>{{ $cotisation->poste->nom }}</td>
                            <td>
                                <span class="text-primary">{{ $cotisation->bureauTrie->code_bureau }}</span>
                                <br><small class="text-muted">{{ $cotisation->bureauTrie->nom_bureau }}</small>
                            </td>
                            <td class="text-end">{{ number_format($cotisation->montant_cotisation_courante, 0, ',', ' ') }}</td>
                            <td class="text-end">
                                @if($cotisation->montant_apurement > 0)
                                    <span class="text-warning fw-bold">{{ number_format($cotisation->montant_apurement, 0, ',', ' ') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <strong class="text-success">{{ number_format($cotisation->montant_total, 0, ',', ' ') }}</strong>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('trie.cotisations.show', $cotisation) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @php
                                        $user = Auth::user();
                                        $peutModifier = in_array($user->role, ['admin', 'acct']) || $user->poste_id == $cotisation->poste_id;
                                    @endphp
                                    @if($peutModifier)
                                        <a href="{{ route('trie.cotisations.edit', $cotisation) }}" 
                                           class="btn btn-sm btn-outline-warning"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">TOTAL:</th>
                            <th class="text-end">{{ number_format($cotisations->sum('montant_cotisation_courante'), 0, ',', ' ') }}</th>
                            <th class="text-end">{{ number_format($cotisations->sum('montant_apurement'), 0, ',', ' ') }}</th>
                            <th class="text-end text-success">{{ number_format($cotisations->sum('montant_total'), 0, ',', ' ') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $cotisations->appends(request()->except('page'))->links() }}
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Aucune cotisation trouvée.</p>
                <a href="{{ route('trie.cotisations.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-1"></i>Créer une Cotisation
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

