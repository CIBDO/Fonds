@extends('layouts.master')

@section('title', 'Statistiques Autres Demandes PCS')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('pcs.autres-demandes.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<x-vuexy.card title="Filtrage" icon="tabler-filter" class="mb-4">
    <form method="GET" action="{{ route('pcs.autres-demandes.statistiques') }}" class="row g-3">
        <div class="col-md-4">
            <label for="annee" class="form-label fw-bold">Année</label>
            <select name="annee" id="annee" class="form-select">
                @for ($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-8 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-filter me-1"></i>Filtrer
            </button>
            <a href="{{ route('pcs.autres-demandes.statistiques') }}" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-refresh me-1"></i>Réinitialiser
            </a>
        </div>
    </form>
</x-vuexy.card>

@php
    $totalDemandes = $stats->sum('nombre');
    $montantTotal = (float) $stats->sum('total_montant');
    $montantAccordeTotal = (float) $stats->sum('total_montant_accord');
    $pourcentageGlobal = $montantTotal > 0 ? ($montantAccordeTotal / $montantTotal) * 100 : 0;
    $differenceGlobale = $montantAccordeTotal - $montantTotal;
@endphp

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-start border-primary border-3">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div class="min-w-0">
                        <p class="text-body-secondary small mb-1">Demandes validées</p>
                        <h3 class="mb-1 text-primary">{{ number_format($totalDemandes, 0, ',', ' ') }}</h3>
                        <small class="text-body-secondary">Année {{ $annee }}</small>
                    </div>
                    <span class="avatar avatar-lg flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="icon-base ti tabler-file-check icon-28px"></i>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-start border-info border-3">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div class="min-w-0">
                        <p class="text-body-secondary small mb-1">Montant demandé</p>
                        <h3 class="mb-1 text-info text-break" style="font-size: clamp(1.1rem, 2.5vw, 1.5rem);">
                            {{ number_format($montantTotal, 0, ',', ' ') }}
                            <small class="fs-6 fw-normal">FCFA</small>
                        </h3>
                        <small class="text-body-secondary">Total des demandes validées</small>
                    </div>
                    <span class="avatar avatar-lg flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="icon-base ti tabler-hand-stop icon-28px"></i>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-start border-success border-3">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div class="min-w-0">
                        <p class="text-body-secondary small mb-1">Montant accordé</p>
                        <h3 class="mb-1 text-success text-break" style="font-size: clamp(1.1rem, 2.5vw, 1.5rem);">
                            {{ number_format($montantAccordeTotal, 0, ',', ' ') }}
                            <small class="fs-6 fw-normal">FCFA</small>
                        </h3>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                            <span class="badge bg-label-{{ $pourcentageGlobal >= 100 ? 'success' : ($pourcentageGlobal >= 80 ? 'warning' : 'danger') }}">
                                {{ number_format($pourcentageGlobal, 1) }}% du demandé
                            </span>
                            @if($differenceGlobale != 0)
                                <small class="text-body-secondary">
                                    {{ $differenceGlobale > 0 ? '+' : '' }}{{ number_format($differenceGlobale, 0, ',', ' ') }} FCFA
                                </small>
                            @endif
                        </div>
                    </div>
                    <span class="avatar avatar-lg flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="icon-base ti tabler-cash icon-28px"></i>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<x-vuexy.card title="Statistiques par Poste" icon="tabler-table">
    @if ($stats->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="statsTable">
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
                            <td class="fw-medium">{{ $stat->poste->nom ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-info">{{ $stat->nombre }}</span>
                            </td>
                            <td class="text-end fw-medium text-primary">{{ number_format($stat->total_montant, 0, ',', ' ') }}</td>
                            <td class="text-end fw-medium text-success">{{ number_format($montantAccorde, 0, ',', ' ') }}</td>
                            <td class="text-center">
                                @if($difference != 0)
                                    <span class="badge {{ $difference > 0 ? 'bg-label-warning' : 'bg-label-info' }}">
                                        {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 0, ',', ' ') }}
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $pourcentageAccorde >= 100 ? 'bg-label-success' : ($pourcentageAccorde >= 80 ? 'bg-label-warning' : 'bg-label-danger') }}">
                                    {{ number_format($pourcentageAccorde, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="text-end">TOTAL</th>
                        <th class="text-center">
                            <span class="badge bg-label-secondary">{{ $totalDemandes }}</span>
                        </th>
                        <th class="text-end fw-bold text-primary">{{ number_format($montantTotal, 0, ',', ' ') }}</th>
                        <th class="text-end fw-bold text-success">{{ number_format($montantAccordeTotal, 0, ',', ' ') }}</th>
                        <th class="text-center">
                            @php $totalDifference = $montantAccordeTotal - $montantTotal; @endphp
                            <span class="badge {{ $totalDifference > 0 ? 'bg-label-warning' : 'bg-label-info' }}">
                                {{ $totalDifference > 0 ? '+' : '' }}{{ number_format($totalDifference, 0, ',', ' ') }}
                            </span>
                        </th>
                        <th class="text-center">
                            @php $pourcentageGlobal = $montantTotal > 0 ? ($montantAccordeTotal / $montantTotal) * 100 : 0; @endphp
                            <span class="badge bg-label-success">{{ number_format($pourcentageGlobal, 1) }}%</span>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-4">
            <h5 class="mb-3"><i class="icon-base ti tabler-chart-bar me-2"></i>Répartition par Poste</h5>
            <canvas id="statsChart" style="max-height: 400px;"></canvas>
        </div>
    @else
        <x-vuexy.alert type="info">
            Aucune demande validée pour l'année {{ $annee }}
        </x-vuexy.alert>
    @endif
</x-vuexy.card>
@endsection

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
                    legend: { display: true, position: 'top' },
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
