<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de Fonds Salaires - {{ $mois }} {{ $annee }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 11px;
            line-height: 1.3;
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
            font-size: 13px;
            margin: 3px 0;
        }

        .subtitle {
            font-size: 11px;
            margin: 2px 0;
        }

        .stars {
            font-size: 9px;
            margin: 3px 0;
        }

        .main-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin: 25px 0 20px 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 10px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
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
        }

        .date-signature {
            font-size: 11px;
            margin-bottom: 50px;
        }

        .agent-signature {
            font-size: 11px;
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
        DEMANDE DE FONDS SALAIRES DU MOIS DE {{ strtoupper($mois) }} {{ $annee }}
    </div>

    <!-- Tableau -->
    <table>
        <thead>
            <tr>
                <th style="width: 20%;">POSTES</th>
                <th style="width: 16%;">SALAIRE BRUT (1)</th>
                <th style="width: 16%;">REALISATION RECETTES DOUANIERES</th>
                <th style="width: 16%;">SALAIRE DEMANDÉ (2)</th>
                <th style="width: 16%;">SALAIRE ENVOYÉ</th>
                <th style="width: 16%;">OBSERVATIONS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSalaireDemandeAjuste = 0;
            @endphp
            @forelse($demandesParPoste as $demande)
            @php
                // Si REALISATION RECETTES DOUANIERES > SALAIRE BRUT, alors SALAIRE DEMANDÉ = 0
                // Sinon, afficher la différence entre Salaire brut et Recette douanière
                $montantDisponible = $demande['montant_disponible'] ?? 0;
                $salaireBrut = $demande['salaire_brut'] ?? 0;
                $salaireDemandeAffiche = ($montantDisponible > $salaireBrut) ? 0 : max(0, $salaireBrut - $montantDisponible);
                $totalSalaireDemandeAjuste += $salaireDemandeAffiche;
            @endphp
            <tr>
                <td class="text-left"><strong>{{ $demande['poste'] }}</strong></td>
                <td class="text-right">{{ number_format($demande['salaire_brut'], 0, ',', ' ') }}</td>
                <td class="text-right">{{ ($demande['montant_disponible'] > 0) ? number_format($demande['montant_disponible'], 0, ',', ' ') : '-' }}</td>
                <td class="text-right">{{ number_format($salaireDemandeAffiche, 0, ',', ' ') }}</td>
                <td class="text-right">{{ $demande['montant'] !== null ? number_format($demande['montant'], 0, ',', ' ') : '-' }}</td>
                <td>-</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; font-style: italic;">
                    Aucune donnée disponible pour {{ $mois }} {{ $annee }}
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($demandesParPoste->count() > 0)
        <tfoot>
            <tr class="total-row">
                <td class="text-left"><strong>TOTAL GÉNÉRAL</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral['salaire_brut'], 0, ',', ' ') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral['montant_disponible'] ?? 0, 0, ',', ' ') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalSalaireDemandeAjuste, 0, ',', ' ') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalGeneral['montant'] ?? 0, 0, ',', ' ') }}</strong></td>
                <td>-</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Signature -->
    <div class="signature" >
        <div class="date-signature" >
            Bamako, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            L'Agent Comptable Central
        </div>
    </div>
</body>
</html>
