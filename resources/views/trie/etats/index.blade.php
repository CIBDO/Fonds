@extends('layouts.master')

@section('title', 'États et Rapports TRIE')

@section('content')
<x-vuexy.page-header title="États et Rapports TRIE" subtitle="Génération des états mensuels et consolidés TRIE/CCIM">
    <x-slot:actions>
        <a href="{{ route('trie.cotisations.index') }}" class="btn btn-label-secondary btn-sm">
            <i class="ti tabler-arrow-left me-1"></i>Retour aux Cotisations
        </a>
    </x-slot:actions>
</x-vuexy.page-header>

<div class="row g-6">
    <div class="col-md-6">
        <x-vuexy.card title="État Mensuel des Paiements" icon="tabler-calendar">
            <div class="text-center mb-4">
                <i class="ti tabler-receipt text-primary icon-48px mb-3"></i>
                <p class="text-body-secondary mb-0">
                    Générer l'état des paiements TRIE/CCIM pour un mois donné.
                    Regroupe les données par <strong>POSTE</strong> avec le détail des paiements.
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
                        <i class="ti tabler-download me-1"></i>Générer l'État Mensuel
                    </button>
                </div>
            </form>
        </x-vuexy.card>
    </div>

    <div class="col-md-6">
        <x-vuexy.card title="État Consolidé Annuel" icon="tabler-chart-bar">
            <div class="text-center mb-4">
                <i class="ti tabler-table text-success icon-48px mb-3"></i>
                <p class="text-body-secondary mb-0">
                    Générer l'état consolidé des cotisations par poste et bureau pour une année complète.
                    Affiche le détail <strong>mensuel par BUREAU</strong> + récapitulatif bi-annuel.
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
                        <i class="ti tabler-download me-1"></i>Générer l'État Consolidé
                    </button>
                </div>
            </form>
        </x-vuexy.card>
    </div>
</div>

<x-vuexy.card title="Statistiques Rapides" icon="tabler-chart-pie">
    <div class="row g-4">
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
            <x-vuexy.stat-card
                label="Total Cotisations {{ $anneeActuelle }}"
                :value="number_format($totalAnnee, 0, ',', ' ') . ' FCFA'"
                icon="tabler-coins"
                variant="customs-receipts"
                icon-bg="bg-label-primary"
            />
        </div>
        <div class="col-md-4">
            <x-vuexy.stat-card
                label="Cotisations du Mois"
                :value="number_format($totalMois, 0, ',', ' ') . ' FCFA'"
                icon="tabler-calendar-check"
                variant="success"
                icon-bg="bg-label-success"
            />
        </div>
        <div class="col-md-4">
            <x-vuexy.stat-card
                label="Total Apurements {{ $anneeActuelle }}"
                :value="number_format($totalApurement, 0, ',', ' ') . ' FCFA'"
                icon="tabler-refresh"
                variant="funds-to-send"
                icon-bg="bg-label-warning"
            />
        </div>
    </div>
</x-vuexy.card>
@endsection
