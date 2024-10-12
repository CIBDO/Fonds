<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Fonds - Impression</title>
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
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header div {
            width: 30%;
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
    <div class="container">
        <div style="display: flex; justify-content: space-between;">
            <div>
                <strong>Ministère de l'Économie et des Finances</strong>
                <span style="margin-left: 800px;"><strong>République du Mali</strong></span><br>
                <strong>Direction Nationale du Trésor et de la </strong>
                <span style="margin-left: 790px;"><strong>Un Peuple - Un But - Une Foi</strong></span><br>
                <span style="margin-left: 45px;"><strong>Comptabilité Publique</strong></span>
            </div>
        </div>
    </div>
    <!-- Titre de la demande de fods -->
    <h2>Demande de Fonds</h2>

    <!-- En-tête avec la date d'envoi, le poste et le mois -->
    <div class="header">
        <div>
            <label>Date d'envoi : <strong>{{ $demandeFonds->created_at ? $demandeFonds->created_at->format('d/m/Y') : 'N/A' }}</strong></label>
        </div>
        <div>
            <label>Poste : <strong>{{ $demandeFonds->poste->nom ?? 'N/A' }}</strong></label>
        </div>
        <div>
            <label>Salaire du mois de : <strong>{{ $demandeFonds->mois . ' ' . $demandeFonds->annee }}</strong></label>
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
            <tr>
                <td>Fonctionnaires BCS</td>
                <td>{{ number_format($demandeFonds->fonctionnaires_bcs_net, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->fonctionnaires_bcs_revers, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->fonctionnaires_bcs_total_courant, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->fonctionnaires_bcs_salaire_ancien, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->fonctionnaires_bcs_total_courant - $demandeFonds->fonctionnaires_bcs_salaire_ancien, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Personnel Collectivité Santé</td>
                <td>{{ number_format($demandeFonds->collectivite_sante_net, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->collectivite_sante_revers, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->collectivite_sante_total_courant, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->collectivite_sante_salaire_ancien, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->collectivite_sante_total_courant - $demandeFonds->collectivite_sante_salaire_ancien, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Personnel Collectivité Éducation</td>
                <td>{{ number_format($demandeFonds->collectivite_education_net, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->collectivite_education_revers, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->collectivite_education_total_courant, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->collectivite_education_salaire_ancien, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->collectivite_education_total_courant - $demandeFonds->collectivite_education_salaire_ancien, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Personnels Saisonniers</td>
                <td>{{ number_format($demandeFonds->personnels_saisonniers_net, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->personnels_saisonniers_revers, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->personnels_saisonniers_total_courant, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->personnels_saisonniers_salaire_ancien, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->personnels_saisonniers_total_courant - $demandeFonds->personnels_saisonniers_salaire_ancien, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>EPN</td>
                <td>{{ number_format($demandeFonds->epn_net, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->epn_revers, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->epn_total_courant, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->epn_salaire_ancien, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->epn_total_courant - $demandeFonds->epn_salaire_ancien, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>CED</td>
                <td>{{ number_format($demandeFonds->ced_net, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->ced_revers, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->ced_total_courant, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->ced_salaire_ancien, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->ced_total_courant - $demandeFonds->ced_salaire_ancien, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>ECOM</td>
                <td>{{ number_format($demandeFonds->ecom_net, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->ecom_revers, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->ecom_total_courant, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->ecom_salaire_ancien, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->ecom_total_courant - $demandeFonds->ecom_salaire_ancien, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>CFP CPAM</td>
                <td>{{ number_format($demandeFonds->cfp_cpam_net, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->cfp_cpam_revers, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->cfp_cpam_total_courant, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->cfp_cpam_salaire_ancien, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->cfp_cpam_total_courant - $demandeFonds->cfp_cpam_salaire_ancien, 0, ',', ' ') }}</td>
            </tr>
            <tr class="total-row">
                <td>Total</td>
                <td>{{ number_format($demandeFonds->total_net, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->total_revers, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->total_courant, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->total_ancien, 0, ',', ' ') }}</td>
                <td>{{ number_format($demandeFonds->total_courant - $demandeFonds->total_ancien, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>
    </div>
</body>
<Label style="text-align: center; font-weight: bold; font-size: 22px;">Veuillez mettre à notre disposition la somme de : {{ number_format($demandeFonds->total_courant, 0, ',', ' ') }}</Label>
</html>
