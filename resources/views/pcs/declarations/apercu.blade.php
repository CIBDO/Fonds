<div class="apercu-container">
    @if($declarations->count() > 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-1"></i>
            <strong>{{ $declarations->count() }}</strong> déclaration(s) trouvée(s) avec les critères sélectionnés
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Programme</th>
                        <th>Mois</th>
                        <th>Poste/Bureau</th>
                        <th>Montant Recouvrement</th>
                        <th>Montant Reversement</th>
                        <th>Statut</th>
                        <th>Année</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($declarations as $declaration)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $declaration->created_at->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $declaration->programme == 'UEMOA' ? 'success' : 'warning' }} programme-badge">
                                {{ $declaration->programme }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ \Carbon\Carbon::create()->month((int)$declaration->mois)->translatedFormat('F') }}
                            </span>
                        </td>
                        <td>
                            @if($declaration->poste_id)
                                <span class="badge bg-primary poste-badge">
                                    {{ $declaration->poste->nom }}
                                </span>
                            @else
                                <span class="badge bg-info bureau-badge">
                                    {{ $declaration->bureauDouane->libelle }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-success">
                                {{ number_format($declaration->montant_recouvrement, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-primary">
                                {{ number_format($declaration->montant_reversement, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td>
                            @switch($declaration->statut)
                                @case('brouillon')
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-pencil-alt me-1"></i>Brouillon
                                    </span>
                                    @break
                                @case('soumis')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Soumis
                                    </span>
                                    @break
                                @case('valide')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Validé
                                    </span>
                                    @break
                                @case('rejete')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Rejeté
                                    </span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($declaration->statut) }}</span>
                            @endswitch
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ $declaration->annee }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($declarations->count() >= 50)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                <strong>Note :</strong> Seules les 50 premières déclarations sont affichées dans l'aperçu.
                L'état PDF complet contiendra toutes les données correspondant aux critères.
            </div>
        @endif

        <!-- Résumé des données -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">{{ $declarations->count() }}</h5>
                        <p class="card-text">Total déclarations</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">{{ number_format($declarations->sum('montant_recouvrement'), 0, ',', ' ') }}</h5>
                        <p class="card-text">Total recouvrements (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info">{{ number_format($declarations->sum('montant_reversement'), 0, ',', ' ') }}</h5>
                        <p class="card-text">Total reversements (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">
                            {{ $declarations->where('programme', 'UEMOA')->count() }}
                        </h5>
                        <p class="card-text">UEMOA</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h5>Aucune déclaration trouvée</h5>
            <p class="mb-0">Aucune déclaration PCS ne correspond aux critères de filtrage sélectionnés.</p>
        </div>
    @endif
</div>

<style>
.apercu-container {
    max-height: 600px;
    overflow-y: auto;
}

.table th {
    position: sticky;
    top: 0;
    background-color: #343a40;
    z-index: 10;
}

.badge {
    font-size: 0.8rem;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.alert {
    border-radius: 8px;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,0.02);
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.1);
}
</style>
