<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation des Autres Demandes - {{ $poste->nom }} {{ $annee }}</title>
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
            margin-bottom: 25px;
        }

        .table-title {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .note {
            font-size: 7px;
            font-style: italic;
            color: #555;
            margin-top: 5px;
        }

        .page-break {
            page-break-before: always;
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
        </div>

        <div class="header-right">
            <div class="title">RÉPUBLIQUE DU MALI</div>
            <div class="subtitle">Un Peuple - Un But - Une Foi</div>
            <div class="stars" style="margin-right: 40px;">**************</div>
        </div>
    </div>

    <div class="main-title">
        SITUATION CONSOLIDÉE DES AUTRES DEMANDES FINANCIÈRES AU TITRE DE L'EXERCICE {{ $annee }}
    </div>

    <div class="poste-info">
        POSTE ÉMETTEUR : {{ strtoupper($poste->nom) }}
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

    {{-- Synthèse mensuelle --}}
    <div class="table-section">
        <div class="table-title">SYNTHÈSE MENSUELLE {{ $annee }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 11%;">MOIS</th>
                    <th style="width: 10%;">NB DEMANDES</th>
                    <th style="width: 15%;">MONTANT DEMANDÉ</th>
                    <th style="width: 10%;">NB VERSEMENTS</th>
                    <th style="width: 13%;">MONTANT VERSÉ</th>
                    <th style="width: 14%;">PLAFOND ACCORDÉ*</th>
                    <th style="width: 13%;">% VERSÉ / DEMANDÉ</th>
                </tr>
            </thead>
            <tbody>
                @for($mois = 1; $mois <= 12; $mois++)
                @php
                    $demandeMois = $montantDemandeParMois[$mois] ?? 0;
                    $verseMois = $montantVerseParMois[$mois] ?? 0;
                    $plafondMois = $montantPlafondParMois[$mois] ?? 0;
                    $pourcentageLabel = \App\Models\AutreDemande::pourcentageVerseLabel($verseMois, $demandeMois);
                @endphp
                <tr>
                    <td class="text-left"><strong>{{ $moisList[$mois] }}</strong></td>
                    <td class="text-right">{{ $demandesParMois[$mois] ?? 0 }}</td>
                    <td class="text-right">{{ number_format($demandeMois, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ $versementsParMois[$mois] ?? 0 }}</td>
                    <td class="text-right">{{ number_format($verseMois, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ $plafondMois > 0 ? number_format($plafondMois, 0, ',', ' ') : '-' }}</td>
                    <td class="text-right">{{ $demandeMois > 0 ? $pourcentageLabel : '-' }}</td>
                </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td class="text-left"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>{{ $totalDemandes }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalMontantDemande, 0, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ $totalVersements }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalMontantVerse, 0, ',', ' ') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalMontantPlafond, 0, ',', ' ') }}</strong></td>
                    <td class="text-right">
                        <strong>{{ \App\Models\AutreDemande::pourcentageVerseLabel($totalMontantVerse, $totalMontantDemande) }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="note">
            * Les demandes sont comptabilisées au mois de leur saisie. Les versements sont comptabilisés au mois de leur date effective.
            Le plafond accordé correspond aux demandes ayant reçu au moins un versement dans le mois.
            Reste global à verser sur les demandes avec plafond : <strong>{{ number_format(max(0, $totalMontantPlafond - $totalMontantVerse), 0, ',', ' ') }} FCFA</strong>.
        </div>
    </div>

    {{-- Détail des versements --}}
    @php
        $aUnDetailVersements = $versements->isNotEmpty() || $demandes->contains(fn ($d) => $d->echelons->isEmpty() && $d->statut === 'valide' && $d->montant_accord);
    @endphp
    @if($aUnDetailVersements)
    <div class="table-section">
        <div class="table-title">DÉTAIL DES VERSEMENTS ENREGISTRÉS — {{ $annee }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">DATE VERSEMENT</th>
                    <th style="width: 8%;">DATE DEMANDE</th>
                    <th style="width: 30%;">DÉSIGNATION</th>
                    <th style="width: 8%;">N°</th>
                    <th style="width: 12%;">MONTANT VERSEMENT</th>
                    <th style="width: 12%;">CUMUL VERSÉ</th>
                    <th style="width: 12%;">MONTANT DEMANDÉ</th>
                    <th style="width: 10%;">STATUT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cumulsParDemande = [];
                @endphp
                @foreach($versements as $echelon)
                @php
                    $demande = $echelon->demande;
                    if (! $demande) {
                        continue;
                    }
                    $demandeId = $demande->id;
                    $cumulsParDemande[$demandeId] = ($cumulsParDemande[$demandeId] ?? 0) + (float) $echelon->montant;

                    $statutLabel = match($demande->statut) {
                        'valide' => 'Validé',
                        'soumis' => 'Soumis',
                        'rejete' => 'Rejeté',
                        'brouillon' => 'Brouillon',
                        default => $demande->statut,
                    };
                @endphp
                <tr>
                    <td>{{ $echelon->date_echeance->format('d/m/Y') }}</td>
                    <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                    <td class="text-left">{{ $demande->designation }}</td>
                    <td>{{ $echelon->ordre }}</td>
                    <td class="text-right">{{ number_format($echelon->montant, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($cumulsParDemande[$demandeId], 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($demande->montant, 0, ',', ' ') }}</td>
                    <td>{{ $statutLabel }}</td>
                </tr>
                @endforeach

                @foreach($demandes as $demande)
                @if($demande->echelons->isEmpty() && $demande->statut === 'valide' && $demande->montant_accord)
                @php
                    $dateRef = $demande->date_validation ?? $demande->date_demande;
                @endphp
                @if((int) $dateRef->format('Y') === (int) $annee)
                <tr>
                    <td>{{ $dateRef->format('d/m/Y') }}</td>
                    <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                    <td class="text-left">{{ $demande->designation }}</td>
                    <td>1</td>
                    <td class="text-right">{{ number_format($demande->montant_accord, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($demande->montant_accord, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($demande->montant, 0, ',', ' ') }}</td>
                    <td>Validé</td>
                </tr>
                @endif
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-left"><strong>TOTAL VERSEMENTS</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalMontantVerse, 0, ',', ' ') }}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <div class="signature">
        <div class="date-signature">
            {{ $poste->nom }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            Le Responsable du Poste comptable
        </div>
    </div>
</body>
</html>
