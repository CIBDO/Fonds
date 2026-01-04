<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation des Autres Demandes - {{ $poste->nom }} {{ $annee }}</title>
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
            <div class="title">ET DES FINANCES</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            <div class="subtitle">DIRECTION GÉNÉRALE DU TRÉSOR</div>
            <div class="subtitle">ET DE LA COMPTABILITÉ PUBLIQUE</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            {{-- <div class="subtitle">POSTE ÉMETTEUR: {{ strtoupper($poste->nom) }}</div> --}}
        </div>

        <div class="header-right">
            <div class="title">RÉPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
            <div class="stars" style="margin-right: 40px;">**************</div>
        </div>
    </div>

    <!-- Titre principal -->
    <div class="main-title">
        SITUATION CONSOLIDÉE DES AUTRES DEMANDES FINANCIÈRES PCS AU TITRE DE L'EXERCICE {{ $annee }}
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

    @php
        $moisList = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
    @endphp

    <!-- Tableau des Autres Demandes -->
    <div class="table-section">
        <div class="table-title">AUTRES DEMANDES FINANCIÈRES {{ $annee }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">MOIS</th>
                    <th style="width: 20%;">NOMBRE DE DEMANDES</th>
                    <th style="width: 21.25%;">MONTANT DEMANDÉ</th>
                    <th style="width: 21.25%;">MONTANT ACCORDÉ</th>
                    <th style="width: 22.5%;">% ACCORD</th>
                </tr>
            </thead>
            <tbody>
                @for($mois = 1; $mois <= 12; $mois++)
                <tr>
                    <td class="text-left"><strong>{{ $moisList[$mois] }}</strong></td>
                    <td class="text-right">{{ $demandesSoumisesParMois[$mois] ?? 0 }}</td>
                    <td class="text-right">{{ number_format($montantSoumisParMois[$mois] ?? 0, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($montantValideParMois[$mois] ?? 0, 0, ',', ' ') }}</td>
                    <td class="text-right">
                        @php
                            $pourcentage = ($montantSoumisParMois[$mois] ?? 0) > 0
                                ? round((($montantValideParMois[$mois] ?? 0) / ($montantSoumisParMois[$mois] ?? 1)) * 100, 1)
                                : 0;
                        @endphp
                        {{ $pourcentage }}%
                    </td>
                </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td class="text-left"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>{{ $totalDemandesSoumises }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalMontantSoumis, 0, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalMontantValide, 0, ',', ' ') }}</strong></td>
                    <td class="text-right">
                        <strong>
                            @php
                                $pourcentageTotal = $totalMontantSoumis > 0
                                    ? round(($totalMontantValide / $totalMontantSoumis) * 100, 1)
                                    : 0;
                            @endphp
                            {{ $pourcentageTotal }}%
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

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

