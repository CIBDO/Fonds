@include('partials.demandes.table-salaires-styles')

@php
    $categories = [
        ['icon' => 'tabler-user', 'label' => 'Fonctionnaires BCS', 'prefix' => 'fonctionnaires_bcs'],
        ['icon' => 'tabler-heart', 'label' => 'Personnel Collectivité Santé', 'prefix' => 'collectivite_sante'],
        ['icon' => 'tabler-school', 'label' => 'Personnel Collectivité Éducation', 'prefix' => 'collectivite_education'],
        ['icon' => 'tabler-calendar', 'label' => 'Personnels Saisonniers', 'prefix' => 'personnels_saisonniers'],
        ['icon' => 'tabler-building', 'label' => 'EPN', 'prefix' => 'epn'],
        ['icon' => 'tabler-building-bank', 'label' => 'CED', 'prefix' => 'ced'],
        ['icon' => 'tabler-building-store', 'label' => 'ECOM', 'prefix' => 'ecom'],
        ['icon' => 'tabler-certificate', 'label' => 'CFP-CPAM', 'prefix' => 'cfp_cpam'],
    ];
@endphp

<div class="table-salaires-wrapper p-3">
    <table class="table table-bordered table-salaires table-hover mb-0">
        <thead>
            <tr>
                <th style="width: 22%;"><i class="ti tabler-users me-1"></i>Catégorie Personnel</th>
                <th style="width: 15%;"><i class="ti tabler-cash me-1"></i>Salaire Net</th>
                <th style="width: 15%;"><i class="ti tabler-receipt me-1"></i>Revers/Salaire</th>
                <th style="width: 15%;"><i class="ti tabler-calculator me-1"></i>Total Mois Courant</th>
                <th style="width: 16%;"><i class="ti tabler-history me-1"></i>Salaire Mois Antérieur</th>
                <th style="width: 17%;"><i class="ti tabler-trending-up me-1"></i>Écart (Demande)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr data-category="{{ $category['prefix'] }}">
                <td>
                    <i class="ti {{ $category['icon'] }} me-2 text-body-secondary"></i>{{ $category['label'] }}
                </td>
                <td>
                    <input type="text" name="{{ $category['prefix'] }}_net" class="form-control net"
                           value="{{ number_format($demande->{$category['prefix'] . '_net'}, 0, ',', ' ') }}">
                </td>
                <td>
                    <input type="text" name="{{ $category['prefix'] }}_revers" class="form-control revers"
                           value="{{ number_format($demande->{$category['prefix'] . '_revers'}, 0, ',', ' ') }}">
                </td>
                <td>
                    <input type="text" name="{{ $category['prefix'] }}_total_courant" class="form-control total_courant" readonly
                           value="{{ number_format($demande->{$category['prefix'] . '_total_courant'}, 0, ',', ' ') }}">
                </td>
                <td>
                    <input type="text" name="{{ $category['prefix'] }}_salaire_ancien" class="form-control ancien_salaire"
                           value="{{ number_format($demande->{$category['prefix'] . '_salaire_ancien'}, 0, ',', ' ') }}">
                </td>
                <td>
                    <input type="text" name="{{ $category['prefix'] }}_total_demande" class="form-control total_demande" readonly
                           value="{{ number_format($demande->{$category['prefix'] . '_total_demande'}, 0, ',', ' ') }}">
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td><i class="ti tabler-sum me-2"></i><strong>TOTAL GÉNÉRAL</strong></td>
                <td><input type="text" name="total_net" id="total_net" class="form-control" readonly
                           value="{{ number_format($demande->total_net, 0, ',', ' ') }}"></td>
                <td><input type="text" name="total_revers" id="total_revers" class="form-control" readonly
                           value="{{ number_format($demande->total_revers, 0, ',', ' ') }}"></td>
                <td><input type="text" name="total_courant" id="total_courant" class="form-control" readonly
                           value="{{ number_format($demande->total_courant, 0, ',', ' ') }}"></td>
                <td><input type="text" name="total_salaire_ancien" id="total_salaire_ancien" class="form-control" readonly
                           value="{{ number_format($demande->total_salaire_ancien, 0, ',', ' ') }}"></td>
                <td><input type="text" name="total_demande" id="total_demande" class="form-control" readonly
                           value="{{ number_format($demande->total_demande, 0, ',', ' ') }}"></td>
            </tr>
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function formatNumber(number) {
        return new Intl.NumberFormat('fr-FR').format(number);
    }

    function unformatNumber(formattedNumber) {
        if (typeof formattedNumber === 'string') {
            return parseFloat(formattedNumber.replace(/\s/g, '').replace(',', '.')) || 0;
        }
        return formattedNumber || 0;
    }

    const netFields = document.querySelectorAll('.net');
    const reversFields = document.querySelectorAll('.revers');
    const totalCourantFields = document.querySelectorAll('.total_courant');
    const salaireAncienFields = document.querySelectorAll('.ancien_salaire');
    const totalDemandeFields = document.querySelectorAll('.total_demande');
    const totalNetField = document.getElementById('total_net');
    const totalReversField = document.getElementById('total_revers');
    const totalCourantField = document.getElementById('total_courant');
    const totalSalaireAncienField = document.getElementById('total_salaire_ancien');
    const totalDemandeField = document.getElementById('total_demande');
    const montantDisponibleField = document.getElementById('montant_disponible');
    const soldeField = document.getElementById('solde');

    function calculateTotals() {
        let totalNet = 0, totalRevers = 0, totalCourant = 0, totalSalaireAncien = 0, totalDemande = 0;

        netFields.forEach((field, index) => {
            const net = unformatNumber(field.value);
            const revers = unformatNumber(reversFields[index].value);
            const courant = net + revers;
            const ancien = unformatNumber(salaireAncienFields[index].value);
            const demande = courant - ancien;

            totalCourantFields[index].value = formatNumber(courant);
            totalDemandeFields[index].value = formatNumber(demande);

            totalNet += net;
            totalRevers += revers;
            totalCourant += courant;
            totalSalaireAncien += ancien;
            totalDemande += demande;
        });

        totalNetField.value = formatNumber(totalNet);
        totalReversField.value = formatNumber(totalRevers);
        totalCourantField.value = formatNumber(totalCourant);
        totalSalaireAncienField.value = formatNumber(totalSalaireAncien);
        totalDemandeField.value = formatNumber(totalDemande);

        calculateSolde();
    }

    function calculateSolde() {
        if (!montantDisponibleField || !soldeField || !totalCourantField) return;
        const montantDisponible = unformatNumber(montantDisponibleField.value);
        const totalCourant = unformatNumber(totalCourantField.value);
        soldeField.value = formatNumber(totalCourant - montantDisponible);
    }

    function handleInput(e) {
        const value = unformatNumber(e.target.value);
        e.target.value = formatNumber(value);
        calculateTotals();
    }

    netFields.forEach(field => field.addEventListener('input', handleInput));
    reversFields.forEach(field => field.addEventListener('input', handleInput));
    salaireAncienFields.forEach(field => field.addEventListener('input', handleInput));
    if (montantDisponibleField) montantDisponibleField.addEventListener('input', handleInput);

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            document.querySelectorAll('.net, .revers, .total_courant, .ancien_salaire, .total_demande, #total_net, #total_revers, #total_courant, #total_salaire_ancien, #total_demande, #montant_disponible, #solde')
                .forEach(field => { field.value = unformatNumber(field.value); });
        });
    }

    calculateTotals();
});
</script>
@endpush
