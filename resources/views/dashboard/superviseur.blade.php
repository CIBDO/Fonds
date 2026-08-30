@extends('layouts.master')

@section('title', 'Tableau de Bord Superviseur')

@section('content')
<x-vuexy.page-header
    title="DGTCP — Supervision & Contrôle"
    subtitle="Monitoring national des opérations du Trésor Public"
>
    <x-slot:actions>
        <button class="btn btn-primary" onclick="generateReport()">
            <i class="icon-base ti tabler-file-type-pdf me-1"></i>Rapport
        </button>
        <button class="btn btn-label-secondary" onclick="exportData()">
            <i class="icon-base ti tabler-download me-1"></i>Exporter
        </button>
    </x-slot:actions>
</x-vuexy.page-header>

@include('partials.dashboard.analytics-charts')

<div class="row g-6 mb-6">
    <div class="col-md-3">
        <div class="card border-start border-success border-3 h-100">
            <div class="card-body">
                <p class="text-body-secondary small mb-1">Efficacité Globale</p>
                <h3 class="mb-1">{{ $stats['efficaciteGlobale'] }}%</h3>
                <small class="text-success"><i class="icon-base ti tabler-trending-up me-1"></i>Taux de validation</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-info border-3 h-100">
            <div class="card-body">
                <p class="text-body-secondary small mb-1">Demandes Aujourd'hui</p>
                <h3 class="mb-1">{{ $stats['demandesToday'] }}</h3>
                <small class="text-body-secondary">{{ $stats['demandesEnAttente'] }} en attente</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-primary border-3 h-100">
            <div class="card-body">
                <p class="text-body-secondary small mb-1">Postes Actifs</p>
                <h3 class="mb-1">{{ $stats['postesActifs'] }}</h3>
                <small class="text-body-secondary">{{ $stats['totalDemandes'] }} demandes totales</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-warning border-3 h-100">
            <div class="card-body">
                <p class="text-body-secondary small mb-1">Taux de Conformité</p>
                <h3 class="mb-1">{{ $stats['tauxConformite'] }}%</h3>
                <small class="text-success"><i class="icon-base ti tabler-shield-check me-1"></i>Conformité opérationnelle</small>
            </div>
        </div>
    </div>
</div>

<x-vuexy.card title="Suivi Détaillé des Demandes" icon="tabler-list-details">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Poste</th>
                    <th>Période</th>
                    <th>Total Courant</th>
                    <th>Recettes</th>
                    <th>Statut</th>
                    <th>Mise à jour</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandesFonds as $demande)
                    <tr>
                        <td><strong>{{ $demande->poste->nom ?? '—' }}</strong></td>
                        <td>{{ $demande->mois }}</td>
                        <td>{{ number_format($demande->total_courant, 0, '', ' ') }} FCFA</td>
                        <td>{{ number_format($demande->montant_disponible, 0, '', ' ') }} FCFA</td>
                        <td>
                            @if($demande->status === 'approuve')
                                <span class="badge bg-label-success">Approuvé</span>
                            @elseif($demande->status === 'rejete')
                                <span class="badge bg-label-danger">Rejeté</span>
                            @else
                                <span class="badge bg-label-warning">En attente</span>
                            @endif
                        </td>
                        <td><small>{{ $demande->updated_at?->diffForHumans() }}</small></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $demandesFonds->links('custom.pagination') }}</div>
</x-vuexy.card>

<div class="row mt-4 g-3">
    <div class="col-md-3">
        <button class="btn btn-primary w-100" onclick="generateReport()">
            <i class="icon-base ti tabler-file-type-pdf me-1"></i>Générer Rapport
        </button>
    </div>
    <div class="col-md-3">
        <a href="{{ route('demandes-fonds.index') }}" class="btn btn-success w-100">
            <i class="icon-base ti tabler-circle-check me-1"></i>Valider Demandes
        </a>
    </div>
    <div class="col-md-3">
        <button class="btn btn-label-secondary w-100" onclick="exportData()">
            <i class="icon-base ti tabler-download me-1"></i>Exporter Données
        </button>
    </div>
    <div class="col-md-3">
        <button class="btn btn-outline-primary w-100" onclick="viewAnalytics()">
            <i class="icon-base ti tabler-chart-line me-1"></i>API Analytics
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateReport() { window.location.href = '{{ route("demandes-fonds.consolide") }}'; }
function validatePendingRequests() { window.location.href = '{{ route("demandes-fonds.index") }}'; }
function exportData() { window.location.href = '{{ route("demandes-fonds.consolide-detaille") }}'; }
function viewAnalytics() { window.location.href = '{{ route("demandes-fonds.consolide") }}'; }
</script>
@endpush
