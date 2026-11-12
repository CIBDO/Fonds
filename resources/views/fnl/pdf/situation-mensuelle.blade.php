<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de paiement FNL {{ $periodeLibelle }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            padding: 4px;
            font-size: 9px;
            line-height: 1.15;
            color: #000;
        }

        .header {
            margin-bottom: 10px;
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
            margin: 1px 0;
        }

        .subtitle {
            font-size: 9px;
            margin: 1px 0;
        }

        .stars {
            font-size: 7px;
            margin: 2px 0;
        }

        .main-title {
            font-size: 12px;
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0 4px 0;
            text-align: center;
            text-transform: uppercase;
        }

        .region {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 4px;
        }

        .note-montants {
            font-size: 8px;
            font-style: italic;
            text-align: center;
            margin: 2px 0 8px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 8px;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }

        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .section-row td {
            background-color: #cfe8f3;
            font-weight: bold;
            text-align: center;
        }

        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .signature-block {
            page-break-inside: avoid;
            break-inside: avoid;
            margin-top: 14px;
            margin-right: 80px;
            text-align: right;
        }

        .date-signature {
            font-size: 9px;
            margin-bottom: 28px;
        }

        .agent-signature {
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="header-left">
            <div class="title">MINISTÈRE DE L'ÉCONOMIE</div>
            <div class="title">ET DES FINANCES</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            <div class="subtitle">DIRECTION GÉNÉRALE DU TRÉSOR</div>
            <div class="subtitle">ET DE LA COMPTABILITÉ PUBLIQUE</div>
            <div class="stars" style="margin-left: 40px;">**************</div>
            @if(empty($estGlobal))
                <div class="subtitle">{{ $intituleOfficiel }}</div>
            @endif
        </div>

        <div class="header-right">
            <div class="title">RÉPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
            <div class="stars" style="margin-right: 40px;">**************</div>
        </div>
    </div>

    <div class="main-title">
        TABLEAU DE PAIEMENT FONDS NATIONAL DE LOGEMENT (FNL) {{ $periodeLibelle }}
    </div>

    @if(!empty($regionLibelle))
        <div class="region">{{ $regionLibelle }}</div>
    @endif

    <div class="note-montants">(Montants en francs CFA)</div>

    <table>
        <thead>
            <tr>
                <th style="width: 34%;">LIBELLÉS</th>
                <th style="width: 18%;">MONTANT FCFA</th>
                <th style="width: 28%;">RÉF DE PAIEMENT</th>
                <th style="width: 20%;">OBSERVATION</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lignes as $ligne)
                @if(($ligne['type'] ?? '') === 'section')
                    <tr class="section-row">
                        <td colspan="4">{{ $ligne['libelle'] }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="text-left">{{ $ligne['libelle'] }}</td>
                        <td class="text-center">
                            @if($ligne['montant'] !== null && $ligne['montant'] !== '')
                                {{ number_format((float) $ligne['montant'], 0, ',', ' ') }}
                            @endif
                        </td>
                        <td class="text-left">{{ $ligne['reference'] }}</td>
                        <td class="text-left">{{ $ligne['observation'] }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="4">Aucun paiement validé pour cette période.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td class="text-left">TOTAL</td>
                <td class="text-center">{{ number_format((float) $totalRetenue, 0, ',', ' ') }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="signature-block">
        <div class="date-signature">
            {{ $posteNom ?? '' }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            @if(!empty($estGlobal))
                L'Agent Comptable Central du Trésor
            @else
                Le Responsable du Poste comptable
            @endif
        </div>
    </div>
</body>
</html>
