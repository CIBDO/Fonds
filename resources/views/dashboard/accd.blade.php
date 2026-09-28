@extends('layouts.master')

@section('title', 'Tableau de Bord ACCD')

@section('content')
<x-vuexy.page-header
    title="Agence Comptable Centrale des Dépôts (ACCD)"
    subtitle="Validation des paiements FNL et supervision comptable nationale des dépôts"
/>

@include('partials.dashboard.analytics-charts-fnl')

<x-vuexy.card title="Contrôle FNL — Vue d'Ensemble des Paiements" icon="tabler-home">
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <label class="form-label">Recherche globale</label>
            <input type="text" id="filterInput" class="form-control" placeholder="Filtrer par poste, montant...">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <a href="{{ route('fnl.paiements.index') }}" class="btn btn-success w-100">
                <i class="icon-base ti tabler-circle-check me-1"></i>Valider les Paiements FNL
            </a>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <a href="{{ route('fnl.paiements.situation-mensuelle') }}" class="btn btn-label-secondary w-100">
                <i class="icon-base ti tabler-printer me-1"></i>Situation Mensuelle FNL
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-label-success text-center p-3">
                <div class="h3 mb-1">{{ $paiementsFnl->where('statut', 'valide')->count() }}</div>
                <small>Validés</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-label-warning text-center p-3">
                <div class="h3 mb-1">{{ $paiementsFnl->where('statut', 'soumis')->count() }}</div>
                <small>Soumis</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-label-danger text-center p-3">
                <div class="h3 mb-1">{{ $paiementsFnl->where('statut', 'rejete')->count() }}</div>
                <small>Rejetés</small>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="financialTable">
            <thead class="table-light">
                <tr>
                    <th>Poste</th>
                    <th>Retenue FNL</th>
                    <th>Référence</th>
                    <th>Période</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paiementsFnl as $paiement)
                    <tr>
                        <td><strong>{{ $paiement->poste->nom ?? '—' }}</strong></td>
                        <td>{{ number_format($paiement->retenue_fnl, 0, '', ' ') }}</td>
                        <td>{{ $paiement->reference_paiement ?? '—' }}</td>
                        <td>{{ $paiement->nom_mois }} {{ $paiement->annee }}</td>
                        <td>
                            @if($paiement->statut === 'valide')
                                <span class="badge bg-label-success">Validé</span>
                            @elseif($paiement->statut === 'rejete')
                                <span class="badge bg-label-danger">Rejeté</span>
                            @else
                                <span class="badge bg-label-warning">Soumis</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
        <small class="text-body-secondary">
            Conformité : {{ $paiementsFnl->count() > 0 ? round(($paiementsFnl->where('statut', 'valide')->count() / $paiementsFnl->count()) * 100, 1) : 0 }}%
        </small>
        {{ $paiementsFnl->links('custom.pagination') }}
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
