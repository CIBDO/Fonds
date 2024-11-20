<div class="row mb-4">
    <h4>Salaires</h4>
    <table class="table table-bordered">
        <!-- Le contenu de la table reste identique jusqu'au dernier tr -->
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Salaire Net</th>
                <th>Revers/ Salaire</th>
                <th>Total mois courant</th>
                <th>Salaire mois antérieur</th>
                <th>Ecart</th>
            </tr>
        </thead>
        <tbody>
            <!-- Fonctionnaires BCS -->
            <tr>
                <td>Fonctionnaires BCS</td>
                <td><input type="text" name="fonctionnaires_bcs_net" class="form-control net" value="{{ number_format($demande->fonctionnaires_bcs_net, 0, ',', ' ') }}"></td>
                <td><input type="text" name="fonctionnaires_bcs_revers" class="form-control revers" value="{{ number_format($demande->fonctionnaires_bcs_revers, 0, ',', ' ') }}"></td>
                <td><input type="text" name="fonctionnaires_bcs_total_courant" class="form-control total_courant" value="{{ number_format($demande->fonctionnaires_bcs_total_courant, 0, ',', ' ') }}" readonly></td>
                <td><input type="text" name="fonctionnaires_bcs_salaire_ancien" class="form-control ancien_salaire" value="{{ number_format($demande->fonctionnaires_bcs_salaire_ancien, 0, ',', ' ') }}"></td>
                <td><input type="text" name="fonctionnaires_bcs_total_demande" class="form-control total_demande" value="{{ number_format($demande->fonctionnaires_bcs_total_demande, 0, ',', ' ') }}" readonly></td>
            </tr>

            <!-- Personnel Collectivité Santé -->
            <tr>
                <td>Personnel Collectivité Santé</td>
                <td><input type="text" name="collectivite_sante_net" class="form-control net" value="{{ number_format($demande->collectivite_sante_net, 0, ',', ' ') }}"></td>
                <td><input type="text" name="collectivite_sante_revers" class="form-control revers" value="{{ number_format($demande->collectivite_sante_revers, 0, ',', ' ') }}"></td>
                <td><input type="text" name="collectivite_sante_total_courant" class="form-control total_courant" value="{{ number_format($demande->collectivite_sante_total_courant, 0, ',', ' ') }}" readonly></td>
                <td><input type="text" name="collectivite_sante_salaire_ancien" class="form-control ancien_salaire" value="{{ number_format($demande->collectivite_sante_salaire_ancien, 0, ',', ' ') }}"></td>
                <td><input type="text" name="collectivite_sante_total_demande" class="form-control total_demande" value="{{ number_format($demande->collectivite_sante_total_demande, 0, ',', ' ') }}" readonly></td>
            </tr>

            <!-- Personnel Collectivité Éducation -->
            <tr>
                <td>Personnel Collectivité Éducation</td>
                <td><input type="text" name="collectivite_education_net" class="form-control net" value="{{ number_format($demande->collectivite_education_net, 0, ',', ' ') }}"></td>
                <td><input type="text" name="collectivite_education_revers" class="form-control revers" value="{{ number_format($demande->collectivite_education_revers, 0, ',', ' ') }}"></td>
                <td><input type="text" name="collectivite_education_total_courant" class="form-control total_courant" value="{{ number_format($demande->collectivite_education_total_courant, 0, ',', ' ') }}" readonly></td>
                <td><input type="text" name="collectivite_education_salaire_ancien" class="form-control ancien_salaire" value="{{ number_format($demande->collectivite_education_salaire_ancien, 0, ',', ' ') }}"></td>
                <td><input type="text" name="collectivite_education_total_demande" class="form-control total_demande" value="{{ number_format($demande->collectivite_education_total_demande, 0, ',', ' ') }}" readonly></td>
            </tr>

            <!-- Personnels Saisonniers -->
            <tr>
                <td>Personnels Saisonniers</td>
                <td><input type="text" name="personnels_saisonniers_net" class="form-control net" value="{{ number_format($demande->personnels_saisonniers_net, 0, ',', ' ') }}"></td>
                <td><input type="text" name="personnels_saisonniers_revers" class="form-control revers" value="{{ number_format($demande->personnels_saisonniers_revers, 0, ',', ' ') }}"></td>
                <td><input type="text" name="personnels_saisonniers_total_courant" class="form-control total_courant" value="{{ number_format($demande->personnels_saisonniers_total_courant, 0, ',', ' ') }}" readonly></td>
                <td><input type="text" name="personnels_saisonniers_salaire_ancien" class="form-control ancien_salaire" value="{{ number_format($demande->personnels_saisonniers_salaire_ancien, 0, ',', ' ') }}"></td>
                <td><input type="text" name="personnels_saisonniers_total_demande" class="form-control total_demande" value="{{ number_format($demande->personnels_saisonniers_total_demande, 0, ',', ' ') }}" readonly></td>
            </tr>

            <!-- EPN -->
            <tr>
                <td>EPN</td>
                <td><input type="text" name="epn_net" class="form-control net" value="{{ number_format($demande->epn_net, 0, ',', ' ') }}"></td>
                <td><input type="text" name="epn_revers" class="form-control revers" value="{{ number_format($demande->epn_revers, 0, ',', ' ') }}"></td>
                <td><input type="text" name="epn_total_courant" class="form-control total_courant" value="{{ number_format($demande->epn_total_courant, 0, ',', ' ') }}" readonly></td>
                <td><input type="text" name="epn_salaire_ancien" class="form-control ancien_salaire" value="{{ number_format($demande->epn_salaire_ancien, 0, ',', ' ') }}"></td>
                <td><input type="text" name="epn_total_demande" class="form-control total_demande" value="{{ number_format($demande->epn_total_demande, 0, ',', ' ') }}" readonly></td>
            </tr>

            <!-- CED -->
            <tr>
                <td>CED</td>
                <td><input type="text" name="ced_net" class="form-control net" value="{{ number_format($demande->ced_net, 0, ',', ' ') }}"></td>
                <td><input type="text" name="ced_revers" class="form-control revers" value="{{ number_format($demande->ced_revers, 0, ',', ' ') }}"></td>
                <td><input type="text" name="ced_total_courant" class="form-control total_courant" value="{{ number_format($demande->ced_total_courant, 0, ',', ' ') }}" readonly></td>
                <td><input type="text" name="ced_salaire_ancien" class="form-control ancien_salaire" value="{{ number_format($demande->ced_salaire_ancien, 0, ',', ' ') }}"></td>
                <td><input type="text" name="ced_total_demande" class="form-control total_demande" value="{{ number_format($demande->ced_total_demande, 0, ',', ' ') }}" readonly></td>
            </tr>

            <!-- ECOM -->
            <tr>
                <td>ECOM</td>
                <td><input type="text" name="ecom_net" class="form-control net" value="{{ number_format($demande->ecom_net, 0, ',', ' ') }}"></td>
                <td><input type="text" name="ecom_revers" class="form-control revers" value="{{ number_format($demande->ecom_revers, 0, ',', ' ') }}"></td>
                <td><input type="text" name="ecom_total_courant" class="form-control total_courant" value="{{ number_format($demande->ecom_total_courant, 0, ',', ' ') }}" readonly></td>
                <td><input type="text" name="ecom_salaire_ancien" class="form-control ancien_salaire" value="{{ number_format($demande->ecom_salaire_ancien, 0, ',', ' ') }}"></td>
                <td><input type="text" name="ecom_total_demande" class="form-control total_demande" value="{{ number_format($demande->ecom_total_demande, 0, ',', ' ') }}" readonly></td>
            </tr>

            <!-- CFP-CPAM -->
            <tr>
                <td>CFP-CPAM</td>
                <td><input type="text" name="cfp_cpam_net" class="form-control net" value="{{ number_format($demande->cfp_cpam_net, 0, ',', ' ') }}"></td>
                <td><input type="text" name="cfp_cpam_revers" class="form-control revers" value="{{ number_format($demande->cfp_cpam_revers, 0, ',', ' ') }}"></td>
                <td><input type="text" name="cfp_cpam_total_courant" class="form-control total_courant" value="{{ number_format($demande->cfp_cpam_total_courant, 0, ',', ' ') }}" readonly></td>
                <td><input type="text" name="cfp_cpam_salaire_ancien" class="form-control ancien_salaire" value="{{ number_format($demande->cfp_cpam_salaire_ancien, 0, ',', ' ') }}"></td>
                <td><input type="text" name="cfp_cpam_total_demande" class="form-control total_demande" value="{{ number_format($demande->cfp_cpam_total_demande, 0, ',', ' ') }}" readonly></td>
            </tr>

            <!-- Total -->
            <tr>
                <td colspan="6">
                    <div class="d-flex justify-content-between">
                        <input type="text" id="total_net" name="total_net" value="{{ number_format($demande->total_net, 0, ',', ' ') }}" readonly class="form-control">
                        <input type="text" id="total_revers" name="total_revers" value="{{ number_format($demande->total_revers, 0, ',', ' ') }}" readonly class="form-control">
                        <input type="text" id="total_courant" name="total_courant" value="{{ number_format($demande->total_courant, 0, ',', ' ') }}" readonly class="form-control">
                        <input type="text" id="total_salaire_ancien" name="total_salaire_ancien" value="{{ number_format($demande->total_salaire_ancien, 0, ',', ' ') }}" readonly class="form-control">
                        <input type="text" id="total_demande" name="total_demande" value="{{ number_format($demande->total_demande, 0, ',', ' ') }}" readonly class="form-control">
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- <div class="row mt-3">
        <div class="col-md-4">
            <div class="form-group">
                <label for="montant_disponible">Recettes Douanières :</label>
                <input type="text" id="montant_disponible" name="montant_disponible" class="form-control" value="{{ number_format($demande->montant_disponible ?? 0, 0, ',', ' ') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="solde">Solde :</label>
                <input type="text" id="solde" name="solde" class="form-control" value="{{ number_format($demande->solde ?? 0, 0, ',', ' ') }}" readonly>
            </div>
        </div>
    </div> --}}
    <button type="submit" class="btn btn-primary mt-3">Mettre à jour</button>
</div>

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
        let totalNet = 0;
        let totalRevers = 0;
        let totalCourant = 0;
        let totalSalaireAncien = 0;
        let totalDemande = 0;

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
        const montantDisponible = unformatNumber(montantDisponibleField.value);
        const totalCourant = unformatNumber(totalCourantField.value);
        const solde = montantDisponible - totalCourant;
        soldeField.value = formatNumber(solde);
    }

    function handleInput(e) {
        const value = unformatNumber(e.target.value);
        e.target.value = formatNumber(value);
        calculateTotals();
    }

    netFields.forEach(field => field.addEventListener('input', handleInput));
    reversFields.forEach(field => field.addEventListener('input', handleInput));
    salaireAncienFields.forEach(field => field.addEventListener('input', handleInput));
    montantDisponibleField.addEventListener('input', handleInput);

    document.querySelector('form').addEventListener('submit', function(e) {
        const numericFields = document.querySelectorAll('.net, .revers, .total_courant, .ancien_salaire, .total_demande, #total_net, #total_revers, #total_courant, #total_salaire_ancien, #total_demande, #montant_disponible, #solde');

        numericFields.forEach(field => {
            field.value = unformatNumber(field.value);
        });
    });

    calculateTotals();
});
</script>
