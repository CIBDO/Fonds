@extends('layouts.master')

@section('title', 'Collecte PCS')

@section('content')
<div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
    <div class="dropdown">
        <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
            <i class="icon-base ti tabler-file-type-pdf me-1"></i>États PDF
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-collecte', ['programme' => $programme, 'annee' => $annee]) }}">
                    <i class="icon-base ti tabler-coins text-success me-2"></i>État de Collecte {{ $annee }}
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('pcs.destockages.pdf.etat-consolide', ['programme' => $programme, 'annee' => $annee]) }}">
                    <i class="icon-base ti tabler-chart-bar text-primary me-2"></i>État Consolidé Règlements {{ $annee }}
                </a>
            </li>
        </ul>
    </div>
    <a href="{{ route('pcs.destockages.create', ['programme' => $programme, 'mois' => $mois, 'annee' => $annee]) }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-plus me-1"></i>Nouveau Règlement
    </a>
    <a href="{{ route('pcs.destockages.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-list me-1"></i>Liste des Règlements
    </a>
</div>

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
            <button type="submit" class="btn btn-primary d-block w-100">
                <i class="icon-base ti tabler-filter me-1"></i>Filtrer
            </button>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card :title="'Fonds Collectés - ' . $programme . ' - ' . $moisList[$mois] . ' ' . $annee" icon="tabler-list">
    <x-slot:actions>
        <span class="badge bg-label-secondary">{{ count($collectesParPoste) }} entités</span>
    </x-slot:actions>

    @if(count($collectesParPoste) > 0)
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="icon-base ti tabler-building me-1"></i>Entité</th>
                    <th class="text-end"><i class="icon-base ti tabler-arrow-up me-1"></i>Montant Collecté</th>
                    <th class="text-end"><i class="icon-base ti tabler-arrow-down me-1"></i>Déjà Règlement</th>
                    <th class="text-end"><i class="icon-base ti tabler-scale me-1"></i>Solde Disponible</th>
                    <th class="text-center" width="80">Actions</th>
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
                            <span class="badge bg-label-{{ $collecte['type'] == 'poste' ? 'primary' : 'info' }} me-1">
                                {{ $collecte['type'] == 'poste' ? 'Poste' : 'Bureau' }}
                            </span>
                            <strong>{{ $collecte['nom'] }}</strong>
                        </td>
                        <td class="text-end fw-medium text-success">
                            {{ number_format($collecte['montant_collecte'], 0, ',', ' ') }} FCFA
                        </td>
                        <td class="text-end fw-medium text-warning">
                            {{ number_format($collecte['montant_deja_destocke'], 0, ',', ' ') }} FCFA
                        </td>
                        <td class="text-end fw-medium {{ $collecte['solde_disponible'] > 0 ? 'text-success' : 'text-body-secondary' }}">
                            {{ number_format($collecte['solde_disponible'], 0, ',', ' ') }} FCFA
                        </td>
                        <td class="text-center">
                            @if($collecte['solde_disponible'] > 0)
                                <a href="{{ route('pcs.destockages.create', ['programme' => $programme, 'mois' => $mois, 'annee' => $annee]) }}"
                                   class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                   data-bs-toggle="tooltip"
                                   title="Créer un règlement">
                                    <i class="icon-base ti tabler-cash icon-22px"></i>
                                </a>
                            @else
                                <span class="text-body-secondary small">Solde épuisé</span>
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
    <x-vuexy.alert type="info">
        Aucun fonds collecté pour cette période.
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
