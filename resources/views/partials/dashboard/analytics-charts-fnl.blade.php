{{-- Section KPI avec sparklines --}}
<div class="row g-6 mb-6">
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-1 text-body-secondary small">Retenues FNL Totales</p>
                        <h4 class="mb-0">{{ number_format($analytics['kpis']['fonds_demandes'], 0, '', ' ') }}</h4>
                        <small class="text-{{ $analytics['kpis']['evolution_mensuelle'] >= 0 ? 'success' : 'danger' }}">
                            <i class="icon-base ti tabler-arrow-{{ $analytics['kpis']['evolution_mensuelle'] >= 0 ? 'up' : 'down' }} me-1"></i>
                            {{ abs($analytics['kpis']['evolution_mensuelle']) }}% ce mois
                        </small>
                    </div>
                    <span class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-home"></i></span></span>
                </div>
                <div id="sparkDemandes"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-1 text-body-secondary small">Retenues Validées</p>
                        <h4 class="mb-0">{{ number_format($analytics['kpis']['fonds_recettes'], 0, '', ' ') }}</h4>
                        <small class="text-body-secondary">{{ $analytics['kpis']['total'] }} paiement(s) au total</small>
                    </div>
                    <span class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-building-bank"></i></span></span>
                </div>
                <div id="sparkRecettes"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-1 text-body-secondary small">En Attente</p>
                        <h4 class="mb-0">{{ number_format($analytics['kpis']['fonds_en_cours'], 0, '', ' ') }}</h4>
                        <small class="text-warning">{{ $analytics['kpis']['en_attente'] }} paiement(s)</small>
                    </div>
                    <span class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-danger"><i class="icon-base ti tabler-clock"></i></span></span>
                </div>
                <div id="sparkEnCours"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-1 text-body-secondary small">Validés / Encaissés</p>
                        <h4 class="mb-0">{{ number_format($analytics['kpis']['paiements_effectues'], 0, '', ' ') }}</h4>
                        <small class="text-success">{{ $analytics['kpis']['taux_validation'] }}% taux validation</small>
                    </div>
                    <span class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-circle-check"></i></span></span>
                </div>
                <div id="sparkValidations"></div>
            </div>
        </div>
    </div>
</div>

{{-- Alertes dynamiques --}}
@foreach($analytics['alertes'] as $alerte)
    <x-vuexy.alert :type="$alerte['type']">{{ $alerte['message'] }}</x-vuexy.alert>
@endforeach

{{-- Graphiques principaux --}}
<div class="row g-6 mb-6">
    <div class="col-xxl-8 col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1"><i class="icon-base ti tabler-chart-area me-2"></i>Évolution des Retenues FNL</h5>
                    <small class="text-body-secondary">12 derniers mois — montants et validations</small>
                </div>
                <span class="badge bg-label-primary">Temps réel</span>
            </div>
            <div class="card-body">
                <div id="chartEvolution"></div>
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-1"><i class="icon-base ti tabler-chart-donut me-2"></i>Répartition par Statut</h5>
                <small class="text-body-secondary">Soumis · Validé · Rejeté</small>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div id="chartStatus" class="w-100"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-6 mb-6">
    @if(!empty($analytics['topPostes']['labels']))
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-1"><i class="icon-base ti tabler-building me-2"></i>Performance par Poste</h5>
                <small class="text-body-secondary">Top postes — retenues vs validations (M FCFA)</small>
            </div>
            <div class="card-body">
                <div id="chartTopPostes"></div>
            </div>
        </div>
    </div>
    @else
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-1"><i class="icon-base ti tabler-arrows-diff me-2"></i>Retenues vs Validations</h5>
                <small class="text-body-secondary">Comparaison mensuelle et écart</small>
            </div>
            <div class="card-body">
                <div id="chartCompare"></div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-1"><i class="icon-base ti tabler-gauge me-2"></i>Indicateur de Performance</h5>
            </div>
            <div class="card-body">
                <div id="chartGauge"></div>
                <div class="row text-center mt-2 g-2">
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="fw-bold text-success">{{ $analytics['kpis']['approuve'] }}</div>
                            <small class="text-body-secondary">Validés</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="fw-bold text-warning">{{ $analytics['kpis']['en_attente'] }}</div>
                            <small class="text-body-secondary">En attente</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="fw-bold text-danger">{{ $analytics['kpis']['rejete'] }}</div>
                            <small class="text-body-secondary">Rejetés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-6 mb-6">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-1"><i class="icon-base ti tabler-chart-radar me-2"></i>Répartition par Poste</h5>
                <small class="text-body-secondary">Montants FNL par catégorie (M FCFA)</small>
            </div>
            <div class="card-body">
                <div id="chartCategories"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-1"><i class="icon-base ti tabler-chart-bar me-2"></i>Validations Mensuelles</h5>
            </div>
            <div class="card-body">
                <div id="chartValidations"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h5 class="mb-1"><i class="icon-base ti tabler-activity me-2"></i>Activité Récente</h5>
                    <small class="text-body-secondary">Dernières opérations</small>
                </div>
                <span class="badge bg-label-info">{{ count($analytics['activities']) }}</span>
            </div>
            <div class="card-body p-0" style="max-height: 340px; overflow-y: auto;">
                <ul class="list-group list-group-flush">
                    @forelse($analytics['activities'] as $activity)
                        <li class="list-group-item">
                            <div class="d-flex align-items-start gap-3">
                                <span class="avatar avatar-sm flex-shrink-0">
                                    <span class="avatar-initial rounded bg-label-{{ $activity['color'] }}">
                                        <i class="icon-base ti {{ $activity['icon'] }}"></i>
                                    </span>
                                </span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 small">{{ $activity['label'] }}</h6>
                                    <small class="text-body-secondary d-block">{{ $activity['detail'] }}</small>
                                    <small class="text-body-secondary">{{ $activity['time'] }}</small>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-body-secondary py-4">Aucune activité récente</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

@push('vendor-style')
@include('partials.vuexy.apexcharts-assets')
@endpush

@push('scripts')
<script>window.dgtcpDashboard = @json($analytics);</script>
<script src="{{ asset('vuexy/assets/js/dgtcp-dashboard.js') }}?v=1"></script>
@endpush
