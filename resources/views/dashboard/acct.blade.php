@extends('layouts.master')

@section('title', 'Tableau de Bord ACCT')

@section('content')
<x-vuexy.page-header
    title="Agence Comptable Centrale du Trésor (ACCT)"
    subtitle="Validation des demandes de fonds et supervision comptable nationale"
/>

@include('partials.dashboard.analytics-charts')

<x-vuexy.card title="Contrôle Comptable — Vue d'Ensemble des Postes" icon="tabler-calculator">
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <label class="form-label">Recherche globale</label>
            <input type="text" id="filterInput" class="form-control" placeholder="Filtrer par poste, montant...">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <a href="{{ route('demandes-fonds.envois') }}" class="btn btn-success w-100">
                <i class="icon-base ti tabler-circle-check me-1"></i>Valider les Demandes
            </a>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <a href="{{ route('demandes-fonds.situation-mensuelle') }}" class="btn btn-label-secondary w-100">
                <i class="icon-base ti tabler-printer me-1"></i>Situation Mensuelle
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-label-success text-center p-3">
                <div class="h3 mb-1">{{ $demandesFonds->where('status', 'approuve')->count() }}</div>
                <small>Validées</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-label-warning text-center p-3">
                <div class="h3 mb-1">{{ $demandesFonds->where('status', 'en_attente')->count() }}</div>
                <small>En attente</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-label-danger text-center p-3">
                <div class="h3 mb-1">{{ $demandesFonds->where('status', 'rejete')->count() }}</div>
                <small>Rejetées</small>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="financialTable">
            <thead class="table-light">
                <tr>
                    <th>Poste</th>
                    <th>Total Net</th>
                    <th>Total Revers</th>
                    <th>Total Courant</th>
                    <th>Total Ancien</th>
                    <th>Période</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandesFonds as $demande)
                    <tr>
                        <td><strong>{{ $demande->poste->nom ?? '—' }}</strong></td>
                        <td>{{ number_format($demande->total_net, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->total_revers, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->total_courant, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->total_ancien, 0, '', ' ') }}</td>
                        <td>{{ $demande->mois }}</td>
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
            Conformité : {{ $demandesFonds->count() > 0 ? round(($demandesFonds->where('status', 'approuve')->count() / $demandesFonds->count()) * 100, 1) : 0 }}%
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
