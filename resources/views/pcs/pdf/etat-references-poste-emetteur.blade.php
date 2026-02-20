<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État des références - {{ $poste->nom }} {{ $annee }}</title>
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

        .col-reference {
            font-weight: bold;
            background-color: #e8f4fc;
            min-width: 120px;
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

        .empty-msg {
            font-style: italic;
            color: #666;
            padding: 8px;
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
        </div>
        <div class="header-right">
            <div class="title">RÉPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
            <div class="stars" style="margin-right: 40px;">**************</div>
        </div>
    </div>

    <!-- Titre principal -->
    <div class="main-title">
        ÉTAT DES RÉFÉRENCES – DÉCLARATIONS PCS ET COTISATIONS TRIE – EXERCICE {{ $annee }}
    </div>

    <div class="poste-info">
        POSTE ÉMETTEUR : {{ strtoupper($poste->nom) }}
    </div>

    <div style="text-align: center; font-size: 9px; font-style: italic; margin-bottom: 15px; color: #666;">
        (Montants en francs CFA)
    </div>

    <!-- Tableau 1 : Déclarations PCS (référence en évidence) -->
    <div class="table-section">
        <div class="table-title">DÉCLARATIONS PCS – RÉFÉRENCES</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 18%;" class="col-reference">RÉFÉRENCE</th>
                    <th style="width: 10%;">Programme</th>
                    <th style="width: 10%;">Mois</th>
                    <th style="width: 18%;">Entité</th>
                    <th style="width: 14%;" class="text-right">Recouvrement</th>
                    <th style="width: 14%;" class="text-right">Reversement</th>
                </tr>
            </thead>
            <tbody>
                @if($declarations->isEmpty())
                    <tr>
                        <td colspan="6" class="empty-msg">Aucune déclaration pour cette période.</td>
                    </tr>
                @else
                    @foreach($declarations as $d)
                    <tr>
                        <td class="text-left col-reference">{{ $d->reference ?? '—' }}</td>
                        <td>{{ $d->programme ?? '—' }}</td>
                        <td>{{ $moisList[$d->mois] ?? $d->mois }}</td>
                        <td class="text-left">{{ $d->poste_id && !$d->bureau_douane_id ? ($d->poste->nom ?? '—') : ($d->bureauDouane->libelle ?? '—') }}</td>
                        <td class="text-right">{{ number_format($d->montant_recouvrement ?? 0, 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($d->montant_reversement ?? 0, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Tableau 2 : Cotisations TRIE (référence paiement en évidence) -->
    <div class="table-section">
        <div class="table-title">COTISATIONS TRIE (CCIM) – RÉFÉRENCES DE PAIEMENT</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 22%;" class="col-reference">RÉFÉRENCE PAIEMENT</th>
                    <th style="width: 18%;">Bureau</th>
                    <th style="width: 12%;">Mois</th>
                    <th style="width: 18%;" class="text-right">Montant total</th>
                </tr>
            </thead>
            <tbody>
                @if($cotisations->isEmpty())
                    <tr>
                        <td colspan="4" class="empty-msg">Aucune cotisation pour cette période.</td>
                    </tr>
                @else
                    @foreach($cotisations as $c)
                    <tr>
                        <td class="text-left col-reference">{{ $c->reference_paiement ?? '—' }}</td>
                        <td class="text-left">{{ $c->bureauTrie->nom_bureau ?? '—' }}</td>
                        <td>{{ $moisList[$c->mois] ?? $c->mois }}</td>
                        <td class="text-right">{{ number_format($c->montant_total ?? 0, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
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
