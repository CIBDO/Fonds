<table class="table table-bordered">
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

        <!-- Ligne de total automatique -->
        <tr>
            <td>Total</td>
            <td><input type="number" name="total_net" class="form-control" id="total_net" readonly></td>
            <td><input type="number" name="total_revers" class="form-control" id="total_revers" readonly></td>
            <td><input type="number" name="total_courant" class="form-control" id="total_courant" readonly></td>
            <td><input type="number" name="total_salaire_ancien" class="form-control" id="total_salaire_ancien" readonly></td>
            <td><input type="number" name="total_demande" class="form-control" id="total_demande" readonly></td>
        </tr>
    </tbody>
</table>