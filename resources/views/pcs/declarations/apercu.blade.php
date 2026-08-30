<div class="apercu-container" style="max-height: 600px; overflow-y: auto;">
    @if($declarations->count() > 0)
        <div class="alert alert-info">
            <i class="icon-base ti tabler-info-circle me-1"></i>
            <strong>{{ $declarations->count() }}</strong> déclaration(s) trouvée(s) avec les critères sélectionnés
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Programme</th>
                        <th>Mois</th>
                        <th>Poste/Bureau</th>
                        <th class="text-end">Montant Recouvrement</th>
                        <th class="text-end">Montant Reversement</th>
                        <th>Statut</th>
                        <th class="text-center">Preuve</th>
                        <th>Année</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($declarations as $declaration)
                    <tr>
                        <td>
                            <span class="badge bg-label-secondary">
                                {{ $declaration->created_at->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $declaration->programme == 'UEMOA' ? 'success' : 'warning' }}">
                                {{ $declaration->programme }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-label-info">
                                {{ \Carbon\Carbon::create()->month((int)$declaration->mois)->translatedFormat('F') }}
                            </span>
                        </td>
                        <td>
                            @if($declaration->poste_id)
                                <span class="badge bg-label-primary">
                                    {{ $declaration->poste->nom }}
                                </span>
                            @else
                                <span class="badge bg-label-info">
                                    {{ $declaration->bureauDouane->libelle }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="fw-medium text-success">
                                {{ number_format($declaration->montant_recouvrement, 0, ',', ' ') }} <small class="text-body-secondary">FCFA</small>
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-medium text-primary">
                                {{ number_format($declaration->montant_reversement, 0, ',', ' ') }} <small class="text-body-secondary">FCFA</small>
                            </span>
                        </td>
                        <td>
                            @switch($declaration->statut)
                                @case('brouillon')
                                    <span class="badge bg-label-secondary">
                                        <i class="icon-base ti tabler-pencil me-1"></i>Brouillon
                                    </span>
                                    @break
                                @case('soumis')
                                    <span class="badge bg-label-warning">
                                        <i class="icon-base ti tabler-clock me-1"></i>Soumis
                                    </span>
                                    @break
                                @case('valide')
                                    <span class="badge bg-label-success">
                                        <i class="icon-base ti tabler-circle-check me-1"></i>Validé
                                    </span>
                                    @break
                                @case('rejete')
                                    <span class="badge bg-label-danger">
                                        <i class="icon-base ti tabler-circle-x me-1"></i>Rejeté
                                    </span>
                                    @break
                                @default
                                    <span class="badge bg-label-secondary">{{ ucfirst($declaration->statut) }}</span>
                            @endswitch
                        </td>
                        <td class="text-center">
                            @if($declaration->preuve_paiement)
                                <a href="{{ route('pcs.declarations.preuve', $declaration) }}"
                                   class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                   target="_blank"
                                   data-bs-toggle="tooltip"
                                   title="Voir la preuve de paiement">
                                    <i class="icon-base ti tabler-paperclip icon-22px"></i>
                                </a>
                            @else
                                <span class="text-body-secondary">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-info">{{ $declaration->annee }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($declarations->count() >= 50)
            <div class="alert alert-warning">
                <i class="icon-base ti tabler-alert-triangle me-1"></i>
                <strong>Note :</strong> Seules les 50 premières déclarations sont affichées dans l'aperçu.
                L'état PDF complet contiendra toutes les données correspondant aux critères.
            </div>
        @endif

        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <h5 class="text-primary mb-1">{{ $declarations->count() }}</h5>
                    <p class="text-body-secondary small mb-0">Total déclarations</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <h5 class="text-success mb-1">{{ number_format($declarations->sum('montant_recouvrement'), 0, ',', ' ') }}</h5>
                    <p class="text-body-secondary small mb-0">Total recouvrements (FCFA)</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <h5 class="text-info mb-1">{{ number_format($declarations->sum('montant_reversement'), 0, ',', ' ') }}</h5>
                    <p class="text-body-secondary small mb-0">Total reversements (FCFA)</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <h5 class="text-warning mb-1">{{ $declarations->where('programme', 'UEMOA')->count() }}</h5>
                    <p class="text-body-secondary small mb-0">UEMOA</p>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center">
            <i class="icon-base ti tabler-alert-triangle icon-lg mb-2 d-block"></i>
            <h5>Aucune déclaration trouvée</h5>
            <p class="mb-0">Aucune déclaration PCS ne correspond aux critères de filtrage sélectionnés.</p>
        </div>
    @endif
</div>
