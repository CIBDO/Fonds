@extends('layouts.master')

@section('title', 'Tableau de Bord Administrateur')

@section('content')
<x-vuexy.page-header
    title="Tableau de Bord Administrateur"
    subtitle="Direction Générale du Trésor et de la Comptabilité Publique — Vue consolidée nationale"
/>

@include('partials.dashboard.analytics-charts')

<x-vuexy.card title="Situation Financière Détaillée — Trésor Public" icon="tabler-table">
    <div class="row mb-4">
        <div class="col-md-6">
            <label class="form-label">Recherche rapide</label>
            <input type="text" id="filterInput" class="form-control" placeholder="Filtrer par mois, poste, montant...">
        </div>
        <div class="col-md-6 d-flex align-items-end gap-2 justify-content-md-end">
            <a href="{{ route('demandes-fonds.create') }}" class="btn btn-primary">
                <i class="icon-base ti tabler-plus me-1"></i>Nouvelle demande
            </a>
            <a href="{{ route('demandes-fonds.consolide') }}" class="btn btn-label-secondary">
                <i class="icon-base ti tabler-chart-bar me-1"></i>Rapports
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="financialTable">
            <thead class="table-light">
                <tr>
                    <th>Période</th>
                    <th>Total Net</th>
                    <th>Total Revers</th>
                    <th>Total Courant</th>
                    <th>Total Ancien</th>
                    <th>Poste</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandesFonds as $demande)
                    <tr>
                        <td><strong>{{ $demande->mois }}</strong></td>
                        <td><span class="badge bg-label-primary">{{ number_format($demande->total_net, 0, '', ' ') }}</span></td>
                        <td><span class="badge bg-label-info">{{ number_format($demande->total_revers, 0, '', ' ') }}</span></td>
                        <td><span class="badge bg-label-warning">{{ number_format($demande->total_courant, 0, '', ' ') }}</span></td>
                        <td><span class="badge bg-label-secondary">{{ number_format($demande->total_ancien, 0, '', ' ') }}</span></td>
                        <td><strong>{{ $demande->poste->nom ?? '—' }}</strong></td>
                        <td>
                            @if($demande->status === 'approuve')
                                <span class="badge bg-label-success">Approuvé</span>
                            @elseif($demande->status === 'rejete')
                                <span class="badge bg-label-danger">Rejeté</span>
                            @else
                                <span class="badge bg-label-warning">En attente</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
        <small class="text-body-secondary">
            {{ $demandesFonds->firstItem() ?? 0 }}–{{ $demandesFonds->lastItem() ?? 0 }} sur {{ $demandesFonds->total() }}
        </small>
        {{ $demandesFonds->links('custom.pagination') }}
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterInput = document.getElementById('filterInput');
    if (!filterInput) return;
    filterInput.addEventListener('keyup', function() {
        const filter = this.value.toUpperCase();
        document.querySelectorAll('#financialTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toUpperCase().includes(filter) ? '' : 'none';
        });
    });
});
</script>
@endpush
