@if($demande->echelons->isNotEmpty())
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-success text-white">
        <h6 class="mb-0">
            <i class="fas fa-calendar-check me-2"></i>
            Versements enregistrés ({{ $demande->echelons->count() }})
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">N°</th>
                        <th>Date d'échéance</th>
                        <th class="text-end">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demande->echelons as $echelon)
                    <tr>
                        <td class="text-center">{{ $echelon->ordre }}</td>
                        <td>{{ $echelon->date_echeance->format('d/m/Y') }}</td>
                        <td class="text-end fw-bold text-success">{{ number_format($echelon->montant, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="text-end">Total accordé</th>
                        <th class="text-end text-success">{{ number_format($demande->montant_accord, 0, ',', ' ') }} FCFA</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif
