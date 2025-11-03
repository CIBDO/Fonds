<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bordereau de Déstockage - {{ $destockage->reference_destockage }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 10px;
            line-height: 1.3;
        }

        .container {
            width: 100%;
        }

        /* En-tête officiel style PCS */
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
            font-size: 11px;
            margin: 3px 0;
        }

        .subtitle {
            font-size: 9px;
            margin: 2px 0;
        }

        .stars {
            font-size: 8px;
            margin: 3px 0;
        }

        .main-title {
            font-size: 12px;
            font-weight: bold;
            text-decoration: underline;
            margin: 25px 0 15px 0;
            text-align: center;
        }

        .subtitle-period {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }

        .doc-info {
            margin-bottom: 20px;
            font-size: 9px;
            border: 1px solid #000;
            padding: 10px;
        }

        .doc-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .doc-info td {
            padding: 4px 8px;
            border: none;
        }

        .doc-info .label {
            font-weight: bold;
            width: 35%;
        }

        .doc-info .value {
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px 3px;
            text-align: center;
        }

        th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 8px;
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
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .observation {
            margin: 15px 0;
            padding: 8px;
            border: 1px solid #000;
            background-color: #fffbea;
            font-size: 9px;
        }

        .observation strong {
            text-decoration: underline;
        }

        .signature {
            margin-right: 80px;
            text-align: right;
            margin-top: 40px;
        }

        .date-signature {
            font-size: 10px;
            margin-bottom: 60px;
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

        .note-montants {
            font-size: 9px;
            font-style: italic;
            text-align: center;
            margin: 10px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête officiel style PCS -->
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
            BORDEREAU DE DÉSTOCKAGE DES FONDS PCS-{{ $destockage->programme }}
        </div>

        <div class="subtitle-period">
            AU TITRE DU MOIS DE {{ strtoupper($destockage->nom_mois) }} {{ $destockage->periode_annee }}
        </div>

        <!-- Informations du document -->
        <div class="doc-info">
            <table>
                <tr>
                    <td class="label">Référence du Déstockage :</td>
                    <td class="value" colspan="3"><strong>{{ $destockage->reference_destockage }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Programme :</td>
                    <td class="value"><strong>{{ $destockage->programme }}</strong></td>
                    <td class="label">Date de Déstockage :</td>
                    <td class="value"><strong>{{ \Carbon\Carbon::parse($destockage->date_destockage)->format('d/m/Y') }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Nombre d'Entités :</td>
                    <td class="value"><strong>{{ $destockage->postes->count() }}</strong></td>
                    <td class="label">Montant Total Déstocké :</td>
                    <td class="value"><strong>{{ number_format($destockage->montant_total_destocke, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
            </table>
        </div>

        <div class="note-montants">
            (Montants en francs CFA)
        </div>

        <!-- Tableau détail par poste -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">N°</th>
                    <th style="width: 30%;">POSTE / BUREAU</th>
                    <th style="width: 15%;">MONTANT COLLECTÉ</th>
                    <th style="width: 15%;">MONTANT DÉSTOCKÉ</th>
                    <th style="width: 15%;">SOLDE AVANT</th>
                    <th style="width: 15%;">SOLDE APRÈS</th>
                    <th style="width: 5%;">%</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalCollecte = 0;
                    $totalDestocke = 0;
                    $totalSoldeAvant = 0;
                    $totalSoldeApres = 0;
                @endphp
                @foreach($destockage->postes as $index => $posteDestockage)
                @php
                    $totalCollecte += $posteDestockage->montant_collecte;
                    $totalDestocke += $posteDestockage->montant_destocke;
                    $totalSoldeAvant += $posteDestockage->solde_avant;
                    $totalSoldeApres += $posteDestockage->solde_apres;
                    $taux = $posteDestockage->montant_collecte > 0
                        ? ($posteDestockage->montant_destocke / $posteDestockage->montant_collecte) * 100
                        : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">
                        @if($posteDestockage->poste_id)
                            <strong>{{ $posteDestockage->poste->nom ?? 'N/A' }}</strong>
                        @else
                            <strong>{{ $posteDestockage->bureauDouane->libelle ?? 'N/A' }}</strong>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($posteDestockage->montant_collecte, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($posteDestockage->montant_destocke, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($posteDestockage->solde_avant, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($posteDestockage->solde_apres, 0, ',', ' ') }}</td>
                    <td class="text-center">{{ number_format($taux, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-right"><strong>TOTAUX</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalCollecte, 0, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalDestocke, 0, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalSoldeAvant, 0, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalSoldeApres, 0, ',', ' ') }}</strong></td>
                    <td class="text-center"><strong>{{ $totalCollecte > 0 ? number_format(($totalDestocke / $totalCollecte) * 100, 1) : 0 }}%</strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Observation -->
        @if($destockage->observation)
        <div class="observation">
            <strong>Observation :</strong> {{ $destockage->observation }}
        </div>
        @endif

        <!-- Signature -->
        <div class="signature">
            <div class="date-signature">
                Bamako, le {{ \Carbon\Carbon::parse($destockage->date_destockage)->format('d/m/Y') }}
            </div>
            <div class="agent-signature">
                L'Agent Comptable Central du Trésor
            </div>
        </div>
    </div>
</body>
</html>

