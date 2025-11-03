<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation des Cotisations TRIE/CCIM {{ $annee }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
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
            font-size: 14px;
            margin: 3px 0;
        }

        .main-title {
            font-size: 12px;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 7px;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 6px;
        }

        th.header-poste {
            background-color: #d8d8d8;
            font-size: 7px;
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
            background-color: #e8e8e8;
            font-weight: bold;
        }

        .summary-table {
            width: 70%;
            margin: 20px auto;
            font-size: 8px;
        }

        .summary-table th {
            background-color: #d8d8d8;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header clearfix">
        <div class="header-left">
            <div class="title">MINISTERE DE L'ECONOMIE</div>
            <div class="title" style="margin-left: 40px;">ET DES FINANCES</div>
            <div class="stars" style="margin-left: 20px;">* * * * *</div>
            <div class="subtitle">DIRECTION GENERALE DU TRESOR</div>
            <div class="subtitle">ET DE LA COMPTABILITE PUBLIQUE</div>
        </div>
        <div class="header-right">
            <div class="title">REPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
           {{--  <div class="stars">* * * * *</div> --}}
        </div>
    </div>

    <!-- Titre principal -->
    <div class="main-title">
        SITUATION DES COTISATIONS AU FONDS DE GARANTIE TRIE p/c CCIM {{ $annee }}
    </div>

    <!-- Tableau principal détaillé -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 12%;">POSTE / Mois</th>
                @foreach($donneesParPoste as $poste)
                    <th colspan="{{ count($poste['bureaux']) }}" class="header-poste">{{ $poste['nom'] }}</th>
                @endforeach
                <th rowspan="2" style="width: 10%;">TOTAL</th>
            </tr>
            <tr>
                @foreach($donneesParPoste as $poste)
                    @foreach($poste['bureaux'] as $bureau)
                        <th style="font-size: 7pt;">{{ $bureau['nom'] }}</th>
                    @endforeach
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
                @foreach($donneesParPoste as $poste)
                    @foreach($poste['bureaux'] as $bureau)
                        <td class="text-right">
                            @if($bureau['mois'][$m] > 0)
                                {{ number_format($bureau['mois'][$m], 0, ',', ' ') }}
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                @endforeach
                <td class="text-right"><strong>{{ number_format($totauxMensuels[$m], 0, ',', ' ') }}</strong></td>
            </tr>
            @endfor

            <!-- Ligne TOTAL -->
            <tr class="total-row">
                <td class="text-left">TOTAL</td>
                @foreach($donneesParPoste as $poste)
                    @foreach($poste['bureaux'] as $bureau)
                        <td class="text-right">{{ number_format($bureau['total'], 0, ',', ' ') }}</td>
                    @endforeach
                @endforeach
                <td class="text-right">{{ number_format($totalGeneral, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Tableau récapitulatif 2 années -->
    <table class="summary-table">
        <thead>
            <tr>
                <th style="width: 40%;">DESIGNATION</th>
                <th style="width: 25%;">{{ $anneePrecedente }}</th>
                <th style="width: 25%;">{{ $annee }}</th>
                <th style="width: 25%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAnneePrecedente = 0;
                $totalAnneeActuelle = 0;

                // Fusionner les postes des deux années
                $tousPostes = array_unique(array_merge(
                    array_keys($totauxParPosteAnneePrecedente),
                    array_keys($totauxParPosteAnneeActuelle)
                ));
                sort($tousPostes);
            @endphp

            @foreach($tousPostes as $posteNom)
                @php
                    $montantPrecedent = $totauxParPosteAnneePrecedente[$posteNom] ?? 0;
                    $montantActuel = $totauxParPosteAnneeActuelle[$posteNom] ?? 0;
                    $totalPoste = $montantPrecedent + $montantActuel;

                    $totalAnneePrecedente += $montantPrecedent;
                    $totalAnneeActuelle += $montantActuel;
                @endphp
                <tr>
                    <td class="text-left">{{ $posteNom }}</td>
                    <td class="text-right">{{ number_format($montantPrecedent, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($montantActuel, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($totalPoste, 0, ',', ' ') }}</td>
                </tr>
            @endforeach

            <!-- Ligne TOTAL -->
            <tr class="total-row">
                <td class="text-left">TOTAL</td>
                <td class="text-right">{{ number_format($totalAnneePrecedente, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($totalAnneeActuelle, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($totalAnneePrecedente + $totalAnneeActuelle, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

