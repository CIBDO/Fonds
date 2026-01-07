<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État de Collecte PCS - {{ $programme }} {{ $annee }}</title>
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

        .note-montants {
            font-size: 9px;
            font-style: italic;
            text-align: center;
            margin: 10px 0;
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

        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
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
        ÉTAT DE COLLECTE DES FONDS PCS-{{ $programme }} AU TITRE DE L'EXERCICE {{ $annee }}
    </div>

    {{-- <div class="subtitle-period">
        PÉRIODE DU 01/01/{{ $annee }} AU {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div> --}}

    <div class="note-montants">
        (Montants en francs CFA)
    </div>

    <!-- Tableau principal -->
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">POSTES</th>
                <th style="width: 6%;">JAN</th>
                <th style="width: 6%;">FÉV</th>
                <th style="width: 6%;">MAR</th>
                <th style="width: 6%;">AVR</th>
                <th style="width: 6%;">MAI</th>
                <th style="width: 6%;">JUN</th>
                <th style="width: 6%;">JUL</th>
                <th style="width: 6%;">AOÛ</th>
                <th style="width: 6%;">SEP</th>
                <th style="width: 6%;">OCT</th>
                <th style="width: 6%;">NOV</th>
                <th style="width: 6%;">DÉC</th>
                <th style="width: 12%;">TOTAL COLLECTÉ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collectesParPoste as $poste => $data)
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
                <td class="text-left"><strong>TOTAL COLLECTÉ</strong></td>
                @for($mois = 1; $mois <= 12; $mois++)
                    <td class="text-right"><strong>{{ number_format($totalCollecteMensuel[$mois] ?? 0, 0, ',', ' ') }}</strong></td>
                @endfor
                <td class="text-right"><strong>{{ number_format($totalCollecteGeneral, 0, ',', ' ') }}</strong></td>
            </tr>
            <tr class="total-row">
                <td class="text-left"><strong>TOTAL DÉSTOCKÉ</strong></td>
                @for($mois = 1; $mois <= 12; $mois++)
                    <td class="text-right"><strong>{{ number_format($totalDestockeMensuel[$mois] ?? 0, 0, ',', ' ') }}</strong></td>
                @endfor
                <td class="text-right"><strong>{{ number_format($totalDestockeGeneral, 0, ',', ' ') }}</strong></td>
            </tr>
            <tr class="total-row">
                <td class="text-left"><strong>SOLDE DISPONIBLE</strong></td>
                @for($mois = 1; $mois <= 12; $mois++)
                    <td class="text-right {{ (($totalCollecteMensuel[$mois] ?? 0) - ($totalDestockeMensuel[$mois] ?? 0)) > 0 ? 'positive' : '' }}">
                        <strong>{{ number_format(($totalCollecteMensuel[$mois] ?? 0) - ($totalDestockeMensuel[$mois] ?? 0), 0, ',', ' ') }}</strong>
                    </td>
                @endfor
                <td class="text-right {{ ($totalCollecteGeneral - $totalDestockeGeneral) > 0 ? 'positive' : '' }}">
                    <strong>{{ number_format($totalCollecteGeneral - $totalDestockeGeneral, 0, ',', ' ') }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

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

