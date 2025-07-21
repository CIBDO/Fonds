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
            padding: 0;
            font-size: 12px;
            line-height: 1.3;
        }
        .container {
            margin: 0;
            padding: 0 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .header-text {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 10px;
        }
        .header-left {
            text-align: left;
            font-weight: bold;
        }
        .header-right {
            text-align: right;
            font-weight: bold;
        }
        h2 {
            font-size: 18px;
            text-align: center;
            margin: 30px 0 18px 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        .info-section {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            border: 1px solid #000;
            background-color: #fff;
            margin-bottom: 18px;
            padding: 8px 12px;
            font-size: 13px;
        }
        .info-section span {
            margin-right: 32px;
            font-weight: normal;
        }
        .info-section span strong {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 18px 0;
            table-layout: fixed;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 7px 4px;
            text-align: center;
            font-size: 12px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
            font-size: 13px;
        }
        .montant-final {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin: 18px 0 18px 0;
            padding: 8px;
            border: 1px solid #000;
            background-color: #fff;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            font-size: 13px;
        }
        .agent-info {
            text-align: left;
        }
        .comptable-info {
            text-align: right;
            padding-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- <div class="header-text">
            <div class="header-left">
                MINISTÈRE DE L'ÉCONOMIE ET DES FINANCES<br>
                DIRECTION NATIONALE DU TRÉSOR<br>
                ET DE LA COMPTABILITÉ PUBLIQUE<br>
                AGENCE COMPTABLE CENTRALE DU TRÉSOR
            </div>
            <div class="header-right">
                RÉPUBLIQUE DU MALI<br>
                UN PEUPLE - UN BUT - UNE FOI
            </div>
        </div> --}}
        <h2>DEMANDE DE FONDS</h2>
        <div class="info-section">
            <span><strong>Date d'envoi :</strong> {{ $demandeFonds->created_at ? $demandeFonds->created_at->format('d/m/Y') : 'N/A' }}</span>
            <span><strong>Poste :</strong> {{ $demandeFonds->poste->nom ?? 'N/A' }}</span>
            <span><strong>Recette Douanière :</strong> {{ number_format($demandeFonds->montant_disponible, 0, ',', ' ') }}</span>
            <span><strong>Salaire du mois de :</strong> {{ $demandeFonds->mois . ' ' . $demandeFonds->annee }}</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>DÉSIGNATION</th>
                    <th>SALAIRE NET</th>
                    <th>REVERS/SALAIRE</th>
                    <th>TOTAL MOIS COURANT</th>
                    <th>SALAIRE MOIS ANTÉRIEUR</th>
                    <th>ÉCART</th>
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
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $demandeFonds->{$key . '_net'} ? number_format($demandeFonds->{$key . '_net'}, 0, ',', ' ') : '-' }}</td>
                        <td>{{ $demandeFonds->{$key . '_revers'} ? number_format($demandeFonds->{$key . '_revers'}, 0, ',', ' ') : '-' }}</td>
                        <td>{{ $demandeFonds->{$key . '_total_courant'} ? number_format($demandeFonds->{$key . '_total_courant'}, 0, ',', ' ') : '-' }}</td>
                        <td>{{ $demandeFonds->{$key . '_salaire_ancien'} ? number_format($demandeFonds->{$key . '_salaire_ancien'}, 0, ',', ' ') : '-' }}</td>
                        <td>{{ ($demandeFonds->{$key . '_total_courant'} && $demandeFonds->{$key . '_salaire_ancien'}) ? number_format($demandeFonds->{$key . '_total_courant'} - $demandeFonds->{$key . '_salaire_ancien'}, 0, ',', ' ') : '-' }}</td>
                    </tr>
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
            Veuillez mettre à notre disposition la somme de : {{ number_format(abs($demandeFonds->solde), 0, ',', ' ') }} FCFA
        </div>
        <div class="signature-section">
            <div class="agent-info">
                <p><strong>Agent Traitant :</strong></p>
                <p>{{ $demandeFonds->user->name ?? 'N/A' }}</p>
            </div>
            <div class="comptable-info">
                {{-- <p>Bamako, le {{ $demandeFonds->created_at ? $demandeFonds->created_at->format('d/m/Y') : date('d/m/Y') }}</p> --}}
                <p style="margin-top: 30px; "><strong>Le Trésorier Payeur</strong></p>
            </div>
        </div>
    </div>
</body>
</html>



