<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Fonds - Impression</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 12px;
            line-height: 1.3;
        }

        .container {
            margin: 0;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        h2 {
            font-size: 16px;
            text-align: center;
            margin: 10px 0;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header-text {
            margin: 0 0 15px 0;
            line-height: 1.3;
            width: 100%;
            display: flex;
            justify-content: space-between;
            box-sizing: border-box;
        }

        .header-left {
            width: 60%;
            text-align: left;
            padding-right: 20px;
        }

        .header-right {
            width: 40%;
            text-align: right;
            padding-right: 5px;
        }

        .header-text strong {
            font-size: 13px;
            display: block;
            margin-bottom: 3px;
            white-space: nowrap;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #000;
            background-color: #f9f9f9;
        }

        .info-item {
            flex: 1;
            margin: 3px 8px;
            min-width: 180px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            table-layout: fixed;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 6px 4px;
            text-align: center;
            font-size: 11px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }

        th:first-child { width: 20%; }
        th:nth-child(2) { width: 15%; }
        th:nth-child(3) { width: 15%; }
        th:nth-child(4) { width: 15%; }
        th:nth-child(5) { width: 15%; }
        th:nth-child(6) { width: 20%; }

        .total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
            font-size: 12px;
        }

        .montant-final {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            margin: 15px 0;
            padding: 8px;
            border: 1px solid #000;
            background-color: #f9f9f9;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 12px;
        }

        .agent-info {
            text-align: left;
        }

        .comptable-info {
            text-align: right;
            padding-right: 5px;
        }

        @media print {
            .back-to-index {
                display: none;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .container {
                padding: 5px;
            }

            table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-text">
            <div class="header-left">
                <strong>MINISTÈRE DE L'ÉCONOMIE ET DES FINANCES</strong>
                <strong>DIRECTION NATIONALE DU TRÉSOR</strong>
                <strong>ET DE LA COMPTABILITÉ PUBLIQUE</strong>
                <strong>AGENCE COMPTABLE CENTRALE DU TRÉSOR</strong>
            </div>
            <div class="header-right">
                <strong style="letter-spacing: 0.5px;">RÉPUBLIQUE DU MALI</strong>
                <strong style="letter-spacing: 0.5px;">UN PEUPLE - UN BUT - UNE FOI</strong>
            </div>
        </div>

        <h2>Demande de Fonds</h2>

        <div class="info-section">
            <div class="info-item">
                <strong>Date d'envoi :</strong> {{ $demandeFonds->created_at ? $demandeFonds->created_at->format('d/m/Y') : 'N/A' }}
            </div>
            <div class="info-item">
                <strong>Poste :</strong> {{ $demandeFonds->poste->nom ?? 'N/A' }}
            </div>
            <div class="info-item">
                <strong>Recette Douanière :</strong> {{ number_format($demandeFonds->montant_disponible, 0, ',', ' ') }}
            </div>
            <div class="info-item">
                <strong>Salaire du mois de :</strong> {{ $demandeFonds->mois . ' ' . $demandeFonds->annee }}
            </div>
        </div>

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
                @foreach(['fonctionnaires_bcs' => 'Fonctionnaires BCS',
                         'collectivite_sante' => 'Personnel Collectivité Santé',
                         'collectivite_education' => 'Personnel Collectivité Éducation',
                         'personnels_saisonniers' => 'Personnels Saisonniers',
                         'epn' => 'EPN',
                         'ced' => 'CED',
                         'ecom' => 'ECOM',
                         'cfp_cpam' => 'CFP CPAM'] as $key => $label)
                    @if($demandeFonds->{$key . '_net'} || $demandeFonds->{$key . '_revers'} || $demandeFonds->{$key . '_total_courant'} || $demandeFonds->{$key . '_salaire_ancien'})
                        <tr>
                            <td>{{ $label }}</td>
                            <td>{{ $demandeFonds->{$key . '_net'} ? number_format($demandeFonds->{$key . '_net'}, 0, ',', ' ') : '-' }}</td>
                            <td>{{ $demandeFonds->{$key . '_revers'} ? number_format($demandeFonds->{$key . '_revers'}, 0, ',', ' ') : '-' }}</td>
                            <td>{{ $demandeFonds->{$key . '_total_courant'} ? number_format($demandeFonds->{$key . '_total_courant'}, 0, ',', ' ') : '-' }}</td>
                            <td>{{ $demandeFonds->{$key . '_salaire_ancien'} ? number_format($demandeFonds->{$key . '_salaire_ancien'}, 0, ',', ' ') : '-' }}</td>
                            <td>{{ ($demandeFonds->{$key . '_total_courant'} && $demandeFonds->{$key . '_salaire_ancien'}) ? number_format($demandeFonds->{$key . '_total_courant'} - $demandeFonds->{$key . '_salaire_ancien'}, 0, ',', ' ') : '-' }}</td>
                        </tr>
                    @endif
                @endforeach
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td>{{ $demandeFonds->total_net ? number_format($demandeFonds->total_net, 0, ',', ' ') : '-' }}</td>
                    <td>{{ $demandeFonds->total_revers ? number_format($demandeFonds->total_revers, 0, ',', ' ') : '-' }}</td>
                    <td>{{ $demandeFonds->total_courant ? number_format($demandeFonds->total_courant, 0, ',', ' ') : '-' }}</td>
                    <td>{{ $demandeFonds->total_ancien ? number_format($demandeFonds->total_ancien, 0, ',', ' ') : '-' }}</td>
                    <td>{{ ($demandeFonds->total_courant && $demandeFonds->total_ancien) ? number_format($demandeFonds->total_courant - $demandeFonds->total_ancien, 0, ',', ' ') : '-' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="montant-final">
            Veuillez mettre à notre disposition la somme de : {{ number_format($demandeFonds->solde, 0, ',', ' ') }} FCFA
        </div>

        <div class="signature-section">
            <div class="agent-info">
                <p><strong>Agent Traitant :</strong></p>
                <p>{{ $demandeFonds->user->name ?? 'N/A' }}</p>
            </div>
            <div class="comptable-info">
                <p>Bamako, le {{ $demandeFonds->created_at ? $demandeFonds->created_at->format('d/m/Y') : date('d/m/Y') }}</p>
                <p style="margin-top: 30px;"><strong>L'Agent Comptable Central</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
