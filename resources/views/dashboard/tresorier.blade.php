@extends('layouts.master')

@section('title', 'Tableau de Bord Trésorier')

@section('content')
<x-vuexy.page-header
    title="Trésorerie Régionale — {{ Auth::user()->poste->nom ?? 'Mon Poste' }}"
    subtitle="Suivi en temps réel des demandes de fonds de votre poste douanier"
/>

@include('partials.dashboard.analytics-charts')

<x-vuexy.card title="Mes Demandes de Fonds" icon="tabler-building">
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <label class="form-label">Recherche</label>
            <input type="text" id="filterInput" class="form-control" placeholder="Filtrer par mois, montant...">
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <a href="{{ route('demandes-fonds.create') }}" class="btn btn-primary w-100">
                <i class="icon-base ti tabler-plus me-1"></i>Nouvelle Demande de Fonds
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="financialTable">
            <thead class="table-light">
                <tr>
                    <th>Période</th>
                    <th>Total Net</th>
                    <th>Total Courant</th>
                    <th>Recettes</th>
                    <th>Solde</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandesFonds as $demande)
                    <tr>
                        <td><strong>{{ $demande->mois }}</strong></td>
                        <td>{{ number_format($demande->total_net, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->total_courant, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->montant_disponible, 0, '', ' ') }}</td>
                        <td>{{ number_format($demande->solde, 0, '', ' ') }}</td>
                        <td>
                            @if($demande->status === 'approuve')
                                <span class="badge bg-label-success">Approuvé</span>
                            @elseif($demande->status === 'rejete')
                                <span class="badge bg-label-danger">Rejeté</span>
                            @else
                                <span class="badge bg-label-warning">En attente</span>
                            @endif
                        </td>
                        <td>
                            @if($demande->status !== 'approuve')
                                <a href="{{ route('demandes-fonds.edit', $demande->id) }}" class="btn btn-sm btn-label-primary">
                                    <i class="icon-base ti tabler-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
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
