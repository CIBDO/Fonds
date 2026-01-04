<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation des Cotisations TRIE/CCIM - {{ $poste->nom }} {{ $annee }}</title>
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

        .poste-info {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            color: #333;
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
            background-color: #e8e8e8;
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
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header clearfix">
        <div class="header-left">
            <div class="title">MINISTÈRE DE L'ÉCONOMIE</div>
            <div class="title" style="margin-left: 40px;">ET DES FINANCES</div>
            <div class="stars" style="margin-left: 20px;">* * * * *</div>
            <div class="subtitle">DIRECTION GÉNÉRALE DU TRÉSOR</div>
            <div class="subtitle">ET DE LA COMPTABILITÉ PUBLIQUE</div>
            <div class="stars" style="margin-left: 20px;">* * * * *</div>
            {{-- <div class="subtitle">POSTE ÉMETTEUR: {{ strtoupper($poste->nom) }}</div> --}}
        </div>
        <div class="header-right">
            <div class="title">RÉPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
        </div>
    </div>

    <!-- Titre principal -->
    <div class="main-title">
        SITUATION DES COTISATIONS AU FONDS DE GARANTIE TRIE p/c CCIM {{ $annee }}
    </div>

    <div class="poste-info">
        POSTE ÉMETTEUR : {{ strtoupper($poste->nom) }}
    </div>

    <div class="subtitle-period">
        PÉRIODE DU 01/01/{{ $annee }} AU {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

    <div style="text-align: center; font-size: 9px; font-style: italic; margin-bottom: 15px; color: #666;">
        (Montants en francs CFA)
    </div>

    <!-- Tableau principal -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 15%;">BUREAU / Mois</th>
                @foreach($donneesParBureau as $bureau)
                    <th colspan="1" style="width: {{ 85 / count($donneesParBureau) }}%;">{{ $bureau['code'] }}</th>
                @endforeach
                <th rowspan="2" style="width: 10%;">TOTAL</th>
            </tr>
            <tr>
                @foreach($donneesParBureau as $bureau)
                    <th style="font-size: 7pt;">{{ $bureau['nom'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $moisList = [
                    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                ];
            @endphp

            @for($m = 1; $m <= 12; $m++)
            <tr>
                <td class="text-left">{{ $moisList[$m] }} {{ $annee }}</td>
                @foreach($donneesParBureau as $bureau)
                    <td class="text-right">
                        @if($bureau['mois'][$m] > 0)
                            {{ number_format($bureau['mois'][$m], 0, ',', ' ') }}
                        @else
                            -
                        @endif
                    </td>
                @endforeach
                <td class="text-right"><strong>{{ number_format($totalMensuel[$m], 0, ',', ' ') }}</strong></td>
            </tr>
            @endfor

            <!-- Ligne TOTAL -->
            <tr class="total-row">
                <td class="text-left">TOTAL</td>
                @foreach($donneesParBureau as $bureau)
                    <td class="text-right">{{ number_format($bureau['total'], 0, ',', ' ') }}</td>
                @endforeach
                <td class="text-right">{{ number_format($totalGeneral, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Signature -->
    <div class="signature">
        <div class="date-signature">
            {{ $poste->nom }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            Le Trésorier Payeur
        </div>
    </div>
</body>
</html>

