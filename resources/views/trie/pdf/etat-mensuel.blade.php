<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation des Paiements TRIE/CCIM - {{ $nomMois }} {{ $annee }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 9px;
            line-height: 1.2;
        }

        .header {
            margin-bottom: 25px;
        }

        .header-left {
            float: left;
            width: 45%;
            text-align: left;
        }

        .header-right {
            float: right;
            width: 45%;
            text-align: right;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .title {
            font-weight: bold;
            font-size: 12px;
            margin: 3px 0;
        }

        .subtitle {
            font-size: 10px;
            margin: 2px 0;
        }

        .stars {
            font-size: 8px;
            margin: 3px 0;
        }

        .main-title {
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
            margin: 25px 0 20px 0;
            text-align: center;
        }

        .reference {
            text-align: left;
            font-size: 9px;
            margin-bottom: 15px;
            font-style: italic;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 8px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 7px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header clearfix">
        <div class="header-left">
            <div class="title">MINISTERE DE L'ECONOMIE</div>
            <div class="title">ET DES FINANCES</div>
            <div class="stars">* * * * *</div>
            <div class="subtitle">DIRECTION GENERALE DU TRESOR</div>
            <div class="subtitle">ET DE LA COMPTABILITE PUBLIQUE</div>
        </div>
        <div class="header-right">
            <div class="title">REPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
            <div class="stars">* * * * *</div>
        </div>
    </div>

    <!-- Titre principal -->
    <div class="main-title">
        SITUATION DES PAIEMENTS CFG-TRIE / CCIM<br>
        DU MOIS DE {{ strtoupper($nomMois) }} {{ $annee }}
    </div>

    <!-- Référence -->
    {{-- <div class="reference">
        Réf: lettre circulaire n°00006/DNTCP-DN du 22 janvier 2025 relative au paiement des cotisations du fonds TRIE
    </div>
 --}}
    <!-- Tableau des paiements -->
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Postes</th>
                <th style="width: 18%;">Recouvrements<br>du mois courant</th>
                <th style="width: 18%;">Apurement<br>solde antérieur</th>
                <th style="width: 15%;">Montants<br>Payés</th>
                <th style="width: 20%;">Réf. Paiement</th>
                <th style="width: 9%;">Observations</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donneesParPoste as $donnees)
            <tr>
                <td class="text-left"><strong>{{ $donnees['nom'] }}</strong></td>
                <td class="text-right">{{ number_format($donnees['recouvrement_courant'], 0, ',', ' ') }}</td>
                <td class="text-right">
                    @if($donnees['apurement'] > 0)
                        {{ number_format($donnees['apurement'], 0, ',', ' ') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">{{ number_format($donnees['montant_total'], 0, ',', ' ') }}</td>
                <td class="text-left" style="font-size: 7px;">
                    @if(count($donnees['references']) > 0)
                        {{ implode('; ', array_unique($donnees['references'])) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-left" style="font-size: 7px;">
                    @if(count($donnees['observations']) > 0)
                        {{ implode('; ', array_unique($donnees['observations'])) }}
                    @endif
                </td>
            </tr>
            @endforeach

            <!-- Ligne TOTAL -->
            <tr class="total-row">
                <td class="text-left"><strong>TOTAL</strong></td>
                <td class="text-right">{{ number_format($totalRecouvrement, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($totalApurement, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($totalGeneral, 0, ',', ' ') }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Section signature -->
   {{--  <div class="signature-section">
        <p>
            Le Directeur National du Trésor<br>
            et de la Comptabilité Publique<br>
            <br><br>
            _________________________
        </p>
    </div> --}}
</body>
</html>

