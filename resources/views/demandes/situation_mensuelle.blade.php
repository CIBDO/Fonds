@extends('layouts.master')

@section('title', 'Situation Mensuelle des Demandes')

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="icon-base ti tabler-alert-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" action="{{ route('demandes-fonds.situation-mensuelle') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="mois" class="form-label">Mois</label>
            <select name="mois" id="mois" class="form-select">
                @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $m)
                    <option value="{{ $m }}" {{ $mois == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="annee" class="form-label">Année</label>
            <select name="annee" id="annee" class="form-select">
                @for($i = 2020; $i <= 2030; $i++)
                    <option value="{{ $i }}" {{ $annee == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-search me-1"></i>Filtrer</button>
        </div>
        <div class="col-md-3 d-flex gap-2 justify-content-md-end">
            @if($demandesParPoste->count() > 0)
            <a href="{{ route('demandes-fonds.situation-mensuelle', array_merge(request()->all(), ['print' => 1])) }}"
               class="btn btn-label-secondary" target="_blank">
                <i class="icon-base ti tabler-printer me-1"></i>Imprimer
            </a>
            <a href="{{ route('demandes-fonds.situation-mensuelle', array_merge(request()->all(), ['pdf' => 1])) }}"
               class="btn btn-label-secondary">
                <i class="icon-base ti tabler-file-type-pdf me-1"></i>PDF
            </a>
            @else
            <button type="button" class="btn btn-label-secondary" disabled title="Aucune demande validée">
                <i class="icon-base ti tabler-printer me-1"></i>Imprimer
            </button>
            <button type="button" class="btn btn-label-secondary" disabled title="Aucune demande validée">
                <i class="icon-base ti tabler-file-type-pdf me-1"></i>PDF
            </button>
            @endif
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Situation mensuelle" icon="tabler-table">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Postes</th>
                    <th class="text-end">Salaire Brut (1)</th>
                    <th class="text-end">Réalisation Recettes</th>
                    <th class="text-end">Salaire Demandé (2)</th>
                    <th class="text-end">Salaire Envoyé</th>
                    <th>Observations</th>
                </tr>
            </thead>
            <tbody>
                @php $totalSalaireDemandeAjuste = 0; @endphp
                @forelse($demandesParPoste as $demande)
                @php
                    $montantDisponible = $demande['montant_disponible'] ?? 0;
                    $salaireBrut = $demande['salaire_brut'] ?? 0;
                    $salaireDemandeAffiche = ($montantDisponible > $salaireBrut) ? 0 : max(0, $salaireBrut - $montantDisponible);
                    $totalSalaireDemandeAjuste += $salaireDemandeAffiche;
                @endphp
                <tr>
                    <td class="fw-medium">{{ $demande['poste'] }}</td>
                    <td class="text-end">{{ number_format($demande['salaire_brut'], 0, ',', ' ') }}</td>
                    <td class="text-end">{{ ($demande['montant_disponible'] > 0) ? number_format($demande['montant_disponible'], 0, ',', ' ') : '—' }}</td>
                    <td class="text-end">{{ number_format($salaireDemandeAffiche, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ $demande['montant'] !== null ? number_format($demande['montant'], 0, ',', ' ') : '—' }}</td>
                    <td>—</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-body-secondary py-4">Aucune demande validée pour {{ $mois }} {{ $annee }}. Validez les envois avant de consulter ou imprimer la situation.</td></tr>
                @endforelse
            </tbody>
            @if($demandesParPoste->count() > 0)
            <tfoot class="table-light">
                <tr>
                    <th>Total général</th>
                    <th class="text-end">{{ number_format($totalGeneral['salaire_brut'], 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($totalGeneral['montant_disponible'] ?? 0, 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($totalSalaireDemandeAjuste, 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($totalGeneral['montant'] ?? 0, 0, ',', ' ') }}</th>
                    <th>—</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
$('#mois, #annee').on('change', function() { $(this).closest('form').submit(); });
</script>
@endpush
