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
            <td><input type="number" name="fonctionnaires_bcs_net" class="form-control net"></td>
            <td><input type="number" name="fonctionnaires_bcs_revers" class="form-control revers"></td>
            <td><input type="number" name="fonctionnaires_bcs_total_courant" class="form-control total_courant"></td>
            <td><input type="number" name="fonctionnaires_bcs_salaire_ancien" class="form-control ancien_salaire"></td>
            <td><input type="number" name="fonctionnaires_bcs_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- Personnel Collectivité Santé -->
        <tr>
            <td>Personnel Collectivité Santé</td>
            <td><input type="number" name="collectivite_sante_net" class="form-control net"></td>
            <td><input type="number" name="collectivite_sante_revers" class="form-control revers"></td>
            <td><input type="number" name="collectivite_sante_total_courant" class="form-control total_courant"></td>
            <td><input type="number" name="collectivite_sante_salaire_ancien" class="form-control ancien_salaire"></td>
            <td><input type="number" name="collectivite_sante_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- Personnel Collectivité Éducation -->
        <tr>
            <td>Personnel Collectivité Éducation</td>
            <td><input type="number" name="collectivite_education_net" class="form-control net"></td>
            <td><input type="number" name="collectivite_education_revers" class="form-control revers"></td>
            <td><input type="number" name="collectivite_education_total_courant" class="form-control total_courant"></td>
            <td><input type="number" name="collectivite_education_salaire_ancien" class="form-control ancien_salaire"></td>
            <td><input type="number" name="collectivite_education_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- Personnels Saisonniers -->
        <tr>
            <td>Personnels Saisonniers</td>
            <td><input type="number" name="personnels_saisonniers_net" class="form-control net"></td>
            <td><input type="number" name="personnels_saisonniers_revers" class="form-control revers"></td>
            <td><input type="number" name="personnels_saisonniers_total_courant" class="form-control total_courant"></td>
            <td><input type="number" name="personnels_saisonniers_salaire_ancien" class="form-control ancien_salaire"></td>
            <td><input type="number" name="personnels_saisonniers_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- EPN -->
        <tr>
            <td>EPN</td>
            <td><input type="number" name="epn_net" class="form-control net"></td>
            <td><input type="number" name="epn_revers" class="form-control revers"></td>
            <td><input type="number" name="epn_total_courant" class="form-control total_courant"></td>
            <td><input type="number" name="epn_salaire_ancien" class="form-control ancien_salaire"></td>
            <td><input type="number" name="epn_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <tr>
            <td>CED</td>
            <td><input type="number" name="ced_net" class="form-control net"></td>
            <td><input type="number" name="ced_revers" class="form-control revers"></td>
            <td><input type="number" name="ced_total_courant" class="form-control total_courant"></td>
            <td><input type="number" name="ced_salaire_ancien" class="form-control ancien_salaire"></td>
            <td><input type="number" name="ced_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <tr>
            <td>ECOM</td>
            <td><input type="number" name="ecom_net" class="form-control net"></td>
            <td><input type="number" name="ecom_revers" class="form-control revers"></td>
            <td><input type="number" name="ecom_total_courant" class="form-control total_courant"></td>
            <td><input type="number" name="ecom_salaire_ancien" class="form-control ancien_salaire"></td>
            <td><input type="number" name="ecom_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <tr>
            <td>CFP-CPAM</td>
            <td><input type="number" name="cfp_cpam_net" class="form-control net" ></td>
            <td><input type="number" name="cfp_cpam_revers" class="form-control revers" ></td>
            <td><input type="number" name="cfp_cpam_total_courant" class="form-control total_courant" ></td>
            <td><input type="number" name="cfp_cpam_salaire_ancien" class="form-control ancien_salaire" ></td>
            <td><input type="number" name="cfp_cpam_total_demande" class="form-control total_demande" readonly></td>
        </tr>

        <!-- Ligne de total automatique -->
        <tr class="total-row">
            <td>Total</td>
            <td><input type="number" name="total_net" class="form-control" id="total_net" readonly></td>
            <td><input type="number" name="total_revers" class="form-control" id="total_revers" readonly></td>
            <td><input type="number" name="total_courant" class="form-control" id="total_courant" readonly></td>
            <td><input type="number" name="total_salaire_ancien" class="form-control" id="total_salaire_ancien" readonly></td>
            <td><input type="number" name="total_demande" class="form-control" id="total_demande" readonly></td>
        </tr>
    </tbody>

</table>

<?php /**PATH /home/c2251405c/public_html/tresor.dntcp.com/resources/views/demandes/_form.blade.php ENDPATH**/ ?>