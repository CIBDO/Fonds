@extends('layouts.master')

@section('title', 'État FNL — '.$periodeLibelle)

@section('content')
<div class="d-flex justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h4 class="mb-1">Tableau de paiement FNL</h4>
        <div class="text-body-secondary">{{ $intituleOfficiel }} — {{ $periodeLibelle }}</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('fnl.paiements.index', ['mois' => $mois, 'annee' => $annee, 'poste_id' => $poste->id]) }}" class="btn btn-label-secondary btn-sm">
            <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
        </a>
        <a href="{{ route('fnl.paiements.situation-mensuelle', ['mois' => $mois, 'annee' => $annee, 'poste_id' => $poste->id, 'statut' => $statutFiltre]) }}"
           class="btn btn-primary btn-sm">
            <i class="icon-base ti tabler-download me-1"></i>Télécharger PDF
        </a>
    </div>
</div>

<x-vuexy.card title="Prévisualisation" icon="tabler-file-text">
    <div class="mb-3">
        <div class="fw-semibold">{{ $intituleOfficiel }}</div>
        @if($regionLibelle)
            <div class="text-body-secondary">{{ $regionLibelle }}</div>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Libellés</th>
                    <th class="text-end">Montant FCFA</th>
                    <th>Réf de Paiement</th>
                    <th>Observation</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lignes as $ligne)
                    @if(($ligne['type'] ?? '') === 'section')
                        <tr class="table-info">
                            <td colspan="5" class="fw-semibold text-center">{{ $ligne['libelle'] }}</td>
                        </tr>
                    @else
                        @php $paiementLigne = $ligne['paiement'] ?? null; @endphp
                        <tr>
                            <td>{{ $ligne['libelle'] }}</td>
                            <td class="text-end">
                                @if($ligne['montant'] !== null && $ligne['montant'] !== '')
                                    {{ number_format((float) $ligne['montant'], 0, ',', ' ') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $ligne['reference'] ?: '—' }}</td>
                            <td>{{ $ligne['observation'] ?: '—' }}</td>
                            <td class="text-end text-nowrap">
                                @if($paiementLigne)
                                    <a href="{{ route('fnl.paiements.show', $paiementLigne) }}" class="btn btn-icon btn-sm btn-text-secondary rounded-pill" title="Voir">
                                        <i class="icon-base ti tabler-eye icon-22px"></i>
                                    </a>
                                    <a href="{{ route('fnl.paiements.edit', $paiementLigne) }}" class="btn btn-icon btn-sm btn-text-primary rounded-pill" title="Éditer">
                                        <i class="icon-base ti tabler-edit icon-22px"></i>
                                    </a>
                                @elseif(auth()->user()->hasRole('tresorier'))
                                    <a href="{{ route('fnl.paiements.create') }}" class="btn btn-icon btn-sm btn-text-primary rounded-pill" title="Saisir">
                                        <i class="icon-base ti tabler-plus icon-22px"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr class="table-secondary fw-semibold">
                    <td>TOTAL</td>
                    <td class="text-end">{{ number_format((float) $totalRetenue, 0, ',', ' ') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
    </div>
</x-vuexy.card>
@endsection
