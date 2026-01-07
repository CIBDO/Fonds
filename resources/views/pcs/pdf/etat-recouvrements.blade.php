<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État des Recouvrements PCS - {{ $programme }} {{ $annee }}</title>
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
        ÉTAT DES RECOUVREMENTS DU PCS-{{ $programme ? $programme : 'TOUS LES PROGRAMMES' }} AU TITRE DE L'EXERCICE {{ $annee }}
    </div>

        {{-- <div class="subtitle-period">
            PÉRIODE DU 01/01/{{ $annee }} AU {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div> --}}

    <div style="text-align: center; font-size: 10px; font-style: italic; margin-bottom: 15px; color: #666;">
        (Montants en francs CFA)
    </div>

    <!-- Tableau RECOUVREMENTS -->
    @if(count($recouvrementsParPoste) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 22%;">POSTES</th>
                <th style="width: 6%;">JANVIER</th>
                <th style="width: 6%;">FÉVRIER</th>
                <th style="width: 6%;">MARS</th>
                <th style="width: 6%;">AVRIL</th>
                <th style="width: 6%;">MAI</th>
                <th style="width: 6%;">JUIN</th>
                <th style="width: 6%;">JUILLET</th>
                <th style="width: 6%;">AOÛT</th>
                <th style="width: 6%;">SEPTEMBRE</th>
                <th style="width: 6%;">OCTOBRE</th>
                <th style="width: 6%;">NOVEMBRE</th>
                <th style="width: 6%;">DÉCEMBRE</th>
                <th style="width: 10%;">TOTAL GÉNÉRAL</th>
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
                <td class="text-left"><strong>TOTAL RECOUVREMENTS</strong></td>
                @for($mois = 1; $mois <= 12; $mois++)
                    <td class="text-right"><strong>{{ number_format($totalRecouvrementsMensuel[$mois] ?? 0, 0, ',', ' ') }}</strong></td>
                @endfor
                <td class="text-right"><strong>{{ number_format($totalRecouvrements, 0, ',', ' ') }}</strong></td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="text-align: center; padding: 40px; font-size: 12px; color: #666;">
        <p>Aucune donnée de recouvrement trouvée pour les critères sélectionnés.</p>
    </div>
    @endif

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

