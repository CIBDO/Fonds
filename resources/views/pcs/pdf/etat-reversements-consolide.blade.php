<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation des Reversements PCS - {{ $programme }} {{ $annee }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 5px;
            font-size: 8px;
            line-height: 1.1;
        }

        .header {
            margin-bottom: 15px;
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
            font-size: 10px;
            margin: 2px 0;
        }

        .subtitle {
            font-size: 8px;
            margin: 1px 0;
        }

        .stars {
            font-size: 7px;
            margin: 2px 0;
        }

        .main-title {
            font-size: 11px;
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0 8px 0;
            text-align: center;
        }

        .subtitle-period {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 6.5px;
        }

        th, td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            white-space: nowrap;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 6px;
        }

        .text-left {
            text-align: left;
            white-space: normal;
        }

        .text-right {
            text-align: right;
            white-space: nowrap;
        }

        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .signature {
            margin-right: 80px;
            text-align: right;
            margin-top: 15px;
        }

        .date-signature {
            font-size: 8px;
            margin-bottom: 30px;
        }

        .agent-signature {
            font-size: 8px;
            font-weight: bold;
        }

        .negative {
            color: #d32f2f;
        }

        .positive {
            color: #2e7d32;
        }

        .table-section {
            margin-bottom: 10px;
        }

        .table-title {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
            text-decoration: underline;
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
        SITUATION CONSOLIDÉE DES RECOUVREMENTS ET REVERSEMENTS DU PCS-{{ $programme }} AU TITRE DE L'EXERCICE {{ $annee }}
    </div>

    <div class="subtitle-period">
        PÉRIODE DU 01/01/{{ $annee }} AU {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

    <div style="text-align: center; font-size: 8px; font-style: italic; margin-bottom: 8px; color: #666;">
        (Montants en francs CFA)
    </div>

    <!-- Tableau RECOUVREMENTS -->
    <div class="table-section">
        <div class="table-title">RECOUVREMENTS {{ $annee }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 14%;">POSTES</th>
                    <th style="width: 5.5%;">JANV.</th>
                    <th style="width: 5.5%;">FÉV.</th>
                    <th style="width: 5.5%;">MARS</th>
                    <th style="width: 5.5%;">AVR.</th>
                    <th style="width: 5.5%;">MAI</th>
                    <th style="width: 5.5%;">JUIN</th>
                    <th style="width: 5.5%;">JUIL.</th>
                    <th style="width: 5.5%;">AOÛT</th>
                    <th style="width: 5.5%;">SEPT.</th>
                    <th style="width: 5.5%;">OCT.</th>
                    <th style="width: 5.5%;">NOV.</th>
                    <th style="width: 5.5%;">DÉC.</th>
                    <th style="width: 14%;">TOTAL GÉNÉRAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recouvrementsParPoste as $poste => $data)
                <tr>
                    <td class="text-left"><strong>{{ $poste }}</strong></td>
                    @for($mois = 1; $mois <= 12; $mois++)
                        <td class="text-right">{{ number_format($data['mois'][$mois] ?? 0, 0, ',', ' ') }}</td>
                    @endfor
                    <td class="text-right"><strong>{{ number_format($data['total'], 0, ',', ' ') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td class="text-left"><strong>TOTAL recouvrements</strong></td>
                    @for($mois = 1; $mois <= 12; $mois++)
                        <td class="text-right"><strong>{{ number_format($totalRecouvrementsMensuel[$mois] ?? 0, 0, ',', ' ') }}</strong></td>
                    @endfor
                    <td class="text-right"><strong>{{ number_format($totalRecouvrements, 0, ',', ' ') }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td class="text-left"><strong>TOTAL liquidations</strong></td>
                    @for($mois = 1; $mois <= 12; $mois++)
                        <td class="text-right"><strong>0</strong></td>
                    @endfor
                    <td class="text-right"><strong>0</strong></td>
                </tr>
                <tr class="total-row">
                    <td class="text-left"><strong>Reste à recouvrer</strong></td>
                    @for($mois = 1; $mois <= 12; $mois++)
                        <td class="text-right"><strong>0</strong></td>
                    @endfor
                    <td class="text-right {{ ($totalRecouvrements - $totalRecouvrements) < 0 ? 'negative' : 'positive' }}">
                        <strong>{{ number_format($totalRecouvrements - $totalRecouvrements, 0, ',', ' ') }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Tableau REVERSEMENTS -->
    <div class="table-section">
        <div class="table-title">REVERSEMENTS {{ $annee }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 14%;">POSTES</th>
                    <th style="width: 5.5%;">JANV.</th>
                    <th style="width: 5.5%;">FÉV.</th>
                    <th style="width: 5.5%;">MARS</th>
                    <th style="width: 5.5%;">AVR.</th>
                    <th style="width: 5.5%;">MAI</th>
                    <th style="width: 5.5%;">JUIN</th>
                    <th style="width: 5.5%;">JUIL.</th>
                    <th style="width: 5.5%;">AOÛT</th>
                    <th style="width: 5.5%;">SEPT.</th>
                    <th style="width: 5.5%;">OCT.</th>
                    <th style="width: 5.5%;">NOV.</th>
                    <th style="width: 5.5%;">DÉC.</th>
                    <th style="width: 14%;">TOTAL GÉNÉRAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reversementsParPoste as $poste => $data)
                <tr>
                    <td class="text-left"><strong>{{ $poste }}</strong></td>
                    @for($mois = 1; $mois <= 12; $mois++)
                        <td class="text-right">{{ number_format($data['mois'][$mois] ?? 0, 0, ',', ' ') }}</td>
                    @endfor
                    <td class="text-right"><strong>{{ number_format($data['total'], 0, ',', ' ') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td class="text-left"><strong>TOTAL reversements</strong></td>
                    @for($mois = 1; $mois <= 12; $mois++)
                        <td class="text-right"><strong>{{ number_format($totalReversementsMensuel[$mois] ?? 0, 0, ',', ' ') }}</strong></td>
                    @endfor
                    <td class="text-right"><strong>{{ number_format($totalReversements, 0, ',', ' ') }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td class="text-left"><strong>TOTAL recouvrements</strong></td>
                    @for($mois = 1; $mois <= 12; $mois++)
                        <td class="text-right"><strong>{{ number_format($totalRecouvrementsMensuel[$mois] ?? 0, 0, ',', ' ') }}</strong></td>
                    @endfor
                    <td class="text-right"><strong>{{ number_format($totalRecouvrements, 0, ',', ' ') }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td class="text-left"><strong>Reste à reverser</strong></td>
                    @for($mois = 1; $mois <= 12; $mois++)
                        <td class="text-right {{ ($totalRecouvrementsMensuel[$mois] - ($totalReversementsMensuel[$mois] ?? 0)) > 0 ? 'negative' : 'positive' }}">
                            <strong>{{ number_format(($totalRecouvrementsMensuel[$mois] ?? 0) - ($totalReversementsMensuel[$mois] ?? 0), 0, ',', ' ') }}</strong>
                        </td>
                    @endfor
                    <td class="text-right {{ ($totalRecouvrements - $totalReversements) > 0 ? 'negative' : 'positive' }}">
                        <strong>{{ number_format($totalRecouvrements - $totalReversements, 0, ',', ' ') }}</strong>
                    </td>
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
