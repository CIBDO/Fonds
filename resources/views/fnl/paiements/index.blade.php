@extends('layouts.master')

@section('title', 'Paiements FNL')

@section('content')
<div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
    @if(auth()->user()->hasRole('tresorier') && auth()->user()->poste_id)
        <a href="{{ route('fnl.paiements.create') }}" class="btn btn-primary btn-sm">
            <i class="icon-base ti tabler-plus me-1"></i>Nouveau paiement
        </a>
    @endif
</div>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" action="{{ route('fnl.paiements.index') }}" class="row g-3 align-items-end">
        @if(auth()->user()->hasAnyRole(['accd', 'admin']))
        <div class="col-lg">
            <label class="form-label">Poste / RP</label>
            <select name="poste_id" class="form-select">
                <option value="">Tous les postes</option>
                @foreach($postes as $poste)
                    <option value="{{ $poste->id }}" {{ request('poste_id') == $poste->id ? 'selected' : '' }}>
                        {{ $poste->libelleAffiche() }}@if($poste->isRp() && $poste->parent) — {{ $poste->parent->nom }}@endif
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-lg-2 col-md-3">
            <label class="form-label">Mois</label>
            <select name="mois" class="form-select">
                <option value="">Tous les mois</option>
                @foreach($moisList as $moisNum => $moisNom)
                    <option value="{{ $moisNum }}" {{ request('mois') == $moisNum ? 'selected' : '' }}>{{ $moisNom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-3">
            <label class="form-label">Année</label>
            <select name="annee" class="form-select">
                <option value="">Toutes</option>
                @foreach($annees as $annee)
                    <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-3">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
            </select>
        </div>
        <div class="col-lg-auto">
            <div class="d-flex flex-nowrap align-items-center gap-2">
                <button type="submit" class="btn btn-primary text-nowrap">
                    <i class="icon-base ti tabler-filter me-1"></i>Filtrer
                </button>
                <button type="submit" class="btn btn-label-secondary text-nowrap" formaction="{{ route('fnl.paiements.situation-mensuelle') }}" formtarget="_blank"
                        title="Année obligatoire. Sans poste = état global. Sans mois = tous les mois."
                        onclick="this.form.preview && (this.form.preview.value=''); this.form.querySelector('[name=preview]')?.remove();">
                    <i class="icon-base ti tabler-printer me-1"></i>PDF
                </button>
                <button type="submit" name="preview" value="1" class="btn btn-label-secondary text-nowrap" formaction="{{ route('fnl.paiements.situation-mensuelle') }}"
                        title="Année obligatoire. Sans poste = état global. Sans mois = tous les mois.">
                    <i class="icon-base ti tabler-eye me-1"></i>État
                </button>
                @if(auth()->user()->hasAnyRole(['accd', 'admin']))
                    <button type="submit" class="btn btn-success text-nowrap" formaction="{{ route('fnl.paiements.export-excel') }}">
                        <i class="icon-base ti tabler-file-spreadsheet me-1"></i>Excel
                    </button>
                @endif
            </div>
        </div>
    </form>
</x-vuexy.card>

@if(!empty($reconstitution))
    <div class="alert alert-primary d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4" role="status">
        <div>
            <div class="fw-semibold">Total reconstitué — {{ $reconstitution['periode'] }}</div>
            <div class="small mb-0">
                Somme des paiements validés
                @if($reconstitution['poste'])
                    pour {{ $reconstitution['poste']->libelleAffiche() }}
                @endif
                ({{ $reconstitution['nb'] }} virement{{ $reconstitution['nb'] > 1 ? 's' : '' }})
            </div>
        </div>
        <div class="fs-5 fw-semibold text-nowrap">
            {{ number_format($reconstitution['montant'], 0, ',', ' ') }} FCFA
        </div>
    </div>
@endif

<x-vuexy.card title="Paiements FNL" icon="tabler-home">
    <x-slot:header>
        <span class="badge bg-label-primary">{{ $paiements->total() }} paiement(s)</span>
    </x-slot:header>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Période</th>
                    <th>Poste / RP</th>
                    <th>Libellé</th>
                    <th class="text-end">Retenue FNL</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $paiement)
                <tr>
                    <td class="fw-medium">{{ $paiement->nom_mois }} {{ $paiement->annee }}</td>
                    <td>
                        {{ $paiement->poste?->libelleAffiche() ?? '—' }}
                        @if($paiement->poste?->isRp())
                            <span class="badge bg-label-info ms-1">RP</span>
                        @endif
                    </td>
                    <td>{{ $paiement->libelle_affiche }}</td>
                    <td class="text-end">{{ number_format($paiement->retenue_fnl, 0, ',', ' ') }}</td>
                    <td>
                        @if($paiement->statut === 'valide')
                            <span class="badge bg-label-success">Validé</span>
                        @elseif($paiement->statut === 'rejete')
                            <span class="badge bg-label-danger">Rejeté</span>
                        @else
                            <span class="badge bg-label-warning">Soumis</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        @if(auth()->user()->hasAnyRole(['accd', 'admin']) && $paiement->statut === 'soumis')
                            <form method="POST" action="{{ route('fnl.paiements.valider', $paiement) }}" class="d-inline">
                                @csrf
                                <button type="submit"
                                        class="btn btn-icon btn-sm btn-text-success rounded-pill"
                                        data-bs-toggle="tooltip"
                                        title="Valider">
                                    <i class="icon-base ti tabler-check icon-22px"></i>
                                </button>
                            </form>
                            <button type="button"
                                    class="btn btn-icon btn-sm btn-text-danger rounded-pill"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRejetFnl"
                                    data-action="{{ route('fnl.paiements.rejeter', $paiement) }}"
                                    data-rejet-id="{{ $paiement->id }}"
                                    data-label="{{ $paiement->nom_mois }} {{ $paiement->annee }} — {{ $paiement->libelle_affiche }}"
                                    title="Rejeter">
                                <i class="icon-base ti tabler-x icon-22px"></i>
                            </button>
                        @endif
                        <a href="{{ route('fnl.paiements.show', $paiement) }}"
                           class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                           data-bs-toggle="tooltip"
                           title="Consulter">
                            <i class="icon-base ti tabler-eye icon-22px"></i>
                        </a>
                        @php
                            $posteEtatId = auth()->user()->isRecettePerception()
                                ? auth()->user()->poste_id
                                : ($paiement->poste?->isRp()
                                    ? ($paiement->poste->parent_id ?? $paiement->poste_id)
                                    : $paiement->poste_id);
                        @endphp
                        <a href="{{ route('fnl.paiements.situation-mensuelle', ['mois' => $paiement->mois, 'annee' => $paiement->annee, 'poste_id' => $posteEtatId, 'preview' => 1]) }}"
                           class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                           data-bs-toggle="tooltip"
                           title="Voir l'état">
                            <i class="icon-base ti tabler-file-text icon-22px"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-body-secondary py-4">Aucun paiement FNL pour ces critères.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $paiements->links('custom.pagination') }}
    </div>
</x-vuexy.card>

@if(auth()->user()->hasAnyRole(['accd', 'admin']))
<div class="modal fade" id="modalRejetFnl" tabindex="-1" aria-labelledby="modalRejetFnlLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formRejetFnl" class="modal-content">
            @csrf
            <input type="hidden" name="_paiement_id" id="rejetPaiementId" value="{{ old('_paiement_id') }}">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRejetFnlLabel">Rejeter le paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="text-body-secondary mb-3" id="rejetFnlLibelle"></p>
                <label class="form-label" for="motif_rejet_liste">Motif du rejet</label>
                <textarea name="motif_rejet" id="motif_rejet_liste" class="form-control" rows="3" required>{{ old('motif_rejet') }}</textarea>
                @error('motif_rejet')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger">Rejeter</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@if(auth()->user()->hasAnyRole(['accd', 'admin']))
@push('scripts')
<script>
document.getElementById('modalRejetFnl')?.addEventListener('show.bs.modal', function (event) {
    const bouton = event.relatedTarget;
    if (!bouton) return;
    document.getElementById('formRejetFnl').action = bouton.dataset.action;
    document.getElementById('rejetPaiementId').value = bouton.dataset.rejetId;
    document.getElementById('rejetFnlLibelle').textContent = bouton.dataset.label || '';
});

@if($errors->has('motif_rejet') && old('_paiement_id'))
document.addEventListener('DOMContentLoaded', function () {
    const bouton = document.querySelector('[data-rejet-id="{{ old('_paiement_id') }}"]');
    const modal = document.getElementById('modalRejetFnl');
    if (!bouton || !modal) return;
    document.getElementById('formRejetFnl').action = bouton.dataset.action;
    document.getElementById('rejetPaiementId').value = bouton.dataset.rejetId;
    document.getElementById('rejetFnlLibelle').textContent = bouton.dataset.label || '';
    bootstrap.Modal.getOrCreateInstance(modal).show();
});
@endif
</script>
@endpush
@endif
