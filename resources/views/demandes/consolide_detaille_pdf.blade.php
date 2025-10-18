<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etat Détaillé par type de personnel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
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

        .filters {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            font-size: 9px;
        }

        .filters p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .signature {
            margin-right: 100px;
            text-align: right;
            margin-top: 30px;
        }

        .date-signature {
            font-size: 11px;
            margin-bottom: 50px;
        }

        .agent-signature {
            font-size: 11px;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 10mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
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
        VUE PAR TYPE DE PERSONNEL - DEMANDES DE FONDS DE SALAIRE
    </div>

    <!-- Filtres appliqués -->
    @if(!empty($filtres) && (isset($filtres['poste']) || isset($filtres['mois']) || isset($filtres['annee']) || isset($filtres['status']) || isset($filtres['date_debut']) || isset($filtres['date_fin'])))
    <div class="filters">
        <strong>Filtres appliqués :</strong><br>
        @if(isset($filtres['poste']) && $filtres['poste'])
            <p><strong>Poste :</strong> {{ $filtres['poste'] }}</p>
        @endif
        @if(isset($filtres['mois']) && $filtres['mois'])
            <p><strong>Mois :</strong> {{ $filtres['mois'] }}</p>
        @endif
        @if(isset($filtres['annee']) && $filtres['annee'])
            <p><strong>Année :</strong> {{ $filtres['annee'] }}</p>
        @endif
        @if(isset($filtres['status']) && $filtres['status'])
            <p><strong>Statut :</strong> {{ ucfirst($filtres['status']) }}</p>
        @endif
        @if(isset($filtres['date_debut']) && $filtres['date_debut'])
            <p><strong>Date début :</strong> {{ \Carbon\Carbon::parse($filtres['date_debut'])->format('d/m/Y') }}</p>
        @endif
        @if(isset($filtres['date_fin']) && $filtres['date_fin'])
            <p><strong>Date fin :</strong> {{ \Carbon\Carbon::parse($filtres['date_fin'])->format('d/m/Y') }}</p>
        @endif
    </div>
    @endif

    <!-- Tableau agrégé -->
    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Désignation</th>
                <th class="text-right" style="width: 14%;">Salaire Net (FCFA)</th>
                <th class="text-right" style="width: 14%;">Reversement (FCFA)</th>
                <th class="text-right" style="width: 14%;">Total Courant (FCFA)</th>
                <th class="text-right" style="width: 14%;">Salaire Ancien (FCFA)</th>
                <th class="text-right" style="width: 14%;">Total Demande (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($typesPersonnel as $type)
            <tr>
                <td><strong>{{ $type['designation'] }}</strong></td>
                <td class="text-right">{{ number_format($type['net'], 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($type['revers'], 0, ',', ' ') }}</td>
                <td class="text-right"><strong>{{ number_format($type['total_courant'], 0, ',', ' ') }}</strong></td>
                <td class="text-right">{{ number_format($type['salaire_ancien'], 0, ',', ' ') }}</td>
                <td class="text-right"><strong>{{ number_format($type['total_demande'], 0, ',', ' ') }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Aucune donnée trouvée avec les critères sélectionnés</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($typesPersonnel) > 0)
        <tfoot>
            <tr class="total-row">
                <th>TOTAUX GÉNÉRAUX</th>
                <th class="text-right">{{ number_format($totaux['total_net'], 0, ',', ' ') }}</th>
                <th class="text-right">{{ number_format($totaux['total_revers'], 0, ',', ' ') }}</th>
                <th class="text-right">{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</th>
                <th class="text-right">{{ number_format($totaux['total_ancien'], 0, ',', ' ') }}</th>
                <th class="text-right">{{ number_format($totaux['total_demande'], 0, ',', ' ') }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Légende -->
    {{-- <div style="margin-top: 20px; font-size: 9px; background-color: #f8f9fa; padding: 10px; border: 1px solid #dee2e6;">
        <strong>Légende :</strong>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li><strong>Salaire Net :</strong> Montant net après déductions</li>
            <li><strong>Reversement :</strong> Montants à reverser (charges, cotisations, etc.)</li>
            <li><strong>Total Courant :</strong> Net + Reversement = Montant total du mois en cours</li>
            <li><strong>Salaire Ancien :</strong> Montant du mois précédent pour comparaison</li>
            <li><strong>Total Demande :</strong> Montant total demandé (peut inclure arriérés)</li>
        </ul>
    </div> --}}

    <!-- Signature -->
    <div class="signature">
        <div class="date-signature">
            Bamako, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="agent-signature">
            L'Agent Comptable Central du Trésor
        </div>
    </div>

    <!-- Pied de page -->
    {{-- <div class="footer">
        <p>Document confidentiel - Direction Générale du Trésor et de la Comptabilité Publique</p>
        <p>Page générée le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
    </div> --}}
</body>
</html>
