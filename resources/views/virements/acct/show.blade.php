@extends('layouts.master')

@section('title', 'Détail VIR '.$virement->numero_vir)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">VIR {{ $virement->numero_vir }}</h4>
    <a href="{{ route('virements.acct.index') }}" class="btn btn-label-secondary btn-sm">Retour</a>
</div>

<x-vuexy.card title="Informations capitales" icon="tabler-info-circle" class="mb-4">
    <div class="row g-3">
        <div class="col-md-2"><div class="text-muted small">N° VIR</div><strong>{{ $virement->numero_vir }}</strong></div>
        <div class="col-md-3"><div class="text-muted small">Poste</div><strong>{{ $virement->poste->nom ?? '-' }}</strong></div>
        <div class="col-md-3"><div class="text-muted small">Banque</div><strong>{{ $virement->banque->nom ?? '-' }}</strong></div>
        <div class="col-md-2"><div class="text-muted small">Mois</div><strong>{{ $virement->mois }} {{ $virement->annee }}</strong></div>
        <div class="col-md-2"><div class="text-muted small">Montant</div><strong>{{ number_format((float) $virement->montant, 0, ',', ' ') }} FCFA</strong></div>
    </div>
    <div class="mt-3">
        Statut :
        @if($virement->isPaye())
            <span class="badge bg-label-success">Payé</span>
            <span class="text-muted small ms-2">{{ optional($virement->date_paiement)->format('d/m/Y H:i') }}
                @if($virement->payePar) par {{ $virement->payePar->name }} @endif
            </span>
        @else
            <span class="badge bg-label-warning">Non payé</span>
            <form action="{{ route('virements.acct.marquer-paye') }}" method="POST" class="d-inline ms-2">
                @csrf
                <input type="hidden" name="virement_ids[]" value="{{ $virement->id }}">
                <button class="btn btn-sm btn-success" onclick="return confirm('Confirmer le paiement bancaire de ce VIR ?');">Marquer payé</button>
            </form>
        @endif
    </div>
</x-vuexy.card>

<x-vuexy.card title="Lignes détail du bordereau (BE / Mdt / Prog)" icon="tabler-list-details">
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>N° BE</th><th>N° Mdt</th><th>Ord.</th><th>Programme</th><th>Créancier</th><th>N° Compte</th><th class="text-end">Montant</th>
                </tr>
            </thead>
            <tbody>
                @forelse($virement->lignes as $ligne)
                    <tr>
                        <td>{{ $ligne->num_be }}</td>
                        <td>{{ $ligne->num_mdt }}</td>
                        <td>{{ $ligne->ord }}</td>
                        <td>{{ $ligne->programme }}</td>
                        <td>{{ $ligne->creancier_libelle }}</td>
                        <td>{{ $ligne->num_compte }}</td>
                        <td class="text-end">{{ number_format((float) $ligne->montant, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Pas de lignes détail</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-end">Total lignes</th>
                    <th class="text-end">{{ number_format($virement->lignes->sum('montant'), 0, ',', ' ') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</x-vuexy.card>
@endsection
