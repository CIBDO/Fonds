@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-primary">
                        <i class="fas fa-file-pdf me-2"></i>États et Rapports TRIE
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('trie.cotisations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour aux Cotisations
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Carte État Mensuel -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>État Mensuel des Paiements
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-receipt fa-4x text-primary mb-3"></i>
                        <p class="text-muted">
                            Générer l'état des paiements TRIE/CCIM pour un mois donné.
                            <br>Regroupe les données par <strong>POSTE</strong> avec le détail des paiements.
                        </p>
                    </div>
                    <form method="GET" action="{{ route('trie.etats.mensuel') }}" target="_blank">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mois <span class="text-danger">*</span></label>
                            <select name="mois" class="form-select" required>
                                @php
                                    $moisList = [
                                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                                    ];
                                @endphp
                                @foreach($moisList as $num => $nom)
                                    <option value="{{ $num }}" {{ $num == date('n') ? 'selected' : '' }}>
                                        {{ $nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                            <select name="annee" class="form-select" required>
                                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-download me-1"></i>Générer l'État Mensuel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Carte État Consolidé Annuel -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>État Consolidé Annuel
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-table fa-4x text-success mb-3"></i>
                        <p class="text-muted">
                            Générer l'état consolidé des cotisations par poste et bureau
                            pour une année complète.
                            <br>Affiche le détail <strong>mensuel par BUREAU</strong> + récapitulatif bi-annuel.
                        </p>
                    </div>
                    <form method="GET" action="{{ route('trie.etats.consolide') }}" target="_blank">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Année <span class="text-danger">*</span></label>
                            <select name="annee" class="form-select" required>
                                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-download me-1"></i>Générer l'État Consolidé
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques Rapides -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-chart-pie me-2"></i>Statistiques Rapides
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @php
                    $anneeActuelle = date('Y');
                    $totalAnnee = \App\Models\CotisationTrie::where('annee', $anneeActuelle)
                        ->where('statut', 'valide')
                        ->sum('montant_total');

                    $moisActuel = date('n');
                    $totalMois = \App\Models\CotisationTrie::where('annee', $anneeActuelle)
                        ->where('mois', $moisActuel)
                        ->where('statut', 'valide')
                        ->sum('montant_total');

                    $totalApurement = \App\Models\CotisationTrie::where('annee', $anneeActuelle)
                        ->where('statut', 'valide')
                        ->sum('montant_apurement');
                @endphp

                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-coins fa-2x text-primary mb-2"></i>
                            <h6>Total Cotisations {{ $anneeActuelle }}</h6>
                            <h4 class="fw-bold text-primary">
                                {{ number_format($totalAnnee, 0, ',', ' ') }} FCFA
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                            <h6>Cotisations du Mois</h6>
                            <h4 class="fw-bold text-success">
                                {{ number_format($totalMois, 0, ',', ' ') }} FCFA
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-undo fa-2x text-warning mb-2"></i>
                            <h6>Total Apurements {{ $anneeActuelle }}</h6>
                            <h4 class="fw-bold text-warning">
                                {{ number_format($totalApurement, 0, ',', ' ') }} FCFA
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

