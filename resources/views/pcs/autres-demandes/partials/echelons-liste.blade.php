@if($demande->echelons->isNotEmpty())
<x-vuexy.card title="Versements enregistrés ({{ $demande->echelons->count() }})" icon="tabler-calendar-check" class="mb-4">
    <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
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
                        <th colspan="2" class="text-end">Total versé</th>
                        <th class="text-end text-success">{{ number_format($demande->montant_verse, 0, ',', ' ') }} FCFA</th>
                    </tr>
                    @if($demande->montant_restant_accord > 0)
                    <tr>
                        <th colspan="2" class="text-end">Reste à verser</th>
                        <th class="text-end text-warning">{{ number_format($demande->montant_restant_accord, 0, ',', ' ') }} FCFA</th>
                    </tr>
                    @endif
                </tfoot>
        </table>
    </div>
</x-vuexy.card>
@endif
