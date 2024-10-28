<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Fonds</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            margin: 0 auto;
            padding: 20px;
            width: 100%;
        }

        h2 {
            font-size: 24px;
            text-align: center;
        }

        .header {
    display: flex; /* Utilise flexbox pour aligner les éléments sur une ligne */
    justify-content: space-between; /* Espace égal entre les éléments */
    align-items: center; /* Aligne les éléments verticalement au centre */
    margin-bottom: 20px; /* Optionnel : Ajoute un espace en bas */
}

.header div {
    margin-right: 20px; /* Optionnel : Ajoute un espace entre les éléments */
}

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        @media print {
            @page {
                size: landscape;
            }

            .container {
                width: 100%;
                margin: 0 auto;
            }

            h2 {
                font-size: 24px;
                text-align: center;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th, td {
                padding: 10px;
                border: 1px solid #ddd;
                text-align: center;
            }

            .total-row td {
                font-weight: bold;
                background-color: #e9ecef;
            }
        }
    </style>
</head>
<body>
       <!-- Titre de la demande de fods -->
    <h2>Demande de Fonds</h2>

    <!-- En-tête avec la date d'envoi, le poste et le mois -->
    <div class="header">

        <div>
            <label>Date d'envoi : <strong><?php echo e($demandeFonds->created_at ? $demandeFonds->created_at->format('d/m/Y') : 'N/A'); ?></strong></label>
        </div>
        <div>
            <label>Poste : <strong><?php echo e($demandeFonds->poste->nom ?? 'N/A'); ?></strong></label>
        </div>
        <div>
            <label>Recette Douanière: <strong><?php echo e(number_format($demandeFonds->montant_disponible, 0, ',', ' ')); ?></strong></label>
        </div>
        <div>
            <label>Salaire du mois de : <strong><?php echo e($demandeFonds->mois . ' ' . $demandeFonds->annee); ?></strong></label>
        </div>
    </div>

    <!-- Tableau des données -->
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Salaire Net</th>
                <th>Revers/Salaire</th>
                <th>Total mois courant</th>
                <th>Salaire mois antérieur</th>
                <th>Écart</th>
            </tr>
        </thead>
        <tbody>
            <?php if($demandeFonds->fonctionnaires_bcs_net || $demandeFonds->fonctionnaires_bcs_revers || $demandeFonds->fonctionnaires_bcs_total_courant || $demandeFonds->fonctionnaires_bcs_salaire_ancien): ?>
            <tr>
                <td>Fonctionnaires BCS</td>
                <td><?php echo e($demandeFonds->fonctionnaires_bcs_net ? number_format($demandeFonds->fonctionnaires_bcs_net, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->fonctionnaires_bcs_revers ? number_format($demandeFonds->fonctionnaires_bcs_revers, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->fonctionnaires_bcs_total_courant ? number_format($demandeFonds->fonctionnaires_bcs_total_courant, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->fonctionnaires_bcs_salaire_ancien ? number_format($demandeFonds->fonctionnaires_bcs_salaire_ancien, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e(($demandeFonds->fonctionnaires_bcs_total_courant && $demandeFonds->fonctionnaires_bcs_salaire_ancien) ? number_format($demandeFonds->fonctionnaires_bcs_total_courant - $demandeFonds->fonctionnaires_bcs_salaire_ancien, 0, ',', ' ') : ''); ?></td>
            </tr>
            <?php endif; ?>
        
            <?php if($demandeFonds->collectivite_sante_net || $demandeFonds->collectivite_sante_revers || $demandeFonds->collectivite_sante_total_courant || $demandeFonds->collectivite_sante_salaire_ancien): ?>
            <tr>
                <td>Personnel Collectivité Santé</td>
                <td><?php echo e($demandeFonds->collectivite_sante_net ? number_format($demandeFonds->collectivite_sante_net, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->collectivite_sante_revers ? number_format($demandeFonds->collectivite_sante_revers, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->collectivite_sante_total_courant ? number_format($demandeFonds->collectivite_sante_total_courant, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->collectivite_sante_salaire_ancien ? number_format($demandeFonds->collectivite_sante_salaire_ancien, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e(($demandeFonds->collectivite_sante_total_courant && $demandeFonds->collectivite_sante_salaire_ancien) ? number_format($demandeFonds->collectivite_sante_total_courant - $demandeFonds->collectivite_sante_salaire_ancien, 0, ',', ' ') : ''); ?></td>
            </tr>
            <?php endif; ?>
        
            <?php if($demandeFonds->collectivite_education_net || $demandeFonds->collectivite_education_revers || $demandeFonds->collectivite_education_total_courant || $demandeFonds->collectivite_education_salaire_ancien): ?>
            <tr>
                <td>Personnel Collectivité Éducation</td>
                <td><?php echo e($demandeFonds->collectivite_education_net ? number_format($demandeFonds->collectivite_education_net, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->collectivite_education_revers ? number_format($demandeFonds->collectivite_education_revers, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->collectivite_education_total_courant ? number_format($demandeFonds->collectivite_education_total_courant, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->collectivite_education_salaire_ancien ? number_format($demandeFonds->collectivite_education_salaire_ancien, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e(($demandeFonds->collectivite_education_total_courant && $demandeFonds->collectivite_education_salaire_ancien) ? number_format($demandeFonds->collectivite_education_total_courant - $demandeFonds->collectivite_education_salaire_ancien, 0, ',', ' ') : ''); ?></td>
            </tr>
            <?php endif; ?>
        
            <?php if($demandeFonds->personnels_saisonniers_net || $demandeFonds->personnels_saisonniers_revers || $demandeFonds->personnels_saisonniers_total_courant || $demandeFonds->personnels_saisonniers_salaire_ancien): ?>
            <tr>
                <td>Personnels Saisonniers</td>
                <td><?php echo e($demandeFonds->personnels_saisonniers_net ? number_format($demandeFonds->personnels_saisonniers_net, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->personnels_saisonniers_revers ? number_format($demandeFonds->personnels_saisonniers_revers, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->personnels_saisonniers_total_courant ? number_format($demandeFonds->personnels_saisonniers_total_courant, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->personnels_saisonniers_salaire_ancien ? number_format($demandeFonds->personnels_saisonniers_salaire_ancien, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e(($demandeFonds->personnels_saisonniers_total_courant && $demandeFonds->personnels_saisonniers_salaire_ancien) ? number_format($demandeFonds->personnels_saisonniers_total_courant - $demandeFonds->personnels_saisonniers_salaire_ancien, 0, ',', ' ') : ''); ?></td>
            </tr>
            <?php endif; ?>
        
            <?php if($demandeFonds->epn_net || $demandeFonds->epn_revers || $demandeFonds->epn_total_courant || $demandeFonds->epn_salaire_ancien): ?>
            <tr>
                <td>EPN</td>
                <td><?php echo e($demandeFonds->epn_net ? number_format($demandeFonds->epn_net, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->epn_revers ? number_format($demandeFonds->epn_revers, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->epn_total_courant ? number_format($demandeFonds->epn_total_courant, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->epn_salaire_ancien ? number_format($demandeFonds->epn_salaire_ancien, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e(($demandeFonds->epn_total_courant && $demandeFonds->epn_salaire_ancien) ? number_format($demandeFonds->epn_total_courant - $demandeFonds->epn_salaire_ancien, 0, ',', ' ') : ''); ?></td>
            </tr>
            <?php endif; ?>
        
            <?php if($demandeFonds->ced_net || $demandeFonds->ced_revers || $demandeFonds->ced_total_courant || $demandeFonds->ced_salaire_ancien): ?>
            <tr>
                <td>CED</td>
                <td><?php echo e($demandeFonds->ced_net ? number_format($demandeFonds->ced_net, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->ced_revers ? number_format($demandeFonds->ced_revers, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->ced_total_courant ? number_format($demandeFonds->ced_total_courant, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->ced_salaire_ancien ? number_format($demandeFonds->ced_salaire_ancien, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e(($demandeFonds->ced_total_courant && $demandeFonds->ced_salaire_ancien) ? number_format($demandeFonds->ced_total_courant - $demandeFonds->ced_salaire_ancien, 0, ',', ' ') : ''); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($demandeFonds->ecom_net || $demandeFonds->ecom_revers || $demandeFonds->ecom_total_courant || $demandeFonds->ecom_salaire_ancien): ?>   
            <tr>
                <td>ECOM</td>
                <td><?php echo e($demandeFonds->ecom_net ? number_format($demandeFonds->ecom_net, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->ecom_revers ? number_format($demandeFonds->ecom_revers, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->ecom_total_courant ? number_format($demandeFonds->ecom_total_courant, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->ecom_salaire_ancien ? number_format($demandeFonds->ecom_salaire_ancien, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e(($demandeFonds->ecom_total_courant && $demandeFonds->ecom_salaire_ancien) ? number_format($demandeFonds->ecom_total_courant - $demandeFonds->ecom_salaire_ancien, 0, ',', ' ') : ''); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($demandeFonds->cfp_cpam_net || $demandeFonds->cfp_cpam_revers || $demandeFonds->cfp_cpam_total_courant || $demandeFonds->cfp_cpam_salaire_ancien): ?>   
            <tr>
                <td>CFP CPAM</td>
                <td><?php echo e($demandeFonds->cfp_cpam_net ? number_format($demandeFonds->cfp_cpam_net, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->cfp_cpam_revers ? number_format($demandeFonds->cfp_cpam_revers, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->cfp_cpam_total_courant ? number_format($demandeFonds->cfp_cpam_total_courant, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->cfp_cpam_salaire_ancien ? number_format($demandeFonds->cfp_cpam_salaire_ancien, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e(($demandeFonds->cfp_cpam_total_courant && $demandeFonds->cfp_cpam_salaire_ancien) ? number_format($demandeFonds->cfp_cpam_total_courant - $demandeFonds->cfp_cpam_salaire_ancien, 0, ',', ' ') : ''); ?></td>
            </tr>
            <?php endif; ?>
                <tr class="total-row">
                <td>Total</td>
                <td><?php echo e($demandeFonds->total_net ? number_format($demandeFonds->total_net, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->total_revers ? number_format($demandeFonds->total_revers, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->total_courant ? number_format($demandeFonds->total_courant, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e($demandeFonds->total_ancien ? number_format($demandeFonds->total_ancien, 0, ',', ' ') : ''); ?></td>
                <td><?php echo e(($demandeFonds->total_courant && $demandeFonds->total_ancien) ? number_format($demandeFonds->total_courant - $demandeFonds->total_ancien, 0, ',', ' ') : ''); ?></td>
            </tr>
        </tbody>
    </table>
    </div>
    <Label style="text-align: center; font-weight: bold; font-size: 22px;">Veuillez mettre à notre disposition la somme de : <?php echo e(number_format($demandeFonds->solde, 0, ',', ' ')); ?></Label>
</body>

</html>
<?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/pdf/demande_fonds.blade.php ENDPATH**/ ?>