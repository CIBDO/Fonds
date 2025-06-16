@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-file-alt"></i> État des Demandes de Fonds Salaires (Avant Envoi)
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('demandes-fonds.etat-avant-envoi') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="mois" class="form-label">Mois :</label>
                                <select name="mois" id="mois" class="form-select">
                                    <option value="Janvier" {{ $mois == 'Janvier' ? 'selected' : '' }}>Janvier</option>
                                    <option value="Février" {{ $mois == 'Février' ? 'selected' : '' }}>Février</option>
                                    <option value="Mars" {{ $mois == 'Mars' ? 'selected' : '' }}>Mars</option>
                                    <option value="Avril" {{ $mois == 'Avril' ? 'selected' : '' }}>Avril</option>
                                    <option value="Mai" {{ $mois == 'Mai' ? 'selected' : '' }}>Mai</option>
                                    <option value="Juin" {{ $mois == 'Juin' ? 'selected' : '' }}>Juin</option>
                                    <option value="Juillet" {{ $mois == 'Juillet' ? 'selected' : '' }}>Juillet</option>
                                    <option value="Août" {{ $mois == 'Août' ? 'selected' : '' }}>Août</option>
                                    <option value="Septembre" {{ $mois == 'Septembre' ? 'selected' : '' }}>Septembre</option>
                                    <option value="Octobre" {{ $mois == 'Octobre' ? 'selected' : '' }}>Octobre</option>
                                    <option value="Novembre" {{ $mois == 'Novembre' ? 'selected' : '' }}>Novembre</option>
                                    <option value="Décembre" {{ $mois == 'Décembre' ? 'selected' : '' }}>Décembre</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="annee" class="form-label">Année :</label>
                                <select name="annee" id="annee" class="form-select">
                                    @for($i = 2020; $i <= 2030; $i++)
                                        <option value="{{ $i }}" {{ $annee == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filtrer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Boutons d'action -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                           {{--  <a href="{{ route('demandes-fonds.etat-avant-envoi', array_merge(request()->all(), ['print' => 1])) }}"
                               class="btn btn-info me-2" target="_blank">
                                <i class="fas fa-print"></i> Imprimer
                            </a> --}}
                            <a href="{{ route('demandes-fonds.etat-avant-envoi', array_merge(request()->all(), ['pdf' => 1])) }}"
                               class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Télécharger PDF
                            </a>
                        </div>
                    </div>

                    <!-- Aperçu du tableau -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>POSTES</th>
                                    <th>SALAIRE BRUT (1)</th>
                                    <th>RÉALISATION RECETTES DOUANIÈRES 3=(1-2)</th>
                                    <th>SALAIRE DEMANDÉ (2)</th>
                                    <th>OBSERVATIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demandesParPoste as $demande)
                                <tr>
                                    <td><strong>{{ $demande['poste'] }}</strong></td>
                                    <td class="text-end">{{ number_format($demande['salaire_brut'], 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($demande['realisation_recettes_douanieres'], 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($demande['salaire_demande'], 0, ',', ' ') }}</td>
                                    <td>{{ $demande['observations'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Aucune donnée disponible pour {{ $mois }} {{ $annee }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($demandesParPoste->count() > 0)
                            <tfoot class="table-secondary">
                                <tr>
                                    <th><strong>TOTAL GÉNÉRAL</strong></th>
                                    <th class="text-end"><strong>{{ number_format($totalGeneral['salaire_brut'], 0, ',', ' ') }}</strong></th>
                                    <th class="text-end"><strong>{{ number_format($totalGeneral['realisation_recettes_douanieres'], 0, ',', ' ') }}</strong></th>
                                    <th class="text-end"><strong>{{ number_format($totalGeneral['salaire_demande'], 0, ',', ' ') }}</strong></th>
                                    <th>-</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('#mois, #annee').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endsection
