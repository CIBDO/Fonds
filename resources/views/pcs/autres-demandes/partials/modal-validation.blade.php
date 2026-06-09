@php
    $modalId = $modalId ?? 'validationModal' . $demande->id;
    $montantDemande = (float) $demande->montant;
    $montantPlafond = $demande->montant_accord !== null ? (float) $demande->montant_accord : $montantDemande;
    $montantVerse = (float) $demande->montant_verse;
    $montantRestant = max(0, $montantPlafond - $montantVerse);
    $plafondVerrouille = $demande->montant_accord !== null;
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content d-flex flex-column" style="max-height: 90vh;">
            <div class="modal-header bg-success text-white flex-shrink-0">
                <h5 class="modal-title" id="{{ $modalId }}Label">
                    <i class="fas fa-check-circle me-2"></i>
                    @if($montantVerse > 0)
                        Enregistrer un versement
                    @else
                        Valider la Demande
                    @endif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <p class="text-muted mb-0">{{ $demande->designation }}</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="card bg-light h-100 mb-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="card-title text-primary small mb-1">Montant demandé</h6>
                                    <div class="fw-bold text-primary">{{ number_format($montantDemande, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light h-100 mb-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="card-title text-success small mb-1">Déjà versé</h6>
                                    <div class="fw-bold text-success">{{ number_format($montantVerse, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning h-100 mb-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="card-title text-warning small mb-1">Reste à verser</h6>
                                    <div class="fw-bold text-warning montant-restant-display">{{ number_format($montantRestant, 0, ',', ' ') }} FCFA</div>
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
                                   min="0.01"
                                   {{ $plafondVerrouille ? 'readonly' : '' }}
                                   required>
                            <span class="input-group-text">FCFA</span>
                        </div>
                        @if($plafondVerrouille)
                            <small class="text-muted">Plafond fixé lors du premier versement.</small>
                        @endif
                    </div>

                    @if($demande->echelons->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="text-success"><i class="fas fa-history me-1"></i>Versements déjà enregistrés</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
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

                    <div class="border rounded p-3 bg-light">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-money-bill-wave me-1"></i>
                            @if($montantVerse > 0)
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
                                           value="{{ old('montant_versement', $montantRestant > 0 ? $montantRestant : '') }}"
                                           step="0.01"
                                           min="0.01"
                                           max="{{ $montantRestant > 0 ? $montantRestant : $montantPlafond }}"
                                           data-montant-restant="{{ $montantRestant }}"
                                           required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input btn-verser-total" type="checkbox" id="verser_total_{{ $modalId }}">
                                    <label class="form-check-label small" for="verser_total_{{ $modalId }}">
                                        Verser tout le montant restant
                                    </label>
                                </div>
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
                        <p class="text-muted mb-0">{{ $demande->observation }}</p>
                    </div>
                    @endif

                    <div class="alert alert-info mt-3 mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Vous pouvez accorder une <strong>avance</strong> inférieure au montant demandé et enregistrer le solde plus tard. La demande sera entièrement validée lorsque le total versé atteindra le montant accordé.
                    </div>
                </div>

                <div class="modal-footer flex-shrink-0 border-top bg-white">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>
                        @if($montantVerse > 0)
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
