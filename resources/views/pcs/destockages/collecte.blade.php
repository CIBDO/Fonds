@extends('layouts.master')

@section('title', 'Collecte PCS')

@section('content')
<x-vuexy.page-header title="Fonds Collectés - Vue de Collecte" subtitle="Consultation des fonds collectés par poste">
    <x-slot:actions>
                <div class="btn-group me-2" role="group">
                    <button type="button" class="btn btn-outline-danger btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-file-pdf me-1"></i>États PDF
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-collecte', ['programme' => $programme, 'annee' => $annee]) }}">
                                <i class="fas fa-coins text-success"></i> État de Collecte {{ $annee }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-consolide', ['programme' => $programme, 'annee' => $annee]) }}">
                                <i class="fas fa-chart-bar text-primary"></i> État Consolidé Règlements {{ $annee }}
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('pcs.destockages.create', ['programme' => $programme, 'mois' => $mois, 'annee' => $annee]) }}" class="btn btn-danger btn-sm me-2">
                    <i class="fas fa-plus me-1"></i>Nouveau Règlement
                </a>
                <a href="{{ route('pcs.destockages.index') }}" class="btn btn-label-secondary btn-sm">
                    <i class="ti tabler-list me-1"></i>Liste des Règlements
                </a>
    </x-slot:actions>
</x-vuexy.page-header>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
            <form method="GET" action="{{ route('pcs.destockages.collecte') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Programme</label>
                    <select name="programme" class="form-select" required>
                        <option value="UEMOA" {{ $programme == 'UEMOA' ? 'selected' : '' }}>UEMOA</option>
                        <option value="AES" {{ $programme == 'AES' ? 'selected' : '' }}>AES</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Mois</label>
                    <select name="mois" class="form-select" required>
                        @foreach($moisList as $moisNum => $moisNom)
                            <option value="{{ $moisNum }}" {{ $mois == $moisNum ? 'selected' : '' }}>
                                {{ $moisNom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Année</label>
                    <select name="annee" class="form-select" required>
                        @foreach($annees as $anneeOption)
                            <option value="{{ $anneeOption }}" {{ $annee == $anneeOption ? 'selected' : '' }}>
                                {{ $anneeOption }}
                            </option>
                        @endforeach
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
</x-vuexy.card>

<x-vuexy.card :title="'Fonds Collectés - ' . $programme . ' - ' . $moisList[$mois] . ' ' . $annee" icon="tabler-list">
    <x-slot:header>
        <span class="badge bg-label-primary">{{ count($collectesParPoste) }} entités</span>
    </x-slot:header>
            @if(count($collectesParPoste) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-building"></i> Entité</th>
                            <th class="text-end"><i class="fas fa-arrow-up"></i> Montant Collecté</th>
                            <th class="text-end"><i class="fas fa-arrow-down"></i> Déjà Règlement</th>
                            <th class="text-end"><i class="fas fa-balance-scale"></i> Solde Disponible</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCollecte = 0;
                            $totalDejaDestocke = 0;
                            $totalDisponible = 0;
                        @endphp
                        @foreach($collectesParPoste as $collecte)
                            @php
                                $totalCollecte += $collecte['montant_collecte'];
                                $totalDejaDestocke += $collecte['montant_deja_destocke'];
                                $totalDisponible += $collecte['solde_disponible'];
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $collecte['type'] == 'poste' ? 'primary' : 'info' }}">
                                        {{ $collecte['type'] == 'poste' ? 'Poste' : 'Bureau' }}
                                    </span>
                                    <strong>{{ $collecte['nom'] }}</strong>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    {{ number_format($collecte['montant_collecte'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-end fw-bold text-warning">
                                    {{ number_format($collecte['montant_deja_destocke'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-end fw-bold {{ $collecte['solde_disponible'] > 0 ? 'text-success' : 'text-muted' }}">
                                    {{ number_format($collecte['solde_disponible'], 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-center">
                                    @if($collecte['solde_disponible'] > 0)
                                        <a href="{{ route('pcs.destockages.create', ['programme' => $programme, 'mois' => $mois, 'annee' => $annee]) }}"
                                           class="btn btn-sm btn-outline-danger"
                                           data-bs-toggle="tooltip"
                                           title="Créer un règlement">
                                            <i class="fas fa-cash-register"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">Solde épuisé</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>TOTAUX</th>
                            <th class="text-end text-success">{{ number_format($totalCollecte, 0, ',', ' ') }} FCFA</th>
                            <th class="text-end text-warning">{{ number_format($totalDejaDestocke, 0, ',', ' ') }} FCFA</th>
                            <th class="text-end text-success">{{ number_format($totalDisponible, 0, ',', ' ') }} FCFA</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Aucun fonds collecté pour cette période.</p>
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
@endsection

