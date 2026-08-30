@extends('layouts.master')

@section('title', 'Autres Demandes Financières')

@section('content')
<x-vuexy.page-header title="Autres Demandes Financières" subtitle="Gestion des autres demandes financières PCS">
    <x-slot:actions>
        <div class="btn-group" role="group">
            @if(!auth()->user()->hasRole('acct'))
            <a href="{{ route('pcs.autres-demandes.create') }}" class="btn btn-primary btn-sm">
                <i class="ti tabler-plus me-1"></i>Nouvelle Demande
            </a>
            @endif
            @if(auth()->user()->poste_id && !in_array(auth()->user()->role, ['acct', 'admin']))
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEtatConsolideAutresDemandes">
                <i class="ti tabler-file-export me-1"></i>État Consolidé
            </button>
            @endif
        </div>
    </x-slot:actions>
</x-vuexy.page-header>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Année</label>
                    <select name="annee" class="form-select">
                        <option value="">Toutes</option>
                        @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                            <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                        <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                        <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-danger d-block w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
</x-vuexy.card>

<x-vuexy.card title="Liste des Demandes" icon="tabler-list">
    <x-slot:header>
        <span class="badge bg-label-primary">{{ $demandes->total() }} demandes</span>
    </x-slot:header>
            @if($demandes->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-building"></i> Poste</th>
                            <th><i class="fas fa-tag"></i> Désignation</th>
                            <th class="text-end"><i class="fas fa-money-bill-wave"></i> Montant</th>
                            <th class="text-center"><i class="fas fa-flag"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demandes as $demande)
                        <tr>
                            <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                            <td><span class="badge bg-primary poste-badge">{{ $demande->poste->nom }}</span></td>
                            <td class="fw-bold">{{ Str::limit($demande->designation, 50) }}</td>
                            <td class="text-end">
                                <div class="fw-bold text-primary">{{ number_format($demande->montant, 0, ',', ' ') }} FCFA</div>
                                @if($demande->montant_verse > 0 || $demande->montant_accord !== null)
                                    <div class="small text-success">
                                        Versé : {{ number_format($demande->montant_verse_cumule, 0, ',', ' ') }}
                                        / {{ number_format($demande->montant_accord ?? $demande->montant, 0, ',', ' ') }} FCFA
                                    </div>
                                    @if(($demande->montant_accord ?? 0) > $demande->montant)
                                        <div class="small text-info">+{{ number_format($demande->montant_accord - $demande->montant, 0, ',', ' ') }} au-delà du demandé</div>
                                    @endif
                                    @if($demande->echelons->count() > 0)
                                        <div class="small text-muted"><i class="fas fa-calendar-alt"></i> {{ $demande->echelons->count() }} versement(s)</div>
                                    @endif
                                    @if($demande->montant_restant_accord > 0 && $demande->statut !== 'valide')
                                        <div class="small text-warning">Reste : {{ number_format($demande->montant_restant_accord, 0, ',', ' ') }} FCFA</div>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                @switch($demande->statut)
                                    @case('brouillon')
                                        <span class="badge bg-secondary">Brouillon</span>
                                        @break
                                    @case('soumis')
                                        @if($demande->estPartiellementValidee())
                                            <span class="badge bg-warning text-dark">Partiellement validé</span>
                                        @else
                                            <span class="badge bg-primary">Soumis</span>
                                        @endif
                                        @break
                                    @case('valide')
                                        <span class="badge bg-success">Validé</span>
                                        @break
                                    @case('rejete')
                                        <span class="badge bg-danger">Rejeté</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('pcs.autres-demandes.show', $demande) }}"
                                       class="btn btn-outline-info"
                                       data-bs-toggle="tooltip"
                                       title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($demande->preuve_paiement)
                                        <a href="{{ route('pcs.autres-demandes.preuve', $demande) }}"
                                           class="btn btn-outline-secondary"
                                           data-bs-toggle="tooltip"
                                           title="Télécharger la preuve de paiement"
                                           target="_blank">
                                            <i class="fas fa-paperclip"></i>
                                        </a>
                                    @endif
                                    @if(in_array($demande->statut, ['brouillon', 'soumis', 'rejete']) && $demande->saisi_par == auth()->id())
                                        <a href="{{ route('pcs.autres-demandes.edit', $demande) }}"
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if((auth()->user()->peut_valider_pcs || auth()->user()->hasRole('acct') || auth()->user()->hasRole('admin')) && $demande->peutRecevoirVersement())
                                        <button type="button"
                                                class="btn btn-outline-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#validationModal{{ $demande->id }}"
                                                title="{{ $demande->statut === 'valide' ? 'Enregistrer un versement supplémentaire' : ($demande->estPartiellementValidee() ? 'Enregistrer un versement' : 'Valider') }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    @if((auth()->user()->peut_valider_pcs || auth()->user()->hasRole('acct') || auth()->user()->hasRole('admin')) && $demande->statut == 'soumis')
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejeterModal{{ $demande->id }}"
                                                title="Rejeter la demande">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Affichage de <strong>{{ $demandes->firstItem() ?? 0 }}</strong> à <strong>{{ $demandes->lastItem() ?? 0 }}</strong>
                    sur <strong>{{ $demandes->total() }}</strong> demande(s)
                </div>
                <div>
                    @if ($demandes->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                {{-- Bouton Précédent --}}
                                @if ($demandes->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">« Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $demandes->previousPageUrl() }}" rel="prev">« Précédent</a>
                                    </li>
                                @endif

                                {{-- Numéros de page --}}
                                @foreach ($demandes->getUrlRange(1, $demandes->lastPage()) as $page => $url)
                                    @if ($page == $demandes->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Bouton Suivant --}}
                                @if ($demandes->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $demandes->nextPageUrl() }}" rel="next">Suivant »</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Suivant »</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Aucune demande trouvée. Cliquez sur "Nouvelle Demande" pour commencer.</p>
            </div>
            @endif
</x-vuexy.card>

<!-- Modales de Validation -->
@foreach($demandes as $demande)
@if($demande->peutRecevoirVersement())
@include('pcs.autres-demandes.partials.modal-validation', ['demande' => $demande, 'modalId' => 'validationModal' . $demande->id])
@endif
@if($demande->statut == 'soumis')
<!-- Modal Rejeter -->
<div class="modal fade" id="rejeterModal{{ $demande->id }}" tabindex="-1" aria-labelledby="rejeterModalLabel{{ $demande->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejeterModalLabel{{ $demande->id }}">
                    <i class="fas fa-times-circle me-2"></i>Rejeter la Demande
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-1"></i>Confirmer le Rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@if(auth()->user()->poste_id && !in_array(auth()->user()->role, ['acct','admin']))
<!-- Modal État Consolidé Poste Émetteur -->
<div class="modal fade" id="modalEtatConsolideAutresDemandes" tabindex="-1" aria-labelledby="modalEtatConsolideAutresDemandesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEtatConsolideAutresDemandesLabel">
                    <i class="fas fa-file-export me-2"></i>Générer État Consolidé
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="GET" action="{{ route('pcs.autres-demandes.etat-consolide.poste-emetteur') }}" target="_blank">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Poste émetteur :</strong> {{ auth()->user()->poste->nom }}
                    </div>
                    <div class="mb-3">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-file-pdf me-1"></i>Générer le PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@include('pcs.autres-demandes.partials.scripts-echelons-validation')
@endpush
@endsection

