<style>
    table.table {
        width: 100%;
        table-layout: auto;
    }

    table.table th, table.table td {
        padding: 10px;
        text-align: center;
        vertical-align: middle;
    }

    table.table input[type="number"] {
        width: 100%;
        box-sizing: border-box;
    }

    table.table-bordered {
        border-collapse: collapse;
    }

    table.table-bordered th, table.table-bordered td {
        border: 1px solid #ddd;
    }

    table.table thead th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    table.table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    /* Styling for the total row */
    table.table tbody tr.total-row td {
        font-weight: bold;
        background-color: #e9ecef; /* Couleur de fond pour la ligne de total */
    }

    table.table tbody tr.total-row input {
        background-color: #f8f9fa;
        border: none;
        font-weight: bold; /* Mettre en gras les champs de total */
    }

    /* Style du conteneur du bouton */
    .button-container {
        margin-top: 20px; /* Espace au-dessus du bouton */
        text-align: center; /* Centrer le bouton */
    }

    /* Style du bouton de soumission */
    .submit-button {
        padding: 10px 20px;
        background-color: #3d5ee1; /* Couleur du bouton */
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px; /* Taille de police plus grande */
    }

    .submit-button:hover {
        background-color: #3d5ee1; /* Couleur au survol */
    }

    /* Style pour garder la présentation des champs inchangée */
    /* table.table input[type="text"] {
        width: 100%;
        box-sizing: border-box;
    }
 */
</style>

<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 25%;">Désignation</th>
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
            <td><input type="text" name="fonctionnaires_bcs_net" class="form-control net"></td>
            <td><input type="text" name="fonctionnaires_bcs_revers" class="form-control revers"></td>
            <td><input type="text" name="fonctionnaires_bcs_total_courant" class="form-control total_courant"></td>
            <td><input type="text" name="fonctionnaires_bcs_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->fonctionnaires_bcs_total_courant ?? 0 }}"></td>
            <td><input type="text" name="fonctionnaires_bcs_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- Personnel Collectivité Santé -->
        <tr>
            <td>Personnel Collectivité Santé</td>
            <td><input type="text" name="collectivite_sante_net" class="form-control net"></td>
            <td><input type="text" name="collectivite_sante_revers" class="form-control revers"></td>
            <td><input type="text" name="collectivite_sante_total_courant" class="form-control total_courant"></td>
            <td><input type="text" name="collectivite_sante_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->collectivite_sante_total_courant ?? 0 }}"></td>
            <td><input type="text" name="collectivite_sante_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- Personnel Collectivité Éducation -->
        <tr>
            <td>Personnel Collectivité Éducation</td>
            <td><input type="text" name="collectivite_education_net" class="form-control net"></td>
            <td><input type="text" name="collectivite_education_revers" class="form-control revers"></td>
            <td><input type="text" name="collectivite_education_total_courant" class="form-control total_courant"></td>
            <td><input type="text" name="collectivite_education_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->collectivite_education_total_courant ?? 0 }}"></td>
            <td><input type="text" name="collectivite_education_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- Personnels Saisonniers -->
        <tr>
            <td>Personnels Saisonniers</td>
            <td><input type="text" name="personnels_saisonniers_net" class="form-control net"></td>
            <td><input type="text" name="personnels_saisonniers_revers" class="form-control revers"></td>
            <td><input type="text" name="personnels_saisonniers_total_courant" class="form-control total_courant"></td>
            <td><input type="text" name="personnels_saisonniers_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->personnels_saisonniers_total_courant ?? 0 }}"></td>
            <td><input type="text" name="personnels_saisonniers_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- EPN -->
        <tr>
            <td>EPN</td>
            <td><input type="text" name="epn_net" class="form-control net"></td>
            <td><input type="text" name="epn_revers" class="form-control revers"></td>
            <td><input type="text" name="epn_total_courant" class="form-control total_courant"></td>
            <td><input type="text" name="epn_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->epn_total_courant ?? 0 }}"></td>
            <td><input type="text" name="epn_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <tr>
            <td>CED</td>
            <td><input type="text" name="ced_net" class="form-control net"></td>
            <td><input type="text" name="ced_revers" class="form-control revers"></td>
            <td><input type="text" name="ced_total_courant" class="form-control total_courant"></td>
            <td><input type="text" name="ced_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->ced_total_courant ?? 0 }}"></td>
            <td><input type="text" name="ced_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <tr>
            <td>ECOM</td>
            <td><input type="text" name="ecom_net" class="form-control net"></td>
            <td><input type="text" name="ecom_revers" class="form-control revers"></td>
            <td><input type="text" name="ecom_total_courant" class="form-control total_courant"></td>
            <td><input type="text" name="ecom_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->ecom_total_courant ?? 0 }}"></td>
            <td><input type="text" name="ecom_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <tr>
            <td>CFP-CPAM</td>
            <td><input type="text" name="cfp_cpam_net" class="form-control net"></td>
            <td><input type="text" name="cfp_cpam_revers" class="form-control revers"></td>
            <td><input type="text" name="cfp_cpam_total_courant" class="form-control total_courant"></td>
            <td><input type="text" name="cfp_cpam_salaire_ancien" class="form-control ancien_salaire" value="{{ $previousData->cfp_cpam_total_courant ?? 0 }}"></td>
            <td><input type="text" name="cfp_cpam_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- Ligne de total automatique -->
        <tr class="total-row">
            <td>Total</td>
            <td><input type="text" name="total_net" class="form-control" id="total_net" readonly></td>
            <td><input type="text" name="total_revers" class="form-control" id="total_revers" readonly></td>
            <td><input type="text" name="total_courant" class="form-control" id="total_courant" readonly></td>
            <td><input type="text" name="total_salaire_ancien" class="form-control" id="total_salaire_ancien" readonly></td>
            <td><input type="text" name="total_demande" class="form-control" id="total_demande" readonly></td>
        </tr>
    </tbody>

</table>
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
 
