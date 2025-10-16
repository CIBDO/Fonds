<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État des Recettes PCS - {{ $programme }} {{ $annee }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.3;
        }
        .container {
            margin: 0;
            padding: 0 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-left {
            text-align: left;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header-right {
            text-align: right;
            font-weight: bold;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        h2 {
            font-size: 14px;
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
        }
        th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            font-size: 10px;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .numero-etat {
            font-weight: bold;
            text-align: right;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header-info">
            <div class="header-left">
                <div><strong>MINISTÈRE DE L'ÉCONOMIE ET DES FINANCES</strong></div>
                <div>DIRECTION GÉNÉRALE DU TRÉSOR ET DE LA COMPTABILITÉ PUBLIQUE</div>
                <div>AGENCE COMPTABLE CENTRALE DU TRÉSOR</div>
            </div>
            <div class="header-right">
                <div><strong>RÉPUBLIQUE DU MALI</strong></div>
                <div>Un Peuple - Un But - Une Foi</div>
            </div>
        </div>

        <div class="numero-etat">
            <div>ÉTAT N° ___________</div>
        </div>

        <!-- Titre -->
        <h1>ÉTAT DES RECETTES {{ $programme }}</h1>
        <h2>Programme de Consolidation des Statistiques (PCS)</h2>
        <h2>ANNÉE {{ $annee }}</h2>

        <!-- Tableau des recettes -->
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25%;">POSTE / BUREAU</th>
                    <th rowspan="2" style="width: 10%;">TYPE</th>
                    <th colspan="12">MOIS</th>
                    <th rowspan="2" style="width: 12%;">TOTAL</th>
                </tr>
                <tr>
                    <th>JAN</th>
                    <th>FÉV</th>
                    <th>MAR</th>
                    <th>AVR</th>
                    <th>MAI</th>
                    <th>JUN</th>
                    <th>JUL</th>
                    <th>AOU</th>
                    <th>SEP</th>
                    <th>OCT</th>
                    <th>NOV</th>
                    <th>DÉC</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalGeneral = 0;
                    $totauxMois = array_fill(1, 12, 0);
                @endphp

                @forelse ($donnees as $entite)
                    <tr>
                        <td class="text-left">{{ $entite['nom'] }}</td>
                        <td>{{ $entite['type'] }}</td>
                        @for ($mois = 1; $mois <= 12; $mois++)
                            @php
                                $montant = $entite['mois'][$mois] ?? 0;
                                $totauxMois[$mois] += $montant;
                            @endphp
                            <td class="text-right">
                                {{ number_format($montant, 0, ',', ' ') }}
                            </td>
                        @endfor
                        <td class="text-right">
                            <strong>{{ number_format($entite['total'], 0, ',', ' ') }}</strong>
                        </td>
                    </tr>
                    @php
                        $totalGeneral += $entite['total'];
                    @endphp
                @empty
                    <tr>
                        <td colspan="15" class="text-center">Aucune donnée disponible</td>
                    </tr>
                @endforelse

                <!-- Ligne des totaux -->
                @if (!empty($donnees))
                    <tr class="total-row">
                        <td colspan="2" class="text-left"><strong>TOTAL GÉNÉRAL</strong></td>
                        @for ($mois = 1; $mois <= 12; $mois++)
                            <td class="text-right">
                                <strong>{{ number_format($totauxMois[$mois], 0, ',', ' ') }}</strong>
                            </td>
                        @endfor
                        <td class="text-right">
                            <strong>{{ number_format($totalGeneral, 0, ',', ' ') }}</strong>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Pied de page -->
        <div class="footer">
            <p>Édité le : {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="signature">
            <p>L'Agent Comptable Central du Trésor</p>
            <br><br>
            <p>______________________________</p>
        </div>
    </div>
</body>
</html>

