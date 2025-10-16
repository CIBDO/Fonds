<div class="apercu-container">
    @if($demandes->count() > 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-1"></i>
            <strong>{{ $demandes->count() }}</strong> demande(s) trouvée(s) avec les critères sélectionnés
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Poste</th>
                        <th>Désignation</th>
                        <th>Montant Demandé</th>
                        <th>Montant Accordé</th>
                        <th>Statut</th>
                        <th>Année</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandes as $demande)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">
                                {{ \Carbon\Carbon::parse($demande->date_demande)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary poste-badge">
                                {{ $demande->poste->nom }}
                            </span>
                        </td>
                        <td>
                            <div class="designation-cell">
                                <strong>{{ Str::limit($demande->designation, 40) }}</strong>
                                @if($demande->observation)
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-sticky-note me-1"></i>
                                        {{ Str::limit($demande->observation, 50) }}
                                    </small>
                                @endif
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-primary">
                                {{ number_format($demande->montant, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td class="text-end">
                            @if($demande->montant_accord !== null)
                                @if($demande->montant_accord == $demande->montant)
                                    <span class="fw-bold text-success">
                                        {{ number_format($demande->montant_accord, 0, ',', ' ') }} FCFA
                                    </span>
                                    <br><small class="text-success">100% accordé</small>
                                @elseif($demande->montant_accord > $demande->montant)
                                    <span class="fw-bold text-warning">
                                        {{ number_format($demande->montant_accord, 0, ',', ' ') }} FCFA
                                    </span>
                                    <br><small class="text-warning">
                                        +{{ number_format($demande->montant_accord - $demande->montant, 0, ',', ' ') }} FCFA
                                    </small>
                                @else
                                    <span class="fw-bold text-danger">
                                        {{ number_format($demande->montant_accord, 0, ',', ' ') }} FCFA
                                    </span>
                                    <br><small class="text-danger">
                                        -{{ number_format($demande->montant - $demande->montant_accord, 0, ',', ' ') }} FCFA
                                    </small>
                                @endif
                            @else
                                <span class="text-muted">Non accordé</span>
                            @endif
                        </td>
                        <td>
                            @switch($demande->statut)
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
                                    <span class="badge bg-secondary">{{ ucfirst($demande->statut) }}</span>
                            @endswitch
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ $demande->annee }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($demandes->count() >= 50)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                <strong>Note :</strong> Seules les 50 premières demandes sont affichées dans l'aperçu.
                L'état PDF complet contiendra toutes les données correspondant aux critères.
            </div>
        @endif

        <!-- Résumé des données -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">{{ $demandes->count() }}</h5>
                        <p class="card-text">Total demandes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">{{ number_format($demandes->sum('montant'), 0, ',', ' ') }}</h5>
                        <p class="card-text">Montant total demandé (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info">{{ number_format($demandes->where('statut', 'valide')->sum('montant_accord') ?? 0, 0, ',', ' ') }}</h5>
                        <p class="card-text">Montant total accordé (FCFA)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">
                            {{ $demandes->where('statut', 'valide')->count() > 0 ?
                                round(($demandes->where('statut', 'valide')->sum('montant_accord') ?? 0) / $demandes->sum('montant') * 100, 1) : 0 }}%
                        </h5>
                        <p class="card-text">Taux d'accord</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h5>Aucune demande trouvée</h5>
            <p class="mb-0">Aucune autre demande ne correspond aux critères de filtrage sélectionnés.</p>
        </div>
    @endif
</div>

<style>
.apercu-container {
    max-height: 600px;
    overflow-y: auto;
}

.designation-cell {
    max-width: 300px;
    word-wrap: break-word;
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
