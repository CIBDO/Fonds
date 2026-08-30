@extends('layouts.master')

@section('title', 'Vue Consolidée - Demandes de Fonds')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('demandes-fonds.consolide') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-refresh me-1"></i>Réinitialiser
    </a>
</div>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" action="{{ route('demandes-fonds.consolide') }}" id="filterForm">
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label for="poste" class="form-label">Poste</label>
                <select name="poste" id="poste" class="form-select">
                    <option value="">Tous les postes</option>
                    @foreach($postes as $poste)
                        <option value="{{ $poste->nom }}" {{ request('poste') == $poste->nom ? 'selected' : '' }}>{{ $poste->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="mois" class="form-label">Mois</label>
                <select name="mois" id="mois" class="form-select">
                    <option value="">Tous</option>
                    @foreach($mois as $m)
                        <option value="{{ $m }}" {{ request('mois') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="annee" class="form-label">Année</label>
                <select name="annee" id="annee" class="form-select">
                    <option value="">Toutes</option>
                    @foreach($annees as $a)
                        <option value="{{ $a }}" {{ request('annee') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Statut</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Tous</option>
                    <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="approuve" {{ request('status') == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                    <option value="rejete" {{ request('status') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="user_id" class="form-label">Trésorier</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">Tous</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label for="date_type" class="form-label">Type de date</label>
                <select name="date_type" id="date_type" class="form-select">
                    <option value="date_envois" {{ request('date_type') == 'date_envois' ? 'selected' : '' }}>Date d'envoi</option>
                    <option value="date_reception" {{ request('date_type') == 'date_reception' ? 'selected' : '' }}>Date réception</option>
                    <option value="created_at" {{ request('date_type') == 'created_at' ? 'selected' : '' }}>Date création</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_debut" class="form-label">Date début</label>
                <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-2">
                <label for="date_fin" class="form-label">Date fin</label>
                <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin') }}">
            </div>
            <div class="col-md-6 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-filter me-1"></i>Filtrer</button>
                <button type="button" class="btn btn-label-secondary" onclick="exportCSV()"><i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV</button>
                <button type="button" class="btn btn-label-secondary" onclick="exportPDF()"><i class="icon-base ti tabler-file-type-pdf me-1"></i>PDF</button>
            </div>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Résultats" icon="tabler-table">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Poste</th>
                    <th>Mois/Année</th>
                    <th class="text-end">Total Courant</th>
                    <th class="text-end">Montant Disponible</th>
                    <th class="text-end">Solde</th>
                    <th class="text-end">Montant Envoyé</th>
                    <th>Date Envoi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($demandeFonds as $demande)
                <tr>
                    <td class="fw-medium">{{ $demande->poste->nom ?? 'N/A' }}</td>
                    <td>{{ $demande->mois }} {{ $demande->annee }}</td>
                    <td class="text-end">{{ number_format($demande->total_courant, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->solde, 0, ',', ' ') }}</td>
                    <td class="text-end">
                        @if($demande->status === 'approuve' && $demande->montant)
                            {{ number_format($demande->montant, 0, ',', ' ') }}
                        @else — @endif
                    </td>
                    <td>{{ $demande->date_envois ? \Carbon\Carbon::parse($demande->date_envois)->format('d/m/Y') : '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-body-secondary py-4">
                        <i class="icon-base ti tabler-inbox icon-lg d-block mb-2"></i>
                        Aucune demande trouvée
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($demandeFonds->count() > 0)
            <tfoot class="table-light">
                <tr>
                    <th colspan="2" class="text-end">Totaux</th>
                    <th class="text-end">{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($totaux['montant_disponible'], 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($totaux['solde'], 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($totaux['montant_envoye'], 0, ',', ' ') }}</th>
                    <th></th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
        <span class="text-body-secondary small">
            {{ $demandeFonds->firstItem() ?? 0 }}–{{ $demandeFonds->lastItem() ?? 0 }} sur {{ $demandeFonds->total() }}
        </span>
        {{ $demandeFonds->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
function exportCSV() {
    const url = new URL('{{ route("demandes-fonds.consolide.export-csv") }}', window.location.origin);
    new FormData(document.getElementById('filterForm')).forEach((v, k) => { if (v) url.searchParams.append(k, v); });
    window.location.href = url.toString();
}
function exportPDF() {
    const url = new URL('{{ route("demandes-fonds.consolide.export-pdf") }}', window.location.origin);
    new FormData(document.getElementById('filterForm')).forEach((v, k) => { if (v) url.searchParams.append(k, v); });
    window.location.href = url.toString();
}
</script>
@endpush
