<div class="apercu-container">
    @if($demandes->count() > 0)
        <x-vuexy.alert type="info">
            <i class="icon-base ti tabler-info-circle me-1"></i>
            <strong>{{ $demandes->count() }}</strong> demande(s) trouvée(s) avec les critères sélectionnés
        </x-vuexy.alert>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Poste</th>
                        <th>Désignation</th>
                        <th class="text-end">Montant Demandé</th>
                        <th class="text-end">Montant Accordé</th>
                        <th>Statut</th>
                        <th>Année</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandes as $demande)
                    <tr>
                        <td>
                            <span class="badge bg-label-secondary">
                                {{ \Carbon\Carbon::parse($demande->date_demande)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-label-primary">
                                {{ $demande->poste->nom }}
                            </span>
                        </td>
                        <td>
                            <div class="designation-cell">
                                <strong>{{ Str::limit($demande->designation, 40) }}</strong>
                                @if($demande->observation)
                                    <br>
                                    <small class="text-body-secondary">
                                        <i class="icon-base ti tabler-note me-1"></i>
                                        {{ Str::limit($demande->observation, 50) }}
                                    </small>
                                @endif
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="fw-medium text-primary">
                                {{ number_format($demande->montant, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td class="text-end">
                            @if($demande->montant_accord !== null)
                                @if($demande->montant_accord == $demande->montant)
                                    <span class="fw-medium text-success">
                                        {{ number_format($demande->montant_accord, 0, ',', ' ') }} FCFA
                                    </span>
                                    <br><small class="text-success">100% accordé</small>
                                @elseif($demande->montant_accord > $demande->montant)
                                    <span class="fw-medium text-warning">
                                        {{ number_format($demande->montant_accord, 0, ',', ' ') }} FCFA
                                    </span>
                                    <br><small class="text-warning">
                                        +{{ number_format($demande->montant_accord - $demande->montant, 0, ',', ' ') }} FCFA
                                    </small>
                                @else
                                    <span class="fw-medium text-danger">
                                        {{ number_format($demande->montant_accord, 0, ',', ' ') }} FCFA
                                    </span>
                                    <br><small class="text-danger">
                                        -{{ number_format($demande->montant - $demande->montant_accord, 0, ',', ' ') }} FCFA
                                    </small>
                                @endif
                            @else
                                <span class="text-body-secondary">Non accordé</span>
                            @endif
                        </td>
                        <td>
                            @include('partials.pcs.status-badge', ['statut' => $demande->statut])
                        </td>
                        <td>
                            <span class="badge bg-label-info">
                                {{ $demande->annee }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($demandes->count() >= 50)
            <x-vuexy.alert type="warning">
                <i class="icon-base ti tabler-alert-triangle me-1"></i>
                <strong>Note :</strong> Seules les 50 premières demandes sont affichées dans l'aperçu.
                L'état PDF complet contiendra toutes les données correspondant aux critères.
            </x-vuexy.alert>
        @endif

        <div class="row mt-4 g-3">
            <div class="col-md-3">
                <x-vuexy.card class="text-center">
                    <h5 class="text-primary mb-1">{{ $demandes->count() }}</h5>
                    <p class="text-body-secondary mb-0 small">Total demandes</p>
                </x-vuexy.card>
            </div>
            <div class="col-md-3">
                <x-vuexy.card class="text-center">
                    <h5 class="text-success mb-1">{{ number_format($demandes->sum('montant'), 0, ',', ' ') }}</h5>
                    <p class="text-body-secondary mb-0 small">Montant total demandé (FCFA)</p>
                </x-vuexy.card>
            </div>
            <div class="col-md-3">
                <x-vuexy.card class="text-center">
                    <h5 class="text-info mb-1">{{ number_format($demandes->where('statut', 'valide')->sum('montant_accord') ?? 0, 0, ',', ' ') }}</h5>
                    <p class="text-body-secondary mb-0 small">Montant total accordé (FCFA)</p>
                </x-vuexy.card>
            </div>
            <div class="col-md-3">
                <x-vuexy.card class="text-center">
                    <h5 class="text-warning mb-1">
                        {{ $demandes->where('statut', 'valide')->count() > 0 ?
                            round(($demandes->where('statut', 'valide')->sum('montant_accord') ?? 0) / $demandes->sum('montant') * 100, 1) : 0 }}%
                    </h5>
                    <p class="text-body-secondary mb-0 small">Taux d'accord</p>
                </x-vuexy.card>
            </div>
        </div>
    @else
        <x-vuexy.alert type="warning" class="text-center">
            <i class="icon-base ti tabler-alert-triangle icon-32px mb-2 d-block"></i>
            <h5>Aucune demande trouvée</h5>
            <p class="mb-0">Aucune autre demande ne correspond aux critères de filtrage sélectionnés.</p>
        </x-vuexy.alert>
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

.apercu-container .table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>
