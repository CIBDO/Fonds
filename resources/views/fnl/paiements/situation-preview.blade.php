@extends('layouts.master')

@section('title', 'État FNL — '.$periodeLibelle)

@section('content')
@php
    $estGlobal = $estGlobal ?? false;
    $retourParams = array_filter([
        'mois' => $mois,
        'annee' => $annee,
        'poste_id' => $poste?->id,
    ], fn ($v) => $v !== null && $v !== '');
    $pdfParams = array_merge($retourParams, ['statut' => $statutFiltre ?? 'valide']);
@endphp
<div class="d-flex justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h4 class="mb-1">{{ $estGlobal ? 'Situation globale FNL' : 'Tableau de paiement FNL' }}</h4>
        <div class="text-body-secondary">{{ $intituleOfficiel }} — {{ $periodeLibelle }}</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('fnl.paiements.index', $retourParams) }}" class="btn btn-label-secondary btn-sm">
            <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
        </a>
        <a href="{{ route('fnl.paiements.situation-mensuelle', $pdfParams) }}"
           class="btn btn-primary btn-sm">
            <i class="icon-base ti tabler-download me-1"></i>Télécharger PDF
        </a>
    </div>
</div>

@if($estGlobal)
    <div class="alert alert-info mb-4">
        État consolidé de tous les postes (paiements validés uniquement).
        Filtres : année {{ $annee }}{{ $mois ? ' · mois '.$nomMois : ' · tous les mois' }}.
    </div>
@endif

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
                    <th class="text-center">Montant FCFA</th>
                    <th>Réf de Paiement</th>
                    <th>Observation</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lignes as $ligne)
                    @if(($ligne['type'] ?? '') === 'section')
                        <tr class="table-info">
                            <td colspan="5" class="fw-semibold text-center">{{ $ligne['libelle'] }}</td>
                        </tr>
                    @else
                        @php $paiementLigne = $ligne['paiement'] ?? null; @endphp
                        <tr>
                            <td>{{ $ligne['libelle'] }}</td>
                            <td class="text-center">
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
                                    @if(!$estGlobal)
                                        <a href="{{ route('fnl.paiements.edit', $paiementLigne) }}" class="btn btn-icon btn-sm btn-text-primary rounded-pill" title="Éditer">
                                            <i class="icon-base ti tabler-edit icon-22px"></i>
                                        </a>
                                    @endif
                                @elseif(auth()->user()->hasRole('tresorier') && !$estGlobal)
                                    <a href="{{ route('fnl.paiements.create') }}" class="btn btn-icon btn-sm btn-text-primary rounded-pill" title="Saisir">
                                        <i class="icon-base ti tabler-plus icon-22px"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-body-secondary py-4">
                            Aucun paiement validé pour ces filtres.
                        </td>
                    </tr>
                @endforelse
                <tr class="table-secondary fw-semibold">
                    <td>TOTAL</td>
                    <td class="text-center">{{ number_format((float) $totalRetenue, 0, ',', ' ') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
    </div>
</x-vuexy.card>
@endsection
