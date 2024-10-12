<div class="row mb-4">
    <h4>Salaires</h4>
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
                <td><input type="number" name="fonctionnaires_bcs_net" class="form-control net" value="{{ $demande->fonctionnaires_bcs_net }}"></td>
                <td><input type="number" name="fonctionnaires_bcs_revers" class="form-control revers" value="{{ $demande->fonctionnaires_bcs_revers }}"></td>
                <td><input type="number" name="fonctionnaires_bcs_total_courant" class="form-control total_courant" value="{{ $demande->fonctionnaires_bcs_total_courant }}"></td>
                <td><input type="number" name="fonctionnaires_bcs_salaire_ancien" class="form-control ancien_salaire" value="{{ $demande->fonctionnaires_bcs_salaire_ancien }}"></td>
                <td><input type="number" name="fonctionnaires_bcs_total_demande" class="form-control total_demande" value="{{ $demande->fonctionnaires_bcs_total_demande }}" readonly></td>
            </tr>

            <!-- Personnel Collectivité Santé -->
            <tr>
                <td>Personnel Collectivité Santé</td>
                <td><input type="number" name="collectivite_sante_net" class="form-control net" value="{{ $demande->collectivite_sante_net }}"></td>
                <td><input type="number" name="collectivite_sante_revers" class="form-control revers" value="{{ $demande->collectivite_sante_revers }}"></td>
                <td><input type="number" name="collectivite_sante_total_courant" class="form-control total_courant" value="{{ $demande->collectivite_sante_total_courant }}"></td>
                <td><input type="number" name="collectivite_sante_salaire_ancien" class="form-control ancien_salaire" value="{{ $demande->collectivite_sante_salaire_ancien }}"></td>
                <td><input type="number" name="collectivite_sante_total_demande" class="form-control total_demande" value="{{ $demande->collectivite_sante_total_demande }}" readonly></td>
            </tr>

            <!-- Personnel Collectivité Éducation -->
            <tr>
                <td>Personnel Collectivité Éducation</td>
                <td><input type="number" name="collectivite_education_net" class="form-control net" value="{{ $demande->collectivite_education_net }}"></td>
                <td><input type="number" name="collectivite_education_revers" class="form-control revers" value="{{ $demande->collectivite_education_revers }}"></td>
                <td><input type="number" name="collectivite_education_total_courant" class="form-control total_courant" value="{{ $demande->collectivite_education_total_courant }}"></td>
                <td><input type="number" name="collectivite_education_salaire_ancien" class="form-control ancien_salaire" value="{{ $demande->collectivite_education_salaire_ancien }}"></td>
                <td><input type="number" name="collectivite_education_total_demande" class="form-control total_demande" value="{{ $demande->collectivite_education_total_demande }}" readonly></td>
            </tr>

            <!-- Personnels Saisonniers -->
            <tr>
                <td>Personnels Saisonniers</td>
                <td><input type="number" name="personnels_saisonniers_net" class="form-control net" value="{{ $demande->personnels_saisonniers_net }}"></td>
                <td><input type="number" name="personnels_saisonniers_revers" class="form-control revers" value="{{ $demande->personnels_saisonniers_revers }}"></td>
                <td><input type="number" name="personnels_saisonniers_total_courant" class="form-control total_courant" value="{{ $demande->personnels_saisonniers_total_courant }}"></td>
                <td><input type="number" name="personnels_saisonniers_salaire_ancien" class="form-control ancien_salaire" value="{{ $demande->personnels_saisonniers_salaire_ancien }}"></td>
                <td><input type="number" name="personnels_saisonniers_total_demande" class="form-control total_demande" value="{{ $demande->personnels_saisonniers_total_demande }}" readonly></td>
            </tr>

            <!-- EPN -->
            <tr>
                <td>EPN</td>
                <td><input type="number" name="epn_net" class="form-control net" value="{{ $demande->epn_net }}"></td>
                <td><input type="number" name="epn_revers" class="form-control revers" value="{{ $demande->epn_revers }}"></td>
                <td><input type="number" name="epn_total_courant" class="form-control total_courant" value="{{ $demande->epn_total_courant }}"></td>
                <td><input type="number" name="epn_salaire_ancien" class="form-control ancien_salaire" value="{{ $demande->epn_salaire_ancien }}"></td>
                <td><input type="number" name="epn_total_demande" class="form-control total_demande" value="{{ $demande->epn_total_demande }}" readonly></td>
            </tr>
            <tr>
                <td>CED</td>
            <td><input type="number" name="ced_net" class="form-control net" value="{{ $demande->ced_net }}"    ></td>
                <td><input type="number" name="ced_revers" class="form-control revers" value="{{ $demande->ced_revers }}"></td>
                <td><input type="number" name="ced_total_courant" class="form-control total_courant" value="{{ $demande->ced_total_courant }}"></td>
                <td><input type="number" name="ced_salaire_ancien" class="form-control ancien_salaire" value="{{ $demande->ced_salaire_ancien }}"></td>
                <td><input type="number" name="ced_total_demande" class="form-control total_demande" value="{{ $demande->ced_total_demande }}" readonly></td>
            </tr>
            
            <tr>
                <td>ECOM</td>
                <td><input type="number" name="ecom_net" class="form-control net" value="{{ $demande->ecom_net }}"></td>
                <td><input type="number" name="ecom_revers" class="form-control revers" value="{{ $demande->ecom_revers }}"></td>
                <td><input type="number" name="ecom_total_courant" class="form-control total_courant" value="{{ $demande->ecom_total_courant }}"></td>
                <td><input type="number" name="ecom_salaire_ancien" class="form-control ancien_salaire" value="{{ $demande->ecom_salaire_ancien }}"></td>
                <td><input type="number" name="ecom_total_demande" class="form-control total_demande" value="{{ $demande->ecom_total_demande }}" readonly></td>
            </tr>
    
            <tr>
                <td>CFP-CPAM</td>
                <td><input type="number" name="cfp_cpam_net" class="form-control net" value="{{ $demande->cfp_cpam_net }}"></td>
                <td><input type="number" name="cfp_cpam_revers" class="form-control revers" value="{{ $demande->cfp_cpam_revers }}"></td>
                <td><input type="number" name="cfp_cpam_total_courant" class="form-control total_courant" value="{{ $demande->cfp_cpam_total_courant }}"></td>
                <td><input type="number" name="cfp_cpam_salaire_ancien" class="form-control ancien_salaire" value="{{ $demande->cfp_cpam_salaire_ancien }}"></td>
                <td><input type="number" name="cfp_cpam_total_demande" class="form-control total_demande" value="{{ $demande->cfp_cpam_total_demande }}" readonly></td>
            </tr>
            <!-- Total -->
            <tr>
                <td  colspan="6">
                    <!-- Champs pour les totaux -->
                    <div class="d-flex justify-content-between">
                        <input type="text" id="total_net" name="total_net" value="{{ old('total_net', $demande->total_net) }}" readonly class="form-control">
                        <input type="text" id="total_revers" name="total_revers" value="{{ old('total_revers', $demande->total_revers) }}" readonly class="form-control">
                        <input type="text" id="total_courant" name="total_courant" value="{{ old('total_courant', $demande->total_courant) }}" readonly class="form-control">
                        <input type="text" id="total_salaire_ancien" name="total_salaire_ancien" value="{{ old('total_salaire_ancien', $demande->total_salaire_ancien) }}" readonly class="form-control">
                        <input type="text" id="total_demande" name="total_demande" value="{{ old('total_demande', $demande->total_demande) }}" readonly class="form-control">
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <button type="submit" class="btn btn-primary">Mettre à jour</button>
</div>
