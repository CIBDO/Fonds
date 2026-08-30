@php
    $modalId = $modalId ?? 'validationModal' . $demande->id;
    $montantDemande = (float) $demande->montant;
    $montantPlafond = $demande->montant_accord !== null ? (float) $demande->montant_accord : $montantDemande;
    $montantVerse = (float) $demande->montant_verse;
    $montantRestant = max(0, $montantPlafond - $montantVerse);
    $estValidee = $demande->statut === 'valide';
    $plafondMin = max($montantVerse, 0.01);
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content d-flex flex-column" style="max-height: 90vh;">
            <div class="modal-header flex-shrink-0">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="icon-base ti tabler-circle-check me-2 text-success"></i>
                    @if($estValidee)
                        Versement supplémentaire
                    @elseif($montantVerse > 0)
                        Enregistrer un versement
                    @else
                        Valider la Demande
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('pcs.autres-demandes.valider', $demande) }}" method="POST" class="form-validation-versement d-flex flex-column flex-grow-1 overflow-hidden">
                @csrf
                <div class="modal-body overflow-auto flex-grow-1">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Poste :</strong> {{ $demande->poste->nom }}
                        </div>
                        <div class="col-md-6">
                            <strong>Date demande :</strong> {{ $demande->date_demande->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Désignation :</strong>
                        <p class="text-body-secondary mb-0">{{ $demande->designation }}</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="card bg-label-primary h-100 mb-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="card-title text-primary small mb-1">Montant demandé</h6>
                                    <div class="fw-bold text-primary">{{ number_format($montantDemande, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-label-success h-100 mb-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="card-title text-success small mb-1">Déjà versé</h6>
                                    <div class="fw-bold text-success">{{ number_format($montantVerse, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-label-warning h-100 mb-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="card-title text-warning small mb-1">
                                        @if($estValidee && $montantRestant <= 0)
                                            Complément possible
                                        @else
                                            Reste à verser
                                        @endif
                                    </h6>
                                    <div class="fw-bold text-warning montant-restant-display">
                                        @if($estValidee && $montantRestant <= 0)
                                            —
                                        @else
                                            {{ number_format($montantRestant, 0, ',', ' ') }} FCFA
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant total à accorder</label>
                        <div class="input-group">
                            <input type="number"
                                   class="form-control montant-plafond-input"
                                   name="montant_plafond"
                                   value="{{ old('montant_plafond', $montantPlafond) }}"
                                   step="0.01"
                                   min="{{ $plafondMin }}"
                                   data-montant-verse="{{ $montantVerse }}"
                                   required>
                            <span class="input-group-text">FCFA</span>
                        </div>
                        <small class="text-body-secondary">
                            Vous pouvez accorder un montant supérieur au montant demandé ({{ number_format($montantDemande, 0, ',', ' ') }} FCFA).
                            @if($montantVerse > 0)
                                Le plafond ne peut pas être inférieur au total déjà versé.
                            @endif
                        </small>
                    </div>

                    @if($demande->echelons->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="text-success"><i class="icon-base ti tabler-history me-1"></i>Versements déjà enregistrés</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>N°</th>
                                        <th>Date</th>
                                        <th class="text-end">Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demande->echelons as $echelon)
                                    <tr>
                                        <td>{{ $echelon->ordre }}</td>
                                        <td>{{ $echelon->date_echeance->format('d/m/Y') }}</td>
                                        <td class="text-end">{{ number_format($echelon->montant, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <div class="border rounded p-3 bg-label-secondary">
                        <h6 class="text-success mb-3">
                            <i class="icon-base ti tabler-currency-franc me-1"></i>
                            @if($estValidee)
                                Nouveau versement supplémentaire
                            @elseif($montantVerse > 0)
                                Nouveau versement (avance ou solde)
                            @else
                                Versement
                            @endif
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Montant de ce versement <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number"
                                           class="form-control montant-versement-input"
                                           name="montant_versement"
                                           value="{{ old('montant_versement', (!$estValidee && $montantRestant > 0) ? $montantRestant : '') }}"
                                           step="0.01"
                                           min="0.01"
                                           data-montant-restant="{{ $montantRestant }}"
                                           data-deja-verse="{{ $montantVerse }}"
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @if(!$estValidee && $montantRestant > 0)
                                <div class="form-check mt-2">
                                    <input class="form-check-input btn-verser-total" type="checkbox" id="verser_total_{{ $modalId }}">
                                    <label class="form-check-label small" for="verser_total_{{ $modalId }}">
                                        Verser tout le montant restant
                                    </label>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date du versement <span class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control"
                                       name="date_versement"
                                       value="{{ old('date_versement', now()->format('Y-m-d')) }}"
                                       required>
                            </div>
                        </div>
                    </div>

                    @if($demande->observation)
                    <div class="mt-3">
                        <strong>Observation :</strong>
                        <p class="text-body-secondary mb-0">{{ $demande->observation }}</p>
                    </div>
                    @endif

                    <x-vuexy.alert type="info" class="mt-3 mb-0 small">
                        Vous pouvez accorder <strong>plus que le montant demandé</strong>, verser par <strong>avances partielles</strong>,
                        ou ajouter des <strong>versements supplémentaires</strong> même après validation complète.
                    </x-vuexy.alert>
                </div>

                <div class="modal-footer flex-shrink-0 border-top">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        <i class="icon-base ti tabler-x me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="icon-base ti tabler-check me-1"></i>
                        @if($estValidee)
                            Enregistrer le complément
                        @elseif($montantVerse > 0)
                            Enregistrer le versement
                        @else
                            Valider
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
