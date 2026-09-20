@extends('layouts.master')

@section('title', 'Autres Demandes Financières')

@section('content')
<div class="d-flex justify-content-end flex-wrap gap-2 mb-4">
    @if(!auth()->user()->hasRole('acct'))
    <a href="{{ route('pcs.autres-demandes.create') }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-plus me-1"></i>Nouvelle Demande
    </a>
    @endif
    @if(auth()->user()->poste_id && !in_array(auth()->user()->role, ['acct', 'admin']))
    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEtatConsolideAutresDemandes">
        <i class="icon-base ti tabler-file-export me-1"></i>État Consolidé
    </button>
    @endif
</div>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" class="row g-3">
        @if($estValideurOuAcct && $postes->count() > 1)
        <div class="col-md-3">
            <label class="form-label fw-bold">Poste</label>
            <select name="poste_id" class="form-select">
                <option value="">Tous les postes</option>
                @foreach($postes as $poste)
                    <option value="{{ $poste->id }}" {{ request('poste_id') == $poste->id ? 'selected' : '' }}>
                        {{ $poste->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        @else
        <div class="col-md-3">
            <label class="form-label fw-bold">Poste</label>
            <input type="text" class="form-control" value="{{ $postes->first()->nom ?? 'N/A' }}" disabled>
        </div>
        @endif
        <div class="col-md-3">
            <label class="form-label fw-bold">Année</label>
            <select name="annee" class="form-select">
                <option value="">Toutes</option>
                @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                    <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <button type="submit" class="btn btn-primary d-block w-100">
                <i class="icon-base ti tabler-filter me-1"></i>Filtrer
            </button>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Liste des Demandes" icon="tabler-list">
    <x-slot:actions>
        <span class="badge bg-label-secondary">{{ $demandes->total() }} demandes</span>
    </x-slot:actions>

    @if($demandes->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="icon-base ti tabler-calendar me-1"></i>Date</th>
                    <th><i class="icon-base ti tabler-building me-1"></i>Poste</th>
                    <th><i class="icon-base ti tabler-tag me-1"></i>Désignation</th>
                    <th class="text-end"><i class="icon-base ti tabler-currency-franc me-1"></i>Montant</th>
                    <th class="text-center"><i class="icon-base ti tabler-flag me-1"></i>Statut</th>
                    <th class="text-center" width="170">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandes as $demande)
                <tr>
                    <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                    <td><span class="badge bg-label-primary">{{ $demande->poste->nom }}</span></td>
                    <td class="fw-medium">{{ Str::limit($demande->designation, 50) }}</td>
                    <td class="text-end">
                        <div class="fw-medium text-primary">{{ number_format($demande->montant, 0, ',', ' ') }} FCFA</div>
                        @if($demande->montant_verse > 0 || $demande->montant_accord !== null)
                            <div class="small text-success">
                                Versé : {{ number_format($demande->montant_verse_cumule, 0, ',', ' ') }}
                                / {{ number_format($demande->montant_accord ?? $demande->montant, 0, ',', ' ') }} FCFA
                            </div>
                            @if(($demande->montant_accord ?? 0) > $demande->montant)
                                <div class="small text-info">+{{ number_format($demande->montant_accord - $demande->montant, 0, ',', ' ') }} au-delà du demandé</div>
                            @endif
                            @if($demande->echelons->count() > 0)
                                <div class="small text-body-secondary"><i class="icon-base ti tabler-calendar-event me-1"></i>{{ $demande->echelons->count() }} versement(s)</div>
                            @endif
                            @if($demande->montant_restant_accord > 0 && $demande->statut !== 'valide')
                                <div class="small text-warning">Reste : {{ number_format($demande->montant_restant_accord, 0, ',', ' ') }} FCFA</div>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">
                        @include('partials.pcs.status-badge', [
                            'statut' => $demande->statut,
                            'partiel' => $demande->estPartiellementValidee(),
                        ])
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <a href="{{ route('pcs.autres-demandes.show', $demande) }}"
                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                               data-bs-toggle="tooltip"
                               title="Détails">
                                <i class="icon-base ti tabler-eye icon-22px"></i>
                            </a>
                            <a href="{{ route('pcs.autres-demandes.etat', $demande) }}"
                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                               data-bs-toggle="tooltip"
                               title="Télécharger l'état PDF"
                               target="_blank">
                                <i class="icon-base ti tabler-file-type-pdf icon-22px"></i>
                            </a>
                            @if($demande->preuve_paiement)
                                <a href="{{ route('pcs.autres-demandes.preuve', $demande) }}"
                                   class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                   data-bs-toggle="tooltip"
                                   title="Télécharger la preuve de paiement"
                                   target="_blank">
                                    <i class="icon-base ti tabler-paperclip icon-22px"></i>
                                </a>
                            @endif
                            @if(in_array($demande->statut, ['brouillon', 'soumis', 'rejete']) && $demande->saisi_par == auth()->id())
                                <a href="{{ route('pcs.autres-demandes.edit', $demande) }}"
                                   class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                   data-bs-toggle="tooltip"
                                   title="Modifier">
                                    <i class="icon-base ti tabler-edit icon-22px"></i>
                                </a>
                            @endif
                            @if((auth()->user()->peut_valider_pcs || auth()->user()->hasRole('acct') || auth()->user()->hasRole('admin')) && $demande->peutRecevoirVersement())
                                <button type="button"
                                        class="btn btn-icon btn-sm btn-text-success rounded-pill"
                                        data-bs-toggle="modal"
                                        data-bs-target="#validationModal{{ $demande->id }}"
                                        title="{{ $demande->statut === 'valide' ? 'Enregistrer un versement supplémentaire' : ($demande->estPartiellementValidee() ? 'Enregistrer un versement' : 'Valider') }}">
                                    <i class="icon-base ti tabler-check icon-22px"></i>
                                </button>
                            @endif
                            @if((auth()->user()->peut_valider_pcs || auth()->user()->hasRole('acct') || auth()->user()->hasRole('admin')) && $demande->statut == 'soumis')
                                <button type="button"
                                        class="btn btn-icon btn-sm btn-text-danger rounded-pill"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejeterModal{{ $demande->id }}"
                                        title="Rejeter la demande">
                                    <i class="icon-base ti tabler-x icon-22px"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
        <div class="text-body-secondary small">
            Affichage de <strong>{{ $demandes->firstItem() ?? 0 }}</strong> à <strong>{{ $demandes->lastItem() ?? 0 }}</strong>
            sur <strong>{{ $demandes->total() }}</strong> demande(s)
        </div>
        <div>
            @if ($demandes->hasPages())
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        @if ($demandes->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">« Précédent</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $demandes->previousPageUrl() }}" rel="prev">« Précédent</a></li>
                        @endif
                        @foreach ($demandes->getUrlRange(1, $demandes->lastPage()) as $page => $url)
                            @if ($page == $demandes->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                        @if ($demandes->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $demandes->nextPageUrl() }}" rel="next">Suivant »</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Suivant »</span></li>
                        @endif
                    </ul>
                </nav>
            @endif
        </div>
    </div>
    @else
    <x-vuexy.alert type="info">
        Aucune demande trouvée. Cliquez sur « Nouvelle Demande » pour commencer.
    </x-vuexy.alert>
    @endif
</x-vuexy.card>

@foreach($demandes as $demande)
@if($demande->peutRecevoirVersement())
@include('pcs.autres-demandes.partials.modal-validation', ['demande' => $demande, 'modalId' => 'validationModal' . $demande->id])
@endif
@if($demande->statut == 'soumis')
<div class="modal fade" id="rejeterModal{{ $demande->id }}" tabindex="-1" aria-labelledby="rejeterModalLabel{{ $demande->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejeterModalLabel{{ $demande->id }}">
                    <i class="icon-base ti tabler-circle-x me-2 text-danger"></i>Rejeter la Demande
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('pcs.autres-demandes.rejeter', $demande) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">
                        <strong>Poste :</strong> {{ $demande->poste->nom }} —
                        <strong>Montant :</strong> {{ number_format($demande->montant, 0, ',', ' ') }} FCFA
                    </p>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea name="motif_rejet" class="form-control" rows="4" required
                                  placeholder="Expliquez la raison du rejet (minimum 10 caractères)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        <i class="icon-base ti tabler-x me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="icon-base ti tabler-circle-x me-1"></i>Confirmer le Rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@if(auth()->user()->poste_id && !in_array(auth()->user()->role, ['acct','admin']))
<div class="modal fade" id="modalEtatConsolideAutresDemandes" tabindex="-1" aria-labelledby="modalEtatConsolideAutresDemandesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEtatConsolideAutresDemandesLabel">
                    <i class="icon-base ti tabler-file-export me-2"></i>Générer État Consolidé
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="GET" action="{{ route('pcs.autres-demandes.etat-consolide.poste-emetteur') }}" target="_blank">
                <div class="modal-body">
                    <x-vuexy.alert type="info" class="mb-3">
                        <strong>Poste émetteur :</strong> {{ auth()->user()->poste->nom }}
                    </x-vuexy.alert>
                    <div class="mb-0">
                        <label for="annee_etat_ad" class="form-label fw-bold">
                            Année <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="annee_etat_ad" name="annee" required>
                            @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        <i class="icon-base ti tabler-x me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-file-type-pdf me-1"></i>Générer le PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
</script>
@include('pcs.autres-demandes.partials.scripts-echelons-validation')
@endpush
@endsection
