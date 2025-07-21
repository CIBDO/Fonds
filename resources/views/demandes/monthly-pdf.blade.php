<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes de Fonds - {{ $mois }} {{ $annee }}</title>
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
            position: relative;
        }

        .header-left {
            width: 60%;
            text-align: left;
            display: flex; /* Pour aligner le logo et le texte */
            align-items: center; /* Centrer verticalement logo et texte */
        }

        .logo {
            width: 80px;
            height: auto;
            margin-right: 15px;
        }

        /* .header-right {
            width: 40%;
            text-align: right;
            padding-right: 5px;
        } */


        .header-text strong {
            font-size: 13px;
            display: block;
            margin-bottom: 3px;
            white-space: nowrap;
            position: relative;
        }

        .right-text {
            position: absolute;
            right: -300px;
            display: inline-block;
        }

        .month-summary {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #000;
            background-color: #f9f9f9;
        }

        .demande-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
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

        .total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
            font-size: 12px;
        }

        .poste-header {
            margin-top: 20px;
            padding: 5px;
            background-color: #f0f0f0;
            border: 1px solid #000;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-text">
            <div class="header-left">
                @if(file_exists(public_path('img/logo.png')))
                    <img src="{{ public_path('img/logo.png') }}" alt="Logo" class="logo">
                @endif
                <div>
                    <strong>MINISTÈRE DE L'ÉCONOMIE ET DES FINANCES <span class="right-text">RÉPUBLIQUE DU MALI</span></strong>
                    <strong>DIRECTION NATIONALE DU TRÉSOR <span class="right-text" style="text-align: 20px">UN PEUPLE - UN BUT - UNE FOI</span></strong>
                    <strong>ET DE LA COMPTABILITÉ PUBLIQUE</strong>
                    <p style="text-align: left; margin-left: 100px;">******************</p>
                    <strong>AGENCE COMPTABLE CENTRALE DU TRÉSOR</strong>
                </div>
            </div>
            {{-- <div class="header-right">
                <strong style="letter-spacing: 0.5px;">RÉPUBLIQUE DU MALI</strong>
                <strong style="letter-spacing: 0.5px;">UN PEUPLE - UN BUT - UNE FOI</strong>
            </div> --}}
        </div>

        <h2>Récapitulatif des Demandes de Fonds - {{ $mois }} {{ $annee }}</h2>

        <div class="month-summary">
            <p><strong>Nombre total de demandes :</strong> {{ count($demandes) }}</p>
            <p><strong>Montant total demandé :</strong> {{ number_format(abs($totalDemande), 0, ',', ' ') }} FCFA</p>
            <p><strong>Montant total disponible :</strong> {{ number_format($totalDisponible, 0, ',', ' ') }} FCFA</p>
        </div>

        @foreach($demandes as $demande)
        <div class="demande-section">
            <div class="poste-header">
                <strong>Poste :</strong> {{ $demande->poste->nom ?? 'N/A' }} |
                <strong>Date d'envoi :</strong> {{ $demande->created_at ? $demande->created_at->format('d/m/Y') : 'N/A' }}
                <br>
                <strong>Recette douanière :</strong> {{ number_format($demande->montant_disponible, 0, ',', ' ') }} FCFA
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
                        @if($demande->{$key . '_net'} || $demande->{$key . '_revers'} || $demande->{$key . '_total_courant'} || $demande->{$key . '_salaire_ancien'})
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $demande->{$key . '_net'} ? number_format($demande->{$key . '_net'}, 0, ',', ' ') : '-' }}</td>
                                <td>{{ $demande->{$key . '_revers'} ? number_format($demande->{$key . '_revers'}, 0, ',', ' ') : '-' }}</td>
                                <td>{{ $demande->{$key . '_total_courant'} ? number_format($demande->{$key . '_total_courant'}, 0, ',', ' ') : '-' }}</td>
                                <td>{{ $demande->{$key . '_salaire_ancien'} ? number_format($demande->{$key . '_salaire_ancien'}, 0, ',', ' ') : '-' }}</td>
                                <td>{{ ($demande->{$key . '_total_courant'} && $demande->{$key . '_salaire_ancien'}) ? number_format($demande->{$key . '_total_courant'} - $demande->{$key . '_salaire_ancien'}, 0, ',', ' ') : '-' }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="total-row">
                        <td>TOTAL</td>
                        <td>{{ $demande->total_net ? number_format($demande->total_net, 0, ',', ' ') : '-' }}</td>
                        <td>{{ $demande->total_revers ? number_format($demande->total_revers, 0, ',', ' ') : '-' }}</td>
                        <td>{{ $demande->total_courant ? number_format($demande->total_courant, 0, ',', ' ') : '-' }}</td>
                        <td>{{ $demande->total_ancien ? number_format($demande->total_ancien, 0, ',', ' ') : '-' }}</td>
                        <td>{{ ($demande->total_courant && $demande->total_ancien) ? number_format($demande->total_courant - $demande->total_ancien, 0, ',', ' ') : '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: right; margin-top: 10px;">
                <strong>Montant demandé :</strong> {{ number_format($demande->solde, 0, ',', ' ') }} FCFA
            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
        @endforeach
    </div>
</body>
</html>
