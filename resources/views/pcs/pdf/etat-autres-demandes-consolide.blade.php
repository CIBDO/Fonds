<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation des Autres Demandes PCS - {{ $annee }}</title>
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

        .subtitle-period {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 8px;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
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

        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .total-row td:last-child {
            white-space: nowrap;
            min-width: 120px;
        }

        .signature {
            margin-right: 100px;
            text-align: right;
            margin-top: 30px;
        }

        .date-signature {
            font-size: 10px;
            margin-bottom: 50px;
        }

        .agent-signature {
            font-size: 10px;
            font-weight: bold;
        }

        .negative {
            color: #d32f2f;
        }

        .positive {
            color: #2e7d32;
        }

        .table-section {
            margin-bottom: 30px;
        }

        .table-title {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }

        .status-soumis {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-valide {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejete {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-brouillon {
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header clearfix">
        <div class="header-left">
            <div class="title">MINISTÈRE DE L'ÉCONOMIE</div>
            <div class="title">ET DES FINANCES</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            <div class="subtitle">DIRECTION GÉNÉRALE DU TRÉSOR</div>
            <div class="subtitle">ET DE LA COMPTABILITÉ PUBLIQUE</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            <div class="subtitle">AGENCE COMPTABLE CENTRALE DU TRÉSOR</div>
        </div>

        <div class="header-right">
            <div class="title">RÉPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
            <div class="stars" style="margin-right: 40px;">**************</div>
        </div>
    </div>

    <!-- Titre principal -->
    <div class="main-title">
        SITUATION DES AUTRES DEMANDES PCS AU TITRE DE L'EXERCICE {{ $annee }}
    </div>

    <div class="subtitle-period">
        PÉRIODE DU 01/01/{{ $annee }} AU {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

    <!-- Tableau DÉTAILLÉ DES AUTRES DEMANDES VALIDÉES -->
    <div class="table-section">
        <div class="table-title">DÉTAIL DES AUTRES DEMANDES VALIDÉES {{ $annee }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">DATE</th>
                    <th style="width: 15%;">POSTE</th>
                    <th style="width: 30%;">DÉSIGNATION</th>
                    <th style="width: 12%;">DEMANDÉ (Millions FCFA)</th>
                    <th style="width: 12%;">ACCORDÉ (Millions FCFA)</th>
                    <th style="width: 12%;">ÉCART (Millions FCFA)</th>
                    <th style="width: 11%;">% ACCORDÉ</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalDemande = 0;
                    $totalAccorde = 0;
                @endphp
                @foreach($autresDemandes as $demande)
                @php
                    $montantDemande = $demande->montant / 1000000;
                    $montantAccorde = ($demande->montant_accord ?? $demande->montant) / 1000000;
                    $ecart = $montantAccorde - $montantDemande;
                    $pourcentage = $demande->montant > 0 ? round(($demande->montant_accord ?? $demande->montant) / $demande->montant * 100, 1) : 0;

                    $totalDemande += $demande->montant;
                    $totalAccorde += ($demande->montant_accord ?? $demande->montant);
                @endphp
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($demande->date_demande)->format('d/m/Y') }}</td>
                    <td class="text-left"><strong>{{ $demande->poste->nom }}</strong></td>
                    <td class="text-left">{{ $demande->designation }}</td>
                    <td class="text-right">{{ number_format($montantDemande, 3, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($montantAccorde, 3, ',', ' ') }}</td>
                    <td class="text-right {{ $ecart > 0 ? 'positive' : ($ecart < 0 ? 'negative' : '') }}">
                        {{ number_format($ecart, 3, ',', ' ') }}
                    </td>
                    <td class="text-center">{{ $pourcentage }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-left"><strong>TOTAL GÉNÉRAL</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalDemande / 1000000, 3, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalAccorde / 1000000, 3, ',', ' ') }}</strong></td>
                    <td class="text-right {{ ($totalAccorde - $totalDemande) > 0 ? 'positive' : (($totalAccorde - $totalDemande) < 0 ? 'negative' : '') }}">
                        <strong>{{ number_format(($totalAccorde - $totalDemande) / 1000000, 3, ',', ' ') }}</strong>
                    </td>
                    <td class="text-center"><strong>{{ $totalDemande > 0 ? round(($totalAccorde / $totalDemande) * 100, 1) : 0 }}%</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Signature -->
    <div class="signature">
        <div class="date-signature">
            Bamako, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            L'Agent Comptable Central du Trésor
        </div>
    </div>
</body>
</html>
