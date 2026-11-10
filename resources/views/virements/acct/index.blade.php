@extends('layouts.master')

@section('title', 'Virements salaires')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0"><i class="ti tabler-building-bank me-2"></i>Virements salaires</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('virements.acct.consolidation', request()->query()) }}" class="btn btn-label-primary btn-sm">Consolidation</a>
        <a href="{{ route('virements.acct.export', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="ti tabler-file-spreadsheet me-1"></i>Exporter Excel
        </a>
    </div>
</div>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" class="row g-3">
        <div class="col-md-2">
            <label class="form-label small">N° VIR</label>
            <input type="text" name="numero_vir" value="{{ request('numero_vir') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small">Poste</label>
            <select name="poste_id" class="form-select form-select-sm">
                <option value="">Tous</option>
                @foreach($postes as $poste)
                    <option value="{{ $poste->id }}" @selected(request('poste_id') == $poste->id)>{{ $poste->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Banque</label>
            <select name="banque_id" class="form-select form-select-sm">
                <option value="">Toutes</option>
                @foreach($banques as $banque)
                    <option value="{{ $banque->id }}" @selected(request('banque_id') == $banque->id)>{{ $banque->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Mois</label>
            <select name="mois" class="form-select form-select-sm">
                <option value="">Tous</option>
                @foreach(['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'] as $m)
                    <option value="{{ $m }}" @selected(request('mois') == $m)>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label small">Année</label>
            <select name="annee" class="form-select form-select-sm">
                <option value="">Toutes</option>
                @for($y = (int) date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" @selected((string) request('annee') === (string) $y)>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Statut</label>
            <select name="statut_paiement" class="form-select form-select-sm">
                <option value="">Tous</option>
                <option value="non_paye" @selected(request('statut_paiement') === 'non_paye')>Non payé</option>
                <option value="paye" @selected(request('statut_paiement') === 'paye')>Payé</option>
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button class="btn btn-primary btn-sm w-100">Filtrer</button>
        </div>
    </form>
</x-vuexy.card>

<form action="{{ route('virements.acct.marquer-paye') }}" method="POST">
    @csrf
    <x-vuexy.card title="Liste des VIR (n° VIR · poste · banque · mois · montant)" icon="tabler-list">
        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Marquer les VIR sélectionnés comme payés (exécution bancaire) ?');">
                <i class="ti tabler-check me-1"></i>Marquer payé (sélection)
            </button>
            <input type="text" name="observation" class="form-control form-control-sm" style="max-width:280px" placeholder="Observation (optionnel)">
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width:40px"><input type="checkbox" id="check-all"></th>
                        <th>N° VIR</th>
                        <th>Poste</th>
                        <th>Banque</th>
                        <th>Mois</th>
                        <th class="text-end">Montant</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($virements as $v)
                        <tr>
                            <td>
                                @if(!$v->isPaye())
                                    <input type="checkbox" name="virement_ids[]" value="{{ $v->id }}" class="vir-check">
                                @endif
                            </td>
                            <td><strong>{{ $v->numero_vir }}</strong></td>
                            <td>{{ $v->poste->nom ?? '-' }}</td>
                            <td>{{ $v->banque->nom ?? '-' }}</td>
                            <td>{{ $v->mois }} {{ $v->annee }}</td>
                            <td class="text-end">{{ number_format((float) $v->montant, 0, ',', ' ') }}</td>
                            <td>
                                @if($v->isPaye())
                                    <span class="badge bg-label-success">Payé</span>
                                @else
                                    <span class="badge bg-label-warning">Non payé</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('virements.acct.show', $v) }}" class="btn btn-sm btn-label-primary">Détail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Aucun virement soumis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $virements->links() }}
    </x-vuexy.card>
</form>

<script>
document.getElementById('check-all')?.addEventListener('change', function () {
    document.querySelectorAll('.vir-check').forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
