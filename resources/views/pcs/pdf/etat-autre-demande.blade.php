<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État Demande Financière - {{ $demande->poste->nom }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 10px;
            line-height: 1.3;
        }

        .header { margin-bottom: 20px; }
        .header-left { float: left; width: 45%; text-align: left; }
        .header-right { float: right; width: 45%; text-align: right; }
        .clearfix::after { content: ""; display: table; clear: both; }
        .title { font-weight: bold; font-size: 12px; margin: 3px 0; }
        .subtitle { font-size: 10px; margin: 2px 0; }
        .stars { font-size: 8px; margin: 3px 0; }

        .main-title {
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0 12px 0;
            text-align: center;
        }

        .poste-info {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 12px;
            color: #333;
        }

        .note-centre {
            text-align: center;
            font-size: 9px;
            font-style: italic;
            margin-bottom: 12px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #f5f5f5; font-weight: bold; }

        .info-table td:first-child {
            width: 32%;
            font-weight: bold;
            background-color: #fafafa;
        }

        .table-title {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin: 18px 0 8px 0;
            text-decoration: underline;
        }

        .signature {
            margin-top: 35px;
            text-align: right;
            margin-right: 60px;
        }

        .date-signature { font-size: 10px; margin-bottom: 45px; }
        .agent-signature { font-size: 10px; font-weight: bold; }
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
        </div>
        <div class="header-right">
            <div class="title">RÉPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
            <div class="stars" style="margin-right: 40px;">**************</div>
        </div>
    </div>

    <div class="main-title">
        ÉTAT DE LA DEMANDE FINANCIÈRE
    </div>

    <div class="poste-info">
        POSTE ÉMETTEUR : {{ strtoupper($demande->poste->nom) }}
    </div>

    <div class="note-centre">(Montants en francs CFA)</div>

    <table class="info-table">
        <tbody>
            <tr>
                <td>Date de la demande</td>
                <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Année budgétaire</td>
                <td>{{ $demande->annee }}</td>
            </tr>
            <tr>
                <td>Saisi par</td>
                <td>{{ $demande->saisiPar->name ?? 'N/A' }}</td>
            </tr>
            @if($demande->validePar)
            <tr>
                <td>Validé par</td>
                <td>{{ $demande->validePar->name }}@if($demande->date_validation) — {{ $demande->date_validation->format('d/m/Y à H:i') }}@endif</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="table-title">
        {{ $demandesGroupe->count() > 1 ? 'DÉSIGNATIONS DE LA DEMANDE' : 'DÉSIGNATION DE LA DEMANDE' }}
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">DÉSIGNATION</th>
                <th style="width: 17%;">MONTANT DEMANDÉ</th>
                <th style="width: 17%;">MONTANT VERSÉ</th>
                <th style="width: 16%;">RESTE À VERSER</th>
            </tr>
        </thead>
        <tbody>
            @foreach($demandesGroupe as $ligne)
            <tr>
                <td>{{ $ligne->designation }}</td>
                <td class="text-right">{{ number_format($ligne->montant, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($ligne->montant_verse_cumule, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($ligne->montant_restant_accord, 0, ',', ' ') }}</td>
            </tr>
            @if($ligne->observation)
            <tr>
                <td colspan="4" style="font-style: italic; font-size: 8px;">
                    Observation : {{ $ligne->observation }}
                </td>
            </tr>
            @endif
            @if($ligne->motif_rejet)
            <tr>
                <td colspan="4" style="font-style: italic; font-size: 8px; color: #a00;">
                    Motif du rejet : {{ $ligne->motif_rejet }}
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-right">TOTAUX</td>
                <td class="text-right">{{ number_format($montantDemande, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($montantVerse, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($montantRestant, 0, ',', ' ') }}</td>
            </tr>
        </tfoot>
    </table>

    @if($aDetailVersements)
    <div class="table-title">DÉTAIL DES VERSEMENTS ENREGISTRÉS</div>
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">DATE VERSEMENT</th>
                <th style="width: 12%;">DATE DEMANDE</th>
                <th style="width: 34%;">DÉSIGNATION</th>
                <th style="width: 14%;">MONTANT VERSEMENT</th>
                <th style="width: 14%;">CUMUL VERSÉ</th>
                <th style="width: 14%;">MONTANT DEMANDÉ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($demandesGroupe as $ligne)
                @php $cumul = 0; @endphp
                @foreach($ligne->echelons as $echelon)
                @php $cumul += (float) $echelon->montant; @endphp
                <tr>
                    <td class="text-center">{{ $echelon->date_echeance->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $ligne->date_demande->format('d/m/Y') }}</td>
                    <td>{{ $ligne->designation }}</td>
                    <td class="text-right">{{ number_format($echelon->montant, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($cumul, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($ligne->montant, 0, ',', ' ') }}</td>
                </tr>
                @endforeach

                @if($ligne->echelons->isEmpty() && $ligne->statut === 'valide' && $ligne->montant_accord)
                @php
                    $dateRef = $ligne->date_validation ?? $ligne->date_demande;
                @endphp
                <tr>
                    <td class="text-center">{{ $dateRef->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $ligne->date_demande->format('d/m/Y') }}</td>
                    <td>{{ $ligne->designation }}</td>
                    <td class="text-right">{{ number_format($ligne->montant_accord, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($ligne->montant_accord, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($ligne->montant, 0, ',', ' ') }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL VERSEMENTS</td>
                <td class="text-right">{{ number_format($montantVerse, 0, ',', ' ') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="signature">
        <div class="date-signature">
            {{ $demande->poste->nom }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">Le Responsable du Poste comptable</div>
    </div>
</body>
</html>
