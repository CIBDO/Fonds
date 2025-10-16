@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-chart-pie me-2"></i>Statistiques Autres Demandes
                    </h3>
                    <p class="text-muted mb-0">Vue d'ensemble par poste pour l'année {{ $annee }}</p>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Formulaire de filtrage -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pcs.autres-demandes.statistiques') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="annee" class="form-label">Année</label>
                    <select name="annee" id="annee" class="form-select">
                        @for ($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-8 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('pcs.autres-demandes.statistiques') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques globales -->
    @php
        $totalDemandes = $stats->sum('nombre');
        $montantTotal = $stats->sum('total_montant');
        $montantAccordeTotal = $stats->sum('total_montant_accord');
    @endphp

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Total Demandes Validées</h5>
                            <h2 class="mb-0">{{ $totalDemandes }}</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-file-alt opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Montant Demandé</h5>
                            <h2 class="mb-0">{{ number_format($montantTotal, 0, ',', ' ') }} FCFA</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-hand-holding-usd opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">Montant Accordé</h5>
                            <h2 class="mb-0">{{ number_format($montantAccordeTotal, 0, ',', ' ') }} FCFA</h2>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-money-bill-wave opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des statistiques par poste -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-table me-2"></i>Statistiques par Poste
            </h5>
        </div>
        <div class="card-body">
            @if ($stats->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="statsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 25%;">Poste</th>
                                <th style="width: 12%;" class="text-center">Nombre</th>
                                <th style="width: 18%;" class="text-end">Montant Demandé</th>
                                <th style="width: 18%;" class="text-end">Montant Accordé</th>
                                <th style="width: 12%;" class="text-center">Différence</th>
                                <th style="width: 10%;" class="text-center">% Accordé</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stats as $index => $stat)
                                @php
                                    $montantAccorde = $stat->total_montant_accord ?? 0;
                                    $difference = $montantAccorde - $stat->total_montant;
                                    $pourcentageAccorde = $stat->total_montant > 0 ? ($montantAccorde / $stat->total_montant) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $stat->poste->nom ?? 'N/A' }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $stat->nombre }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">{{ number_format($stat->total_montant, 0, ',', ' ') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($montantAccorde, 0, ',', ' ') }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($difference != 0)
                                            <span class="badge {{ $difference > 0 ? 'bg-warning' : 'bg-info' }}">
                                                {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 0, ',', ' ') }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $pourcentageAccorde >= 100 ? 'bg-success' : ($pourcentageAccorde >= 80 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($pourcentageAccorde, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <th colspan="2" class="text-end">TOTAL</th>
                                <th class="text-center">
                                    <span class="badge bg-dark">{{ $totalDemandes }}</span>
                                </th>
                                <th class="text-end">
                                    <strong class="text-primary">{{ number_format($montantTotal, 0, ',', ' ') }}</strong>
                                </th>
                                <th class="text-end">
                                    <strong class="text-success">{{ number_format($montantAccordeTotal, 0, ',', ' ') }}</strong>
                                </th>
                                <th class="text-center">
                                    @php
                                        $totalDifference = $montantAccordeTotal - $montantTotal;
                                    @endphp
                                    <span class="badge {{ $totalDifference > 0 ? 'bg-warning' : 'bg-info' }}">
                                        {{ $totalDifference > 0 ? '+' : '' }}{{ number_format($totalDifference, 0, ',', ' ') }}
                                    </span>
                                </th>
                                <th class="text-center">
                                    @php
                                        $pourcentageGlobal = $montantTotal > 0 ? ($montantAccordeTotal / $montantTotal) * 100 : 0;
                                    @endphp
                                    <span class="badge bg-success">{{ number_format($pourcentageGlobal, 1) }}%</span>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Graphique -->
                <div class="mt-4">
                    <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Répartition par Poste</h5>
                    <canvas id="statsChart" style="max-height: 400px;"></canvas>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucune demande validée pour l'année {{ $annee }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if ($stats->count() > 0)
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statsChart').getContext('2d');

        const labels = @json($stats->pluck('poste.nom'));
        const montantDemande = @json($stats->pluck('total_montant'));
        const montantAccorde = @json($stats->pluck('total_montant_accord'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Montant Demandé',
                    data: montantDemande,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2
                }, {
                    label: 'Montant Accordé',
                    data: montantAccorde,
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Comparaison Montants Demandés vs Accordés - Année {{ $annee }}'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('fr-FR') + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
    });
    @endif
</script>
@endpush
@endsection

