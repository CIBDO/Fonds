@extends('layouts.master')

@section('title', 'État Avant Envoi')

@section('content')

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" action="{{ route('demandes-fonds.etat-avant-envoi') }}" class="row g-3 align-items-end">
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
        <div class="col-md-3 text-md-end">
            <a href="{{ route('demandes-fonds.etat-avant-envoi', array_merge(request()->all(), ['pdf' => 1])) }}"
               class="btn btn-label-secondary">
                <i class="icon-base ti tabler-file-type-pdf me-1"></i>Télécharger PDF
            </a>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="État par poste" icon="tabler-table">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Postes</th>
                    <th class="text-end">Salaire Brut (1)</th>
                    <th class="text-end">Réalisation Recettes</th>
                    <th class="text-end">Salaire Demandé (2)</th>
                    <th>Observations</th>
                </tr>
            </thead>
            <tbody>
                @forelse($demandesParPoste as $demande)
                <tr>
                    <td class="fw-medium">{{ $demande['poste'] }}</td>
                    <td class="text-end">{{ number_format($demande['salaire_brut'], 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande['realisation_recettes_douanieres'], 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande['salaire_demande'], 0, ',', ' ') }}</td>
                    <td>{{ $demande['observations'] }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-body-secondary py-4">Aucune donnée pour {{ $mois }} {{ $annee }}</td></tr>
                @endforelse
            </tbody>
            @if($demandesParPoste->count() > 0)
            <tfoot class="table-light">
                <tr>
                    <th>Total général</th>
                    <th class="text-end">{{ number_format($totalGeneral['salaire_brut'], 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($totalGeneral['realisation_recettes_douanieres'], 0, ',', ' ') }}</th>
                    <th class="text-end">{{ number_format($totalGeneral['salaire_demande'], 0, ',', ' ') }}</th>
                    <th>—</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
<script>$('#mois, #annee').on('change', function() { $(this).closest('form').submit(); });</script>
@endpush
